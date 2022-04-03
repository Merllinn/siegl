<?php

/**
 * @author Lukáš Piják 2018 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */


namespace BulkGate\Message;

use BulkGate;

class Scheduler
{
    use BulkGate\Strict;

    /** @var null|\DateTime */
    private $datetime = null;

    /** @var int */
    private $per_messages;

    /** @var int */
    private $per_value = 1;

    /** @var string */
    private $per_unit = 'day';

    public function __construct($datetime = null)
    {
        if($datetime instanceof \DateTime)
        {
            $this->datetime = $datetime;
        }
        else
        {
            $this->datetime = new \DateTime();
        }
    }

    public function datetime(\DateTime $datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function restriction($messages, $per_value = 1, $per_unit = 'day')
    {
        if(in_array(strtolower($per_unit), ['day', 'days', 'hour', 'hours', 'minute', 'minutes', 'second', 'seconds']))
        {
            $this->per_unit = strtolower($per_unit);
        }
        else
        {
            throw new InvalidStateException('Invalid restriction time unit');
        }

        $this->per_messages = max(0, (int) $messages);
        $this->per_value = max(1, (int) $per_value);

        return $this;
    }

    public function schedule(IMessage $message)
    {
        $counter = 0;

        $datetime = clone $this->datetime;

        if($message instanceof BulkGate\Utils\Iterator)
        {
            foreach($message as $m)
            {
                if($m instanceof IMessage)
                {
                    $message->schedule($datetime->getTimestamp());

                    if($this->per_messages > 0 && (++ $counter % $this->per_messages === 0))
                    {
                        $datetime->modify('+'.$this->per_value.' '.$this->per_unit);
                        $counter = 0;
                    }
                }
            }
        }
    }
}
