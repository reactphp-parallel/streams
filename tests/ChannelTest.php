<?php

declare(strict_types=1);

namespace ReactParallel\Tests\Streams;

use parallel\Channel;
use ReactParallel\EventLoop\EventLoopBridge;
use ReactParallel\Streams\Factory as StreamFactory;
use WyriHaximus\AsyncTestUtilities\AsyncTestCase;

use function bin2hex;
use function parallel\run;
use function random_bytes;
use function range;
use function React\Async\async;
use function React\Async\await;
use function React\Promise\all;
use function sleep;
use function usleep;

final class ChannelTest extends AsyncTestCase
{
    /** @test */
    public function channel(): void
    {
        $d = bin2hex(random_bytes(13));

        $channels = [Channel::make($d . '_a', Channel::Infinite), Channel::make($d . '_b', Channel::Infinite)];

        $recvObservable = new StreamFactory(new EventLoopBridge());

        run(static function () use ($channels): void {
            foreach (range(0, 13) as $i) {
                usleep(100);
                foreach (range(0, 66) as $j) {
                    foreach ($channels as $channel) {
                        $channel->send($i);
                    }
                }
            }

            sleep(1);
            foreach ($channels as $channel) {
                $channel->close();
            }
        });

        $promises = [];
        foreach ($channels as $channel) {
            $promises[] = async(
                static fn (Channel $channel): array => [
                    ...$recvObservable->channel($channel),
                ]
            )($channel);
        }

        $rd = await(all($promises));

        $range = [];
        foreach (range(0, 13) as $i) {
            foreach (range(0, 66) as $j) {
                $range[] = $i;
            }
        }

        self::assertSame([$range, $range], $rd);
    }
}
