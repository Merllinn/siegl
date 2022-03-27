<?php

/**
 * @author Lukáš Piják 2018 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

namespace BulkGate\Message;

use BulkGate;
use BulkGate\Utils\Json, BulkGate\Utils\JsonException, BulkGate\Utils\Compress;

class Response extends \stdClass
{
    use BulkGate\Strict {
        __get as private strictGet;
    }

    /** @var array */
    private $data;


    /**
     * Response constructor.
     * @param string $result
     * @param string $content_type
     * @throws InvalidContentTypeException
     * @throws MalformedJsonException
     */
    public function __construct($result, $content_type)
    {
        try
        {
            if ($content_type === 'application/json')
            {
                $result = Json::decode($result, true);
            }
            elseif ($content_type === 'application/zip')
            {
                $result = Json::decode(Compress::decompress($result), true);
            }
            else
            {
                throw new InvalidContentTypeException;
            }

            if(isset($result['data']))
            {
                $this->data = $result['data'];
            }
            else
            {
                $this->data = $result;
            }
        }
        catch (JsonException $e)
        {
            throw new MalformedJsonException;
        }
    }


    /**
     * @return bool
     */
    public function isSuccess()
    {
        return empty($this->error);
    }
    

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->data[$name]))
        {
            return $this->data[$name];
        }

        $this->strictGet($name);
        return null;
    }
    

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }
}
