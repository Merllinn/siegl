<?php

/**
 * @author Lukáš Piják 2018 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

namespace BulkGate\Message;

interface IConnection
{
    public function send(Request $request);

    public function getInfo($delete = false);
}
