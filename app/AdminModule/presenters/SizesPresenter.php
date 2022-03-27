<?php

namespace App\AdminModule\Presenters;

use 	TH\Form,
		Nette\Utils\Html;
use     Ublaboo\DataGrid\DataGrid;

class SizesPresenter extends BasePresenter
{
	public $editedId = false;



	public function startup(){
		parent::startup();
		$this->addBreadcrumbs("Rozměry a ceny", $this->link(":Admin:Sizes:default"));

	}

	public function renderDefault(){
	}

	public function actionAdd(){
		$this->setView("addEdit");
	}

	public function actionEdit($id){
		$this->editedId = $id;
		$details = $this->commonManager->findSize($id);
		$this["sizeForm"]->setDefaults($details);
		$this->setView("addEdit");
	}

	public function handleDelete($id){
		$this->commonManager->deleteSize($id);
		$this->redirect("this");
	}

	public function handleActivate($id){
		try{
			$this->commonManager->updateSize(array("active"=>1), $id);
			$this->redirect("this");
		}
		catch(DibiDriverException $e){
			$this->flashMessage($e->getMessage());
		}
	}

	public function handleDeactivate($id){
		try{
			$this->commonManager->updateSize(array("active"=>0), $id);
			$this->redirect("this");
		}
		catch(DibiDriverException $e){
			$this->flashMessage($e->getMessage());
		}
	}

    /**
     * Make table of customers
     *
     * @return \Addons\Tabella
     */
    public function createComponentSizes($name)
    {
        $presenter = $this;

        $source = $this->commonManager->getSizes();

        $grid = new DataGrid($this, $name);
        $grid->setDataSource($source);

        $grid->addColumnText('name', 'Rozměr');
        $grid->addColumnText('price', 'Cena');
        $grid->addColumnText('minOrder', 'Minimální objednávka');
        $grid->addColumnText('active', 'Aktivní')
            ->setRenderer(function($row) use ($presenter) {
                if($row->active){
                	return Html::el("a")->class("tabella_ajax")->href($presenter->link("deactivate!", $row->id))->setHtml(html::el("img")->src(FOLDER."/images/active.png")->class("action"));
                }
                else{
                	return Html::el("a")->class("tabella_ajax")->href($presenter->link("activate!", $row->id))->setHtml(html::el("img")->src(FOLDER."/images/deactive.png")->class("action"));
                }
        });

        $grid->addColumnText('tools', 'Nástroje')
            ->setRenderer(function($row) use ($presenter) {
                $el = Html::el("span");
                $el->insert(0, html::el("a")->class("btn btn-mini btn-light")->href($presenter->link("edit", $row->id))->setHtml(html::el("i")->class("fas fa-edit"))->title("Upravit"));
				/*
                $el->insert(1, " ");
                $el->insert(4, html::el("a")->class("btn btn-mini btn-danger")->href($presenter->link("delete!", $row->id))->setHtml(html::el("i")->class("fas fa-trash-alt"))->title("Smazat"));
                */
                return $el;
        });

        $this->localiseGrid($grid);

        return $grid;
    }

    /* FORMS */

	public function createComponentSizeForm(){
		$form = new Form(null);

		$form ->addText("name", "Rozměr")
				->addRule(Form::FILLED, "Vyplňte rozměr");
		$form ->addText("price", "Cena")
				->addRule(Form::FILLED, "Vyplňte cenu");
		$form ->addText("minOrder", "Minimální objednávka")
				->addRule(Form::FILLED, "Vyplňte minimální objednávané množství");
		$form->addSubmit("submit", "Uložit rozměr")
				->getControlPrototype()->class("btn btn-success");

		$form->onSuccess[] = [$this, 'saveSize'];

		return $form;
	}

	public function saveSize(Form $form){

		// security
		//$this->isAllowed(array(1,3));

		$values = $form->getValues();

		if($form->isValid()){
			try{

				if($this->editedId){
					//update existing
					$this->commonManager->updateSize($values, $this->editedId);
				}
				else{
					//add new
					$this->commonManager->addSize($values);
				}

				$this->flashMessage("Rozměr byl uložen");
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
				$this->commonManager->updateSize(array("order"=>$index+1), $item);
			}
			$this->flashMessage("Pořadí rozměrů bylo uloženo");
			$this->redirect("this");
		}

	}


}
