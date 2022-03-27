<?php

namespace App\Model;

use Nette;


/**
 * Users management.
 */
final class LanguageManager
{
	use Nette\SmartObject;

	const LANGUAGE_TABLE = 'language';
    const TRAN_TABLE = 'tran';
    const TRANSLATE_TABLE = 'translate';


	/** @var Nette\Database\Context */
	private $database;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}


    public function get()
    {
        return $this->database->table(self::LANGUAGE_TABLE);
    }

	public function add($values)
	{
		$this->get()->insert($values);
	}

	public function update($values, $id)
	{
		$this->get()
		->where("id", $id)
		->update($values);
	}

	public function find($id)
	{
		return $this->get()
			->where("id", $id)
			->fetch();
	}

	public function delete($id)
	{
		return $this->get()
			->where("id", $id)
			->delete();
	}
	
    public function getTranslate()
    {
        return $this->database->table(self::TRANSLATE_TABLE);
    }

	public function getTexts(){
		return $this->database->table(self::TRAN_TABLE);
	}
	
	public function replaceTranslates($lang, $key, $data, $translateId = null){
		//return dibi::query("replace into ".self::TRANSLATE_TABLE."", $data);
		if(!empty($translateId)){
			$this->database->table(self::TRANSLATE_TABLE)
				->where("id", $translateId->id)
				->update($data);
		}
		else{
			$data["language_id"] = $lang;
			$data["trans_id"] = $key;
			
			$this->database->table(self::TRANSLATE_TABLE)->insert($data);
		}
	}

	public function getTranslates($id){
			    return $this->getTranslate()
			    	->select("tran.id AS tranId, COALESCE(translate, text) AS translateText")
			    	->where("language_id", $id)
        			->fetchPairs("tranId", "translateText");
	}
	
	public function getTranslatePairs($id){
			    return $this->getTranslate()
			    	->select("tran.key AS tranKey, COALESCE(translate, text) AS translateText")
			    	->where("language_id", $id)
        			->fetchPairs("tranKey", "translateText");
	}

}



