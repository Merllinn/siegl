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

$action_first = 'test_first';
$action_second = 'test_second';

$data_first = ['test' => true];
$data_second = ['test' => false];

foreach ([false => 'application/json', true => 'application/zip'] as $compress => $content_type)
{
    $request = new BulkGate\Message\Request($action_first, $data_first, (bool) $compress);

    Assert::equal($action_first, $request->getAction());

    if((bool) $compress)
    {
        Assert::equal(BulkGate\Utils\Compress::compress(BulkGate\Utils\Json::encode($data_first)), $request->getData());
    }
    else
    {
        Assert::equal(BulkGate\Utils\Json::encode($data_first), $request->getData());
    }

    Assert::equal($content_type, $request->getContentType());

    $request->setAction($action_second);
    Assert::equal($action_second, $request->getAction());

    $request->setData($data_second, (bool) $compress);

    if((bool) $compress)
    {
        Assert::equal(BulkGate\Utils\Compress::compress(BulkGate\Utils\Json::encode($data_second)), $request->getData());
    }
    else
    {
        Assert::equal(BulkGate\Utils\Json::encode($data_second), $request->getData());
    }

    Assert::equal($data_second, $request->getRawData());
}

