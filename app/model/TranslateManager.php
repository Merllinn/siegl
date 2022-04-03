<?php

namespace App\Model;

use Nette;


/**
 * Users management.
 */
final class TranslateManager
{
	use Nette\SmartObject;

	const
		LANG_TABLE = 'language',
		TRAN_TABLE = 'tran',
		TRANSLATES_TABLE = 'translate';


	/** @var Nette\Database\Context */
	private $database;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}


	public function getLanguages()
	{
			return $this->database->table(self::LANG_TABLE)
				->fetchPairs("id", "name");
	}

    public function getTranslate()
    {
        return $this->database->table(self::TRANSLATES_TABLE);
    }


	public function getTranslates($lang)
	{
			return $this->database->query("select t.key, coalesce(translate, text) as translateText from ".self::TRAN_TABLE." as t left join ".self::TRANSLATES_TABLE." tr on t.id=tr.trans_id where tr.language_id = ?", $lang)
				->fetchPairs("key", "translateText");
	}


    public function getOneByKey($key)
    {
        return $this->database->table(self::TRAN_TABLE)
            ->where("key = ?", $key)
            ->limit(1)
            ->fetch();

    }

	public function findTranslate($langId, $tranId)
	{
		return $this->getTranslate()
            ->where("language_id = ?", $langId)
			->where("trans_id = ?", $tranId)
			->fetch();

	}

	public function addTran($data)
	{
		$this->database->table(self::TRAN_TABLE)->insert($data);
	}

    public function getDownloadTranslates($id, $what="*"){
        return $this->database->query("select $what from ".self::TRAN_TABLE." t left join ".self::TRANSLATES_TABLE." tr on t.id = tr.trans_id and tr.language_id = ?", $id);
    }

    public function addTranslate($data){
        $this->getTranslate()->insert($data);
    }

    public function updateTranslate($translateId, $data){
        $this->getTranslate()
            ->where("id = ?", $translateId)
            ->update($data);
    }



}



