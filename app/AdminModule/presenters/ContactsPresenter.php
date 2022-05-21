<?php

namespace App\AdminModule\Presenters;

use 	TH\Form,
		Nette\Utils\Html,
		Nette\Utils\Image;
use     Ublaboo\DataGrid\DataGrid;

class ContactsPresenter extends BasePresenter
{
	public $editedId = false;



	public function startup(){
		parent::startup();
		$this->addBreadcrumbs("Osoby", $this->link(":Admin:Contacts:default"));

	}

	public function renderDefault(){
	}

	public function actionAdd(){
		$this->addBreadcrumbs("Přidání osoby");
		$this->setView("addEdit");
		$this["adminForm"]["imgDel"]->getControlPrototype()->style("display", "none");
		$this["adminForm"]["imgDel"]->getLabelPrototype()->style("display", "none");
		$this["adminForm"]["imgShow"]->getControlPrototype()->style("display", "none");
		$this["adminForm"]["imgShow"]->getLabelPrototype()->style("display", "none");
	}

	public function actionEdit($id){
		$this->editedId = $id;
		$details = $this->userManager->findPerson($id);
		if(!empty($details->file)){
			//$details["fileShow"] = $details->file;
			$this["adminForm"]["imgShow"]->getControlPrototype()->height("200px")->src("/data/original/".$details->file)->onClick("return false;");
			$this["adminForm"]["file"]->getControlPrototype()->style("display", "none");
			$this["adminForm"]["file"]->getLabelPrototype()->style("display", "none");			
		}
		else{
			$this["adminForm"]["imgDel"]->getControlPrototype()->style("display", "none");
			$this["adminForm"]["imgDel"]->getLabelPrototype()->style("display", "none");
			$this["adminForm"]["imgShow"]->getControlPrototype()->style("display", "none");
			$this["adminForm"]["imgShow"]->getLabelPrototype()->style("display", "none");
			
		}
		$this["adminForm"]->setDefaults($details);
		$this->addBreadcrumbs("Editace osoby ".$details->name);
		$this->setView("addEdit");
	}

	public function handleDelete($id){
		$this->userManager->deletePerson($id);
		$this->redirect("this");
	}

	public function handleDeactivate($id){
		$this->userManager->updatePerson(array("active"=>0), $id);
		$this->redirect("this");
	}

	public function handleActivate($id){
		$this->userManager->updatePerson(array("active"=>1), $id);
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

        $source = $this->userManager->getPersons()->order("order");

        $grid = new DataGrid($this, $name);
        $grid->setDataSource($source);

        $grid->addColumnText('img', 'Obrázek')
            ->setRenderer(function($row) use ($presenter) {
                if(!empty($row->file)){
                        return html::el("img")->src($presenter->thumb($row, 100, 100))->width("100");
                    }
                    else{
                        return "";
                    }
        });

        $grid->addColumnText('name', 'Jméno');
        $grid->addColumnText('role', 'Pozice')
            ->setRenderer(function($row) use ($presenter) {
                    return $presenter->contactCategories[$row->role];
        });

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
                $el->insert(0, html::el("a")->class("btn btn-mini btn-light")->href($presenter->link("edit", $row->id))->setHtml(html::el("i")->class("fas fa-edit"))->title("Upravit"));
                $el->insert(1, " ");
                $el->insert(2, html::el("a")->class("btn btn-mini btn-danger")->href($presenter->link("delete!", $row->id))->setHtml(html::el("i")->class("fas fa-trash-alt"))->title("Smazat"));
                return $el;
        });
		}

        $this->localiseGrid($grid);

        return $grid;
    }

    /* FORMS */

	public function createComponentAdminForm(){
		$form = new Form(null);

		$form ->addText("name", "Jméno")
				->addRule(Form::FILLED, "Vyplňte jméno");
        $form->addSelect("role", "Pozice", $this->contactCategories)
                ->setPrompt("vyberte roli")
				->addRule(Form::FILLED, "Vyberte pozici")
                ->getControlPrototype()->class("form-control");
		$form ->addUpload("file", "Obrázek");
		$form->addImage("imgShow", "");
		$form->addCheckbox("imgDel", "Smazat obrázek");
		$form->addSubmit("submit", "Uložit osobu")
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
				$img = $values->file;
				unset($values->file);
				$imgDel = $values->imgDel;
				unset($values->imgDel);
				if($this->editedId){
					//update existing
					$this->userManager->updatePerson($values, $this->editedId);
				}
				else{
					//add new
					//$values->password = md5($values->password);
					$this->editedId = $this->userManager->addPerson($values);
				}

				//upload image
	            if ($img->isOk()) {
            		$image = $img;
					$ext = pathinfo($img->getSanitizedName(), PATHINFO_EXTENSION);
					$name = $this->generateString(15);
					$fileName = $name.".".$ext;
					$tempFile = BASE_DIR."/data/temp/".$fileName;
					$resizedFile = BASE_DIR."/data/original/".$fileName;
					$image->move($tempFile);

    				//resize and move
    				$bigImage = Image::fromFile($tempFile);
    				//$bigImage->resize(800, 600, Image::FIT);
    				$bigImage->save($resizedFile);

    				if(file_exists($tempFile))
    					unlink($tempFile);

    				//save to DB
    				$data = array(
    					"file"=>$fileName,
    				);

    				$this->userManager->updatePerson($data, $this->editedId);

	            }
				elseif($imgDel==true){
    				$data = array(
    					"file"=>"",
    				);
    				$this->userManager->updatePerson($data, $this->editedId);
				}	            

				$this->flashMessage("Osoba byla uložena");
				$this->redirect("default");
			}
			catch(DibiDriverException $e){
				$this->flashMessage($e->getMessage(), "error");
			}
		}
	}
	
    public function handleSort(array $items){
        foreach($items as $index=>$item){
            $this->userManager->updatePerson(array("order"=>$index+1), $item);
        }
        $this->flashMessage("Pořadí osob bylo uloženo");
        //$this->redirect(":Admin:Pages:default");
    }
	

}
