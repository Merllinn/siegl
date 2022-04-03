<?php

namespace App\AdminModule\Presenters;

use 	TH\Form,
		Nette\Utils\Html;
use     Ublaboo\DataGrid\DataGrid;

class ProductsPresenter extends BasePresenter
{
	public $edited = false;
	public $productId = false;
	public $attrId = false;
	public $pa = false;
	public $fullCats;
    public $categories;

	 /** @persistent */
	public $search = "";

	public $filter;

	public function startup(){
		parent::startup();
		$this->addBreadcrumbs("Produkty", $this->link(":Admin:Products:default"));

		if(!isset($this->filter)){
			$this->filter = $this->getSession("productsFilter");
		}

        $this->categories = $this->categoryManager->getActiveList();

	}

	public function actionDefault($search="", array $items = null){
		$this->search = $search;
	}
	public function renderDefault($search="", array $items = null){
		if(!empty($items)){
			foreach($items as $index=>$item){
				$this->productManager->update(array("order"=>$index+1), $item);
			}
			$this->flashMessage("Pořadí produktů bylo uloženo");
			$this->redirect(":Admin:Products:default", array("search"=>$search));
		}

	}



	public function renderAttributes($id){
		$this->productId = $id;
		$this->template->product = $pageDetails = $this->productManager->getOne($id);

	}

	public function actionEditAttributeValues($id, $attrId, $pa){
		$this->productId = $id;
		$this->attrId = $attrId;
		$this->pa = $pa;
		$this->template->product = $pageDetails = $this->productManager->getOne($id);
		$this->template->attribute = $attibute = $this->productManager->getProductAttribute($id, $attrId);
		$this->template->values = $this->models->attributes->fetchValues($attrId, $pa);
	}

	public function actionAdd(){
		$this->setView("addEdit");
	}

	public function actionEdit($id){
		$pageDetails = $this->productManager->find($id);
		$this["productForm"]->setDefaults($pageDetails);
		$this->edited = $id;
		$this->setView("addEdit");
	}

	public function actionDelete($id){
		try{
			$this->productManager->delete($id);
			$this->flashMessage("Produkt byl smazán");
			$this->redirect(":Admin:Products:default");
		}
		catch(DibiDriverException $e){
			$this->flashMessage($e->getMessage());
		}
	}

	/** Create product form
	*
	* @return Form
	*/
	public function createComponentProductForm(){
		$form = new Form();

		$form->addGroup("Základní údaje");
		//$form ->addHidden("id");
		$form ->addText("name", "Jméno")
				->addRule(Form::FILLED, "Vyplňte jméno produktu");
        $form ->addSelect("category", "Kategorie", $this->categories)
				//->setRequired(true)
				->setPrompt("bez kategorie")
				->getControlPrototype()->placeholder("vyberte");
        /*
		$form ->addText("order", "Pořadí")
				->addRule(Form::FILLED, "Vyplňte pořadí");
		*/
        $form ->addText("price_vat", "Cena s DPH")
                ->addRule(Form::FILLED, "Vyplňte cenu produktu");
		$form ->addTextArea("perex", "Zkrácený popis");
		$form ->addTextArea("description", "Popis")
				->getControlPrototype()
					->class("wysiwyg");
        /*
        $form ->addTextArea("technical", "Technická specifikace")
				->getControlPrototype()
					->class("wysiwyg");
		$form->addGroup("SEO");
		$form ->addText("alias", "Alias");
		$form ->addTextArea("seo_description", "SEO description");
		$form ->addText("seo_keywords", "SEO keywords");
        */
		$form->addSubmit("submit", "Uložit produkt")->getControlPrototype()->class("btn btn-primary");

		if(!empty($this->filter->category)){
			$form->setDefaults(array("category"=>$this->filter->category));
		}

		$form->onSuccess[] = [$this, 'saveProduct'];

		return $form;
	}


	/** callback for page form
	*
	* @param Form data from page form
	* @return void
	*/
	public function saveProduct(Form $form){
		$values = $form->getValues();
		$values->alias = $this->makeAlias("products", $values->name, $this->edited);

		if($form->isValid()){
			try{
                $vat = $this->productManager->getVat(1);
                $values->price = $values->price_vat/(1+($vat->value/100));
				if($this->edited){
					$this->productManager->update($values, $this->edited);

				}
				else{

					if(empty($values->alias)){
						$values->alias = \Nette\Utils\Strings::webalize($values->name);
					}
					$values->type=1;
					$values->created = new \nette\utils\DateTime();
                    $values->vat_id = 1;
					$id = $this->productManager->add($values);

				}
				$this->flashMessage("Produkt byl uložen.");
				$this->redirect(":Admin:Products:default");


			}
			catch(DibiDriverException $e){
				$this->flashMessage($e->getMessage(), "error");
			}
		}
	}

	/** Create product form
	*
	* @return Form
	*/
	public function createComponentAttributeValuesForm(){
		$form = new Form();

		$values = $this->models->attributes->fetchValuesPairs($this->attrId);

		$form->addSubmit("submit", "Uložit hodnoty")->getControlPrototype()->class("btn btn-primary");

		$form->onSuccess[] = [$this, 'saveAttrValues'];

		return $form;
	}

	public function saveAttrValues(Form $form){
	   $httpRequest = $this->getService('httpRequest');
	   $checkboxes = $httpRequest->getPost("value");
	   $prices = $httpRequest->getPost("price");
	   $values = $this->productManager->getProductAttributeValues($this->pa);
	   foreach($checkboxes as $av=>$value){
		   if($value="on"){
			   if(empty($prices[$av]))
			   	$prices[$av] = null;
			   if(empty($values[$av])){
			   	//add
				   $data = array(
			   			"products_attribute_id"	=>$this->pa,
			   			"attribute_value_id"	=>$av,
			   			"price"					=>$prices[$av]
				   );
			   	$this->productManager->addAttributeValue($data);

			   }
			   else{
			   	//update
				   $data = array(
			   			"price"					=>$prices[$av]
				   );
			   	$this->productManager->updateAttributeValue($data, $values[$av]);
			   	unset($values[$av]);
			   }
		   }
	   }
	   //delete removed values
	   foreach($values as $value){
		   $this->productManager->deleteAttributeValue($value);
	   }
	   $this->flashMessage("Hodnoty parametrů byly aktualizovány", "success");
	   $this->redirect("this");
	}


    /**
     * Make table of products
     *
     * @return \Addons\Tabella
     */
    public function createComponentProducts($name)
    {
        $presenter = $this;

        $source = $this->productManager->fetchDS(1, $this->filter);

        //$this->fullCats = $this->models->categories->fetchFullInfo();

        $grid = new DataGrid($this, $name);
        if(!empty($this->filter->category)){
            $source->order("order");
        }
        $grid->setDataSource($source);


        $grid->addColumnText('img', 'Obrázek')
            ->setRenderer(function($row) use ($presenter) {
                $photo = $presenter->productManager->getMainPhoto($row->id);
                if($photo){
                        return html::el("img")->src($presenter->thumb($photo, 100, 100));
                    }
                    else{
                        return "";
                    }
        });
        $grid->addColumnText('name', 'Název');

        $grid->addColumnText('category', 'Kategorie')
            ->setRenderer(function($row) use ($presenter) {
                if(empty($row->category)){
                    return "";
                }
                else{
                    return $presenter->categories[$row->category];
                }
        });

        $grid->addColumnText('price_vat', 'Cena s DPH')
            ->setRenderer(function($row) use ($presenter) {
                return number_format($row->price_vat, 0, ",", " ")." Kč";
        });


        $grid->addColumnText('active', 'Aktivní')
            ->setRenderer(function($row) use ($presenter) {
                $el = Html::el("span");
                if($row->active){
                	$el->insert(0, Html::el("a")->class("tabella_ajax")->href($presenter->link("deactivate!", $row->id))->setHtml(html::el("img")->src(FOLDER."/images/active.png")->class("action")));
                	$el->insert(1, Html::el("a")->class("tabella_ajax")->style("display: none;")->href($presenter->link("activate!", $row->id))->setHtml(html::el("img")->src(FOLDER."/images/deactive.png")->class("action")));
                }
                else{
                	$el->insert(0, Html::el("a")->class("tabella_ajax")->style("display: none;")->href($presenter->link("deactivate!", $row->id))->setHtml(html::el("img")->src(FOLDER."/images/active.png")->class("action")));
                	$el->insert(1, Html::el("a")->class("tabella_ajax")->href($presenter->link("activate!", $row->id))->setHtml(html::el("img")->src(FOLDER."/images/deactive.png")->class("action")));
                }
                return $el;
        });

		if($this->user->identity->role==9){
        $grid->addColumnText('tools', 'Nástroje')
            ->setRenderer(function($row) use ($presenter) {
                $photos = $presenter->productManager->countPhotos($row->id);
                $el = Html::el("span");
                $el->insert(0, html::el("a")->class("btn btn-mini btn-primary")->href($presenter->link(":Admin:ProductGallery:default", $row->id))->setHtml(html::el("i")->class("fas fa-images")." ".$photos->count)->title(" Galerie"));
                $el->insert(1, " ");
                $el->insert(2, html::el("a")->class("btn btn-mini")->href($presenter->link(":Admin:Products:edit", $row->id))->setHtml(html::el("i")->class("fas fa-edit"))->title(" Upravit"));
                $el->insert(3, " ");
                $el->insert(4, html::el("a")->class("btn btn-mini btn-danger")->href($presenter->link(":Admin:Products:delete", $row->id))->setHtml(html::el("i")->class("fas fa-trash-alt"))->setHtml(html::el("i")->class("fas fa-trash-alt"))->title("Smazat"));
                return $el;
        });
		}

        $this->localiseGrid($grid);

        return $grid;

    }


	public function handleActivate($id){
		try{
			$this->productManager->update(array("active"=>1), $id);
			//$this->redirect("this");
		}
		catch(DibiDriverException $e){
			$this->flashMessage($e->getMessage());
		}
	}

	public function handleDeactivate($id){
		try{
			$this->productManager->update(array("active"=>0), $id);
            //$this->redirect("this");
		}
		catch(DibiDriverException $e){
			$this->flashMessage($e->getMessage());
		}
	}

	public function createComponentFilterForm(){
		$form = new Form();

		$form->getElementPrototype()->class("form-inline");

		$form->addSelect("category", "", $this->categories)
            ->setPrompt(" - všechny kategorie - ")
            ->getControlPrototype()->class("form-control");

		$form->addText("fulltext")
			->getControlPrototype()->class("form-control")->placeholder("hledaný výraz");

        $form->addSelect("active", "", array("1"=>"pouze aktivní","9"=>"pouze neaktivní"))
            ->setPrompt(" - aktivní i neaktivní - ")
            ->getControlPrototype()->class("form-control");

		//$form->addCheckbox("noPhoto", "bez fotky")->setDefaultValue(false);

		$form->addSubmit("submit", "Filtruj")->getControlPrototype()->class("btn btn-primary");
		$form->addSubmit("reset", "Zobraz vše")->getControlPrototype()->class("btn btn-light");

		$form->setDefaults($this->filter);

		$form->onSuccess[] = [$this, 'filter'];

		return $form;
	}

	/** callback for seo form
	*
	* @param Form data from page form
	* @return void
	*/
	public function filter(Form $form){
		$values = $form->getValues();

		if($form["reset"]->isSubmittedBy()){
			$this->filter->category = null;
            $this->filter->fulltext = null;
			$this->filter->active = null;
			//$this->filter->noPhoto = false;
			$this->redirect("this");
		}
		else{
			$this->filter->category = $values->category;
            $this->filter->fulltext = $values->fulltext;
			$this->filter->active = $values->active;
			//$this->filter->noPhoto = $values->noPhoto;
			$this->redirect("this");
		}
	}

    public function handleSort(array $items){
        foreach($items as $index=>$item){
            $this->productManager->update(array("order"=>$index+1), $item);
        }
        $this->flashMessage("Pořadí produktů v rámci kategorie bylo uloženo");
        $this->redirect("this");
    }



}
