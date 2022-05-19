<?php

namespace App\AdminModule\Presenters;

use 	TH\Form,
		Nette\Utils\Html;
use     Ublaboo\DataGrid\DataGrid;

class AdminsPresenter extends BasePresenter
{
	public $editedId = false;
    public $roles = array(9=>"Administrátor");



	public function startup(){
		parent::startup();
		$this->addBreadcrumbs("Administrátoři", $this->link(":Admin:Admins:default"));

	}

	public function renderDefault(){
	}

	public function actionAdd(){
		$this->addBreadcrumbs("Přidání admina");
	}

	public function actionEdit($id){
		$this->editedId = $id;
		$details = $this->userManager->find($id);
		$this["adminForm"]->setDefaults($details);
		$this->addBreadcrumbs("Editace admina ".$details->email);
	}

	public function actionPassword($id){
		$this->addBreadcrumbs("Změna hesla");
		$this->editedId = $id;
	}

	public function handleDelete($id){
		$this->userManager->delete($id);
		$this->redirect("this");
	}

	public function handleDeactivate($id){
		$this->userManager->update(array("active"=>0), $id);
		$this->redirect("this");
	}

	public function handleActivate($id){
		$this->userManager->update(array("active"=>1), $id);
        $this->redirect("this");
	}

    /**
     * Make table of customers
     *
     * @return \Addons\Tabella
     */
    public function createComponentUsers($name)
    {
        $presenter = $this;

        $source = $this->userManager->findByRole(5);

        $grid = new DataGrid($this, $name);
        $grid->setDataSource($source);

        $grid->addColumnText('login', 'Login');
        $grid->addColumnText('email', 'E-mail');
        /*
        $grid->addColumnText('role', 'Role')
            ->setRenderer(function($row) use ($presenter) {
                    return $presenter->roles[$row->role];
        });
        */

        $grid->addColumnText('active', 'Aktivní')
            ->setRenderer(function($row) use ($presenter) {
                if($row->active){
					if($this->user->identity->role==9){
                    	return Html::el("a")->href($presenter->link("deactivate!", $row->id))->setHtml(html::el("img")->src(FOLDER."/images/active.png")->class("action"));
					}
					else{
                    	return Html::el("img")->src(FOLDER."/images/active.png")->class("action");
					}
                }
                else{
					if($this->user->identity->role==9){
                    	return Html::el("a")->href($presenter->link("activate!", $row->id))->setHtml(html::el("img")->src(FOLDER."/images/deactive.png")->class("action"));
					}
					else{
                    	return Html::el("img")->src(FOLDER."/images/active.png")->class("deactive");
					}
                }
        });

		if($this->user->identity->role==9){
        $grid->addColumnText('tools', 'Nástroje')
            ->setRenderer(function($row) use ($presenter) {
                $el = Html::el("span");
                $el->insert(0, html::el("a")->class("btn btn-mini btn-light")->href($presenter->link(":Admin:Admins:edit", $row->id))->setHtml(html::el("i")->class("fas fa-edit"))->title("Upravit"));
                $el->insert(1, " ");
                $el->insert(2, html::el("a")->class("btn btn-mini btn-warning")->href($presenter->link(":Admin:Admins:password", $row->id))->setHtml(html::el("i")->class("fas fa-lock"))->title("Změnit heslo"));
                $el->insert(3, " ");
                $el->insert(4, html::el("a")->class("btn btn-mini btn-danger")->href($presenter->link("delete!", $row->id))->setHtml(html::el("i")->class("fas fa-trash-alt"))->title("Smazat"));
                return $el;
        });
		}

        $this->localiseGrid($grid);

        return $grid;
    }

    /* FORMS */

	public function createComponentAdminForm(){
		$form = new Form(null);

		$form ->addText("login", "Login")
				->addRule(Form::FILLED, "Vyplňte login");
		$form ->addText("email", "E-mail")
				->addRule(Form::FILLED, "Vyplňte e-mail");
        /*
        $form->addSelect("role", "Role", $this->roles)
                ->setPrompt("vyberte roli")
				->addRule(Form::FILLED, "Vyberte roli")
                ->getControlPrototype()->class("form-control");
        $form->addSelect("branch", "Pobočka", $this->branchesSimple)
                ->setPrompt("bez přiřazení pobočky")
                ->getControlPrototype()->class("form-control");
		*/
		$form->addSubmit("submit", "Uložit admina")
				->getControlPrototype()->class("btn btn-success");

		$form->onSuccess[] = [$this, 'saveAdmin'];

		return $form;
	}

	public function saveAdmin(Form $form){

		// security
		//$this->isAllowed(array(1,3));

		$values = $form->getValues();

		if($form->isValid()){
			try{

				if($this->editedId){
					//update existing
					$this->userManager->update($values, $this->editedId);
				}
				else{
					//add new
					$values->role=9;
					//$values->password = md5($values->password);
					$this->userManager->add($values);
				}

				$this->flashMessage("Admin byl uložen");
				$this->redirect(":Admin:Admins:default");
			}
			catch(DibiDriverException $e){
				$this->flashMessage($e->getMessage(), "error");
			}
		}
	}

	public function createComponentPasswordForm(){
		$form = new Form(null);

		$form ->addPassword("password", "Heslo")
				->addRule(Form::FILLED, "Vyplňte heslo")
				->addRule(Form::MIN_LENGTH, "Heslo musí mít alespoň 5 znaků", 5);
		$form ->addPassword("password2", "Zopakuj heslo")
				->addConditionOn($form['password'], Form::FILLED)
                    ->setRequired(true)
					->addRule(Form::EQUAL, "Zadaná hesla se musí shodovat", $form['password']);
		$form->addSubmit("submit", "Změnit heslo")
				->getControlPrototype()->class("btn btn-success");

		$form->onSuccess[] = [$this, 'savePassword'];

		return $form;
	}

	public function savePassword(Form $form){

		// security
		//$this->isAllowed(array(2,3));

		$user = $form->getValues();

		if($form->isValid()){
			try{

				unset($user["password2"]);
				$user["password"] = md5($user["password"]);

				//save user
				$userId = $this->userManager->update($user, $this->editedId);

				$this->flashMessage("Heslo bylo změněno");
				$this->redirect(":Admin:Admins:default");
			}
			catch(DibiDriverException $e){
				$this->flashMessage($e->getMessage(), "error");
			}
		}
	}

}
