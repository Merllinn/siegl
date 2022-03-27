<?php

namespace FlexibeeClient\Result\Factory;

use FlexibeeClient\Result\Result;

interface IResultFactory
{
    /**
     * @param string $format
     * @param int $httpCode
     * @param string $data
     * @return Result
     */
    public function createResult($format, $httpCode, $data);
}
