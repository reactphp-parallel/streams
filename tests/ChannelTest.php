<?php declare(strict_types=1);

namespace ReactParallel\Tests\Streams;

use parallel\Channel;
use React\EventLoop\Factory;
use ReactParallel\EventLoop\EventLoopBridge;
use ReactParallel\Streams\Factory as StreamFactory;
use ReactParallel\Streams\RecvObservable;
use WyriHaximus\AsyncTestUtilities\AsyncTestCase;
use function parallel\run;
use function React\Promise\all;
use function sleep;

/**
 * @internal
 */
final class ChannelTest extends AsyncTestCase
{
    /**
     * @test
     */
    public function channel(): void
    {
        $d = bin2hex(random_bytes(13));

        $loop = Factory::create();
        $channels = [Channel::make($d . '_a', Channel::Infinite), Channel::make($d . '_b', Channel::Infinite)];

        $recvObservable = new StreamFactory(new EventLoopBridge($loop));

        run(function () use ($channels): void {
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
            $promises[] = $recvObservable->channel($channel)->toArray()->toPromise();
        }

        $rd = $this->await(all($promises), $loop, 3.3);

        $range = [];
        foreach (range(0, 13) as $i) {
            foreach (range(0, 66) as $j) {
                $range[] = $i;
            }
        }
        self::assertSame([$range, $range], $rd);
    }
}
