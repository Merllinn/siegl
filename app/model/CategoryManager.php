<?php

namespace App\Model;

use Nette;


/**
 * Categories management.
 */
final class CategoryManager
{
	use Nette\SmartObject;

	const
        BASE = 'category',
		IMAGES = 'category_image';


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

    public function fetchDS($parent)
    {
        $ret = $this->get();
        if(!empty($parent)){
            $ret->where("parent = ?", $parent);
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

    public function findByAlias($alias)
    {
        return $this->get()
            ->select("pages.*, layout.view")
            ->where("alias", $alias)
            ->fetch();
    }

    public function getOneByFeedId($feedId)
    {
        return $this->get()
            ->where("feedId", $feedId)
            ->fetch();
    }

    public function find($id)
    {
        return $this->get()
            ->where("id", $id)
            ->fetch();
    }

    public function getActiveList(){
        $p = $this->get()
            ->where("active", 1);

        return $p->fetchPairs("id", "name");
    }

    public function getActive(){
        $p = $this->get()
            ->where("active", 1);

        return $p;
    }

    public function getPh()
    {
        return $this->database->table(self::IMAGES);
    }

    public function getPhotos($product){
        return $this->getPh()
            ->where("category_id", $product)
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

    public function savePhoto($photo){
        $category = $photo["category_id"];
        $oldPhoto = $this->getMainPhoto($category);
        if($oldPhoto){
            $this->deletePhoto($oldPhoto->id);
        }
        $this->addPhoto($photo);
    }

    public function countPhotos($category){
        return $this->getPh()
            ->select("count(id) AS count")
            ->where("category_id", $category)
            ->fetch();

    }

    public function getMainPhoto($category){
        return $this->getPh()
            ->where("category_id", $category)
            ->fetch();

    }

    public function getFullWithIco($id){
        $product = $this->find($id)->toArray();
        $photos = $this->getPhotos($id);
        foreach($photos as $photo){
            if($photo->main==0){
                $product["file"] = $photo->file;
            }
        }
        return Nette\Utils\ArrayHash::from($product);
    }

    public function getVat($id){
        return $this->database->table("vat")
                    ->where("id", $id)
                    ->fetch();
    }





}

