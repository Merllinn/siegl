<?php

namespace App\AdminModule\Presenters;

use 	TH\Form,
		Nette\Utils\Html,
		Nette\Image;
use     Ublaboo\DataGrid\DataGrid;

class PagesPresenter extends BasePresenter
{
	public $type;
	private $id=null;
	private $parent = null;
	public $locations = array("0"=>"Hlavní menu", "1"=>"Patička");

	public function startup(){
		parent::startup();
		//$this->pageManager->lang = $this->lang;
		$this->addBreadcrumbs("Stránky", $this->link(":Admin:Pages:default"));
	}

	public function renderDefault($parent=null){
		if(!empty($parent)){
			$this->parent = $parent;
			$this->template->parent = $this->pageManager->find($parent);
		}

	}

	public function actionAdd($id, $parent=""){
		$this->addBreadcrumbs("Přidání stránky", $this->link(":Admin:Pages:add"));
		$this->type = $id;
		$this->parent = $parent;
		$this->setView("addEdit");
	}

	public function actionEdit($id, $type){
		$this->id = $id;
		$pageDetails = $this->template->edited = $this->pageManager->find($id);
		$this->type = $pageDetails->type;
		$this->parent = $pageDetails->parent;
		$this["pageForm"]->setDefaults($pageDetails);
		$this->setView("addEdit");
		$this->addBreadcrumbs("Úprava stránky ".$pageDetails->name, $this->link(":Admin:Pages:edit", [$id, $type]));
	}

    public function handleSetType($id, $type){
        $this->pageManager->update(array("type"=>$type), $id);
        $this->redirect(":Admin:Pages:edit", array($id, $type));
    }

	public function handleSort(array $items){
        foreach($items as $index=>$item){
            $this->pageManager->update(array("order"=>$index+1), $item);
        }
        $this->flashMessage("Pořadí stránek bylo uloženo");
        //$this->redirect(":Admin:Pages:default");
	}

	public function actionDelete($id){
		try{
			$p = $this->pageManager->find($id);
			$this->pageManager->delete($id);
			$this->flashMessage("Stránka byla smazána");
			$this->redirect(":Admin:Pages:default", array("parent"=>$p->parent));
		}
		catch(DibiDriverException $e){
			$this->flashMessage($e->getMessage());
		}
	}

	public function handleActivate($id){
		try{
			$this->pageManager->update(array("active"=>1), $id);
			$this->redirect("this");
		}
		catch(DibiDriverException $e){
			$this->flashMessage($e->getMessage());
		}
	}

	public function handleDeactivate($id){
		try{
			$this->pageManager->update(array("active"=>0), $id);
			$this->redirect("this");
		}
		catch(DibiDriverException $e){
			$this->flashMessage($e->getMessage());
		}
	}

	public function handleActivateS($id){
		try{
			$this->pageManager->update(array("inSubpages"=>1), $id);
			$this->redirect("this");
		}
		catch(DibiDriverException $e){
			$this->flashMessage($e->getMessage());
		}
	}

	public function handleDeactivateS($id){
		try{
			$this->pageManager->update(array("inSubpages"=>0), $id);
			$this->redirect("this");
		}
		catch(DibiDriverException $e){
			$this->flashMessage($e->getMessage());
		}
	}

    /**
     * Make table of pages
     *
     * @return \Addons\Tabella
     */
    public function createComponentPages($name)
    {
        $presenter = $this;

        $source = $this->pageManager->getByParent($this->parent);
        $source->order("location");
        $source->order("order");

        $grid = new DataGrid($this, $name);
        $grid->setDataSource($source);
        $grid->setPagination(FALSE);
        //$grid->setSortable(true);

        $grid->addColumnText('img', 'Obrázek')
            ->setRenderer(function($row) use ($presenter) {
                $photo = $presenter->pageManager->getMainPhoto($row->id);
                if($photo){
                        return html::el("img")->src($presenter->thumb($photo, 100, 100))->width("100");
                    }
                    else{
                        return "";
                    }
        });
        $grid->addColumnText('name', 'Název')
            ->setRenderer(function($row) use ($presenter) {
                $el = Html::el("span");
                $el->insert(0, $row->name);
                $el->insert(1, "&nbsp;");
                $el->insert(2, html::el("a")->href($presenter->link(":Front:Homepage:page", $row->alias))->target("_blank")->setHtml(html::el("i")->class("fas fa-external-link-alt"))->title("Zobrazit"));
                return $el;
        });
        $grid->addColumnText('alias', 'Alias');


        if(count($this->locations)>1){
            $grid->addColumnText("Umístění", "location")
                ->setRenderer(function($row) use ($presenter) {
                    $locations = $presenter->locations;
                    return $locations[$row->location];
            });
        }

        $grid->addColumnText('active', 'V menu')
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

        $grid->addColumnText('inSubpages', 'V rozcestníku')
            ->setRenderer(function($row) use ($presenter) {
                if($row->inSubpages){
					if($this->user->identity->role==9){
                    	return Html::el("a")->class("")->href($presenter->link("deactivateS!", $row->id))->setHtml(html::el("img")->src(FOLDER."/images/active.png")->class("action"));
					}
					else{
                    	return Html::el("img")->src(FOLDER."/images/active.png")->class("action");
					}
                }
                else{
					if($this->user->identity->role==9){
                    	return Html::el("a")->class("")->href($presenter->link("activateS!", $row->id))->setHtml(html::el("img")->src(FOLDER."/images/deactive.png")->class("action"));
					}
					else{
                    	return Html::el("img")->src(FOLDER."/images/active.png")->class("deactive");
					}
                }
        });

		if($this->user->identity->role==9){
        $grid->addColumnText('tools', 'Nástroje')
            ->setRenderer(function($row) use ($presenter) {
                $photos = $presenter->pageManager->countPhotos($row->id);
                $subpages = $presenter->pageManager->countSubpages($row->id);
                $el = Html::el("span");
                $el->insert(0, html::el("a")->class("btn btn-mini btn-primary")->href($presenter->link(":Admin:PageGallery:default", $row->id))->setHtml(html::el("i")->class("fas fa-images")." ".$photos->count)->title(" Galerie"));
                $el->insert(1, " ");
                $el->insert(2, html::el("a")->class("btn btn-mini btn-info")->href($presenter->link(":Admin:Pages:default", $row->id))->setHtml(html::el("i")->class("fas fa-th")." ".$subpages)->title(" Podstránky"));
                $el->insert(3, " ");
                $el->insert(4, html::el("a")->class("btn btn-mini")->href($presenter->link(":Admin:Pages:edit", $row->id))->setHtml(html::el("i")->class("fas fa-edit"))->title(" Upravit"));
                $el->insert(5, " ");
                $el->insert(6, html::el("a")->class("btn btn-mini btn-danger")->href($presenter->link(":Admin:Pages:delete", $row->id))->setHtml(html::el("i")->class("fas fa-trash-alt"))->title(" Smazat"));;
                return $el;
        });
		}

        $this->localiseGrid($grid);

    }

	/** Create page form
	*
	* @return Form
	*/
	public function createComponentPageForm(){
		$form = new Form();

		$layouts = $this->commonManager->getTableArray("layout");
		//$parents = $this->pageManager->fetchTop();

		$form ->addHidden("type")->setValue($this->type);
		$form->addGroup("Základní údaje");
		$form ->addText("name", "Jméno")
				->addRule(Form::FILLED, "Vyplňte jméno stránky");
        if($this->parent==56){
		$form ->addDatePicker("date", "Datum")
				->addRule(Form::FILLED, "Vyplňte datum akce");
        }
		if($this->type==0){
			$form ->addSelect("layoutId", "Šablona", $layouts)
				->setDefaultValue(1);
		}
		if(count($this->locations)>1)
			$form ->addSelect("location", "Umístění", $this->locations);
		if($this->type==0){
			//$form ->addTextArea("perex", "Úvodní text")->getControlPrototype()->class("wysiwyg");
            $form ->addTextArea("content", "Obsah")->getControlPrototype()->class("wysiwyg");
            $form ->addTextArea("content_continuation", "Pokračování obsahu")->getControlPrototype()->class("wysiwyg");
			$form ->addText("name_menu", "Název v menu");
			$form->addGroup("SEO (pro vyhledávače)");
			$form ->addText("alias", "Alias (do adresy)");
			$form ->addText("hover", "Titulek při najetí");
			$form ->addText("title", "Titulek stránky");
			$form ->addText("seo_keywords", "SEO klíčová slova");
			$form ->addTextArea("seo_description", "SEO popis");
		}
		else{
			$form ->addText("link", "Adresa odkazu")
				->addRule(Form::FILLED, "Vyplňte adresu");
		}
		//$form ->addCheckbox("outside", "Otevřít do nového okna");
		$form->addSubmit("submit", "Uložit stránku")->getControlPrototype()->class("btn btn-primary");

		$form->onSuccess[] = [$this, 'savePage'];

		return $form;
	}

/*
	function decode_entities_full($string, $quotes = ENT_COMPAT, $charset = 'UTF-8') {
  return html_entity_decode(preg_replace_callback('/&([a-zA-Z][a-zA-Z0-9]+);/', 'convert_entity', $string), $quotes, $charset);
}
*/



	/** callback for page form
	*
	* @param Form data from page form
	* @return void
	*/
	public function savePage(Form $form, $values){

		if($form->isValid()){
			try{
				if(!empty($this->parent)){
					$values->parent = $this->parent;
				}
				if(isset($values->img)){
					$img = $values->img;
					unset($values->img);
				}
				if(empty($values->alias) && !empty($values->layoutId) && $values->layoutId<>'7')
					$values->alias = $this->makeAlias("pages", $values->name, $this->id);
				if(empty($values->name_menu))
					$values->name_menu = $values->name;
				if(empty($values->title))
					$values->title = $values->name;
				if(!empty($values->content) && empty($values->seo_description)){
					$values->seo_description = trim(preg_replace('/\s+/', ' ',\Nette\Utils\Strings::truncate(strip_tags($values->content), 160)));
				}
				if(!empty($this->id)){
					$this->pageManager->update($values, $this->id);
				}
				else{
                    $count = $this->pageManager->countPages();
                    $values->order = $count+1;
                    $this->id = $this->pageManager->add($values);

				}

				$this->flashMessage("Stránka byla uložena.");
				$this->redirect(":Admin:Pages:default", array("parent"=>!empty($this->parent)?$this->parent:''));


			}
			catch(DibiDriverException $e){
				$this->flashMessage($e->getMessage(), "error");
			}
		}
	}



}
