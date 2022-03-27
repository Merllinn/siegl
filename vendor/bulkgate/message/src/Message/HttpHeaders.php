<?php

/**
 * @author Lukáš Piják 2018 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

namespace BulkGate\Message;

use BulkGate;

class HttpHeaders
{
    use BulkGate\Strict;

    /** @var array */
    private $headers = [];

    public function __construct($raw_header)
    {
        $this->parseHeaders($raw_header);
    }

    public function getHeader($name, $default = null)
    {
        $name = strtolower((string) $name);

        if(isset($this->headers[$name]))
        {
            return (string) $this->headers[$name];
        }
        return $default;
    }

    public function getContentType()
    {
        $content_type = $this->getHeader('content-type');

        if($content_type !== null)
        {
            preg_match('~^(?<type>[a-zA-Z]*/[a-zA-Z]*)~', trim($content_type), $type);

            if(isset($type['type']))
            {
                return $type['type'];
            }
        }
        return '';
    }

    public function getHeaders()
    {
        return (array) $this->headers;
    }

    private function parseHeaders($raw_header)
    {
        if(!is_array($raw_header))
        {
            $raw_header = explode("\r\n\r\n", $raw_header);
        }

        foreach($raw_header as $index => $request)
        {
            foreach (explode("\r\n", $request) as $i => $line)
            {
                if(strlen($line) > 0)
                {
                    if ((int)$i === 0)
                    {
                        $this->headers['http_code'] = $line;
                    }
                    else
                    {
                        list($key, $value) = explode(':', $line);
                        $this->headers[strtolower($key)] = trim($value);
                    }
                }
            }
        }
    }
}
