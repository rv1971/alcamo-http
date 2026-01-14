<?php

namespace alcamo\http;

use PHPUnit\Framework\TestCase;
use alcamo\exception\Closed;
use alcamo\process\OutputProcess;

class PipeStreamTest extends TestCase
{
    /**
     * @dataProvider basicsProvider
     */
    public function testBasics($command, $expectedOutput): void
    {
        $stream = new PipeStream(new OutputProcess($command));

        $this->assertNull($stream->getStatus());

        $this->expectOutputString($expectedOutput);

        $count = $stream->emit();

        $this->assertSame(strlen($expectedOutput), $count);

        $stream->close();

        $this->assertSame(0, $stream->getStatus());
    }

    public function basicsProvider(): array
    {
        return [
            [ 'echo foo', 'foo' . PHP_EOL ],
            [ 'echo bar baz qux', 'bar baz qux' . PHP_EOL ],
        ];
    }

    public function testEmitException(): void
    {
        $stream = new PipeStream(new OutputProcess('echo'));

        $content = (string)$stream;

        $stream->close();

        $this->expectException(Closed::class);
        $this->expectExceptionMessage(
            'Attempt to use closed object <alcamo\http\PipeStream>""'
        );

        $stream->emit();
    }
}
