<?php

/**
 * @author Lukáš Piják 2018 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

namespace BulkGate\Message;

use BulkGate;

class Request
{
    use BulkGate\Strict;

    const CONTENT_TYPE_JSON = 'application/json';

    const CONTENT_TYPE_ZIP = 'application/zip';

    /** @var string */
    private $action;

    /** @var array */
    private $data = [];

    /** @var string */
    private $content_type;

    public function __construct($action, array $data = [], $compress = false)
    {
        $this->setAction((string) $action);
        $this->setData($data, $compress);
    }

    public function setData(array $data = [], $compress = false)
    {
        $this->data = (array) $data;
        $this->content_type = $compress ? self::CONTENT_TYPE_ZIP : self::CONTENT_TYPE_JSON;

        return $this;
    }

    public function setAction($action): self
    {
        $this->action = (string) $action;

        return $this;
    }

    public function getData()
    {
        try
        {
            if($this->content_type === self::CONTENT_TYPE_ZIP)
            {
                return BulkGate\Utils\Compress::compress(BulkGate\Utils\Json::encode($this->data));
            }
            else
            {
                return BulkGate\Utils\Json::encode($this->data);
            }
        }
        catch (BulkGate\Utils\JsonException $e)
        {
            throw new InvalidRequestException;
        }
    }

    public function getRawData()
    {
        return (array) $this->data;
    }

    public function getAction()
    {
        return (string) $this->action;
    }

    public function getContentType()
    {
        return (string) $this->content_type;
    }
}
