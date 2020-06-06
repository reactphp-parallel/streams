<?php declare(strict_types=1);

namespace ReactParallel\Streams;

use parallel\Events;
use React\EventLoop\LoopInterface;
use React\EventLoop\TimerInterface;
use React\Promise\Promise;
use React\Promise\PromiseInterface;
use Throwable;
use function in_array;

final class SingleRecv
{
    private LoopInterface $loop;

    private Events $events;

    public function __construct(LoopInterface $loop, Events $events)
    {
        $this->loop   = $loop;
        $this->events = $events;
    }

    public function recv(): PromiseInterface
    {
        return new Promise(function (callable $resolve, callable $reject): void {
            // Call 1K times per second
            $timer = $this->loop->addPeriodicTimer(0.001, function () use (&$timer, $resolve): void {
                try {
                    while ($event = $this->events->poll()) {
                        if (! in_array($event->type, [Events\Event\Type::Read, Events\Event\Type::Close], true)) {
                            continue;
                        }

                        if ($timer instanceof TimerInterface) {
                            $this->loop->cancelTimer($timer);
                        }

                        $resolve($event->value);

                        return;
                    }
                } catch (Events\Error\Timeout $timeout) {
                    return;
                } catch (Throwable $throwable) {
                    if ($timer instanceof TimerInterface) {
                        $this->loop->cancelTimer($timer);
                    }

                    throw $throwable;
                }
            });
        });
    }
}
