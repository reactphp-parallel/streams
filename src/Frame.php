<?php declare(strict_types=1);

namespace ReactParallel\Streams;

use function serialize;
use function unserialize;

final class Frame
{
    private string $frame;

    public function __construct($frame)
    {
        $this->frame = serialize($frame);
    }

    /**
     * @return mixed
     */
    public function frame()
    {
        return unserialize($this->frame);
    }
}
