<?php

namespace App\AdminModule\Presenters;

use 	TH\Form,
		Nette\Utils\Html;

class SettingsPresenter extends BasePresenter
{
	public function startup(){
		parent::startup();
        if ($this->user->id<>1){
			//$this->flashMessage("Pro přístup k požadovanému obsahu nemáte dostatečná oprávnění");
			//$this->redirect(":Admin:Homepage:default");
        }
	}

	public function renderDefault(){
        $settings = $this->rowToArray($this->settings);
        $fileName = APP_DIR . '/FrontModule/templates/Mails/sendLink.latte';
        if(file_exists($fileName)){
            $file = fopen($fileName, "r");
            $fileContent = fread($file, 999999);
            $settings->linkMail = $fileContent;
            fclose($file);
        }
		$this["settingsForm"]->setDefaults($settings);
	}
	/** Create page form
	*
	* @return Form
	*/
	public function createComponentSettingsForm(){
		$form = new Form();

		$form->addGroup("Základní nastavení");
		$form ->addText("web", "Adresa webu");
		$form ->addText("email", "E-mail webu");
		$form ->addText("title", "Titulek webu");
		$form ->addCheckbox("production", "Produkční mód");
		$form ->addText("gaCode", "GA kód");
		$form->addGroup("SEO");
		$form ->addTextArea("description", "Popis");
		$form ->addTextArea("keywords", "Klíčová slova");


		$form->addSubmit("submit", "Uložit nastavení")->getControlPrototype()->class("btn btn-primary");

		$form->onSuccess[] = [$this, 'saveSettings'];

		return $form;
	}

	/** callback for page form
	*
	* @param Form data from page form
	* @return void
	*/
	public function saveSettings(Form $form){
		$values = $form->getValues();

		if($form->isValid()){
			try{
				$this->commonManager->saveSettings($values);

				$this->flashMessage("Nastavení bylo uloženo.");
				$this->redirect("this");


			}
			catch(DibiDriverException $e){
				$this->flashMessage($e->getMessage(), "error");
			}
		}
	}



}