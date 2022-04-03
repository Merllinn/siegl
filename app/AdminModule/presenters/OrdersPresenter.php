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
        if(!isset($this->filter->branch) && isset($this->user->identity->branch)){
            $this->filter->branch = $this->user->identity->branch;
        }

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
                $products = $this->orderManager->findOrderProducts($row->id);
                $el = Html::el("span");
                $el->insert(0, $row->id);
                if(count($products)>0){
                    $el->insert(1, " ");    
                    $productsList = array();
                    foreach($products as $product){
                        $productsList[] = $product->name;
                    }
                    $el->insert(2, html::el("i")->title("obsahuje doplňkové produkty: ".implode(", ", $productsList))->addClass("fas fa-gift"));    
                }
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

        $grid->addColumnText('branch', 'Pobočka')
        	->setSortable()->setSortableResetPagination()
            ->setRenderer(function($row) use ($presenter) {
                return $presenter->makeBranchSelect($row->id, $row->branch);
				if(empty($row->branch)){
					return " - ";
				}
				else{
            		return $presenter->branchesSimple[$row->branch];
				}
        });

        $grid->addColumnText('delivery', 'Doprava');

        $grid->addColumnText('voucherCode', 'Slevový kód')
        	->setSortable()->setSortableResetPagination()
        ;

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

        $grid->addColumnText('price', 'Cena')
            ->setRenderer(function($row) use ($presenter) {
                $el = Html::el("span");
                $el->insert(0, number_format($row->price, 0, ',', ' ')." Kč");
                return $el;
        });

        $grid->addColumnText('framing', 'Rámování')
            ->setRenderer(function($row) use ($presenter) {
                $el = Html::el("span");
                if($row->framing){
                	$el->insert(0, "ANO");
                }
                else{
                	$el->insert(0, "NE");
                }
                return $el;
        });

        $grid->addColumnText('order_status_id', 'Stav objednávky')
        	->setSortable()->setSortableResetPagination()
            ->setRenderer(function($row) use ($presenter) {
                return $presenter->makeStatusSelect($row->id, $row->order_status_id);
        });

		/*
        $grid->addColumnText('order_status_id', 'Stav objednávky')
            ->setRenderer(function($row) use ($presenter) {
            	return Html::el("span")->class("badge ".$presenter->statusesFull[$row->order_status_id]->class)->setHtml($presenter->statusesFull[$row->order_status_id]->name);
        });
        */

        $grid->addColumnText('paid', 'Uhrazeno')
        	->setSortable()->setSortableResetPagination()
            ->setRenderer(function($row) use ($presenter) {
            	$el = Html::el("span");
            	switch($row->payment){
					case "Kartou on-line":
						$el->insert(0, Html::el("i")->class("fas fa-credit-card")->title($row->payment));
						break;
					case "Hotově na prodejně":
						$el->insert(0, Html::el("i")->class("fas fa-money-bill-wave")->title($row->payment));
						break;
					case "Dobírka":
						$el->insert(0, Html::el("i")->class("fas fa-hand-holding-usd")->title($row->payment));
						break;
            	}
				$el->insert(1, " ");
                if($row->paid){
                	$el->insert(2, Html::el("a")->class("tabella_ajax")->href($presenter->link("unpay!", $row->id))->setHtml(html::el("img")->src(FOLDER."/images/active.png")->class("action")));
                }
                else{
                	$el->insert(2, Html::el("a")->class("tabella_ajax")->href($presenter->link("pay!", $row->id))->setHtml(html::el("img")->src(FOLDER."/images/deactive.png")->class("action")));
                    if($row->payment=="Kartou on-line"){
                    	$el->insert(3, html::el("a")->class("btn btn-sm btn-info")->href($presenter->link("remindOrder!", $row->id))->setHtml(html::el("i")->class("fas fa-paper-plane"))->title("Poslat připomenutí platby"));
                    }
                }
                return $el;
        });

        $grid->addColumnText('approved', 'Schváleno')
        	->setSortable()->setSortableResetPagination()
            ->setRenderer(function($row) use ($presenter) {
                if($row->approved){
                	return html::el("img")->src(FOLDER."/images/active.png")->class("action");
                }
                else{
                	return Html::el("a")->class("tabella_ajax btn btn-primary")->href($presenter->link("approve!", $row->id))->setHtml("Schválit a odeslat k tisku");
                }
        });

        $grid->addColumnText('invoices', 'Doklady')
            ->setRenderer(function($row) use ($presenter) {
                $el = Html::el("span");
                $invoices = $presenter->orderManager->findOrderInvoices($row->id);
                $i=0;
                foreach($invoices as $invoice){
                    $invoiceNr = $invoice->number;
                    if(empty($invoiceNr)){
                        $invoiceNr = $invoice->invoiceId;
                    }
                    if($invoice->sended == true){
                        $el->insert($i++, html::el("a")->class("btn btn-sm btn-secondary")->target("_blank")->href(FOLDER."/data/invoice/".$invoice->invoiceId.".pdf")->setHtml($invoiceNr)->title("Zobrazit odeslaný doklad"));
                    }
                    else{
                        $el->insert($i++, html::el("a")->class("btn btn-sm btn-light")->target("_blank")->href(FOLDER."/data/invoice/".$invoice->invoiceId.".pdf")->setHtml($invoiceNr)->title("Zobrazit neodeslaný doklad"));
                    }
                    $el->insert($i++, " ");
                }
					/*
                    $el->insert($i++, "<br>");
                    $el->insert($i, html::el("a")->class("btn btn-sm btn-success")->href($presenter->link(":Admin:Orders:newInvoice", $row->id))->setHtml(html::el("i")->class("fas fa-file-invoice-dollar"))->title("Nový doklad"));
                    */
                return $el;
        });

		/*
        $grid->addColumnText('status', 'Stav')
            ->setRenderer(function($row) use ($presenter) {
	        	$el = Html::el("span");
	            foreach($presenter->statuses[$this->branches[$row->branch]->isDelivery] as $stat){
	                if(empty($nextStatus) && $stat->id>$row->order_status_id){
	                    $nextStatus=$stat;
	                }
	            }
	            $el->insert(0, Html::el("span")->class("badge ".$presenter->statuses[$this->branches[$row->branch]->isDelivery][$row->order_status_id]->class)->setHtml($presenter->statuses[$this->branches[$row->branch]->isDelivery][$row->order_status_id]->name));
	            if($row->order_status_id==3){
	                $el->insert(1, Html::el("br"));
	                $el->insert(2, html::el("a")->class("btn btn-sm ".$presenter->statuses[$this->branches[$row->branch]->isDelivery][$row->order_status_id]->class)->href($presenter->link("smsRemind!", $row->id, $row->order_status_id))->setHtml(html::el("i")->class("fas fa-sms"))->title("Připomenout vyzvednutí"));
	            }
	            if(!empty($nextStatus)){
	                $el->insert(3, Html::el("br"));
	                $el->insert(4, html::el("a")->class("btn btn-sm ".$nextStatus->class)->href($presenter->link("setStatus!", $row->id, $nextStatus->id))->setHtml(html::el("i")->class("fas fa-paper-plane"))->title("Přepnout do stavu ".$nextStatus->name));
	            }
                return $el;
        });
        */

        $grid->addColumnText('tools', 'Nástroje')
            ->setRenderer(function($row) use ($presenter) {
                $photos = $presenter->pageManager->countPhotos($row->id);
                $el = Html::el("span");
                $el->insert(4, html::el("a")->class("btn btn-mini btn-danger")->href($presenter->link("delete!", $row->id))->setHtml(html::el("i")->class("fas fa-trash-alt"))->title(" Smazat"));;
                return $el;
        });

        $this->localiseGrid($grid);

        return $grid;
    }

    public function handleFilterStatus($status = null){
            $this->filter->status = $status;
            $this->redirect("this");
    }

    public function handleDelete($id){
        $this->orderManager->delete($id);
        $this->redirect("default");
    }

    public function handleApprove($id){
        $this->approveOrder($id);
        $this->redirect("default");
    }

    public function prepareOrders($date=false){
        $orders = $this->orderManager->getAll();
        if(!empty($this->filter->status)){
            $orders->where("order_status_id = ?", $this->filter->status);
        }
        if(!empty($this->filter->reserver)){
            $like = "%".$this->filter->reserver."%";
            $orders->whereOr(['surname LIKE ?'=>$like,'email LIKE ?'=>$like]);
        }
        if(!empty($this->filter->branch)){
            $orders->where('(branch = ? OR branch IS NULL)', $this->filter->branch);
        }
        if(!empty($this->filter->delivery)){
            $orders->where("delivery = ?", $this->deliveries[$this->filter->delivery]);
        }

        $orders->order("date DESC");
        $this->orders = $orders;
    }

    public function handleSetStatus($orderId, $status){
        $manager = $this->orderManager;
        $manager->update(array("order_status_id"=>$status), $orderId);
        
	    $template = $this->createTemplate();
	    $template->setFile(APP_DIR . '/FrontModule/templates/Mails/orderStatus'.$status.'.latte');

        $order = $this->orderManager->find($orderId);
		$order = \Nette\Utils\ArrayHash::from($order);
	    $template->order = $order;

	    $subjects = explode("|", $this->statusesFull[$status]->subject);
	    $subject = str_replace("[ID]",$order->id , $subjects[0]);

	    //send mail
	    $mail = new Message;
	    $mail->setFrom($this->settings->title." <".$this->settings->email.">")
	        ->addTo($order->email)
	        ->setSubject($subject)
	        ->setHtmlBody($template);

	    $mailer = new SendmailMailer;
	    $mailer->send($mail);
        

        $this->flashMessage("Stav objednávky byl nastaven");
        $this->redrawControl("flashmessages");
    }

    public function handleSetBranch($orderId, $branch){
        $manager = $this->orderManager;
        if(empty($branch)){
			$branch = null;
        }
        $manager->update(array("branch"=>$branch), $orderId);

        $this->flashMessage("Pobočka objednávky byla nastavena");
        $this->redrawControl("flashmessages");
    }

    public function makeStatusSelect($id, $selected){
    	$ret = Html::el("select")->class("statusSelect form-control")->name("status".$id)->data("link", $this->link("setStatus!", $id));
		foreach($this->statuses as $statusId=>$statusName){
			$option = html::el("option")->class($this->statusesFull[$statusId]->class)->value($statusId)->insert(0, $statusName);
			if($selected==$statusId){
				$option->selected("selected");
			}
			$ret->insert($statusId, $option);
		}
		return $ret;
    }

    public function makeBranchSelect($id, $selected){
    	$ret = Html::el("select")->class("branchSelect form-control")->name("branch".$id)->data("link", $this->link("setBranch!", $id));
		$option = html::el("option")->value("")->insert(0, " - nevybráno - ");
		$ret->insert("", $option);
		foreach($this->branchesSimple as $branchId=>$branchName){
			$option = html::el("option")->value($branchId)->insert(0, $branchName);
			if($selected==$branchId){
				$option->selected("selected");
			}
			$ret->insert($branchId, $option);
		}
		return $ret;
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
