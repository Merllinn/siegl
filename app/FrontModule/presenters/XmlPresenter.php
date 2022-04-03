<?php

namespace App\FrontModule\Presenters;

use 	TH\Form,
        TH\FeedParser,
		Nette\Utils\Html,
		Nette\Templating\FileTemplate,
		Nette\Latte\Engine;

class XmlPresenter extends BasePresenter
{
	public $savedItems = array();
    
    public function startup(){
		parent::startup();
	}

    public function actionImportFeed(){
        set_time_limit(1000);
        $feed = "https://www.polagraph.cz/wp-content/xml/heureka_1.xml";
        $feedLocal = BASE_DIR."/feed.xml";
        $f = fopen($feedLocal, "w");
        fputs($f, file_get_contents($feed));

        $this->savedItems = $this->productManager->getArray();

        $parser = new FeedParser($feedLocal);
        $parser->model = $this->productManager;
        $parser->presenter = $this;

        if ($parser->parse()) {
            echo("Produkty byly naimportovány");
        } else {
            echo("Import se nezdařil");
        }

        foreach($this->savedItems as $savedItem){
            $this->productManager->delete($savedItem);
        }

        $this->terminate();
        $this->redirect("default");
    }


}
