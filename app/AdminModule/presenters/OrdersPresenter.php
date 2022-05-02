<?php

namespace App\AdminModule\Presenters;

use     TH\Form,
        Nette\Utils\Html;
use     Ublaboo\DataGrid\DataGrid;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use Vojir\Responses\CsvResponse\SimpleCsvResponse;

class OrdersPresenter extends OrdersForms
{

	public $branches = array();

    public function startup(){
		parent::startup();

        $this->fromPage = $this->getSession("ordersPage");

        $this->filter = $this->getSession("ordersFilter");

}

    public function actionDefault($tw = false, $c=false){
        if($c){
            $this->filter->remove();
            $this->redirect("default");
        }
        $this->fromPage->page = "default";
        if($tw){
            $this->filter->from = new \nette\Utils\DateTime();
            $weekEnd = new \nette\Utils\DateTime();
            $weekEnd->modify("+".(8-date("w"))." days");
            $this->filter->to = $weekEnd;
            $this->redirect(":Admin:Orders:default");
        }
        $this->prepareOrders();
    }

    public function actionDetail($id){
		$this->template->order = $this->orderManager->find($id);
		$this->template->products = $this->orderManager->findOrderProducts($id);
	}

    public function actionAdd(){
        $this->setView("addEdit");
    }

    public function actionEdit($id){
        $this->edited = $id;
        $this->template->order = $details = $this->orderManager->find($id);
        $this->template->items = $this->orderManager->findOrderItems($id);
        $this->template->products = $this->orderManager->findOrderProducts($id);
        $this["orderForm"]->setDefaults($details);
        $this->setView("addEdit");
    }

	public function handlePay($id){
		try{
			$this->orderManager->update(array("paid"=>1), $id);
			$invoices = $this->orderManager->findOrderInvoices($id);
			if(count($invoices)==0){
				$invoiceId = $this->makeInvoice($id);
			}
			$this->redirect("this");
		}
		catch(DibiDriverException $e){
			$this->flashMessage($e->getMessage());
		}
	}

	public function handleUnpay($id){
		try{
			$this->orderManager->update(array("paid"=>0), $id);
			$this->redirect("this");
		}
		catch(DibiDriverException $e){
			$this->flashMessage($e->getMessage());
		}
	}

    /**
     * Make table of orders
     *
     * @return \Addons\Tabella
     */
    public function createComponentOrders($name)
    {
        $presenter = $this;

        $grid = new DataGrid($this, $name);
        $grid->setDataSource($this->orders);
        $grid->setDefaultSort(['id' => 'DESC']);


        $grid->setRowCallback(function($row, $tr) {
			/*
            if($row->order_status_id==6){
                $tr->addClass('badge-canceled');
            }
            elseif($row->order_status_id==4 && $row->to<$now){
                $tr->addClass('unreturned');
            }
            */
        });

        $grid->addColumnText('id', 'Číslo')
        	->setSortable()->setSortableResetPagination()
            ->setRenderer(function($row) use ($presenter) {
                $el = Html::el("span");
                $el->insert(0, $row->id);
                return $el;
        });

        $grid->addColumnText('date', 'Datum')
        	->setSortable()->setSortableResetPagination()
            ->setRenderer(function($row) use ($presenter) {
                return $row->date->format("d. m. Y");
        });

        $grid->addColumnText('customer', 'Zákazník')
        	->setSortable()->setSortableResetPagination()
            ->setRenderer(function($row) use ($presenter) {
                return html::el("a")->href($presenter->link(":Admin:Orders:edit", $row->id))->setHtml($row->customer)->title("Upravit");
        });

        $grid->addColumnText('note', 'Poznámka')
            ->setRenderer(function($row) use ($presenter) {
                $el = Html::el("span");
                    $i=0;
                    if(!empty($row->note)){
                        //$el->insert($i++, '<button class="btn btn-primary btn-sm" type="button" data-toggle="collapse" data-target="#collapseRowNote'.$row->id.'" aria-expanded="false" aria-controls="collapseExample">Zobrazit</button>');
                        //$el->insert($i++, '<div class="collapse" id="collapseRowNote'.$row->id.'">');
                        $el->insert($i++, nl2br($row->note));
                        //$el->insert($i++, '</div>');
                    }
                return $el;
        });

        $grid->addColumnText('price', 'Cena bez DPH')
            ->setRenderer(function($row) use ($presenter) {
                $el = Html::el("span");
                $el->insert(0, number_format($row->price, 0, ',', ' ')." Kč");
                return $el;
        });

        $grid->addColumnText('price_vat', 'Cena s DPH')
            ->setRenderer(function($row) use ($presenter) {
                $el = Html::el("span");
                $el->insert(0, number_format($row->price_vat, 0, ',', ' ')." Kč");
                return $el;
        });

        $grid->addColumnText('tools', 'Nástroje')
            ->setRenderer(function($row) use ($presenter) {
                $el = Html::el("span");
                $el->insert(4, html::el("a")->class("btn btn-mini")->href($presenter->link("detail", $row->id))->setHtml(html::el("i")->class("fas fa-edit"))->title(" Smazat"));;
                return $el;
        });

        $this->localiseGrid($grid);

        return $grid;
    }

    public function handleDelete($id){
        $this->orderManager->delete($id);
        $this->redirect("default");
    }

    public function prepareOrders($date=false){
        $orders = $this->orderManager->getAll();
        if(!empty($this->filter->reserver)){
            $like = "%".$this->filter->reserver."%";
            $orders->whereOr(['surname LIKE ?'=>$like,'email LIKE ?'=>$like]);
        }

        $orders->order("date DESC");
        $this->orders = $orders;
    }

    public function handleRemindOrder($orderId){
        $order = $this->orderManager->find($orderId);
        $template = $this->createTemplate();
        $template->setFile(APP_DIR . '/FrontModule/templates/Mails/orderRemindPayment.latte');
        $template->order = $order;

        //send mail
        $mail = new Message;
        $mail->setFrom($this->settings->title." <".$this->settings->email.">")
            ->addTo($order->email)
            ->setSubject("Připomínáme dosud neuhrazenou platbu za objednávku tisku fotek")
            ->setHtmlBody($template);
        $mailer = new SendmailMailer;
        $mailer->send($mail);

        $this->flashMessage("Připomínka platby byla odeslána.");
        $this->redirect("this");
    }

    
	public function handleExport(){
        $emailsO = $this->orderManager->getEmails();
        $emailsO->fetchAll();
        $emailsP = $this->projectManager->getEmails();
        $emailsP->fetchAll();
        $emails = array();
        foreach($emailsO as $e){
			$emails[$e->email] = $e->email;
        }
        foreach($emailsP as $e){
			$emails[$e->email] = $e->email;
        }
        
        sort($emails);

		$output = array();
		foreach($emails as $e){
			if(!empty($e)){
				$output[]=array(""=>$e);
			}
		}

		$this->sendResponse(new SimpleCsvResponse($output, "export-emails-" .date('Ymd-Hi').".csv"));
	}

}
