<?php

/**
 * Test: BulkGate\Message\Request
 * @author Lukáš Piják 2018 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

namespace Test;

use BulkGate;
use Tester;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$data = ['test' => true, 'bulkgate' => 2018, 'author' => 'Lukáš Piják'];

$test = function (BulkGate\Message\Response $response)
{
    Assert::true($response->test);
    Assert::equal(2018, $response->bulkgate);
    Assert::equal('Lukáš Piják', $response->author);

    Assert::exception(function () use ($response) {
        echo $response->undefined;
    }, "BulkGate\\StrictException");
};

$test(new BulkGate\Message\Response(BulkGate\Utils\Json::encode($data), 'application/json'));
$test(new BulkGate\Message\Response(BulkGate\Utils\Compress::compress(BulkGate\Utils\Json::encode($data)), 'application/zip'));
