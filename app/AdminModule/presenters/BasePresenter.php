<?php

/**
 * Base class for all application presenters.
 *
 * @author     Tomas Hubicka
 * @package    Polagraph
 *
 */

namespace App\AdminModule\Presenters;

use 	Nette\Security as NS;
use		Nette\Http\Session;

abstract class BasePresenter extends \App\FrontModule\Presenters\BasePresenter
{

	 /** @persistent */
	public $lang;

	public $languages;

	public $statuses;
	public $statusesFull;

	public $recieves = array(0=>"Negativy vyzvednout", 1=>"Negativy poslat");

    public $paymentTypes = array("FAKTURA/CASH EET"=>"Hotově","FAKTURA/KARTA"=>"Kartou");

    // default presenter

    public function startup(){
		parent::startup();
		$this->redrawControl("flashMessages");

		// security
		$this->isAllowed(array(5,9));

		//$this->languages = $this->template->languages = $this->models->common->getLanguages();

		//$this->template->activeLang = $this->lang;

		//$this->breadcrumbs = array();

		$this->statuses = $this->orderManager->getStatuses();
		$this->statusesFull = $this->orderManager->getStatusesFull();

	}

    public function localiseGrid($grid){
        $translator = new \Ublaboo\DataGrid\Localization\SimpleTranslator([
            'ublaboo_datagrid.no_item_found_reset' => 'Žádné položky nenalezeny. Filtr můžete vynulovat',
            'ublaboo_datagrid.no_item_found' => 'Žádné položky nenalezeny.',
            'ublaboo_datagrid.here' => 'zde',
            'ublaboo_datagrid.items' => 'Položky',
            'ublaboo_datagrid.all' => 'všechny',
            'ublaboo_datagrid.from' => 'z',
            'ublaboo_datagrid.reset_filter' => 'Resetovat filtr',
            'ublaboo_datagrid.group_actions' => 'Hromadné akce',
            'ublaboo_datagrid.show_all_columns' => 'Zobrazit všechny sloupce',
            'ublaboo_datagrid.hide_column' => 'Skrýt sloupec',
            'ublaboo_datagrid.action' => 'Akce',
            'ublaboo_datagrid.previous' => 'Předchozí',
            'ublaboo_datagrid.next' => 'Další',
            'ublaboo_datagrid.choose' => 'Vyberte',
            'ublaboo_datagrid.execute' => 'Provést',
            'ublaboo_datagrid.per_page_submit' => 'Použít',

        ]);
        $grid->setTranslator($translator);
    }

    public function rowToArray($row){
        return \Nette\Utils\ArrayHash::from($row->toArray());
    }




}
