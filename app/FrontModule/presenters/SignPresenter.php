<?php

namespace App\FrontModule\Presenters;

use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;

final class SignPresenter extends SignForms
{
	/** @persistent */
	public $backlink = '';

	public function actionOut()
	{
		$this->getUser()->logout();
		$this->flashMessage("Byli jste odhlášení.");
	}

    public function actionActivate($hash)
    {
        if($this->userManager->activate($hash)){
            $this->flashMessage("Gratulujeme! Registrace byla úspěšná. Nyní se můžete přihlásit.");
        }
        else{
            $this->flashMessageError("Litujeme, ale odkaz není platný.");
        }
        $this->redirect("in");
    }

	public function actionActivateHard($email)
	{
        $account = $this->userManager->findByEmail($email);
        if($this->userManager->activate($account->hash)){
			$this->flashMessage("Gratulujeme! Registrace byla úspěšná. Nyní se můžete přihlásit.");
		}
		else{
			$this->flashMessageError("Litujeme, ale odkaz není platný.");
		}
		$this->redirect("in");
	}

	public function actionResetPassword($hash)
	{
		$newPass = $this->userManager->resetPassword($hash);
		if($newPass){
			$mail = new Message;
			$mail->setFrom($this->senderName.' <'.$this->senderEmail.'>')
				->addTo($newPass["email"])
				->setSubject("Obnova hesla")
				->setHtmlBody("Drahý uživateli,<br><br>vaše nové heslo je:<br>".$newPass["pass"]."<br><br>S pozdravem SmartTable.com");

			$mailer = new SendmailMailer;
			$mailer->send($mail);
			$this->flashMessage("Nové heslo bylo zasláno na Váš e-mail");
		}
		else{
			$this->flashMessageError("Litujeme, ale odkaz není platný.");
		}
		$this->redirect("in");
	}


}
