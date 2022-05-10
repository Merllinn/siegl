<?php

namespace App\AdminModule\Presenters;

use 	TH\Form,
		Nette\Utils\Html,
		Nette\Utils\Image;
use     Ublaboo\DataGrid\DataGrid;

class CategoriesMPresenter extends BasePresenter
{
    private $parent;
	private $edited;
	private $type=2;

	public function startup(){
		parent::startup();
		$this->addBreadcrumbs("Kategorie", $this->link(":Admin:Categories:default"));
	}

	public function actionDefault($parent=null){
		$this->parent = $parent;
		if($parent<>null){
			$this->template->parent = $this->categoryManager->find($parent);
			$this->addBreadcrumbs("Podkategorie ".$this->template->parent->name, $this->link(":Admin:CategoriesM:default", array('parent', $parent)));
		}
		$this->template->parentId = $parent;

	}

	public function actionAdd($parent=null){
		$this->setView("addEdit");
		$this->parent = $parent;
	}

	public function actionEdit($id, $parent){
		$this->parent = $parent;
        $this->edited = $id;
		$pageDetails = $this->categoryManager->find($id);
		$this["categoryForm"]->setDefaults($pageDetails);
		$this->setView("addEdit");
	}

	public function actionDelete($id){
		$this->categoryManager->delete($id);
		$this->redirect(":Admin:CategoriesM:default");
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


	/** Create page form
	*
	* @return Form
	*/
	public function createComponentCategoryForm(){
		$form = new Form();

		$form ->addText("name", "Jméno")
                ->setRequired(true)
				->addRule(Form::FILLED, "Vyplňte jméno kategorie");
		//$form ->addUpload("img", "Obrázek");
		$form ->addText("link", "Adresa (pokud má odkazovat mimo web)");
		$form ->addText("alias", "Alias");
		$form ->addTextArea("description", "Popis")
				->getControlPrototype()
					->class("wysiwyg");
		/*
		$form ->addText("name_long", "Dlouhé jméno");
		$form ->addTextArea("seo_description", "SEO description");
		$form ->addText("seo_keywords", "SEO keywords");
        */

		$form->addSubmit("submit", "Uložit kategorii")->getControlPrototype()->class("btn btn-primary");

		$form->onSuccess[] = [$this, 'saveCategory'];

		return $form;
	}


	/** callback for page form
	*
	* @param Form data from page form
	* @return void
	*/
	public function saveCategory(Form $form, $values){

		if($form->isValid()){
			try{
				$values->parent = $this->parent;
				//$img = $values->img;
				//unset($values->img);

				if(!empty($this->edited)){
					$this->categoryManager->update($values, $this->edited);
					$categoryId = $this->edited;
				}
				else{
                    $values->alias = $this->makeAlias("category", $values->name, $this->edited);
					$values->type = $this->type;
					$categoryId = $this->categoryManager->add($values);

				}
				//upload image
				/*
	            if ($img->isOk()) {
            		$image = $img;
					$ext = pathinfo($img->getSanitizedName(), PATHINFO_EXTENSION);
					$name = $categoryId . "-" . \Nette\Utils\Strings::webalize($values->name);
					$fileName = $name.".".$ext;
					$tempFile = BASE_DIR."/data/temp/".$fileName;
					$resizedFile = BASE_DIR."/data/original/".$fileName;
					$image->move($tempFile);

    				//resize and move
    				$bigImage = Image::fromFile($tempFile);
    				$bigImage->resize(800, 600, Image::FIT);
    				$bigImage->save($resizedFile);

    				if(file_exists($tempFile))
    					unlink($tempFile);

    				//dave to DB
    				$data = array(
    					"file"=>$fileName,
    					"category_id"=>$categoryId
    				);

    				$this->categoryManager->savePhoto($data);

				//} else {
	                //$this->flashMessage('Obrázek se nezdařilo nahrát na server.', 'warning');
	            }
	            */

				$this->flashMessage("Kategorie byla uložena.");
				$this->redirect(":Admin:CategoriesM:default", $this->parent);


			}
			catch(DibiDriverException $e){
				$this->flashMessage($e->getMessage(), "error");
			}
		}
	}

    /**
     * Make table of categories
     *
     * @return \Addons\Tabella
     */
    public function createComponentCategories($name)
    {
        $presenter = $this;

        $source = $this->categoryManager->fetchDS($this->type, $this->parent);

        $grid = new DataGrid($this, $name);
        $grid->setDataSource($source);

        $grid->addColumnText('name', 'Název');
        $grid->addColumnText('alias', 'Alias');

        $grid->addColumnText('active', 'Aktivní')
            ->setRenderer(function($row) use ($presenter) {
                if($row->active){
					if($this->user->identity->role==9){
                    	return Html::el("a")->class("tabella_ajax")->href($presenter->link("deactivate!", $row->id))->setHtml(html::el("img")->src("/images/active.png")->class("action"));
					}
					else{
                    	return Html::el("img")->src("/images/active.png")->class("action");
					}
                }
                else{
					if($this->user->identity->role==9){
                    	return Html::el("a")->class("tabella_ajax")->href($presenter->link("activate!", $row->id))->setHtml(html::el("img")->src("/images/deactive.png")->class("action"));
					}
					else{
                    	return Html::el("img")->src("/images/active.png")->class("deactive");
					}
                }
        });

		if($this->user->identity->role==9){
        $grid->addColumnText('tools', 'Nástroje')
            ->setRenderer(function($row) use ($presenter) {
                $photos = $presenter->productManager->countPhotos($row->id);
                $el = Html::el("span");
                $el->insert(0, html::el("a")->class("btn btn-mini")->href($presenter->link("edit", $row->id))->setHtml(html::el("i")->class("fas fa-edit"))->title(" Upravit"));
                $el->insert(1, " ");
                $el->insert(2, html::el("a")->class("btn btn-mini btn-danger")->href($presenter->link("delete", $row->id))->setHtml(html::el("i")->class("fas fa-trash-alt"))->title(" Smazat"));;
                return $el;
        });
		}

        $this->localiseGrid($grid);

        return $grid;
    }

	public function handleActivate($id){
		try{
			$this->categoryManager->update(array("active"=>1), $id);
			$this->redirect("this");
		}
		catch(DibiDriverException $e){
			$this->flashMessage($e->getMessage());
		}
	}

	public function handleDeactivate($id){
		try{
			$this->categoryManager->update(array("active"=>0), $id);
            $this->redirect("this");
		}
		catch(DibiDriverException $e){
			$this->flashMessage($e->getMessage());
		}
	}

    public function handleSort(array $items){
        foreach($items as $index=>$item){
            $this->categoryManager->update(array("order"=>$index+1), $item);
        }
        $this->flashMessage("Pořadí kategorií bylo uloženo");
        //$this->redirect(":Admin:Pages:default");
    }



}
