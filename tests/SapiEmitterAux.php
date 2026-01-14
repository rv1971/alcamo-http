<?php

namespace alcamo\http;

use alcamo\process\OutputProcess;
use alcamo\rdfa\RdfaData;

require getenv('PHPUNIT_COMPOSER_INSTALL');

[ , $type, $text ] = $argv;

$emitter = new SapiEmitter();

switch ($type) {
    case 'text':
        $response = Response::newFromStatusAndText(200, $text);

        break;

    case 'pipe':
        $stream = new PipeStream(new OutputProcess($text));

        $response = new Response(RdfaData::newFromIterable([]), $stream);

        break;
}

$emitter->emit($response);
