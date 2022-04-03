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

$data = ['test' => true, 'bulkgate' => 2018, 'author' => 'Lukáš Piják'];
$data_encoded = 'H4sIAAAAAAACC0u0MraqLrYysVIqSS0uUbJOsjK0LraysFJKKs3JTk8sSVWyzrQyMjC0AIqaWSkllpZk5BcpATmGQC0+pdmHFx5dqBCQmXV4YbaSdS0AIC2IaU4AAAA=';

$string = 'random_string';
$string_encoded = 'H4sIAAAAAAACCyu2MjS2UipKzEvJz40vLinKzEtXsgYAP3aBHRUAAAA=';

Assert::equal($data_encoded, BulkGate\Utils\Compress::compress($data));
Assert::equal($string_encoded, BulkGate\Utils\Compress::compress($string));

Assert::equal($data, BulkGate\Utils\Compress::decompress($data_encoded));
Assert::equal($string, BulkGate\Utils\Compress::decompress($string_encoded));

Assert::equal($string_encoded, BulkGate\Utils\Compress::compress(BulkGate\Utils\Compress::decompress($string_encoded)));
Assert::equal($data_encoded, BulkGate\Utils\Compress::compress(BulkGate\Utils\Compress::decompress($data_encoded)));


Assert::equal($data, BulkGate\Utils\Compress::decompress(BulkGate\Utils\Compress::compress($data)));
Assert::equal($string, BulkGate\Utils\Compress::decompress(BulkGate\Utils\Compress::compress($string)));

Assert::same('H4sIAAAAAAACC0u0MrKqLrYytlJKTEpWss60MjQyhpII0VoASinDoSYAAAA=', BulkGate\Utils\Compress::compress(['abc' => 123, '123' => 'abc']));

Assert::same('H4sIAAAAAAAEC0u0MrKqLrYytlJKTEpWss60MjQyhpII0VoASinDoSYAAAA=', BulkGate\Utils\Compress::compress(['abc' => 123, '123' => 'abc'], 1));

Assert::same(['abc' => 123, '123' => 'abc'], BulkGate\Utils\Compress::decompress('H4sIAAAAAAAEC0u0MrKqLrYytlJKTEpWss60MjQyhpII0VoASinDoSYAAAA='));

Assert::same(['abc' => 123, '123' => 'abc'], BulkGate\Utils\Compress::decompress(BulkGate\Utils\Compress::compress(['abc' => 123, '123' => 'abc'])));

$json = BulkGate\Utils\Json::encode([
    'status' => '-1',
    'type' => 'error',
    'message' => 'Unknown action',
    'compress' => false,
    'data' => [
        123456789,
        123456789,
        123456789,
        123456789,
        123456789,
        123456789,
        123456789,
        123456789,
        123456789,
        123456789,
        123456789,
        123456789,
        123456789,
        123456789,
        123456789,
    ],
]);

Assert::true(strlen($json) > strlen(BulkGate\Utils\Compress::compress($json)));