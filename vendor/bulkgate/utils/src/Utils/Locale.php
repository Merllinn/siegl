<?php

/**
 * @author Lukáš Piják 2018 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

namespace BulkGate\Utils;

use BulkGate, NumberFormatter, IntlDateFormatter;

class Locale
{
    use BulkGate\Strict;

    /** @var NumberFormatter */
    private $number_formatter;

    /** @var NumberFormatter */
    private $currency_formatter;

    /** @var IntlDateFormatter */
    private $datetime_formatter;

    /** @var IntlDateFormatter */
    private $date_formatter;

    /** @var IntlDateFormatter */
    private $time_formatter;

    public function __construct($locale, \DateTimeZone $timeZone = null)
    {
        if(extension_loaded('intl'))
        {
            $this->number_formatter = new NumberFormatter((string) $locale, NumberFormatter::DECIMAL);
            $this->currency_formatter = new NumberFormatter((string) $locale, NumberFormatter::CURRENCY);
            $this->datetime_formatter = new IntlDateFormatter((string) $locale,
                IntlDateFormatter::MEDIUM,
                IntlDateFormatter::MEDIUM,
                $timeZone ? $timeZone->getName() : null
            );
            $this->date_formatter = new IntlDateFormatter((string) $locale,
                IntlDateFormatter::MEDIUM,
                IntlDateFormatter::NONE
            );
            $this->time_formatter = new IntlDateFormatter((string) $locale,
                IntlDateFormatter::NONE,
                IntlDateFormatter::MEDIUM,
                $timeZone ? $timeZone->getName() : null
            );
        }
        else
        {
            throw new ExtensionException('PHP extension INTL not installed');
        }
    }


    public function price($price, $currency = null)
    {
        if($currency === null)
        {
            return $this->float((float) $price);
        }

        if($p = $this->currency_formatter->formatCurrency((float) $price, strtoupper((string) $currency)))
        {
            return $p;
        }
        return $this->float((float) $price). (string) $currency;
    }


    public function float($number)
    {
        return $this->number_formatter->format((float) $number, NumberFormatter::TYPE_DOUBLE);
    }


    public function int($number)
    {
        return $this->number_formatter->format((float) $number, NumberFormatter::TYPE_INT64);
    }


    public function datetime(\DateTime $dateTime)
    {
        return $this->datetime_formatter->format($dateTime);
    }


    public function date(\DateTime $date)
    {
        return $this->date_formatter->format($date);
    }


    public function time(\DateTime $date)
    {
        return $this->time_formatter->format($date);
    }
}