<?php

namespace App\Model;

use Nette;


/**
 * Pages management.
 */
final class PageManager
{
	use Nette\SmartObject;

	const
        PAGES = 'pages',
		IMAGES = 'page_image';


	/** @var Nette\Database\Context */
	private $database;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

    public function get()
    {
        return $this->database->table(self::PAGES);
    }

    public function countPages()
    {
        $ret = $this->get()
            ->select("count(id) AS number")
            ->fetch();
        return $ret->number;
    }

    public function countSubpages($parent)
    {
        $ret = $this->get()
            ->select("count(id) AS number")
            ->where("parent", $parent)
            ->fetch();
        return $ret->number;
    }

    public function getByParent($parent)
    {
        if(!empty($parent)){
	        return $this->get()
	            ->where("parent = ?", $parent);
        }
        else{
			return $this->get()
			->where("parent IS NULL");
        }
    }

    public function getActiveByParent($parent)
    {
        return $this->get()
            ->where("active = ?", true)
            ->where("parent = ?", $parent);
    }

    public function add($values)
    {
        $this->get()->insert($values);
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

    public function findByAlias($alias)
    {
        return $this->get()
            ->select("pages.*, layout.view")
            ->where("alias", $alias)
            ->fetch();
    }

    public function find($id)
    {
        return $this->get()
            ->where("id", $id)
            ->fetch();
    }

    public function getPh()
    {
        return $this->database->table(self::IMAGES);
    }

    public function getPhotos($product){
        return $this->getPh()
            ->where("page_id", $product)
            ->order("order");

    }

    public function findPhoto($photo){
        return $this->getPh()
            ->where("id", $photo)
            ->fetch();

    }

    public function addPhoto($values)
    {
        $this->getPh()->insert($values);
    }

    public function updatePhoto($values, $id)
    {
        $this->getPh()
        ->where("id", $id)
        ->update($values);
    }

    public function deletePhoto($id)
    {
        $this->getPh()
        ->where("id", $id)
        ->delete();
    }

    public function countPhotos($product){
        return $this->getPh()
            ->select("count(id) AS count")
            ->where("page_id", $product)
            ->fetch();

    }

    public function getMainPhoto($product){
        return $this->getPh()
            ->where("page_id", $product)
            ->where("main = ?", 1)
            ->fetch();

    }

}

