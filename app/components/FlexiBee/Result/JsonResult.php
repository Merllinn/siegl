<?php

namespace FlexibeeClient\Result;

class JsonResult extends Result
{
    /** @var array */
    protected $data;

    /**
     * @param int $httpCode
     * @param string $data
     */
    public function __construct($httpCode, $data)
    {
        parent::__construct($httpCode, $data);
        $this->parseData($data);
    }

    /**
     * @return bool
     */
    public function isOk()
    {
        return !(isset($this->data['success']) && !$this->data['success']);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $data
     */
    private function parseData($data)
    {
        $decodedData = json_decode($data, true);
        $this->data = isset($decodedData['winstrom']) ? $decodedData['winstrom'] : $decodedData;
    }
}
