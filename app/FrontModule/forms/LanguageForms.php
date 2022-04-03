<?php

namespace App\FrontModule\Presenters;

use TH\Form,
	TH\Translator;
use Nette;
use \Nette\Caching\Cache as cache;

class LanguageForms extends BasePresenter
{

    public function createComponentLanguageForm()
    {

        $form = new Form;
        $form->setTran($this->translateManager);
        $form->addText('id', 'Kód jazyka');
        $form->addText('name', 'Název');

        $form->addSubmit('send', 'Uložit')->getControlPrototype()->class("btn-block");

        $form->onSuccess[] = [$this, 'languageFormSucceeded'];

        return $form;

    }

    public function languageFormSucceeded(\TH\Form $form, $values){
        if($form->isValid()){
            if(!empty($this->id)){
                $this->languageManager->update($values, $this->id);
                $this->flashMessage("Jazyk byl uložen");
            }
            else{
                $this->languageManager->add($values);
                $this->flashMessage("Jazyk byl přidán");
            }

            $this->redirect('default');
        }

    }

	public function createComponentTranslateForm()
	{

		$form = new Form;
		$form->setTran($this->translateManager);
        $form->addText('id', 'kód');
        $form->addUpload('file', 'Soubor s překlady')
            ->getControlPrototype()->class("form-control-file");

		$form->addSubmit('send', 'Nahrát')->getControlPrototype()->class("btn-block");

		$form->onSuccess[] = [$this, 'translateFormSucceeded'];

		return $form;

	}

	public function translateFormSucceeded(\TH\Form $form, $values){
		if($form->isValid()){
            $good = 0;
            $bad = 0;
            if($content = $values->file->contents){
                $rows = explode("\n", $content);
                unset($rows[0]);
                foreach($rows as $nr=>$row){
                    if(!empty($row)){
                        $fields = explode("~", $row);
                        if(isset($fields[2])){
                            if(!empty($fields[2])){
                                $id = $fields[0];
                                $exists = $this->translateManager->findTranslate($values->id, $id);
                                $data = array("translate"=>$fields[2]);
                                if(empty($exists->id)){
                                    $data["language_id"] = $values->id;
                                    $data["trans_id"] = $id;
                                    $this->translateManager->addTranslate($data);
                                }
                                else{
                                    $this->translateManager->updateTranslate($exists->id, $data);
                                }
                                $good++;
                            }
                        }
                        else{
                            $bad++;
                        }
                    }


                }
                $data = array("lastUpdate" => new \nette\utils\DateTime());
                $this->languageManager->update($data, $values->id);
                $badMessage = "";
                if($bad>0){
                    $badMessage = "<br>Chybných řádků: $bad";
                }
                $this->flashMessage("Nahráno ".$good." položek překladů z ".(count($rows)-1).$badMessage);

                $storage = new \Nette\Caching\Storages\FileStorage(BASE_DIR.'/../temp/translates');
                $cache = new cache($storage);
                $cache->clean();
                $translate[$values->id] = $this->translateManager->getTranslates($values->id);
                $cache->save("translates".$values->id, $translate[$values->id]);

                $this->redirect("default");
            }
		}

	}


}
