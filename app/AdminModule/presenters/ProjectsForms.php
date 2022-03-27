<?php

namespace App\AdminModule\Presenters;

use     TH\Form,
        Nette\Utils\Html;
use     Ublaboo\DataGrid\DataGrid;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;

class ProjectsForms extends BasePresenter
{

    public $filter;
    public $edited;
    public $projects;

    public function createComponentProjectForm(){
        $form = new Form();

	    /*
	    $folders = $this->getGfolders();
	    $labFolders = $this->getGfolders(G_ROOT_LAB, true);
	    */
	    
        if(!empty($this->edited)){
	        $details = $this->projectManager->find($this->edited);
	        /*
	        if(!isset($folders[$details->hash])){
				$folders[$details->hash] = "SLOŽKA NA SERVERU NEEXISTUJE!!!";
	        }
	        */
        }
	    

        $form ->addText("name", "Jméno")
                ->getControlPrototype()->class("form-control");
        $form ->addText("surname", "Příjmení")
                ->getControlPrototype()->class("form-control");
        $form["surname"]->addRule(Form::FILLED, "Vyplňte příjmení");
        $form ->addText("email", "E-mail")
                ->getControlPrototype()->class("form-control");
		$form["email"]
			->addRule(Form::FILLED, "Vyplťe email")                
			->addRule(Form::EMAIL, "Zadejte platný email");                
        $form ->addText("phone", "Telefonní číslo")
                ->getControlPrototype()->class("form-control");
        $form ->addSelect("branch", "Pobočka", $this->branchesSimple)
                ->getControlPrototype()->class("form-control");
        $form["branch"]->setPrompt(" - ");
        $form ->addSelect("recieve", "Způsob doručení", $this->recieves)
                ->getControlPrototype()->class("form-control");
        /*
        $form ->addSelect("hash", "Složka zakázky", $folders)
        		->setPrompt(" - Založit novou složku - ")
                ->getControlPrototype()->class("select2");
        */
        if(empty($this->edited)){
		    $form ->addText("films", "Číslo filmu (víc filmů oddělujte čárkou)")
		            ->getControlPrototype()->class("form-control");
        	$form["films"]->addRule(Form::FILLED, "Zadejte čísla filmů");
			/*
	        $form ->addText("count", "Počet filmů")
	                ->getControlPrototype()->class("form-control");
        			$form["count"]->addRule(Form::NUMERIC, "Zadejte počet filmů");
        			$form["count"]->setRequired(true);
        			$form["count"]->setValue(1);
	        */
        }
        else{
		    $form ->addText("labId", "Číslo filmu")
		            ->getControlPrototype()->class("form-control");
        	//$form["labId"]->addRule(Form::FILLED, "Zadejte číslo filmu");
        }

        $form ->addTextArea("note", "Interní poznámka", 30, 2)
                ->getControlPrototype()->class("form-control");

        $form ->addTextArea("publicNote", "Veřejná poznámka", 30, 2)
                ->getControlPrototype()->class("form-control");

        $form ->addText("invoice", "Číslo faktury")
                ->getControlPrototype()->class("form-control");

        $form->addSubmit("submit", "Uložit zakázku")->getControlPrototype()->class("btn btn-primary");
        
        if(!empty($this->edited)){
	        $form->setDefaults($details);
        }

        $form->onSuccess[] = [$this, 'saveProject'];

        return $form;
    }


    /** callback for page form
    *
    * @param Form data from page form
    * @return void
    */
    public function saveProject(Form $form){
        $values = $form->getValues();

        if($form->isValid()){
            try{
                //$camera = $this->productManager->find($values->camera);
                //$values->price = $camera->price*$values->photos;
                if($this->edited){
                    $this->projectManager->update($values, $this->edited);
                    $this->flashMessage("Zakázka byla uložena.");
                    $this->redirect(":Admin:Projects:default");

                }
                else{
                    $values->date = new \nette\utils\DateTime();
                	$films = explode(",",$values->films);
                	unset($values->films);
                	foreach($films as $filmId){
                		$values->labId = trim($filmId);
	                    $newproject = $this->projectManager->add($values);
                		/*
                		if(empty($values->hash)){
                			$name = $newproject."_".\Nette\Utils\Strings::webalize($values->surname)."_".date("Ymd");
							$folder = $this->createGfolder($name);
							$hash = $folder->getId();
							$this->projectManager->update(array("hash"=>$hash), $newproject);
                		}
                		*/
                	}

                    $this->flashMessage(count($films)." zakázek bylo vytvořeno.");
                    $this->redirect(":Admin:Projects:default");
                }


            }
            catch(DibiDriverException $e){
                $this->flashMessage($e->getMessage(), "error");
            }
        }
    }


    public function createComponentFilterForm(){
        $form = new Form();

        //$form->getElementPrototype()->class("form-inline");

        $branches = array(""=>"všechny pobočky");
        foreach($this->branchesSimple as $key=>$val){
			$branches[$key] = $val." nebo nevyplněno";
        }

        $form->addText("reserver", "Jméno nebo příjmení zákazníka")
            ->getControlPrototype()->class("form-control");
        $form->addSelect("branch", "Pobočka", $branches)
            ->getControlPrototype()->class("form-control");
        //$form["branch"]->setPrompt("všechny pobočky");

        $form->addSubmit("submit", "Filtruj")->getControlPrototype()->class("btn btn-primary");
        $form->addSubmit("reset", "Zobraz vše")->getControlPrototype()->class("btn btn-light");

        $form->setDefaults($this->filter);

        $form->onSuccess[] = [$this, 'filter'];

        return $form;
    }

    /** callback for seo form
    *
    * @param Form data from page form
    * @return void
    */
    public function filter(Form $form){
        $values = $form->getValues();

        if($form["reset"]->isSubmittedBy()){
            $this->filter->remove();
            $this->redirect("this");
        }
        else{
            $this->filter->reserver = $values->reserver;
            if(empty($values->branch)){
            	$this->filter->branch = "";
            }
            else{
            	$this->filter->branch = $values->branch;
            }
            $this->redirect("this");
        }
    }



}
