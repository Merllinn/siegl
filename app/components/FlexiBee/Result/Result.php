<?php

namespace FlexibeeClient\Result;

abstract class Result
{
    /** @var int */
    protected $httpCode;

    /** @var string */
    protected $rawData;

    /**
     * @param int $httpCode
     * @param string $data
     */
    public function __construct($httpCode, $data)
    {
        $this->httpCode = $httpCode;
        $this->rawData = $data;
    }

    /**
     * @return bool
     */
    abstract public function isOk();

    /**
     * @return array
     */
    abstract public function getData();
}
