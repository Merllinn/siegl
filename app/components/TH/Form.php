<?php

namespace TH;

use Nette\Utils\Html;
use Nette\Forms\Container;
use Nette\Forms\Controls;

class Form extends \Nette\Application\UI\Form
{

	public $lang;

    function __construct($lang="cz", $parent = NULL, $name = NULL)
    {
        parent::__construct($parent, $name);
		$this->lang = $lang;
        //$this->addProtection();

		$renderer = $this->getRenderer();
		$renderer->wrappers['controls']['container'] = NULL;
		$renderer->wrappers['pair']['container'] = 'div class=form-group';
		$renderer->wrappers['pair']['.error'] = 'has-error';
		//$renderer->wrappers['control']['container'] = 'div class=col';
		//$renderer->wrappers['label']['container'] = 'div class="col control-label"';
		$renderer->wrappers['control']['description'] = 'span class=help-block';
		$renderer->wrappers['control']['errorcontainer'] = 'span class="help-block error"';

        Container::extensionMethod('addDatePicker', function (Container $container, $name, $label = NULL) {
            return $container[$name] = new \JanTvrdik\Components\DatePicker($label);
        });
	}
	public function setTran(\App\Model\TranslateManager $tranManager){
    	$this->setTranslator(new Translator($tranManager, $this->lang));

	}

	public function render(...$args){
		$this->getElementPrototype()->class('form-horizontal');
		foreach ($this->getControls() as $control) {
			if ($control instanceof Controls\Button) {
				$control->getControlPrototype()->addClass(empty($usedPrimary) ? 'btn btn-primary' : 'btn btn-default');
				$usedPrimary = TRUE;
			} elseif ($control instanceof Controls\TextBase || $control instanceof Controls\SelectBox || $control instanceof Controls\MultiSelectBox) {
				$control->getControlPrototype()->addClass('form-control');
			} elseif ($control instanceof Controls\Checkbox || $control instanceof Controls\CheckboxList || $control instanceof Controls\RadioList) {
				$control->getSeparatorPrototype()->setName('div')->addClass($control->getControlPrototype()->type);
			}
		}
		parent::render(...$args);
	}

    // test if field is filled
    public function isFilled($field, $errMsg){
		if($field=="region_id"){
			$value = isset($_REQUEST["region_id"])?$_REQUEST["region_id"]:"";
		}
		else{
			$value = $this->values->$field;
		}
		if(empty($value)){
			$this[$field]->addError($errMsg);
		}
    }

    // test field min length
    public function minLength($field, $errMsg, $minLength){
		if(strlen($this->values->$field)<$minLength){
			$this[$field]->addError($errMsg);
		}
    }

    // test field is equal to another
    public function isEqual($field, $errMsg, $equalField){
		if($this->values->$field<>$this->values->$equalField){
			$this[$field]->addError($errMsg);
		}
    }

    // test field is it is valid email
    public function isEmail($field, $errMsg){
		if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $this->values->$field)){
			$this[$field]->addError($errMsg);
		}
    }


}