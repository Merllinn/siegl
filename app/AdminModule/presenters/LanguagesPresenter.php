<?php

namespace App\AdminModule\Presenters;
 
use 	TH\Form,
		Nette\Utils\Html;
use     Ublaboo\DataGrid\DataGrid;
use \Nette\Caching\Cache as cache;
		
class LanguagesPresenter extends BasePresenter
{
	public $texts;
	public $translates;
	private $id=null;
	
	public function startup(){
		parent::startup();
		//$this->languageManager->lang = $this->lang;
	}
	
	public function renderDefault(){

	}
	
	public function actionAdd(){
		$this->setView("addEdit");
	}
	
	public function actionEdit($id){
		$this->id = $id;
		$details = $this->languageManager->getOne($id);
		$this["languageForm"]["id"]->setDisabled();
		$this["languageForm"]->setDefaults($details);
		$this->setView("addEdit");
	}
	
	public function actionTranslates($id){
		$this->id = $id;
		$this->template->langTran = $this->languages[$this->id];
	}
	
	public function renderTranslates($id){
		$translates = $this->languageManager->getTranslates($this->id);
		$this["translatesForm"]->setDefaults(array("translates"=>$translates));
	}
	
	public function actionDelete($id){
		try{
			$this->languageManager->delete($id);
			$this->flashMessage("Jazyk byl smazán");
			$this->redirect(":Admin:Languages:default");
		}
		catch(DibiDriverException $e){
			$this->flashMessage($e->getMessage());
		}
	}
	
	public function handleActivate($id){
		try{
			$this->languageManager->update(array("active"=>1), $id);
			$this["languages"]->handleReset();
		}
		catch(DibiDriverException $e){
			$this->flashMessage($e->getMessage());
		}
	}
	
	public function handleDeactivate($id){
		try{
			$this->languageManager->update(array("active"=>0), $id);
			$this["languages"]->handleReset();
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
    public function createComponentLanguages($name)
    {
        //$this->texts = $this->languageManager->getTextsCount();
        //$this->translates = $this->languageManager->getTranslatesCount();
        $presenter = $this;
        
        $products = $this->languageManager->get();
        
        $grid = new DataGrid($this, $name);
        $grid->setDataSource($products);


        $grid->addColumnText('name', 'Název');
        $grid->addColumnText('id', 'Zkratka');
        
        $grid->addColumnText('active', 'Aktivní')
            ->setRenderer(function($row) use ($presenter) {
                if($row->active){
                	return Html::el("a")->class("tabella_ajax")->href($presenter->link("deactivate!", $row->id))->setHtml(html::el("img")->src(FOLDER."/images/active.png")->class("action"));
                }
                else{
                	return Html::el("a")->class("tabella_ajax")->href($presenter->link("activate!", $row->id))->setHtml(html::el("img")->src(FOLDER."/images/deactive.png")->class("action"));
                }
        });

        $grid->addColumnText('tools', 'Nástroje')
            ->setRenderer(function($row) use ($presenter) {
                $el = Html::el("span");
                $el->insert(0, html::el("a")->class("btn btn-mini btn-light")->href($presenter->link("edit", $row->id))->setHtml(html::el("i")->class("fas fa-edit"))->title("Upravit"));
                $el->insert(1, " ");
                $el->insert(2, html::el("a")->class("btn btn-mini btn-info")->href($presenter->link("translates", $row->id))->setHtml(html::el("i")->class("fas fa-globe"))->title("Překlady"));
                $el->insert(3, " ");
                $el->insert(4, html::el("a")->class("btn btn-mini btn-danger")->href($presenter->link("delete", $row->id))->setHtml(html::el("i")->class("fas fa-trash-alt"))->title("Smazat"));
                return $el;
        });

        $this->localiseGrid($grid);

        return $grid;
    }
	
	/** Create language form
	* 
	* @return Form
	*/
	public function createComponentLanguageForm(){
		$form = new Form();
		
		$form ->addText("id", "Zkratka")
				->addRule(Form::FILLED, "Vyplňte jméno jazyka");
		$form ->addText("name", "Jméno")
				->addRule(Form::FILLED, "Vyplňte jméno jazyka");
		$form->addSubmit("submit", "Uložit jazyk")->getControlPrototype()->class("btn btn-primary");
		
		$form->onSuccess[] = [$this, 'saveLanguage'];
		
		return $form;
	}
	

	/** callback for language form
	* 
	* @param Form data from page form
	* @return void
	*/
	public function saveLanguage(Form $form){
		$values = $form->getValues();
		
		if($form->isValid()){
			try{
				if(!empty($this->id)){
					$this->languageManager->update($values, $this->id);
				}
				else{
					$this->languageManager->add($values);

				}
				$this->flashMessage("Jazyk byl uložen.");
				$this->redirect(":Admin:Languages:default");	
				
				
			}
			catch(DibiDriverException $e){
				$this->flashMessage($e->getMessage(), "error");
			}
		}
	}
	
	
	/** Create language form
	* 
	* @return Form
	*/
	public function createComponentTranslatesForm(){
		$form = new Form();
		
		$texts = $this->languageManager->getTexts();
		
		$cont = $form->addContainer("translates");
		foreach($texts as $text){
			$cont ->addText($text->id, $text->key);
		}
		
		$form->addSubmit("submit", "Uložit texty")->getControlPrototype()->class("btn btn-primary");
		
		$form->onSuccess[] = [$this, 'saveTranslates'];
		
		return $form;
	}
	

	/** callback for language form
	* 
	* @param Form data from page form
	* @return void
	*/
	public function saveTranslates(Form $form){
		$values = $form->getValues();
		
		if($form->isValid()){
			try{
				foreach($values["translates"] as $key=>$tran){
					$trans = $this->translateManager->findTranslate($this->id, $key);
					if($tran<>""){
						$this->languageManager->replaceTranslates($this->id, $key, array("translate"=>$tran), $trans);
					}
				}
                $storage = new \Nette\Caching\Storages\FileStorage('temp/translates');
                $cache = new cache($storage);
                $cache->clean();
                $translate[$this->id] = $this->languageManager->getTranslatePairs($this->id);
                $cache->save("translates".$this->id, $translate[$this->id]);
				$this->flashMessage("Textové konstanty byly uloženy.");
				$this->redirect("this");	
				
				
			}
			catch(DibiDriverException $e){
				$this->flashMessage($e->getMessage(), "error");
			}
		}
	}
	
	
	
}
