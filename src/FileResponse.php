<?php

namespace alcamo\http;

use alcamo\rdfa\{MediaType, RdfaData};

/**
 * @brief Send a file as response
 *
 * @date Last reviewed 2026-01-14
 */
class FileResponse extends Response
{
    /**
     * @brief Create from path
     *
     * @param $path Path ot file to send.
     *
     * @param RdfaData|array $rdfaData RdfaData onject or iterable of pairs
     * consisting of a property CURIE and object data.
     *
     * Automatically generates `Content-Length` and `Content-Type` headers.
     */
    public static function newFromPath(string $path, $rdfaData = null): Response
    {
        $defaultRdfaData = RdfaData::newFromIterable(
            [
                [ 'dc:format', MediaType::newFromFilename($path) ],
                [ 'http:content-length', filesize($path) ]
            ]
        );

        if (isset($rdfaData)) {
            $rdfaData = $defaultRdfaData->replace(
                $rdfaData instanceof RdfaData
                    ? $rdfaData
                    : RdfaData::newFromIterable($rdfaData)
            );
        } else {
            $rdfaData = $defaultRdfaData;
        }

        return new self(
            $rdfaData,
            new ResourceStream($path),
            200
        );
    }
}
