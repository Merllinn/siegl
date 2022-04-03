<?php

namespace App\Model;

use Nette;


/**
 * Categories management.
 */
final class OrderStatusManager
{
    use Nette\SmartObject;

    const
        BASE = 'order_status';


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

    public function findByName($name)
    {
        return $this->get()
            ->where("name", $name)
            ->fetch();
    }

    public function find($id)
    {
        return $this->get()
            ->where("id", $id)
            ->fetch();
    }

    public function getList(){
        return $this->get()
            ->order("id");
    }

    public function getSimple(){
        return $this->get()
            ->fetchPairs("city", "city");
    }

}

