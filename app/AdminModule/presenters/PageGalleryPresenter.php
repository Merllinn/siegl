<?php

namespace App\AdminModule\Presenters;

use     TH\Form,
        Nette\Utils\Html,
        Nette\Utils\Image;

class PageGalleryPresenter extends BasePresenter
{

    private $page;
    private $photo;

    public function startup(){
        parent::startup();
        $this->addBreadcrumbs("Stránky", $this->link(":Admin:Pages:default"));
    }

    public function actionDefault($page, $edit=null){
        $this->template->page = $this->page = $page;
        $pageDetails = $this->pageManager->find($page);
        $this->template->files = $this->pageManager->getPhotos($page);
        $this->addBreadcrumbs("Správa galerie ".$pageDetails->name);
        if(!empty($edit)){
            $this->template->edit = $this->photo = $this->pageManager->findPhoto($edit);
            $this["editForm"]->setDefaults($this->photo);
        }
    }

    /** Create homepage seo form
    *
    * @return Form
    */
    public function createComponentUpload(){
        $form = new Form();

        $form ->addMultiUpload("img", "Soubor obrázku")
            ->setRequired(true)
            //->addRule(Form::IMAGE, 'Soubor musí být JPG, JPEG, PNG nebo GIF.')
            ;
        /*
        $form ->addText("name", "Popisek obrázku")
            ->addRule(Form::FILLED);
        */
        $form ->addSubmit("submit", "Uložit")
            ->getControlPrototype()->class("btn btn-primary");

        $form->onSuccess[] = [$this, 'savePhoto'];


        return $form;
    }

    /** Create homepage seo form
    *
    * @return Form
    */
    public function createComponentEditForm(){
        $form = new Form();

        $form ->addText("name", "Popisek obrázku");
        $form ->addSubmit("submit", "Uložit")
            ->getControlPrototype()->class("btn btn-primary");

        $form->onSuccess[] = [$this, 'updatePhoto'];


        return $form;
    }

    /** callback for seo form
    *
    * @param Form data from page form
    * @return void
    */
    public function savePhoto(Form $form){
        $values = $form->getValues();

        if ($form->isValid()) {
            // submitted and valid
            $values = $form->getValues();
            /*
             * Kontrola, zda-li byl obrazek skutecne nahran
             */
            if(!empty($values['img'])){
                foreach($values['img'] as $image){
                    if ($image->isOk()) {
                        $ext = pathinfo($image->getSanitizedName(), PATHINFO_EXTENSION);
	                    $name = $this->generateString(15);
	                    $fileName = $name.".".$ext;
	                    $tempFile = BASE_DIR."/data/temp/".$fileName;
	                    $origFile = BASE_DIR."/data/original/".$fileName;
                        if($ext=="svg"){
	                        $image->move($origFile);
                        }
                        else{
	                        $image->move($tempFile);

	                        //resize and move
	                        $bigImage = Image::fromFile($tempFile);
	                        $bigImage->resize(1600, 1200, Image::SHRINK_ONLY);
	                        $bigImage->save($origFile);
                        }

                        if(file_exists($tempFile))
                            unlink($tempFile);

                        $photos = $this->pageManager->countPhotos($this->page);
                        //dave to DB
                        $data = array(
                            "page_id"=>$this->page,
                            "main"=>$photos->count==0?1:0,
                            "order"=>$photos->count+1,
                            //"name"=>$values->name,
                            "file"=>$fileName,
                            //"date"=> new \Nette\Utils\DateTime()
                        );

                        $this->pageManager->addPhoto($data);

                    }
                }
            }
        }
        $this->redirect("this");

    }

    public function updatePhoto(Form $form){
        $values = $form->getValues();

        if ($form->isValid()) {
            $this->pageManager->updatePhoto($values, $this->photo->id);
            $this->redirect("default", array("page"=>$this->page));
        }
    }

    public function handleSetMain($id){
        $main = $this->pageManager->getMainPhoto($this->page);
        if($main)
            $this->pageManager->updatePhoto(array("main"=>0),$main->id);
        $this->pageManager->updatePhoto(array("main"=>1),$id);
        $this->redirect("this");
    }

    public function handleDelete($id){
        $this->pageManager->deletePhoto($id);
        $this->redirect("this");
    }

    public function handleSaveOrder(array $items){
        foreach($items as $index=>$item){
            $this->pageManager->updatePhoto(array("order"=>$index+1), $item);
        }
        $this->flashMessage("Pořadí fotografií bylo uloženo");
    }



}
