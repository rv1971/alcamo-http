<?php

namespace alcamo\http;

use alcamo\iana\MediaType;
use alcamo\rdfa\RdfaData;

/**
 * @brief Send a file as response
 */
class FileResponse extends Response
{
    /**
     * @brief Create from path
     *
     * Automatically generates `Content-Length` and `Content-Type` headers.
     */
    public static function newFromPath(string $path, $rdfaData = null): Response
    {
        $autoRdfaData = RdfaData::newFromIterable(
            [
                'dc:format' => MediaType::newFromFilename($path),
                'http:content-length' => filesize($path)
            ]
        );

        if ($rdfaData instanceof RdfaData) {
            $rdfaData = $autoRdfaData->replace($rdfaData);
        } elseif (isset($rdfaData)) {
            $rdfaData =
                $autoRdfaData->replace(RdfaData::newFromIterable($rdfaData));
        } else {
            $rdfaData = $autoRdfaData;
        }

        return new self(
            $rdfaData,
            new ResourceStream($path),
            200
        );
    }
}
