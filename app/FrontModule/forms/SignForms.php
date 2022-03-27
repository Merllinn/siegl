<?php

namespace App\FrontModule\Presenters;

use TH\Form,
	TH\Translator;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use Nette\Utils\Random;
use Nette;

class SignForms extends BasePresenter
{

	const PASSWORD_MIN_LENGTH = 6;
	const PASSWORD_MAX_LENGTH = 20;

	/**
	 * Sign-in form.
	 * @return Form
	 */
	public function createComponentSignInForm()
	{

		$form = new Form;
		$form->setTran($this->translateManager);
		$form->addText('email', '')
			->setRequired('Vyplňte e-mail.');
        $form["email"]->getControlPrototype()->placeholder($this->translator->translate("Váš e-mail"));

		$form->addPassword('password', '')
			->setRequired('Vyplňte heslo.');
        $form["password"]->getControlPrototype()->placeholder($this->translator->translate("Heslo"));

		$form->addCheckbox('remember', 'Zůstat přihlášen');

		$form->addSelect('language', "", $this->languages);

		$form->addSubmit('send', 'Přihlásit')
			->getControlPrototype()->class("btn-block btn-sm");

		$form->onSuccess[] = [$this, 'signInFormSucceeded'];

		return $form;

	}

	public function signInFormSucceeded(\TH\Form $form, $values){
		try {
			//$this->user->setExpiration($values->remember ? '14 days' : '20 minutes');
            $this->user->login($values->email, $values->password);
			$this->userManager->update(["language"=>$values->language], $this->user->id);
			$this->user->identity->language = $values->language;
			$this->flashMessage("Byli jste přihlášeni.");
			$this->redirect('Homepage:page', array("lang"=>$this->user->identity->language));
		} catch (Nette\Security\AuthenticationException $e) {
			switch($e->getCode()){
				case 1:
					$form["email"]->addError($e->getMessage());
					break;
				case 2:
					$form["password"]->addError($e->getMessage());
					break;
				default:
					$form->addError($e->getMessage());
					break;
			}
		}

	}


	/**
	 * Sign-up form.
	 * @return Form
	 */
	public function createComponentSignUpForm()
	{

		$form = new Form;
		$form->setTran($this->translateManager);

		$form->addEmail('email', '')
			->setRequired('Vyplňte e-mail.')
			->addRule($form::EMAIL, "Vyplňte platný e-mail");
        $form["email"]->getControlPrototype()->placeholder($this->translator->translate("Váš e-mail"));

		$form->addPassword('password', '')
			->setRequired('Vyplňte heslo.')
			->addRule($form::MIN_LENGTH, null, self::PASSWORD_MIN_LENGTH, "zadejte alespoň %d znaků")
			->addRule($form::MAX_LENGTH, null, self::PASSWORD_MAX_LENGTH, "zadejte nejvíce %d znaků");
        $form["password"]->getControlPrototype()->placeholder($this->translator->translate("Heslo (".self::PASSWORD_MIN_LENGTH." až ".self::PASSWORD_MAX_LENGTH." znaků)"));

		$form->addPassword('password2', '')
			->setRequired('Vyplňte heslo.')
			->addRule($form::EQUAL, 'Hesla se neshodují', $form['password']);
        $form["password2"]->getControlPrototype()->placeholder($this->translator->translate("Heslo znovu"));

		$form->addCheckbox('agree', 'Souhlasím s podmínkami')
			->setRequired('Musíte souhlasit s podmínkami.');

		$form->addSelect('language', "", $this->languages);

		$form->addSubmit('send', 'Registrovat')
			->getControlPrototype()->class("btn-block btn-sm");

		$form->setDefaults(['language'=>$this->tableData->languageId]);

		$form->onSuccess[] = [$this, 'signUpFormSucceeded'];

		return $form;

	}

	public function signUpFormSucceeded(\TH\Form $form, $values){
		try {
			$hash = Random::generate(30);
			$userValues = array(
				"email" => $values->email,
				"password" => md5($values->password),
				"language" => $values->language,
				"tableId" => $this->table,
				"hash" => $hash,
                "registered" => new \Nette\Utils\DateTime(),
				"active" => true,
			);
            if($this->table!=0){
                $userValues["role"] = 5;
                $users = $this->userManager->countByTable($this->table);
                if($users==0){
                    $userValues["role"] = 9;
                }
            }
			$this->userManager->add($userValues);

            /*
            $activateLink = $this->link("//Sign:activate", array("hash"=>$hash));


			//send mail
			$mail = new Message;
			$mail->setFrom($this->senderName.' <'.$this->senderEmail.'>')
				->addTo($values->email)
				->setSubject("Aktivace účtu")
				->setHtmlBody("Drahý uživateli,<br><br>prosíme potvrďte Váš e-mail kliknutím <a href='$activateLink'>sem</a>.<br><br>S pozdravem SmartTable.com");

			$mailer = new SendmailMailer;
			$mailer->send($mail);

			$this->flashMessage("Byli jste zaregistrováni. Prosíme potvrďte svou registraci v e-mailu.");
            */
            $this->flashMessage("Byli jste zaregistrováni. Nyní se můžete přihlásit.");
			$this->redirect('in');
		} catch (\App\Model\DuplicateNameException $e) {
			$form['email']->addError('E-mail je již použit.');
		}

	}


	/**
	 * Sign-up form.
	 * @return Form
	 */
	public function createComponentForgotForm()
	{

		$form = new Form;
		$form->setTran($this->translateManager);

		$form->addEmail('email', 'E-mail')
			->setRequired('Vyplňte e-mail.')
			->addRule($form::EMAIL, "Vyplňte platný e-mail");

		$form->addSubmit('submit', 'Obnovit heslo')
			->getControlPrototype()->class("btn-block btn-sm");

		$form->onSuccess[] = [$this, 'forgotFormSucceeded'];

		return $form;

	}

	public function forgotFormSucceeded(\TH\Form $form, $values){
		$account = $this->userManager->findByEmail($values->email);
		if($account && $account->role!=3){
			$hash = Random::generate(30);
			$userValues = array(
				"hash" => $hash,
			);
			$this->userManager->update($userValues, $account->id);

			$resetLink = $this->link("//Sign:resetPassword", array("hash"=>$hash));

			//send mail
			$mail = new Message;
			$mail->setFrom($this->senderName.' <'.$this->senderEmail.'>')
				->addTo($values->email)
				->setSubject("Obnova hesla")
				->setHtmlBody("Drahý uživateli,<br><br>nové heslo vygenerujte kliknutím <a href='$resetLink'>sem</a>.<br><br>S pozdravem SmartTable.com");

			$mailer = new SendmailMailer;
			$mailer->send($mail);

			$this->flashMessage("Odkaz pro obnovu hesla Vám byl zaslán do e-mailu.");
			$this->redirect('in');
        }elseif($account->role==3){
            $form['email']->addError('U servisního účtu není možné resetovat heslo.');
		}else{
			$form['email']->addError('E-mail není v databázi.');
		}

	}



}
