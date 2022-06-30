<?php

namespace App\AdminModule\Presenters;

use 	TH\Form,
		Nette\Utils\Html;
use     Ublaboo\DataGrid\DataGrid;

class ZonesPresenter extends BasePresenter
{
	public $editedId = false;



	public function startup(){
		parent::startup();
		$this->addBreadcrumbs("Zóny", $this->link(":Admin:Zones:default"));

	}

	public function renderDefault(){
	}

	public function actionAdd(){
		$this->addBreadcrumbs("Přidání zóny");
	}

	public function actionEdit($id){
		$this->editedId = $id;
		$details = $this->commonManager->findZone($id);
		$this["zoneForm"]->setDefaults($details);
		$this->addBreadcrumbs("Editace zóny ".$details->name);
	}

	public function handleDelete($id){
		$this->commonManager->deleteZone($id);
		$this->redirect("this");
	}

	public function handleDeactivate($id){
		$this->commonManager->updateZone(array("active"=>0), $id);
		$this->redirect("this");
	}

	public function handleActivate($id){
		$this->commonManager->updateZone(array("active"=>1), $id);
        $this->redirect("this");
	}

    /**
     * Make table of customers
     *
     * @return \Addons\Tabella
     */
    public function createComponentZones($name)
    {
        $presenter = $this;

        $source = $this->commonManager->getZones();

        $grid = new DataGrid($this, $name);
        $grid->setDataSource($source);

        $grid->addColumnText('name', 'Název');
        $grid->addColumnText('lead', 'Doba přistavení [h]');
        $grid->addColumnText('leadExternal', 'Doba přistavení externí [h]');
        $grid->addColumnText('deadline', 'Objednávky do');
        /*
        $grid->addColumnText('role', 'Role')
            ->setRenderer(function($row) use ($presenter) {
                    return $presenter->roles[$row->role];
        });
        */

        $grid->addColumnText('color', 'Barva')
            ->setRenderer(function($row) use ($presenter) {
            	if(!empty($row->color)){
					return html::el("span")->style("color: $row->color;")->setHtml($row->color);
            	}
            	else{
					return "";
            	}
        });

        $grid->addColumnText('orderTimes', 'Objednací časy kont.')
            ->setRenderer(function($row) use ($presenter) {
            	if(!empty($row->orderTimes)){
					return html::el("span")->setHtml(nl2br($row->orderTimes));
            	}
            	else{
					return "";
            	}
        });

        $grid->addColumnText('orderTimesM', 'Objednací časy mat.')
            ->setRenderer(function($row) use ($presenter) {
            	if(!empty($row->orderTimesM)){
					return html::el("span")->setHtml(nl2br($row->orderTimesM));
            	}
            	else{
					return "";
            	}
        });

        $grid->addColumnText('active', 'Aktivní')
            ->setRenderer(function($row) use ($presenter) {
                if($row->active){
					if($this->user->identity->role==9){
                    	return Html::el("a")->class("")->href($presenter->link("deactivate!", $row->id))->setHtml(html::el("img")->src(FOLDER."/images/active.png")->class("action"));
					}
					else{
                    	return Html::el("img")->src(FOLDER."/images/active.png")->class("action");
					}
                }
                else{
					if($this->user->identity->role==9){
                    	return Html::el("a")->class("")->href($presenter->link("activate!", $row->id))->setHtml(html::el("img")->src(FOLDER."/images/deactive.png")->class("action"));
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
                $el->insert(0, html::el("a")->class("btn btn-mini btn-light")->href($presenter->link(":Admin:Zones:edit", $row->id))->setHtml(html::el("i")->class("fas fa-edit"))->title("Upravit"));
                $el->insert(1, " ");
                $el->insert(4, html::el("a")->class("btn btn-mini btn-danger")->href($presenter->link("delete!", $row->id))->setHtml(html::el("i")->class("fas fa-trash-alt"))->title("Smazat"));
                return $el;
        });
		}

        $this->localiseGrid($grid);

        return $grid;
    }

    /* FORMS */

	public function createComponentZoneForm(){
		$form = new Form(null);

		$form ->addText("name", "Název")
				->addRule(Form::FILLED, "Vyplňte název");
		$form ->addText("color", "Barva (např. #FC14D2)");
		$form ->addText("lead", "Doba přistavení [h]");
		$form ->addText("leadExternal", "Doba přistavení externí [h]");
		$form ->addText("deadline", "Do kdy lze poslat objednávku [hh:mm]");
		$form ->addTextarea("points", "Body hranice (souřadnice na jeden řádek)")
				->addRule(Form::FILLED, "Vyplňte hranici");
		$form ->addTextarea("orderTimes", "Rezervační časy kontejnerů [hh:mm - hh:mm] (jeden na řádek)")
				->addRule(Form::FILLED, "Vyplňte časy");
		$form ->addTextarea("orderTimesM", "Rezervační časy materiálů [hh:mm - hh:mm] (jeden na řádek)")
				->addRule(Form::FILLED, "Vyplňte časy");
        /*
        $form->addSelect("role", "Role", $this->roles)
                ->setPrompt("vyberte roli")
				->addRule(Form::FILLED, "Vyberte roli")
                ->getControlPrototype()->class("form-control");
        $form->addSelect("branch", "Pobočka", $this->branchesSimple)
                ->setPrompt("bez přiřazení pobočky")
                ->getControlPrototype()->class("form-control");
		*/
		$form->addSubmit("submit", "Uložit zónu")
				->getControlPrototype()->class("btn btn-success");

		$form->onSuccess[] = [$this, 'saveZone'];

		return $form;
	}

	public function saveZone(Form $form){

		// security
		//$this->isAllowed(array(1,3));

		$values = $form->getValues();

		if($form->isValid()){
			try{
				$values->lead = str_replace(",", ".", $values->lead);
				if($this->editedId){
					//update existing
					$this->commonManager->updateZone($values, $this->editedId);
				}
				else{
					//add new
					$this->commonManager->addZone($values);
				}

				$this->flashMessage("Zóna byla uložen");
				$this->redirect(":Admin:Zones:default");
			}
			catch(DibiDriverException $e){
				$this->flashMessage($e->getMessage(), "error");
			}
		}
	}

}
