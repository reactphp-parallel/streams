<?php declare(strict_types=1);

namespace ReactParallel\Tests\Streams;

use parallel\Channel;
use parallel\Events;
use React\EventLoop\Factory;
use React\Promise\ExtendedPromiseInterface;
use ReactParallel\Streams\SingleRecv;
use WyriHaximus\AsyncTestUtilities\AsyncTestCase;
use ReactParallel\FutureToPromiseConverter\FutureToPromiseConverter;
use ReactParallel\Runtime\Runtime;
use parallel\Runtime\Error\Closed;
use function Safe\sleep;
use function WyriHaximus\React\timedPromise;

/**
 * @internal
 */
final class SingleRecvTest extends AsyncTestCase
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

        $singleRecv = new SingleRecv($loop, $events);

        $loop->addTimer(2, function () use ($channel, $d): void {
            $channel->send($d);
        });

        $rd = $this->await($singleRecv->recv(), $loop, 3.3);

        self::assertSame($d, $rd);
    }

    /**
     * @test
     */
    public function timedOut(): void
    {
        $d = bin2hex(random_bytes(13));

        $loop = Factory::create();
        $channel = Channel::make($d, Channel::Infinite);
        $events = new Events();
        $events->setTimeout(0);
        $events->addChannel($channel);

        $singleRecv = new SingleRecv($loop, $events);

        $loop->futureTick(static function () use ($channel): void {
            $channel->close();
        });

        $rd = $this->await($singleRecv->recv(), $loop, 3.3);

        self::assertNull($rd);
    }
}
