<?php

namespace App\AdminModule\Presenters;

use 	TH\Form,
		Nette\Utils\Html,
		Nette\Utils\Image;
use     Ublaboo\DataGrid\DataGrid;

class AttributesPresenter extends BasePresenter
{
    private $attr;
	private $edited;
	private $types = [1=>"Vlastnost", 2=>"Tvoří cenu"];

	public function startup(){
		parent::startup();
		$this->addBreadcrumbs("Atributy", $this->link(":Admin:Attributes:default"));
	}

	public function actionDefault(){

	}

	public function actionAdd(){
		$this->addBreadcrumbs("Přidání atributu");
		$this->setView("addEdit");
	}

	public function actionEdit($id){
        $this->edited = $id;
		$details = $this->attributeManager->find($id);
		$this["attributeForm"]->setDefaults($details);
		$this->addBreadcrumbs("Úprava atributu");
		$this->setView("addEdit");
	}

	public function actionDelete($id){
		$this->attributeManager->delete($id);
		$this->redirect(":Admin:Attributes:default");
		/*
		try{
			if(){
				// check if category has no subcategories

				$this->flashMessage("Kategorie obsahuje jiné podkategorie, nejprve je smažte, nebo přesuňte");
			}
			elseif(){
				// check if category is not used


				$this->flashMessage("Kategorie obsahuje položky, nejprve je smažte, nebo přesuňte");
			}
			else{
				// if category can be deleted
				$this->categoryManager->delete($id);
				$this->flashMessage("Kategorie byla smazána");
			}
			$this->redirect(":Admin:Categories:default");
		}
		catch(DibiDriverException $e){
			$this->flashMessage($e->getMessage());
		}
		*/
	}

	public function actionValues($id){
        $this->attr = $id;
		$this->template->attribute = $this->attributeManager->find($id);
		$this->addBreadcrumbs($this->template->attribute->name, $this->link(":Admin:Attributes:values", $this->template->attribute->id));
	}

	public function actionAddValue($id){
        $this->attr = $id;
		$this->template->attribute = $this->attributeManager->find($id);
		$this->addBreadcrumbs($this->template->attribute->name, $this->link(":Admin:Attributes:values", $this->template->attribute->id));
		$this->addBreadcrumbs("Přidání hodnoty");
		$this->setView("addEditValue");
	}

	public function actionEditValue($id){
        $this->edited = $id;
		$details = $this->attributeManager->findValue($id);
		$this->attr = $details->attribute;
		$this->template->attribute = $this->attributeManager->find($details->attribute);
		$this["valueForm"]->setDefaults($details);
		$this->setView("addEditValue");
		$this->addBreadcrumbs($this->template->attribute->name, $this->link(":Admin:Attributes:values", $this->template->attribute->id));
		$this->addBreadcrumbs("Úprava hodnoty");
	}

	public function actionDeleteValue($id){
		$details = $this->attributeManager->findValue($id);
		$this->attributeManager->deleteValue($id);
		$this->redirect("values", $details->attribute);
	}



	public function createComponentAttributeForm(){
		$form = new Form();

		$form ->addText("name", "Jméno")
                ->setRequired(true)
				->addRule(Form::FILLED, "Vyplňte jméno atributu");
		$form->addSelect("type", "Typ", $this->types);
		$form ->addText("unit", "Jednotka");
		$form->addCheckbox("forCont", "Pro kontejnery");
		$form->addCheckbox("forMaterial", "Pro materiál");
		$form ->addTextArea("desc", "Popis")
				->getControlPrototype()
					->class("wysiwyg");
		/*
		$form ->addText("name_long", "Dlouhé jméno");
		$form ->addText("alias", "Alias");
		$form ->addTextArea("seo_description", "SEO description");
		$form ->addText("seo_keywords", "SEO keywords");
        */

		$form->addSubmit("submit", "Uložit atribut")->getControlPrototype()->class("btn btn-primary");

		$form->onSuccess[] = [$this, 'saveAttribute'];

		return $form;
	}

	public function saveAttribute(Form $form, $values){

		if($form->isValid()){
			try{
				if(empty($this->edited)){
					$this->attributeManager->add($values);
				}
				else{
					$this->attributeManager->update($values, $this->edited);
				}

				$this->flashMessage("Atribut byl uložen.");
				$this->redirect("default");


			}
			catch(DibiDriverException $e){
				$this->flashMessage($e->getMessage(), "error");
			}
		}
	}

	public function createComponentValueForm(){
		$form = new Form();

		$form ->addText("name", "Hodnota")
                ->setRequired(true)
				->addRule(Form::FILLED, "Vyplňte jméno atributu");
		$form ->addUpload("file", "Obrázek");
		$form ->addTextArea("desc", "Popis")
				->getControlPrototype()
					->class("wysiwyg");
		/*
		$form ->addText("name_long", "Dlouhé jméno");
		$form ->addText("alias", "Alias");
		$form ->addTextArea("seo_description", "SEO description");
		$form ->addText("seo_keywords", "SEO keywords");
        */

		$form->addSubmit("submit", "Uložit hodnotu")->getControlPrototype()->class("btn btn-primary");

		$form->onSuccess[] = [$this, 'saveValue'];

		return $form;
	}

	public function saveValue(Form $form, $values){

		if($form->isValid()){
			try{
				$img = $values->file;
				unset($values->file);
				if(empty($this->edited)){
					$values->attribute = $this->attr;
					$this->edited = $this->attributeManager->addValue($values);
				}
				else{
					$this->attributeManager->updateValue($values, $this->edited);
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

    				$this->attributeManager->updateValue($data, $this->edited);

	            }

				$this->flashMessage("Hodnota byla uložena.");
				$this->redirect("values", $this->attr);


			}
			catch(DibiDriverException $e){
				$this->flashMessage($e->getMessage(), "error");
			}
		}
	}

    public function createComponentAttributes($name)
    {
        $presenter = $this;

        $source = $this->attributeManager->get();
        $source->order("order");

        $grid = new DataGrid($this, $name);
        $grid->setDataSource($source);

        $grid->addColumnText('name', 'Název');
        
        $grid->addColumnText('unit', 'Jednotka')
            ->setTemplateEscaping(false);


        $grid->addColumnText('type', 'Typ')
            ->setRenderer(function($row) use ($presenter) {
            	return $presenter->types[$row->type];
        });

        $grid->addColumnText('forCont', 'Pro kontejnery')
            ->setRenderer(function($row) use ($presenter) {
                if($row->forCont){
                	return "ano";
                }
                else{
                	return "ne";
                }
        });

        $grid->addColumnText('forMaterial', 'Pro materiál')
            ->setRenderer(function($row) use ($presenter) {
                if($row->forMaterial){
                	return "ano";
                }
                else{
                	return "ne";
                }
        });

		if($this->user->identity->role==9){
        $grid->addColumnText('tools', 'Nástroje')
            ->setRenderer(function($row) use ($presenter) {
                $values = $presenter->attributeManager->countValues($row->id);
                $el = Html::el("span");
                $el->insert(0, html::el("a")->class("btn btn-mini")->href($presenter->link("edit", $row->id))->setHtml(html::el("i")->class("fas fa-edit"))->title(" Upravit"));
                $el->insert(1, " ");
                $el->insert(2, html::el("a")->class("btn btn-mini btn-primary")->href($presenter->link("values", $row->id))->setHtml(html::el("i")->class("fas fa-th")." ".$values)->title(" Hodnoty"));
                $el->insert(3, " ");
                $el->insert(4, html::el("a")->class("btn btn-mini btn-danger")->href($presenter->link("delete", $row->id))->setHtml(html::el("i")->class("fas fa-trash-alt"))->title(" Smazat"));;
                return $el;
        });
		}

        $this->localiseGrid($grid);

        return $grid;
    }

    public function createComponentValues($name)
    {
        $presenter = $this;

        $source = $this->attributeManager->getValues($this->attr);
        $source->order("order");

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

        $grid->addColumnText('name', 'Hodnota');

		if($this->user->identity->role==9){
        $grid->addColumnText('tools', 'Nástroje')
            ->setRenderer(function($row) use ($presenter) {
                //$values = $presenter->attributeManager->countValues($row->id);
                $el = Html::el("span");
                $el->insert(0, html::el("a")->class("btn btn-mini")->href($presenter->link("editValue", $row->id))->setHtml(html::el("i")->class("fas fa-edit"))->title(" Upravit"));
                $el->insert(1, " ");
                $el->insert(4, html::el("a")->class("btn btn-mini btn-danger")->href($presenter->link("deleteValue", $row->id))->setHtml(html::el("i")->class("fas fa-trash-alt"))->title(" Smazat"));;
                return $el;
        });
		}

        $this->localiseGrid($grid);

        return $grid;
    }

    public function handleSort(array $items){
        foreach($items as $index=>$item){
            $this->attributeManager->update(array("order"=>$index+1), $item);
        }
        $this->flashMessage("Pořadí atributů bylo uloženo");
    }

    public function handleSortValues(array $items){
        foreach($items as $index=>$item){
            $this->attributeManager->updateValue(array("order"=>$index+1), $item);
        }
        $this->flashMessage("Pořadí hodnot atributu bylo uloženo");
    }



}
