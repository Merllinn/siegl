<?php

namespace App\Model;

use Nette;


/**
 * Orders management.
 */
final class ProjectManager
{
	use Nette\SmartObject;

	const BASE = 'projects';


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
    public function getAll()
    {
        return $this->get()
            ->select("*, concat(name,' ',surname) AS customer");
    }
    public function getEmails()
    {
        return $this->get()
            ->select("email")
            ->group("email")
            ->order("email");
    }
    public function getToSend()
    {
        return $this->get()
            ->where("active", false)
            ->where("generateStatus", 20);
    }
    public function getToRefresh()
    {
        return $this->get()
            //->where("active", false)
            ->where("generateStatus", 10)
            ->where("hash != ?", '')
            ->order("id DESC")
            ;
    }
    public function getLatestToGenerate()
    {
        return $this->get()
            //->where("active", false)
            ->where("generateStatus", 10)
            ->where("toGenerate > 0")
            ->where("hash != ?", '')
            ->order("id ASC")
            ->fetch()
            ;
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

    public function exists($id)
    {
        return $this->get()
            ->where("wpId", $id)
            ->fetch();
    }

    public function findActiveByHash($hash)
    {
        return $this->get()
            ->where("hash", $hash)
            ->where("active", true)
            ->fetch();
    }

    public function findByLabId($labId)
    {
        return $this->get()
            ->where("labId", $labId)
            //->where("active", true)
            ->fetch();
    }

    public function findByHash($hash)
    {
        return $this->get()
            ->where("hash", $hash)
            ->fetch();
    }


    public function getUnasigned($branch)
    {
        return $this->get()
        	->select("CONCAT(id,'_',labId) AS folder, id")
            ->where("hash IS NULL")
            ->where("branch", $branch)
            ->fetchPairs("folder", "id");
    }


}

