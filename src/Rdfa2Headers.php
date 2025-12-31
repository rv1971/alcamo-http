<?php

namespace alcamo\http;

use alcamo\rdfa\{
        DcFormat,
        DcLanguage,
        DcModified,
        DcSource,
        HttpCacheControl,
        HttpContentDisposition,
        HttpContentLength,
        HttpExpires,
        RdfaData,
        StmtInterface
};

class Rdfa2Headers
{
    public const PROP_URI_TO_HEADER = [
        DcFormat::PROP_URI               => 'Content-Type',
        DcLanguage::PROP_URI             => 'Content-Language',
        DcModified::PROP_URI             => 'Last-Modified',
        DcSource::PROP_URI               => 'Link',
        HttpCacheControl::PROP_URI       => 'Cache-Control',
        HttpContentDisposition::PROP_URI => 'Content-Disposition',
        HttpContentLength::PROP_URI      => 'Content-Length',
        HttpExpires::PROP_URI            => 'Expires'
    ];

    public const PROP_URI_TO_FORMATTER = [
        DcModified::PROP_URI             => 'formatDcModified',
        DcSource::PROP_URI               => 'formatDcSource',
        HttpContentDisposition::PROP_URI => 'formatHttpContentDisposition',
        HttpExpires::PROP_URI            => 'formatHttpExpires'
    ];

    /** @return pair of header name and header value, or null */
    public function stmt2Pair(StmtInterface $stmt): ?array
    {
        $uri = $stmt->getPropUri();

        $header = static::PROP_URI_TO_HEADER[$uri] ?? null;

        if (!isset($header)) {
            return null;
        }

        $formatter = static::PROP_URI_TO_FORMATTER[$uri] ?? null;

        return [
            $header,
            isset($formatter)
            ? $this->$formatter($stmt->getObject())
            : (string)$stmt
        ];
    }

    public function formatDcModified($object): string
    {
        return
            (
                $object instanceof \DateTimeInterface
                ? $object
                : (new \DateTimeImmutable($object))
            )
            ->format('r');
    }

    public function formatDcSource($object): string
    {
        return "<$object>; rel=\"canonical\"";
    }

    public function formatHttpContentDisposition($object): string
    {
        return "attachment; filename=\"$object\"";
    }

    public function formatHttpExpires($object): string
    {
        return (new \DateTimeImmutable())
            ->add(
                $object instanceof \DateInterval
                ? $object
                : new \DateInterval($object)
            )
            ->format('r');
    }

    /** @return array mapping header names to arrays of values */
    public function createHeaders(RdfaData $rdfaData): array
    {
        $headers = [];

        foreach ($rdfaData as $stmts) {
            foreach ($stmts as $stmt) {
                [ $name, $value ] = $this->stmt2Pair($stmt);

                if (!isset($name)) {
                    continue;
                }

                if (isset($headers[$name])) {
                    $headers[$name][] = $value;
                } else {
                    $headers[$name] = [ $value ];
                }
            }
        }

        return $headers;
    }
}
