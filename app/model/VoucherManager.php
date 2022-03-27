<?php

namespace App\Model;

use Nette;


/**
 * Categories management.
 */
final class VoucherManager
{
    use Nette\SmartObject;

    const
        BASE = 'vouchers';


    /** @var Nette\Database\Context */
    private $database;


    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function get()
    {
        return $this->database->table(self::BASE);
    }

    public function add($values)
    {
        return $this->get()->insert($values);
    }

    public function update($values, $id)
    {
        $this->get()
        ->where("id", $id)
        ->update($values);
    }

    public function delete($id)
    {
        $this->get()
        ->where("id", $id)
        ->delete();
    }

    public function findByCode($code)
    {
        return $this->get()
            ->where("code", $code)
            ->fetch();
    }

    public function find($id)
    {
        return $this->get()
            ->where("id", $id)
            ->fetch();
    }

}

