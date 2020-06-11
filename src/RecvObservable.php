<?php declare(strict_types=1);

namespace ReactParallel\Streams;

use parallel\Events;
use React\EventLoop\LoopInterface;
use React\EventLoop\TimerInterface;
use Rx\Observable;
use Rx\Subject\Subject;
use Throwable;

final class RecvObservable
{
    private LoopInterface $loop;

    private Events $events;

    public function __construct(LoopInterface $loop, Events $events)
    {
        $this->loop   = $loop;
        $this->events = $events;
    }

    public function recv(): Observable
    {
        $subject = new Subject();

        // Call 1K times per second
        $timer = $this->loop->addPeriodicTimer(0.05, function () use (&$timer, $subject): void {
            try {
                while ($event = $this->events->poll()) {
                    if ($event->type === Events\Event\Type::Read) {
                        $subject->onNext($event->value);
                        $this->events->addChannel($event->object);

                        break;
                    }

                    if ($event->type !== Events\Event\Type::Close) {
                        break;
                    }

                    if ($timer instanceof TimerInterface) {
                        $this->loop->cancelTimer($timer);
                    }

                    $subject->onCompleted();

                    return;
                }
            } catch (Events\Error\Timeout $timeout) {
                return;
            } catch (Throwable $throwable) {
                if ($timer instanceof TimerInterface) {
                    $this->loop->cancelTimer($timer);
                }

                $subject->onError($throwable);
            }
        });

        return $subject;
    }
}
