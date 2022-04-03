<?php

namespace App\AdminModule\Presenters;

use     TH\Form,
        Nette\Utils\Html;
use     Ublaboo\DataGrid\DataGrid;


class VouchersPresenter extends BasePresenter
{
	public $editedId;
	public $filter;
	public $ses;

	public function startup(){
		parent::startup();
		$this->ses = $this->getSession("vouchers");
	}

	public function renderDefault(){
		$this->addBreadcrumbs("Slevové kupóny", $this->link(":Admin:Vouchers:default"));
	}

    public function handleDelete($id){
        $this->voucherManager->delete($id);
        $this->redirect("this");
    }

    /**
     * Make table of vouchers
     *
     * @return \Addons\Tabella
     */
    public function createComponentVouchers($name)
    {
        $presenter = $this;

        $vouchers = $this->voucherManager->get();

        if(!empty($this->ses->filter->used) && $this->ses->filter->used==1){
        	$vouchers->where("used IS NOT NULL");
        }
        if(!empty($this->ses->filter->used) && $this->ses->filter->used==2){
        	$vouchers->where("used IS NULL");
        }
        if(!empty($this->ses->filter->event)){
        	$vouchers->where("event LIKE ?", "%".$this->ses->filter->event."%");
        }
        if(!empty($this->ses->filter->code)){
        	$vouchers->where("code LIKE ?", $this->ses->filter->code);
        }

        $grid = new DataGrid($this, $name);
        $grid->setDataSource($vouchers);

        $grid->addColumnText('event', 'Akce');
        $grid->addColumnText('code', 'Kód');
        $grid->addColumnText('validTo', 'Platnost do')
            ->setRenderer(function($row) use ($presenter) {
                   if(empty($row->validTo)){
                       return Html::el("i")->class("fas fa-infinity")->title("bez omezení platnosti");
                   }
                   else{
                       return $row->validTo->format("j.n.Y");
                   }
        });
        $grid->addColumnText('unlimited', 'Neomezený')
            ->setRenderer(function($row) use ($presenter) {
                   if($row->unlimited){
                       return Html::el("i")->class("fas fa-infinity")->title("bez omezení platnosti");
                   }
                   else{
					   return "-";
                   }
        });

        $grid->addColumnText('used', 'Použit')
            ->setRenderer(function($row) use ($presenter) {
                   if($row->unlimited){
                       return Html::el("i")->class("fas fa-infinity")->title("bez omezení použití");
                   }
                   elseif($row->used){
                       return $row->used->format("j.n.Y H:i");
                   }
                   else{
                       return "-";
                   }
        });

        $grid->addColumnText('sale', 'Sleva')
            ->setRenderer(function($row) use ($presenter) {
                   if($row->sale>0){
                       return number_format($row->sale, 0, ",", " ")." Kč";
                   }
                   else{
                       return "-";
                   }
        });

        $grid->addColumnText('salePercent', 'Sleva v %')
            ->setRenderer(function($row) use ($presenter) {
                   if($row->salePercent>0){
                       return number_format($row->salePercent, 0, ",", " ")."%";
                   }
                   else{
                       return "-";
                   }
        });

        $grid->addColumnText('tools', 'Nástroje')
            ->setRenderer(function($row) use ($presenter) {
                $el = Html::el("span");
                $el->insert(0, html::el("a")->class("btn btn-mini btn-danger")->href($presenter->link("delete!", $row->id))->setHtml(html::el("i")->class("fas fa-trash-alt"))->title("Smazat"));
                return $el;
        });

        $this->localiseGrid($grid);

        return $grid;
    }

    /* FORMS */

	public function createComponentCreateVouchersForm(){
		$form = new Form();

		$form ->addText("event", "Akce")
			->addRule(Form::FILLED);
        //$form ->addText("count", "Počet");
        $form ->addText("code","Kód")->getControlPrototype()->placeholder("ponechte prázdné pro vygenerování");
		$form ->addDatepicker("validTo","Platný do (prázdné = neomezený)")->getControlPrototype()->class("form-control");
		$form ->addText("sale", "Sleva v Kč");
        $form ->addText("salePercent", "Sleva v %");
		$form ->addCheckbox("unlimited", "Neomezený");

		$form->addSubmit("submit", "Vytvořit kupón")
				->getControlPrototype()->class("btn btn-success");

		$form->onSuccess[] = [$this, 'saveVouchers'];

		return $form;
	}

	public function saveVouchers(Form $form, $values){

		// security
		//$this->isAllowed(array(1,3));
        if(!empty($values->code)){
            $exists = $this->voucherManager->findByCode($values->code);
            if(!empty($exists->id)){
                $form["code"]->addError("Tento kód je již použit");
            }
        }

		if($form->isValid()){
			try{
				//$count = $values->count;
				//while($count>0){
                    if(!empty($values->code)){
                        $code = $values->code;
                    }
                    else{
                        $code = $this->generateString(8);
                    }
					$used = $this->voucherManager->findByCode($code);
					if(!$used){
						//$count--;
						$save = array(
							"event"=>$values->event,
							"unlimited"=>$values->unlimited,
                            "code"=>$code,
							"validTo"=>$values->validTo,
							"sale"=>(int)$values->sale,
							"salePercent"=>(int)$values->salePercent,
						);
						$this->voucherManager->add($save);
					}
				//}

				$this->flashMessage("Slevový kupón byly vygenerován");

				$this->redirect(":Admin:Vouchers:default");
			}
			catch(DibiDriverException $e){
				$this->flashMessage($e->getMessage(), "error");
			}
		}
	}

	public function createComponentFilterForm(){

		$form = new Form();

		$form->getElementPrototype()->class("inline");

		$uses = array(
			"1"=>"použité kupóny",
			"2"=>"nepoužité kupóny"
		);

		$form->addText("code")
			->getControlPrototype()->placeholder("kód kupónu")->class("span5");

		$form->addText("event")
			->getControlPrototype()->placeholder("název akce")->class("span5");

		$form->addSelect("used", "", $uses)
			->setPrompt("Stav kupónu nerozhoduje");

		$form->addSubmit("submit", "Hledej")
			->getControlPrototype()->class("btn");

		$form->addSubmit("reset", "Zobrazit vše")
			->getControlPrototype()->class("btn");

		$form->onSuccess[] = [$this, 'filter'];

		if(!empty($this->ses->filter)){
			$form->setDefaults($this->ses->filter);
		}

		$renderer = $form->getRenderer();
		$renderer->wrappers['controls']['container'] = null;


		return $form;
	}

	/** callback for products search
	*
	* @param Form data from search form
	* @return void
	*/
	public function filter(Form $form){

		if($form["reset"]->isSubmittedBy()){
			$this->ses->filter = null;
		}
		else{
			$this->ses->filter = $form->values;
		}

		$this->redirect("this");

	}

	public function handleExport(){
        $vouchers = $this->voucherManager->get();
        if(!empty($this->ses->filter->used) && $this->ses->filter->used==1){
        	$vouchers->where("used is not null");
        }
        if(!empty($this->ses->filter->used) && $this->ses->filter->used==2){
        	$vouchers->where("used is null");
        }
        if(!empty($this->ses->filter->event)){
        	$vouchers->where("event like %~like~", $this->ses->filter->event);
        }
        if(!empty($this->ses->filter->code)){
        	$vouchers->where("code like %s", $this->ses->filter->code);
        }
        $vouchers->fetchAll();

		$output = array();
		foreach($vouchers as $v)		{
			$output[]=array("KOD"=>$v->code);
		}

		$this->sendResponse(new CsvResponse($output, "export-" .date('Ymd-Hi').".csv"));
	}
}
