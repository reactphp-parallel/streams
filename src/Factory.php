<?php

declare(strict_types=1);

namespace ReactParallel\Streams;

use parallel\Channel;
use React\Promise\PromiseInterface;
use ReactParallel\EventLoop\EventLoopBridge;
use Rx\Observable;

final class Factory
{
    private EventLoopBridge $loop;

    public function __construct(EventLoopBridge $loop)
    {
        $this->loop = $loop;
    }

    public function stream(): Observable
    {
        return $this->stream(Channel::open($stream->name()));
    }


    public function channel(Channel $channel): Observable
    {
        return $this->loop->observe($channel);
    }

    public function single(Channel $channel): PromiseInterface
    {
        return $this->channel($channel)->take(1)->toPromise();
    }
}
