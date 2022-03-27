<?php declare(strict_types=1);

/**
 * Test: BulkGate\Message\Scheduler
 * @author Lukáš Piják 2018 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

namespace Test;

use BulkGate;
use Tester;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$message = new class () implements BulkGate\Message\IMessage { public function __toString() { return ''; } public function getType() { return ''; } public function toArray() { return []; } public function schedule($timestamp = null) {} };

$bulk = new class([$message, clone $message, clone $message, clone $message]) extends BulkGate\Utils\Iterator implements BulkGate\Message\IMessage
{
    public function __toString() { return ''; } public function getType() { return ''; } public function toArray() { return []; } public function schedule($timestamp = null)
    {
        global $output;

        $output[] = $timestamp;
    }
};

$timestamp = new \DateTime('2018-08-17 10:15:25', new \DateTimeZone('Europe/Prague'));
$scheduler = new BulkGate\Message\Scheduler($timestamp);

$output = [];
$scheduler->schedule($bulk);
Assert::equal($output, [$timestamp->getTimestamp(), $timestamp->getTimestamp(), $timestamp->getTimestamp(), $timestamp->getTimestamp()]);

$output = [];
$scheduler->restriction(2, 2, 'second');
$scheduler->schedule($bulk);
Assert::equal($output, [$timestamp->getTimestamp(), $timestamp->getTimestamp(), $timestamp->getTimestamp() + 2, $timestamp->getTimestamp() + 2]);

$output = [];
$scheduler->restriction(1, 2, 'second');
$scheduler->schedule($bulk);
Assert::equal($output, [$timestamp->getTimestamp(), $timestamp->getTimestamp() + 2, $timestamp->getTimestamp() + 4, $timestamp->getTimestamp() + 6]);

Assert::exception(function () use ($scheduler) {
    $scheduler->restriction(1, 2, 'sec');
}, BulkGate\Message\InvalidStateException::class);

$timestamp_new = new \DateTime('2018-08-17 11:15:25', new \DateTimeZone('Europe/Prague'));

$output = [];
$scheduler->datetime($timestamp_new);
$scheduler->schedule($bulk);
Assert::equal($output, [$timestamp_new->getTimestamp(), $timestamp_new->getTimestamp() + 2, $timestamp_new->getTimestamp() + 4, $timestamp_new->getTimestamp() + 6]);
