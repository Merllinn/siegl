<?php

namespace TH;

use Nette;
use \Nette\Caching\Cache as cache;

class Translator implements \Nette\Localization\ITranslator
{

	private $lang; 		// Aktualni jazyk
	private $translate; // Translates

	/** @var Model\TranslateManager */
	private $manager;

	public function __construct(\App\Model\TranslateManager $manager, $lang) {
		$this->manager = $manager;
        $this->lang = $lang;
		$storage = new \Nette\Caching\Storages\FileStorage(BASE_DIR.'/temp/translates');
		$cache = new cache($storage);
		if(!$this->translate[$this->lang] = $cache->load("translates".$this->lang)){
			$this->translate[$this->lang] = $this->manager->getTranslates($this->lang);
        	$cache->save("translates".$this->lang, $this->translate[$this->lang]);
		}
    }

    public function translate($message, $count = NULL)
    {

        $lang = $this->lang;
        $storage = new \Nette\Caching\Storages\FileStorage(BASE_DIR.'/temp/translates');
        $cache = new cache($storage);

        if(!isset($this->translate[$lang][$message])){
			if($lang=="cz"){
		        $exists = $this->manager->getOneByKey($message);
		        if(!$exists){
					$tranData = array(
						"key"=>$message,
						"text"=>$message,
						"address"=>"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']
					);
					$this->manager->addTran($tranData);

					$this->translate[$this->lang] = $this->manager->getTranslates($this->lang);
	                $cache->save("translates".$lang, $this->translate[$lang]);

					return  $message;
		        }
		        else{
					return  $message;
		        }
			}
			else{
				return  $message;
			}
        }
        elseif(empty($this->translate[$lang][$message])){
			return $message;
        }
        else{
			return $this->translate[$lang][$message];
        }
    }
}