<?php declare(strict_types=1);

namespace ReactParallel\Streams;

use parallel\Channel;
use function serialize;
use function unserialize;

final class Stream
{
    private Channel $channel;

    public function __construct(Channel $channel)
    {
        $this->channel = $channel;
    }

    public static function createFromName(string $name): self
    {
        return new self(Channel::open($name));
    }

    /**
     * @return mixed
     */
    public function recv()
    {
        return unserialize($this->channel->recv());
    }

    /**
     * @param mixed $value
     */
    public function send($value): void
    {
        $this->channel->send(serialize($value));
    }

    public function name(): string
    {
        return (string) $this->channel;
    }
}
