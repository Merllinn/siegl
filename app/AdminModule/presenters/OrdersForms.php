<?php

namespace App\AdminModule\Presenters;

use     TH\Form,
        Nette\Utils\Html;
use     Ublaboo\DataGrid\DataGrid;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;

class OrdersForms extends BasePresenter
{

    public $payStatuses = array("0"=>"čeká na platbu", "5"=>"platba se nezdařila", "10"=>"uhrazeno", "20"=>"uhrazeno kartou", "30"=>"uhrazeno hotově", "40"=>"uhrazeno převodem");
    public $filter;
    public $edited;
    public $orders;
    public $fromPage;

    public function createComponentOrderForm(){
        $form = new Form();

	    $form->addSelect("order_status_id", "Stav", $this->statuses)
	            ->getControlPrototype()->class("form-control");

        $form ->addSelect("branch", "Pobočka", $this->branchesSimple)
                ->getControlPrototype()->class("form-control");
        $form["branch"]->setPrompt(" - ");

        $form ->addText("name", "Jméno")
                ->getControlPrototype()->class("form-control");
        $form["name"]->addRule(Form::FILLED, "Vyplňte jméno");
        $form ->addText("surname", "Příjmení")
                ->getControlPrototype()->class("form-control");
        $form["surname"]->addRule(Form::FILLED, "Vyplňte příjmení");
        $form ->addText("email", "E-mail")
                ->getControlPrototype()->class("form-control");
        $form["email"]->setRequired(true)->addRule(Form::EMAIL, "Vyplňte e-mail");
        $form ->addText("phone", "Telefonní číslo")
                ->getControlPrototype()->class("form-control");
        $form["phone"]->setRequired(true)->addRule(Form::FILLED, "Vyplňte telefonní číslo");
        $form ->addText("street", "Adresa")
                ->getControlPrototype()->class("form-control");
        $form["street"]->addRule(Form::FILLED, "Vyplňte adresu");
        $form ->addText("city", "Město")
                ->getControlPrototype()->class("form-control");
        $form["city"]->addRule(Form::FILLED, "Vyplňte město");
        $form ->addText("zip", "PSČ")
                ->getControlPrototype()->class("form-control");
        $form["zip"]->addRule(Form::FILLED, "Vyplňte PSČ");

        $form ->addTextArea("note", "Poznámka zákazníka", 30, 2)
                ->getControlPrototype()->class("form-control");

        $form ->addTextArea("publicNote", "Poznámka Polagraph", 30, 2)
                ->getControlPrototype()->class("form-control");

        $form -> addCheckbox("different_delivery", "Přejete si odeslat zboží na jinou adresu?")
                ->getControlPrototype()->class("differentDelivery form-check-input");
        $form["different_delivery"]->getLabelPrototype()->class("form-check-label");

        $form ->addText("delivery_name", "Jméno")
                ->getControlPrototype()->class("form-control");
        $form["delivery_name"];
        $form ->addText("delivery_street", "Adresa")
                ->getControlPrototype()->class("form-control");
        $form["delivery_street"]
        	->addConditionOn($form['different_delivery'], Form::EQUAL, TRUE)
        	->addRule(Form::FILLED, "Vyplňte adresu");
        $form ->addText("delivery_city", "Město")
                ->getControlPrototype()->class("form-control");
        $form["delivery_city"]
        	->addConditionOn($form['different_delivery'], Form::EQUAL, TRUE)
        	->addRule(Form::FILLED, "Vyplňte město");
        $form ->addText("delivery_zip", "PSČ")
                ->getControlPrototype()->class("form-control");
        $form["delivery_zip"]
        	->addConditionOn($form['different_delivery'], Form::EQUAL, TRUE)
        	->addRule(Form::FILLED, "Vyplňte PSČ");

        $form->addSubmit("submit", "Uložit objednávku")->getControlPrototype()->class("btn btn-primary");

        $form->onSuccess[] = [$this, 'saveOrder'];

        return $form;
    }


    /** callback for page form
    *
    * @param Form data from page form
    * @return void
    */
    public function saveOrder(Form $form){
        $values = $form->getValues();

        if($form->isValid()){
            try{
                //$camera = $this->productManager->find($values->camera);
                //$values->price = $camera->price*$values->photos;
                if($this->edited){
                    $this->orderManager->update($values, $this->edited);
                    $this->flashMessage("Zakázka byla uložena.");
                    if(empty($this->fromPage->page)){
                        $this->redirect(":Admin:Orders:default");
                    }
                    else{
                        $this->redirect(":Admin:Orders:".$this->fromPage->page);
                    }

                }
                else{

                    $values->date = new \nette\utils\DateTime();
                    $newOrder = $this->orderManager->add($values);
                    $this->flashMessage("Zakázka byla přidána.");
                    $this->redirect(":Admin:Orders:default");
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

        $form->addText("reserver", "Jméno nebo příjmení zákazníka")
            ->getControlPrototype()->class("form-control");

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
            $this->filter->delivery = $values->delivery;
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
