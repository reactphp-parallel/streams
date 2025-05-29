<?php

declare(strict_types=1);

namespace ReactParallel\Tests\Streams;

use parallel\Channel;
use PHPUnit\Framework\Attributes\Test;
use React\EventLoop\Loop;
use ReactParallel\EventLoop\EventLoopBridge;
use ReactParallel\Streams\Factory as StreamFactory;
use WyriHaximus\AsyncTestUtilities\AsyncTestCase;

use function bin2hex;
use function random_bytes;

final class SingleTest extends AsyncTestCase
{
    #[Test]
    public function single(): void
    {
        $d = bin2hex(random_bytes(13));

        $channel = Channel::make($d, Channel::Infinite);

        $singleRecv = new StreamFactory(new EventLoopBridge());

        Loop::addTimer(2, static function () use ($channel, $d): void {
            $channel->send($d);
        });
        Loop::addTimer(3, static function () use ($channel): void {
            $channel->close();
        });

        self::assertSame($d, $singleRecv->single($channel));
    }

    #[Test]
    public function timedOut(): void
    {
        $d = bin2hex(random_bytes(13));

        $channel = Channel::make($d, Channel::Infinite);

        $singleRecv = new StreamFactory(new EventLoopBridge());

        Loop::futureTick(static function () use ($channel): void {
            $channel->close();
        });

        self::assertNull($singleRecv->single($channel));
    }
}
