<?php declare(strict_types=1);

namespace ReactParallel\Tests\Streams;

use parallel\Channel;
use React\EventLoop\Factory;
use ReactParallel\EventLoop\EventLoopBridge;
use ReactParallel\FutureToPromiseConverter\FutureToPromiseConverter;
use ReactParallel\Runtime\Runtime;
use ReactParallel\Streams\Factory as StreamFactory;
use ReactParallel\Streams\SingleRecv;
use WyriHaximus\AsyncTestUtilities\AsyncTestCase;

/**
 * @internal
 */
final class SingleTest extends AsyncTestCase
{
    /**
     * @test
     */
    public function single(): void
    {
        $d = bin2hex(random_bytes(13));

        $loop = Factory::create();
        $channel = Channel::make($d, Channel::Infinite);

        $singleRecv = new StreamFactory(new EventLoopBridge($loop));

        $loop->addTimer(2, function () use ($channel, $d): void {
            $channel->send($d);
        });

        $rd = $this->await($singleRecv->single($channel), $loop, 3.3);

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

        $singleRecv = new StreamFactory(new EventLoopBridge($loop));

        $loop->futureTick(static function () use ($channel): void {
            $channel->close();
        });

        $rd = $this->await($singleRecv->single($channel), $loop, 3.3);

        self::assertNull($rd);
    }
}
