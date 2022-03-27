<?php

/**
 * Test: BulkGate\Message\Connection
 * @author Lukáš Piják 2018 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

namespace Test;

use BulkGate;
use Tester;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

if (!extension_loaded('openssl')) {
	Tester\Environment::skip('Test requires php_openssl extension to be loaded.');
}

$connection = new BulkGate\Message\Connection(-1, md5('-1'), 'https://portal.bulkgate.com/api/welcome');

$response = new BulkGate\Message\Response('{"message": "BulkGate API"}', 'application/json');

Assert::equal($response, $connection->send(new BulkGate\Message\Request('default', [])));
