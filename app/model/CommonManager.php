<?php

namespace App\Model;

use Nette;


/**
 * Common management.
 */
final class CommonManager
{
	use Nette\SmartObject;

	const
		SETTINGS = 'settings',
		SIZES = 'sizes',
		SLIDER = 'slider';


	/** @var Nette\Database\Context */
	private $database;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

    public function getSettings()
    {
        return $this->database->table(self::SETTINGS)
            ->where("id", 1)
            ->fetch();
    }

    public function saveSettings($values)
    {
        $this->database->table(self::SETTINGS)
            ->where("id", 1)
            ->update($values);
    }

    public function getTableArray($table)
    {
        return $this->database->table($table)
            ->fetchPairs("id", "name");
    }

    public function existsAlias($table, $alias, $id = null, $lang = null){
        $ret = $this->database->table($table)
                ->select("id")
                ->where("alias = ?", $alias);
        if(!empty($id)){
            $ret->where("id != ?", $id);
        }
        $ret->fetch();
        if(empty($ret->id)){
            return false;
        }
        else{
            return true;
        }
    }

    public function getSizes()
    {
        return $this->database->table(self::SIZES);
    }

    public function getActiveSizes()
    {
        return $this->getSizes()
        	->where("active", true);
    }

	public function addSize($values)
	{
			$this->getSizes()->insert($values);
	}

	public function updateSize($values, $id)
	{
			$this->getSizes()
			->where("id", $id)
			->update($values);
	}

	public function deleteSize($id)
	{
		$this->getSizes()
		->where("id", $id)
		->delete();
	}

    public function findSize($id)
    {
		return $this->getSizes()
		->where("id", $id)
        ->fetch();
    }


    public function getSlider()
    {
        return $this->database->table(self::SLIDER);
    }

	public function addSlide($values)
	{
			return $this->getSlider()->insert($values);
	}

	public function updateSlide($values, $id)
	{
			$this->getSlider()
			->where("id", $id)
			->update($values);
	}

	public function deleteSlide($id)
	{
		$this->getSlider()
		->where("id", $id)
		->delete();
	}

    public function findSlide($id)
    {
		return $this->getSlider()
		->where("id", $id)
        ->fetch();
    }



}

