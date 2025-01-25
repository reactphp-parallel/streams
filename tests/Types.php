<?php

declare(strict_types=1);

use parallel\Channel;
use ReactParallel\EventLoop\EventLoopBridge;
use ReactParallel\Streams\Factory;

use function PHPStan\Testing\assertType;

$factory = new Factory(new EventLoopBridge());

/**
 * Observe
 */

/** @var Channel<bool> $channelBool */
$channelBool = new Channel();

/** @var Channel<stdClass> $channelStd */
$channelStd = new Channel();

assertType('iterable<bool>', $factory->channel($channelBool));
assertType('iterable<stdClass>', $factory->channel($channelStd));

assertType('bool|null', $factory->single($channelBool));
assertType('stdClass|null', $factory->single($channelStd));
