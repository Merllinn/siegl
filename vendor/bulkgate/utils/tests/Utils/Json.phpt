<?php

/**
 * Test: BulkGate\Utils\Connection
 * @author Lukáš Piják 2018 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

namespace Test;

use BulkGate;
use Tester;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$data = ['test' => true, 'bulkgate' => 2018, 'author' => 'Lukáš Piják', 'float' => 1.0];
$data_encoded = '{"test":true,"bulkgate":2018,"author":"Lukáš Piják","float":1.0}';

$string = 'random_string';
$string_encoded = '"random_string"';

Assert::equal($data_encoded, BulkGate\Utils\Json::encode($data));
Assert::equal($string_encoded, BulkGate\Utils\Json::encode($string));

Assert::equal($data, BulkGate\Utils\Json::decode($data_encoded, true));
Assert::equal($string, BulkGate\Utils\Json::decode($string_encoded, true));

Assert::equal((object)$data, BulkGate\Utils\Json::decode($data_encoded));

Assert::equal($string_encoded, BulkGate\Utils\Json::encode(BulkGate\Utils\Json::decode($string_encoded)));
Assert::equal($data_encoded, BulkGate\Utils\Json::encode(BulkGate\Utils\Json::decode($data_encoded)));

Assert::equal($data, BulkGate\Utils\Json::decode(BulkGate\Utils\Json::encode($data), true));
Assert::equal($string, BulkGate\Utils\Json::decode(BulkGate\Utils\Json::encode($string), true));

Assert::exception(function () {
    BulkGate\Utils\Json::decode("}");
}, "BulkGate\\Utils\\JsonException");

Assert::exception(function () {
    BulkGate\Utils\Json::decode("");
}, "BulkGate\\Utils\\JsonException");

