<?php

namespace App\AdminModule\Presenters;

use 	TH\Form,
		Nette\Utils\Html;
use     Ublaboo\DataGrid\DataGrid;

class OrderStatusesPresenter extends BasePresenter
{
	public $editedId = false;
    public $statuseStyles = array("badge-primary","badge-secondary","badge-success","badge-danger","badge-warning","badge-info","badge-light","badge-dark","badge-canceled");

	public function startup(){
		parent::startup();
		$this->addBreadcrumbs("Stavy objednávek", $this->link(":Admin:OrderStatuses:default"));
	}

	public function renderDefault(){
	}

    public function actionAdd(){
        $this->setView("addEdit");
    }

    public function actionEdit($id){
        $this->editedId = $id;
        $details = $this->orderStatusManager->find($id)->toArray();
        $details = \Nette\Utils\ArrayHash::from($details);
        $fileName = APP_DIR . '/FrontModule/templates/Mails/orderStatus'.$id.'.latte';
        if(file_exists($fileName)){
            $file = fopen($fileName, "r");
            $fileContent = fread($file, 999999);
            $details->file = $fileContent;
            fclose($file);
        }
        $this["statusForm"]->setDefaults($details);
        $this->setView("addEdit");
    }

	public function handleDelete($id){
		$this->orderStatusManager->delete($id);
		$this->redirect("this");
	}

	public function handleDeactivate($id){
		$this->orderStatusManager->update(array("active"=>0), $id);
		$this->redirect("this");
	}

	public function handleActivate($id){
		$this->orderStatusManager->update(array("active"=>1), $id);
        $this->redirect("this");
	}

    /**
     * Make table of customers
     *
     * @return \Addons\Tabella
     */
    public function createComponentStatuses($name)
    {
        $presenter = $this;

        $source = $this->orderStatusManager->get();

        $grid = new DataGrid($this, $name);
        $grid->setDataSource($source);

        $grid->addColumnText('id', 'ID');
        $grid->addColumnText('name', 'Název')
            ->setRenderer(function($row) use ($presenter) {
                return Html::el("span")->class("badge ".$row->class)->setHtml($row->name);
        });
        /*
        $grid->addColumnText('sendDocuments', 'Odeslat faktury')
            ->setRenderer(function($row) use ($presenter) {
                if($row->sendDocuments){
                    return Html::el("span")->class("badge badge-success")->setHtml("ano");
                }
                else{
                    return Html::el("span")->class("badge badge-danger")->setHtml("ne");
                }
        });
        */

		if($this->user->identity->role==9){
        $grid->addColumnText('tools', 'Nástroje')
            ->setRenderer(function($row) use ($presenter) {
                $el = Html::el("span");
                $el->insert(0, html::el("a")->class("btn btn-mini btn-light")->href($presenter->link(":Admin:OrderStatuses:edit", $row->id))->setHtml(html::el("i")->class("fas fa-edit"))->title("Upravit"));
                if($row->id!=1&&$row->id!=100){
                    $el->insert(1, " ");
                    $el->insert(2, html::el("a")->class("btn btn-mini btn-danger")->href($presenter->link("delete!", $row->id))->setHtml(html::el("i")->class("fas fa-trash-alt"))->title("Smazat"));
                }
                return $el;
        });
		}

        $this->localiseGrid($grid);

        return $grid;
    }

    /* FORMS */

	public function createComponentStatusForm(){
		$form = new Form(null);

        $statuses = array();
        $i = 1;
        foreach($this->statuseStyles as $status){
            $statuses[$status] = "  Varianta ".$i;
            $i++;
        }

        $form ->addText("id", "ID")
                ->addRule(Form::FILLED, "Vyplňte ID");
        $form ->addText("name", "Název");
		/*
        $form ->addText("smsText", "Text SMS");
        $form ->addCheckbox("sendDocuments", "Odeslat neodeslané faktury");
        */
        $form ->addRadioList("class", "Vzhled", $statuses);
        $form ->addText("subject", "Předmět mailu");
		$form ->addTextArea("file", "Text mailu", 30, 30)->getControlPrototype()->class("highlight");

        $form["class"]->getControlPrototype()->class("classRadio");


		$form->addSubmit("submit", "Uložit stav")
				->getControlPrototype()->class("btn btn-success");

		$form->onSuccess[] = [$this, 'saveStatus'];

		return $form;
	}

	public function saveStatus(Form $form, $values){

		// security
		//$this->isAllowed(array(1,3));

		if($form->isValid()){
			try{

                $fileContent = $values->file;
                unset($values->file);

				if($this->editedId){
					//update existing
					$this->orderStatusManager->update($values, $this->editedId);
				}
				else{
					$this->orderStatusManager->add($values);
				}

                $file = fopen(APP_DIR . '/FrontModule/templates/Mails/orderStatus'.$values->id.'.latte', "w");
                fputs($file, $fileContent);
                fclose($file);

				$this->flashMessage("Stav byl uložen");
				$this->redirect(":Admin:OrderStatuses:default");
			}
			catch(DibiDriverException $e){
				$this->flashMessage($e->getMessage(), "error");
			}
		}
	}

}
