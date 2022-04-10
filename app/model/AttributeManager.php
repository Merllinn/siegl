<?php

namespace App\Model;

use Nette;


/**
 * Categories management.
 */
final class AttributeManager
{
	use Nette\SmartObject;

	const
        BASE = 'attributes',
		VALUES = 'attribute_values';


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

    public function getAttributes($type=null)
    {
        $ret = $this->get();
        
        if(!empty($type)){
			$ret->where("type", $type);
        }
        
        return $ret;
    }

    public function getForCont($type=null)
    {
        $ret = $this->get()->where("forCont", true);
        if(!empty($type)){
			$ret->where("type", $type);
        }
        return $ret;
    }

    public function getForMaterial($type=null)
    {
        $ret = $this->get()->where("forMaterial", true);
        if(!empty($type)){
			$ret->where("type", $type);
        }
        return $ret;
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

   public function find($id)
    {
        return $this->get()
            ->where("id", $id)
            ->fetch();
    }


    public function getV()
    {
        return $this->database->table(self::VALUES);
    }

    public function getValues($a){
        return $this->getV()
            ->where("attribute", $a)
            //->order("name")
            ;

    }

    public function getValuesArr($a){
        return $this->getV()
            ->where("attribute", $a)
            ->order("order")
            ->fetchPairs("id", "name");

    }

    public function getAllValues(){
        $vals = $this->getV()
            ->order("order");
        $ret = array();
        foreach($vals as $val){
			$ret[$val->id] = $val;
        }
        return $ret;
    }

    public function countValues($a){
        $ret = $this->getV()
        	->select("COUNT(id) AS count")
            ->where("attribute", $a)
            ->fetch();
        return $ret->count;

    }

    public function findValue($a){
        return $this->getV()
            ->where("id", $a)
            ->fetch();

    }

    public function addValue($values)
    {
        $this->getV()->insert($values);
    }

    public function updateValue($values, $id)
    {
        $this->getV()
        ->where("id", $id)
        ->update($values);
    }

    public function deleteValue($id)
    {
        $this->getV()
        ->where("id", $id)
        ->delete();
    }


}

