<?php

/**
 * Test: BulkGate\Strict
 * @author Lukáš Piják 2018 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

namespace Test;

use BulkGate;
use Tester;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$object = new class ()
{
    use BulkGate\Strict;

    public $defined = true;

    public function defined()
    {
        return true;
    }

    public static function staticDefined()
    {
        return true;
    }
};

Assert::exception(function () use ($object) {
    $object->undefined = 'undefined';
}, "BulkGate\\StrictException");

Assert::exception(function () use ($object) {
    echo $object->undefined;
}, "BulkGate\\StrictException");

Assert::exception(function () use ($object) {
    unset($object->undefined);
}, "BulkGate\\StrictException");

Assert::exception(function () use ($object) {
    $object->undefined();
}, "BulkGate\\StrictException");

Assert::exception(function () use ($object) {
    $object::undefined();
}, "BulkGate\\StrictException");

Assert::false(isset($object->undefined));

Assert::true($object->defined);

Assert::true($object->defined());

Assert::true($object::staticDefined());

$object->defined = false;

Assert::false($object->defined);

unset($object->defined);

Assert::exception(function () use ($object) {
    echo $object->defined;
}, "BulkGate\\StrictException");
