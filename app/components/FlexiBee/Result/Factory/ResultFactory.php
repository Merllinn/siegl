<?php

namespace FlexibeeClient\Result\Factory;

use FlexibeeClient\Exception\ResultClassNotFoundException;
use FlexibeeClient\Result\JsonResult;
use FlexibeeClient\Result\PdfResult;
use FlexibeeClient\Result\Result;

class ResultFactory implements IResultFactory
{
    /** @var array */
    private $resultClass = [
        'json' => JsonResult::class,
        'pdf' => PdfResult::class,
    ];

    /**
     * @param string $format
     * @param string $class
     */
    public function registerResultClass($format, $class)
    {
        $this->resultClass[$format] = $class;
    }

    /**
     * @param string $format
     * @param int $httpCode
     * @param string $data
     * @return Result
     * @throws ResultClassNotFoundException
     */
    public function createResult($format, $httpCode, $data)
    {
        $class = isset($this->resultClass[$format]) ? $this->resultClass[$format] : null;
        if (!$class) {
            throw new ResultClassNotFoundException("Result class for format $format not registered");
        }

        return new $class($httpCode, $data);

    }
}
