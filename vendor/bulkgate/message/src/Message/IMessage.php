<?php

/**
 * @author Lukáš Piják 2018 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

namespace BulkGate\Message;

interface IMessage
{
    const NUMBER = 'number';

    const TEXT = 'text';

    const PRICE = 'price';

    const CREDIT = 'credit';

    const STATUS = 'status';

    const ID = 'id';

    const ISO = 'iso';

    const VARIABLES = 'variables';

    const SCHEDULED = 'scheduled';

    /**
     * @return string
     */
    public function __toString() ;


    /**
     * @return string
     */
    public function getType();


    /**
     * @return array
     */
    public function toArray();


    /**
     * @param int|null $timestamp
     */
    public function schedule($timestamp = null);

}
