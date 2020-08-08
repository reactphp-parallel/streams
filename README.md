# Streams abstraction around ext-parallel Channels for ReactPHP

![Continuous Integration](https://github.com/Reactphp-parallel/streams/workflows/Continuous%20Integration/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/React-parallel/streams/v/stable.png)](https://packagist.org/packages/React-parallel/streams)
[![Total Downloads](https://poser.pugx.org/React-parallel/streams/downloads.png)](https://packagist.org/packages/React-parallel/streams)
[![Code Coverage](https://scrutinizer-ci.com/g/Reactphp-parallel/streams/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Reactphp-parallel/streams/?branch=master)
[![Type Coverage](https://shepherd.dev/github/Reactphp-parallel/streams/coverage.svg)](https://shepherd.dev/github/Reactphp-parallel/streams)
[![License](https://poser.pugx.org/React-parallel/streams/license.png)](https://packagist.org/packages/React-parallel/streams)

### Installation ###

To install via [Composer](http://getcomposer.org/), use the command below, it will automatically detect the latest version and bind it with `~`.

```
composer require react-parallel/streams 
```

# Usage

This package currently only offers channel to observable conversion through two methods:

```php
$loop = EventLoopFactory::create();
$eventLoopBridge = new EventLoopBridge($loop);
$factory = new Factory($eventLoopBridge);
$factory->channel($channel); // Returns an observble that will continue until the channel closes
$factory->single($channel); // Returns a promise that will resolve with the first message it receives
```

## Contributing ##

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License ##

Copyright 2020 [Cees-Jan Kiewiet](http://wyrihaximus.net/)

Permission is hereby granted, free of charge, to any person
obtaining a copy of this software and associated documentation
files (the "Software"), to deal in the Software without
restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following
conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.
