<?php

namespace TH;

use \Prewk\XmlStreamer;
use \Nette;
use \Nette\Utils\Image;


class FeedParser extends XmlStreamer {

    public $model;
    public $presenter;

    public function processNode($xmlString, $elementName, $nodeIndex) {
        $item = simplexml_load_string($xmlString);

        $itemData = new \Nette\Utils\ArrayHash();
        //$itemData->code = (string)$item->EAN;
        $itemData->vat_id = 1;
        $vat = $this->model->getVat($itemData->vat_id);
        $itemData->price_vat = (float)$item->PRICE_VAT;
        $itemData->price = $itemData->price_vat/(1+($vat->value/100));

        $itemId = (int)$item->ITEM_ID;

        $exists = $this->model->getOneByFeedId($itemId);

        if(empty($exists)){
            $itemData->name = (string)$item->PRODUCTNAME;
            $itemData->description = (string)$item->DESCRIPTION;
            $itemData->feedId = $itemId;
            $itemData->active = 0;
            $itemData->alias = $this->presenter->makeAlias("products", $itemData->name);

            $productId = $this->model->add($itemData);

            if(!empty($item->IMGURL)){
                $photo = $item->IMGURL;
                $image = Image::fromFile($photo);
                //if ($image->isOk()) {
                    $ext = pathinfo($photo, PATHINFO_EXTENSION);
                    $name = $this->presenter->generateString(15);
                    $fileName = $name.".".$ext;
                    $origFile = BASE_DIR."/data/original/".$fileName;

                    //resize and move
                    $image->resize(1600, 1200, Image::SHRINK_ONLY);
                    $image->save($origFile);

                    //$photos = $this->models->products->getPhotos($this->product);
                    //save to DB
                    $data = array(
                        "products_id"=>$productId,
                        "main"=>true,
                        "order"=>1,
                        //"name"=>$values->name,
                        "file"=>$fileName,
                        "date"=> new \Nette\Utils\DateTime()
                    );

                    $this->model->addPhoto($data);

            //}
                //$this->model->addCategory($productId, 932);
            }
        }
        else{
            $this->model->update($itemData, $exists->id);
            unset($this->presenter->savedItems[$exists->id]);
        }
        
        return true;
    }
}

