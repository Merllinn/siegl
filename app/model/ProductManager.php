<?php

namespace App\Model;

use Nette;


/**
 * Products management.
 */
final class ProductManager
{
	use Nette\SmartObject;

	const
        BASE = 'products',
		IMAGES = 'product_image',
		PRICES = 'product_prices';


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
    
    public function getArray(){
        return $this->get()->fetchPairs("id", "id");
    }

    public function fetchDS($type, $filter)
    {
        $ret = $this->get()
            ->where("type = ?", $type);
        if(!empty($filter->category) && $type==1){
            $ret->where("category = ".$filter->category);
        }
        if(!empty($filter->active)){
            if($filter->active==1){
                $ret->where("active = ?", true);
            }
            if($filter->active==9){
                $ret->where("active = ?", false);
            }
        }
        if(!empty($filter->fulltext)){
            $ret->where("name LIKE ?","%".$filter->fulltext."%");
        }

        return $ret;
    }

    public function getByType($type)
    {
        return $this->get()
            ->where("type = ?", $type)
            ->where("active = ?", true)
            ->order("order");
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
            ->where("alias", $alias)
            ->fetch();
    }

    public function getOneByFeedId($feedId)
    {
        return $this->get()
            ->where("feedId", $feedId)
            ->fetch();
    }

    public function getActive()
    {
        return $this->database->query("select p.*, i.file from ".self::BASE." p left join ".self::IMAGES." i on i.products_id = p.id and i.main=1 WHERE p.active=1 ORDER BY `order`");
    }

    public function findByCategory($category)
    {
        return $this->database->query("select p.*, i.file from ".self::BASE." p left join ".self::IMAGES." i on i.products_id = p.id and i.main=1 where category=? ORDER BY `order`", $category);
    }

    public function find($id)
    {
        return $this->get()
            ->where("id", $id)
            ->fetch();
    }

    public function getCameras($limit=false){
        $p = $this->get()
            ->where("type", 10)
            ->where("active", 1)
            ->order("order");
        if($limit!=false){
            $p->limit($limit);
        }

        return $p;
    }

    public function getPh()
    {
        return $this->database->table(self::IMAGES);
    }

    public function getPhotos($product){
        return $this->getPh()
            ->where("products_id", $product)
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
            ->where("products_id", $product)
            ->fetch();

    }

    public function getMainPhoto($product){
        return $this->getPh()
            ->where("products_id", $product)
            ->where("main = ?", 1)
            ->fetch();

    }

    public function getFull($id){
        $product = $this->find($id)->toArray();
        $photos = $this->getPhotos($id);
        foreach($photos as $photo){
            if($photo->main==1){
                $product["file"] = $photo->file;
            }
        }
        return Nette\Utils\ArrayHash::from($product);
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

    public function getPr()
    {
        return $this->database->table(self::PRICES);
    }

    public function savePrices($values)
    {
        $this->getPr()->insert($values);
    }

    public function findPrice($id)
    {
        return $this->getPr()
        ->where("id", $id)
        ->fetch();
    }

    public function getPrices($id)
    {
        return $this->getPr()
        ->where("product", $id);
    }

    public function deletePrices($id)
    {
        $this->getPr()
        ->where("product", $id)
        ->delete();
    }




}

