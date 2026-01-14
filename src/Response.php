<?php

namespace alcamo\http;

use Laminas\Diactoros\Response as BaseResponse;
use alcamo\rdfa\RdfaData;

/**
 * @brief Response with RDFa data
 *
 * @date Last reviewed 2026-01-14
 */
class Response extends BaseResponse
{
    private $rdfaData_; ///< RdfaData

    /// Default RDFa data used in alcamo::http::Response::newFromStatusAndText()
    public const DEFAULT_RDFA_DATA = [ [ 'dc:format', 'text/plain' ] ];

    /**
     * @brief Create a response, deriving text from status if not given.
     *
     * @param $status Defaults to 200, as in
     * Laminas::Diactoros::Response::__construct().
     *
     * @param $text Defaults to getReasonPhrase().
     *
     * @param RdfaData|array $rdfaData RdfaData onject or iterable of pairs
     * consisting of a property CURIE and object data.
     */
    public static function newFromStatusAndText(
        int $status,
        ?string $text = null,
        $rdfaData = null
    ) {
        /** To create RDFa data, start with
         *  alcamo::http::Response::DEFAULT_RDFA_DATA and replace statements
         *  by those given in $rdfaData. */

        $defaultRdfaData = RdfaData::newFromIterable(static::DEFAULT_RDFA_DATA);

        if (isset($rdfaData)) {
            $rdfaData = $defaultRdfaData->replace(
                $rdfaData instanceof RdfaData
                    ? $rdfaData
                    : RdfaData::newFromIterable($rdfaData)
            );
        } else {
            $rdfaData = $defaultRdfaData;
        }

        $response = new self($rdfaData, null, $status);

        if (isset($text)) {
            $response->getBody()->write($text);
        } else {
            $response->getBody()->write($response->getReasonPhrase());
        }

        return $response;
    }

    /**
     * @param $rdfaData Data used to create HTTP headers.
     *
     * @param $body Defaults to 'php://memory', as in
     * Laminas::Diactoros::Response::__construct().
     *
     * @param $status Defaults to 200, as in
     * Laminas::Diactoros::Response::__construct().
     */
    public function __construct(
        RdfaData $rdfaData = null,
        $body = null,
        ?int $status = null
    ) {
        $this->rdfaData_ = $rdfaData ?? new RdfaData();

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
    public function emit()
    {
        (new SapiEmitter())->emit($this);
    }
}
