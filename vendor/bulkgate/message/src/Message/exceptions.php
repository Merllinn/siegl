<?php

/**
 * @author Lukáš Piják 2018 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

namespace BulkGate\Message;

use BulkGate;

class ConnectionException extends BulkGate\Exception
{
}

class MalformedJsonException extends BulkGate\Exception
{
}

class InvalidContentTypeException extends BulkGate\Exception
{
}

class InvalidRequestException extends BulkGate\Exception
{
}

class InvalidStateException extends BulkGate\Exception
{
}