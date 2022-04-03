<?php

namespace App\AdminModule\Presenters;

use 	TH\Form,
		Nette\Utils\Html;
use     Ublaboo\DataGrid\DataGrid;

class SliderPresenter extends BasePresenter
{
	public $editedId = false;



	public function startup(){
		parent::startup();
		$this->addBreadcrumbs("Slider", $this->link(":Admin:Slider:default"));

	}

	public function actionDefault(array $items = null){
		if(!empty($items)){
			foreach($items as $index=>$item){
				$this->commonManager->updateSlide(array("order"=>$index+1), $item);
			}
			$this->flashMessage("Pořadí slidů bylo uloženo");
			$this->redirect("this");
		}
	}

	public function actionAdd(){
		$this->setView("addEdit");
	}

	public function actionEdit($id){
		$this->editedId = $id;
		$details = $this->commonManager->findSlide($id);
		$this["slideForm"]->setDefaults($details);
		$this->setView("addEdit");
	}

	public function handleDelete($id){
		$this->commonManager->deleteSlide($id);
		$this->redirect("this");
	}

    /**
     * Make table of customers
     *
     * @return \Addons\Tabella
     */
    public function createComponentSlides($name)
    {
        $presenter = $this;

        $source = $this->commonManager->getSlider()->order("order");

        $grid = new DataGrid($this, $name);
        $grid->setDataSource($source);

        $grid->addColumnText('fileSmall', 'Ikona')
            ->setRenderer(function($row) use ($presenter) {
                	if($row->fileSmall){
                        $smallPhoto = new \nette\utils\ArrayHash();
                        $smallPhoto->file = $row->fileSmall;
                        return html::el("img")->src($presenter->thumb($smallPhoto, 50, null));
                    }
                    else{
                        return "";
                    }
        });

        $grid->addColumnText('file', 'Obrázek')
            ->setRenderer(function($row) use ($presenter) {
                	if($row->file){
                        return html::el("img")->src($presenter->thumb($row, 100, 100));
                    }
                    else{
                        return "";
                    }
        });

        $grid->addColumnText('name', 'Rozměr');
        
        $grid->addColumnText('price', 'Cena');

        $grid->addColumnText('tools', 'Nástroje')
            ->setRenderer(function($row) use ($presenter) {
                $el = Html::el("span");
                $el->insert(0, html::el("a")->class("btn btn-mini btn-light")->href($presenter->link("edit", $row->id))->setHtml(html::el("i")->class("fas fa-edit"))->title("Upravit"));
                $el->insert(1, " ");
                $el->insert(4, html::el("a")->class("btn btn-mini btn-danger")->href($presenter->link("delete!", $row->id))->setHtml(html::el("i")->class("fas fa-trash-alt"))->title("Smazat"));
                return $el;
        });

        $this->localiseGrid($grid);

        return $grid;
    }

    /* FORMS */

	public function createComponentSlideForm(){
		$form = new Form(null);

		$form ->addText("caption", "Text")
				->addRule(Form::FILLED, "Vyplňte text");
		$form ->addText("name", "Rozměr")
				->addRule(Form::FILLED, "Vyplňte rozměr");
		$form ->addText("price", "Cena")
				->addRule(Form::FILLED, "Vyplňte cenu");
        $form ->addUpload("img", "Soubor obrázku")
            //->setRequired(true)
            //->addRule(Form::IMAGE, 'Soubor musí být JPG, JPEG, PNG nebo GIF.')
            ;
        $form ->addUpload("small", "Soubor ikony")
            //->setRequired(true)
            //->addRule(Form::IMAGE, 'Soubor musí být JPG, JPEG, PNG nebo GIF.')
            ;
		$form->addSubmit("submit", "Uložit slide")
				->getControlPrototype()->class("btn btn-success");

		$form->onSuccess[] = [$this, 'saveSlide'];

		return $form;
	}

	public function saveSlide(Form $form){

		// security
		//$this->isAllowed(array(1,3));

		$values = $form->getValues();

		if($form->isValid()){
			try{
				$image = $values->img;
				$small = $values->small;
				unset($values->img);
				unset($values->small);

				if($this->editedId){
					//update existing
					$this->commonManager->updateSlide($values, $this->editedId);
				}
				else{
					//add new
					$this->editedId = $this->commonManager->addSlide($values);
				}

                if (!empty($image) && $image->isOk()) {
                    $ext = pathinfo($image->getSanitizedName(), PATHINFO_EXTENSION);
                    $name = $this->generateString(15);
                    $fileName = $name.".".$ext;
                    $origFile = BASE_DIR."/data/original/".$fileName;
                    $image->move($origFile);

                    //resize and move
                    /*
                    $bigImage = Image::fromFile($tempFile);
                    $bigImage->resize(1600, 1200, Image::SHRINK_ONLY);
                    $bigImage->save($origFile);
                    */
                    
                    $this->commonManager->updateSlide(array("file"=>$fileName), $this->editedId);
                }
                if (!empty($small) && $small->isOk()) {
                    $image = $small;
                    $ext = pathinfo($image->getSanitizedName(), PATHINFO_EXTENSION);
                    $name = $this->generateString(15);
                    $fileName = $name.".".$ext;
                    $origFile = BASE_DIR."/data/original/".$fileName;
                    $image->move($origFile);

                    //resize and move
                    /*
                    $bigImage = Image::fromFile($tempFile);
                    $bigImage->resize(1600, 1200, Image::SHRINK_ONLY);
                    $bigImage->save($origFile);
                    */
                    
                    $this->commonManager->updateSlide(array("fileSmall"=>$fileName), $this->editedId);
                }

				$this->flashMessage("Slide byl uložen");
				$this->redirect("default");
			}
			catch(DibiDriverException $e){
				$this->flashMessage($e->getMessage(), "error");
			}
		}
	}

	public function handleSort(array $items = null){
		if(!empty($items)){
			foreach($items as $index=>$item){
				$this->commonManager->updateSlide(array("order"=>$index+1), $item);
			}
			$this->flashMessage("Pořadí slidů bylo uloženo");
			$this->redirect("this");
		}

	}


}
