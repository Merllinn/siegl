<?php

namespace App\AdminModule\Presenters;

use     TH\Form;

class HomepagePresenter extends BasePresenter
{
	public function startup(){
		parent::startup();
	}

	public function actionDefault(){
        $this->redirect(":Admin:Pages:default");
	}

	public function actionAuthorize(){
		$client = $this->getClientObject();
	    // Request authorization from the user.
	    $this->template->authUrl = $client->createAuthUrl();
	}

    public function createComponentAuthorizeForm(){
        $form = new Form();

        $form ->addText("password", "Autorizační heslo")
                ->getControlPrototype()->class("form-control");
        $form["password"]->addRule(Form::FILLED, "zadejte autorizační heslo");
        $form->addSubmit("submit", "Autorizovat")->getControlPrototype()->class("btn btn-primary");

        $form->onSuccess[] = [$this, 'doAuthorize'];

        return $form;
    }


    /** callback for page form
    *
    * @param Form data from page form
    * @return void
    */
    public function doAuthorize(Form $form){
        $values = $form->getValues();

		$client = $this->getClientObject();
		$authCode = trim($values->password);

		// Exchange authorization code for an access token.
		try{
			$accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
			$client->setAccessToken($accessToken);
		}
		catch(\InvalidArgumentException $e){
			$form["password"]->addError($e->getMessage());
		}

        if($form->isValid()){

		    // Check to see if there was an error.
		    if (array_key_exists('error', $accessToken)) {
		        $this->flashMessageError("Autorizace se nezdařila");
		    }
		    else{
				// Save the token to a file.
				if (!file_exists(dirname(G_TOKEN_PATH))) {
				    mkdir(dirname(G_TOKEN_PATH), 0700, true);
				}
				file_put_contents(G_TOKEN_PATH, json_encode($client->getAccessToken()));
			    $this->flashMessage("Autorizace proběhla v pořádku");
			    $this->redirect(":Admin:Projects:default");
		    }
        }
    }


}
