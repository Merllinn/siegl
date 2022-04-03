<?php

namespace App\FrontModule\Presenters;

use TH\Form;
use TH\Translator;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use Nette;
use Markette\Gopay\Service\PaymentService;

class HomepageForms extends BasePresenter
{

    /** @var PaymentService @inject */
    public $paymentService;

    /** Create contact form
    *
    * @return Form
    */
    public function createComponentRentForm(){
        $form = new Form($this->lang);

        $places = array();
        foreach($this->template->branchesFull as $place){
            $places[$place->city] = $place->city." - ".$place->name;
			/*
            if($place->price > 0){
				$places[$place->city].=" (".number_format($place->price, 0, ",", " ").",-)";
            }
            */
        }

        $form->getElementPrototype()->class("ajax");
        $form->addSelect("branch", "Místo zapůjčení", $places)
        	->setPrompt("Vyberte místo zapůjčení")
            ->getControlPrototype()->class("form-control selectpicker");
        $form["branch"]->addRule(Form::FILLED, "Vyberte místo zapůjčení");
        $form ->addText("dates", "Datum půjčení/vrácení:")
                ->getControlPrototype()->class("form-control");
        $form["dates"]->addRule(Form::FILLED, "Vyberte datum půjčení a vrácení");

        $form ->addText("firstname", "Vaše jméno")
                ->getControlPrototype()->class("form-control");
        $form ->addText("surname", "Vaše příjmení")
                ->getControlPrototype()->class("form-control");
        $form["surname"]->addRule(Form::FILLED, "Vyplňte příjmení");
        $form ->addText("email", "Váš e-mail")
                ->getControlPrototype()->class("form-control");
        $form["email"]->addRule(Form::FILLED, "Vyplňte e-mail");
        $form["email"]->addRule(Form::EMAIL, "Vyplňte platný e-mail");
        $form ->addText("phone", "Vaše telefonní číslo")
                ->getControlPrototype()->class("form-control");
        $form["phone"]->addRule(Form::FILLED, "Vyplňte telefonní číslo");

        $form->addSubmit("submit", "Pokračovat")->getControlPrototype()->setName('button')->class("btn btn-primary to-step-three")->title("Pokračovat na další krok")->setHtml('Pokračovat');

        $form["dates"]->getControlPrototype()->data("value", "");
        if(!empty($this->basket->details)){
            $defaults = $this->basket->details;
            $form["dates"]->getControlPrototype()->data("value", $this->basket->details->from->format("d/m/Y"));
            $defaults->dates = $this->basket->details->from->format("d/m/Y")." - ".$this->basket->details->to->format("d/m/Y");
            $form->setDefaults($defaults);
        }

        $form->onSuccess[] = [$this, 'sendRent'];

        return $form;
    }

    /** callback for contact form
    *
    * @param Form data from contact form
    * @return void
    */
    public function sendRent(Form $form, $values){

        $fromTo = $values->dates;
        unset($values->dates);
        list($from, $to) = explode(" - ", $fromTo);
        $values->from = \nette\utils\DateTime::createFromFormat("d/m/Y", $from);
        $values->to = \nette\utils\DateTime::createFromFormat("d/m/Y", $to);


        $values->name = $values->firstname;
        $this->basket->details = $values;
        $this->basket->step = 3;
        $this->basket->step2 = true;

        $this->template->ajax = true;
        $this->redrawControl("rent");

    }

    public function createComponentVoucherForm(){
        $form = new Form($this->lang);

        $form->getElementPrototype()->class("ajax");
        $form ->addText("voucher", "Zde můžete uplatnit váš slevový kupón")
                ->getControlPrototype()->class("form-control")->placeholder("Zde můžete uplatnit váš slevový kupón");

        $form->addSubmit("submit", "Použít slevový kupón")->getControlPrototype()->setName('button')->class("btn btn-primary btn-grey to-step-three ")->title("Použít slevový kupón")->setHtml('Použít slevový kupón');

        if(!empty($this->basket->details)){
            $form->setDefaults($this->basket->details);
        }

        $form->onSuccess[] = [$this, 'useVoucher'];

        return $form;
    }

    /** callback for contact form
    *
    * @param Form data from contact form
    * @return void
    */
    public function useVoucher(Form $form, $values){

        $exists = $this->voucherManager->findByCode($values->voucher);
        $now = new \nette\utils\DateTime();
        if(!$exists){
            $form->addError("Slevový kód není platný");
        }
        elseif(!empty($exists->validTo) && $exists->validTo<$now){
            $form->addError("Slevový kód již není platný");
        }
        elseif(!$exists->unlimited && !empty($exists->used)){
            $form->addError("Slevový kód již byl použit");
        }
        else{
            $exists = \Nette\Utils\ArrayHash::from($exists->toArray());
            $this->basket->voucher = $exists;
            $this->flashMessage("Slevový kód byl úspěčně použit");
        }

        $this->recalculateBasket();

        $this->template->ajax = true;
        $this->redrawControl("rent");

    }

    /** Create contact form
    *
    * @return Form
    */
    public function createComponentFinishRentForm(){
        $form = new Form($this->lang);

        $places = array();
        foreach($this->branches as $br=>$place){
            $places[$br] = $br;
        }

        //$form->getElementPrototype()->class("ajax");

        $form ->addTextArea("note", "Poznámka", 30, 2)
                ->getControlPrototype()->class("form-control");
        $form ->addCheckbox("newsletter", "Souhlasím se zasíláním novinek ze světa Polaroidů")
                ->getControlPrototype()->class("form-check-input");


        $form->addSubmit("reserve", "Rezervovat")->getControlPrototype()->setName('button')->class("btn btn-primary btn-grey btn-final-reservation")->title("Pokračovat na další krok")->setHtml('Rezervovat');
        $form->addSubmit("pay", "Rezervovat a zaplatit")->getControlPrototype()->setName('button')->class("btn btn-primary")->title("Pokračovat na další krok")->setHtml('Rezervovat a zaplatit');

        $form->onSuccess[] = [$this, 'finishRent'];

        return $form;
    }

    /** callback for contact form
    *
    * @param Form data from contact form
    * @return void
    */
    public function finishRent(Form $form, $values){

        $order = $this->saveRent($values);

        $this->basket->remove();

        if($form['pay']->isSubmittedBy()){
            $this->payOrder($order);
            $this->template->pay = true;
        }
        $this->template->order = $order;


        $this->template->final = true;

        //$this->template->ajax = true;
        $this->redrawControl("rent");

    }

    public function saveRent($values, $pay = false){
        $reservation = new \nette\utils\ArrayHash();
        //$reservation->camera = $this->basket->camera->id;
        //$reservation->photos = $this->basket->amount;
        $reservation->price = $this->basket->totalPrice;
        $reservation->price_actual = $this->basket->totalPrice;
        $reservation->date = new \nette\utils\DateTime();
        $reservation->from = $this->basket->details->from;
        $reservation->to = $this->basket->details->to;
        $reservation->branch = $this->basket->details->branch;
        $reservation->name = $this->basket->details->name;
        $reservation->surname = $this->basket->details->surname;
        $reservation->email = $this->basket->details->email;
        $reservation->phone = $this->basket->details->phone;
        $reservation->note = $values->note;
        $reservation->newsletter = $values->newsletter;

        if(!empty($this->basket->voucher)){
            $reservation->voucherCode = $this->basket->voucher->code;
            $reservation->voucherSale = $this->basket->voucherSale;
        }

        $items = $this->basket->items;
        $cameras = array();
        $cameras[] = array(
            "id"=>$this->basket->camera->id,
            "name"=>$this->basket->camera->name,
            "cameras"=>1,
            "photos"=>$this->basket->amount,
            "price"=>$this->basket->camera->price
        );
        $orderId = $this->orderManager->saveOrder($reservation, $cameras, $items);
        if(!empty($this->basket->voucher) && !$this->basket->voucher->unlimited){
            $this->voucherManager->update(array("used"=>new \Nette\Utils\DateTime()), $this->basket->voucher->id);
        }
        if($values->newsletter){
            $this->context->getService("mailchimp")->subscribe($this->basket->details->email, $this->basket->details->name, $this->basket->details->surname);
        }

        $order = $this->orderManager->find($orderId);
        $orderCameras = $this->orderManager->findOrderCameras($orderId);
        $orderItems = $this->orderManager->findOrderItems($orderId);


        $orderStatus = $this->orderStatusManager->find(1);
        if($orderStatus->smsText!=''){
            $this->sendSMS($order->phone, $orderStatus->smsText, $order->id);
        }
        //customer mail
        $template = $this->createTemplate();
        $template->setFile(APP_DIR . '/FrontModule/templates/Mails/orderStatus1.latte');
        $template->order = $order;
        $template->items = $orderItems;
        $template->cameras = $orderCameras;

        $subject = str_replace("[ID]",$order->id , $orderStatus->subject);

        $mail = new Message;
        $mail->setFrom($this->settings->title." <".$this->settings->email.">")
            ->addTo($this->basket->details->email)
            ->setSubject($subject)
            ->setHtmlBody($template);
        $mailer = new SendmailMailer;
        $mailer->send($mail);

        //eshop mail
        $template = $this->createTemplate();
        $template->setFile(APP_DIR . '/FrontModule/templates/Mails/orderConfirmEshop.latte');
        $template->order = $order;
        $template->items = $orderItems;
        $template->cameras = $orderCameras;


        $mail = new Message;
        $mail->setFrom($this->basket->details->email)
            ->addTo($this->settings->email)
            ->setSubject("Nová rezervace")
            ->setHtmlBody($template);
        $mailer = new SendmailMailer;
        $mailer->send($mail);

        return $order;
    }

    /** Create login form
    *
    * @return Form
    */
    public function createComponentLoginForm(){
        $form = new Form($this->lang);

        $form->getElementPrototype();
        $form->addText("login", "")
            ->addRule(Form::FILLED, "Vyplňte přihlašovací jméno")
            ->getControlPrototype()->placeholder("Přihlašovací jméno");
        $form->addPassword("pass", "")
            ->getControlPrototype()->placeholder("Heslo");
        $form->addSubmit("submit", "Přihlásit");

        $form->onSuccess[] = [$this, 'sendLoginForm'];

        return $form;
    }

    public function sendLoginForm(Form $form, $values){

        try {
            // try to login
                //$this->user->setExpiration($values->remember ? '14 days' : '20 minutes');
                $this->user->login($values->login, $values->pass);
                $this->flashMessage("Vítejte.");
                $this->template->user = $this->user;
                $this->redrawControl("loginForm");

                if(isset($form["submit"])&&$form["submit"]->isSubmittedBy()){
                    if($this->user->isLoggedIn() && $this->user->identity->role>=5){
                        $this->redirect(":Admin:Homepage:default");
                    }
                    else{
                        $this->redirect(":Front:Homepage:page");
                    }
                }

        } catch (Nette\Security\AuthenticationException $e) {
            //$this->flashMessageError($e->getMessage());
            $form->addError($e->getMessage());
            if($e->getCode()==4){
                $form->addError(html::el("a")->href($this->link(":Front:User:reactivate", array("login"=>$values->login)))->add("Znovu odeslat aktivační mail"));
            }

        }

    }





}
