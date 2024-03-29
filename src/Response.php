<?php

namespace alcamo\http;

use Laminas\Diactoros\Response as BaseResponse;
use alcamo\rdfa\RdfaData;

/**
 * @brief Enhanced response
 */
class Response extends BaseResponse
{
    private $rdfaData_; ///< RdfaData

    public const DEFAULT_RDFA_DATA = [ 'dc:format' => 'text/plain' ];

    public static function newFromStatusAndText(
        int $status,
        ?string $text = null,
        $rdfaData = null
    ) {
        /** RDFa data is obtained by merging @ref DEFAULT_RDFA_DATA with
         *  $rdfaData. */

        $autoRdfaData = RdfaData::newFromIterable(static::DEFAULT_RDFA_DATA);

        if ($rdfaData instanceof RdfaData) {
            $rdfaData = $autoRdfaData->replace($rdfaData);
        } elseif (isset($rdfaData)) {
            $rdfaData =
                $autoRdfaData->replace(RdfaData::newFromIterable($rdfaData));
        } else {
            $rdfaData = $autoRdfaData;
        }

        $response = new self($rdfaData, null, $status);

        /** If $text is not provided, use getReasonPhrase(). */
        if (isset($text)) {
            $response->getBody()->write($text);
        } else {
            $response->getBody()->write($response->getReasonPhrase());
        }

        return $response;
    }

    public function __construct(
        RdfaData $rdfaData = null,
        $body = null,
        ?int $status = null
    ) {
        $this->rdfaData_ = $rdfaData ?? new RdfaData([]);

        /** Create HTTP headers from $rdfaData. */
        parent::__construct(
            $body ?? 'php://memory',
            $status ?? 200,
            (new Rdfa2Headers())->createHeaders($this->rdfaData_)
        );
    }

    public function getRdfaData(): RdfaData
    {
        return $this->rdfaData_;
    }

    /// Emit using SapiEmitter
    public function emit(?bool $sendContentLength = null)
    {
        (new SapiEmitter())->emit($this, $sendContentLength);
    }
}
