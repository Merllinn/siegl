<?php

/**
 * @author Lukáš Piják 2018 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

namespace BulkGate\Message;

use BulkGate;

class Connection implements IConnection
{
    use BulkGate\Strict;

    /** @var string */
    private $api;

    /** @var int */
    private $application_id;

    /** @var string */
    private $application_token;

    /** @var string */
    private $application_product;

    /** @var array Response */
    private $responses = [];

    public function __construct($application_id, $application_token, $api = 'https://portal.bulkgate.com/api/1.0/php-sdk', $application_product = 'php-sdk')
    {
        $this->api = (string) $api;
        $this->application_id = $application_id;
        $this->application_token = $application_token;
        $this->application_product = (string) $application_product;
    }

    public function send(Request $request)
    {
        $context = stream_context_create(['http' => [
            'method' => 'POST',
            'header' => [
                'Content-type: ' . $request->getContentType(),
                'X-BulkGate-Application-ID: ' . (string)$this->application_id,
                'X-BulkGate-Application-Token: ' . (string)$this->application_token,
                'X-BulkGate-Application-Product: ' . (string)$this->application_product
            ],
            'content' => $request->getData(),
            'ignore_errors' => true
        ]]);

        $connection = fopen($this->api . '/' . $request->getAction(), 'r', false, $context);

        if ($connection)
        {
            $meta = stream_get_meta_data($connection);
            $header = new HttpHeaders(implode("\r\n", $meta['wrapper_data']));
            $result = stream_get_contents($connection);
            fclose($connection);

            $response = new Response($result, $header->getContentType());

            $this->responses[] = (object)['action' => $request->getAction(), 'request' => $request->getRawData(), 'response' => $response];
            return $response;
        }

        throw new ConnectionException('SMS server is unavailable - ' . $this->api . '/' . $request->getAction());
    }


    public function getInfo($delete = false)
    {
        $responses = $this->responses;

        if ($delete)
        {
            $this->responses = [];
        }
        return $responses;
    }
}
