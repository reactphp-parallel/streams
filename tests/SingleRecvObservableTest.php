<?php declare(strict_types=1);

namespace ReactParallel\Tests\Streams;

use parallel\Channel;
use parallel\Events;
use React\EventLoop\Factory;
use ReactParallel\Streams\RecvObservable;
use WyriHaximus\AsyncTestUtilities\AsyncTestCase;
use function parallel\run;
use function sleep;

/**
 * @internal
 */
final class SingleRecvObservableTest extends AsyncTestCase
{
    /**
     * @test
     */
    public function recv(): void
    {
        $d = bin2hex(random_bytes(13));

        $loop = Factory::create();
        $channel = Channel::make($d, Channel::Infinite);
        $events = new Events();
        $events->setTimeout(0);
        $events->addChannel($channel);

        $recvObservable = new RecvObservable($loop, $events);

        run(function () use ($channel): void {
            foreach (range(0, 13) as $i) {
                usleep(100);
                $channel->send($i);
            }
            sleep(1);
            $channel->close();
        });

        $rd = $this->await($recvObservable->recv()->toArray()->toPromise(), $loop, 3.3);

        self::assertSame(range(0, 13), $rd);
    }
}
