<?php

namespace App\AdminModule\Presenters;

use     TH\Form,
        Nette\Utils\Html;
use     Ublaboo\DataGrid\DataGrid;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;

class ProjectsPresenter extends ProjectsForms
{
	
	public $generateStatuses = array(10=>"Probíhá generování", 20=>"Generování dokončeno", 90=>"Chyba generování");

    public function startup(){
		parent::startup();

        $this->filter = $this->getSession("projectsFilter");
        if(!isset($this->filter->branch) && isset($this->user->identity->branch)){
            $this->filter->branch = $this->user->identity->branch;
        }

}

    public function actionDefault($tw = false, $c=false){
        if($c){
            $this->filter->remove();
            $this->redirect("default");
        }
        if($tw){
            $this->filter->from = new \nette\Utils\DateTime();
            $weekEnd = new \nette\Utils\DateTime();
            $weekEnd->modify("+".(8-date("w"))." days");
            $this->filter->to = $weekEnd;
            $this->redirect(":Admin:Projects:default");
        }
        $this->prepareProjects();
    }

    public function actionAdd(){
        $this->setView("addEdit");
    }

    public function actionEdit($id){
        $this->edited = $id;
        $this->template->project = $this->projectManager->find($id);
        $this->setView("addEdit");
    }

	public function handleActivate($id){
		try{
			$this->projectManager->update(array("active"=>1), $id);
			$this->redirect("this");
		}
		catch(DibiDriverException $e){
			$this->flashMessage($e->getMessage());
		}
	}

	public function handleDeactivate($id){
		try{
			$this->projectManager->update(array("active"=>0), $id);
			$this->redirect("this");
		}
		catch(DibiDriverException $e){
			$this->flashMessage($e->getMessage());
		}
	}

    /**
     * Make table of projects
     *
     * @return \Addons\Tabella
     */
    public function createComponentProjects($name)
    {
        $presenter = $this;

        $grid = new DataGrid($this, $name);
        $grid->setDataSource($this->projects);
        $grid->setDefaultSort(['id' => 'DESC']);


        $grid->setRowCallback(function($row, $tr) {
            if(empty($row->labId)){
                $tr->style('background-color:#ffcccc;');
            }
			/*
            if($row->project_status_id==6){
                $tr->addClass('badge-canceled');
            }
            elseif($row->project_status_id==4 && $row->to<$now){
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

        $grid->addColumnDateTime('date', 'Založena')
        	->setSortable()->setSortableResetPagination()
        	->setFormat('d.m.Y H:i:s');
        
        $grid->addColumnText('customer', 'Zákazník')
        	->setSortable()->setSortableResetPagination()
            ->setRenderer(function($row) use ($presenter) {
                $el = Html::el("span");
                    $i=0;
                    if(!empty($row->customer)){
                        $el->insert($i++, $row->customer);
                        $el->insert($i++, " ");
                        $el->insert($i++, html::el("a")->href($presenter->link("filterName!", $row->customer))->setHtml(Html::el("i")->class("fa fa-filter"))->title("Filtrovat podle jména"));
                    }
                return $el;
		});

        $grid->addColumnText('labId', 'Složka')
            ->setRenderer(function($row) use ($presenter) {
				if(!empty($row->hash)){
                    $el =html::el("span");
                    $el->insert(1,html::el("a")->href("https://drive.google.com/drive/folders/".$row->hash)->target("_blank")->setHtml($row->id."_".$row->labId/*$presenter->link("//:Front:Homepage:album", $row->hash)*/)->title("Otevřít"));
                    $el->insert(2, " ");
                    $el->insert(3, html::el("a")->class("btn btn-mini btn-danger")->href($presenter->link("unlinkFolder!", $row->id))->setHtml(html::el("i")->class("fas fa-unlink"))->title("Odstranit párování"));
                    return $el;
				}
				else{
					return $row->id."_".$row->labId;
				}
        });

        $grid->addColumnText('hash', 'Odkaz na album')
            ->setRenderer(function($row) use ($presenter) {
				if(!empty($row->hash)){
                    return html::el("a")->href($presenter->link(":Front:Homepage:album", $row->hash))->target("_blank")->setHtml(Html::el("i")->class("fas fa-external-link-alt")/*$presenter->link("//:Front:Homepage:album", $row->hash)*/)->title("Otevřít");
				}
				else{
					return "";
				}
        });

        $grid->addColumnText('branch', 'Pobočka')
        	->setSortable()->setSortableResetPagination()
            ->setRenderer(function($row) use ($presenter) {
				if(empty($row->branch)){
					return " - ";
				}
				else{
            		return $presenter->branchesSimple[$row->branch];
				}
        });

        $grid->addColumnText('recieve', 'Způsob doručení')
        	->setSortable()->setSortableResetPagination()
            ->setRenderer(function($row) use ($presenter) {
            	return $presenter->recieves[$row->recieve];
        });

        $grid->addColumnText('note', 'Poznámka')
            ->setRenderer(function($row) use ($presenter) {
                $el = Html::el("span");
                    $i=0;
                    if(!empty($row->note)){
                        $el->insert($i++, '<button class="btn btn-primary btn-sm" type="button" data-toggle="collapse" data-target="#collapseRowNote'.$row->id.'" aria-expanded="false" aria-controls="collapseExample">Zobrazit</button>');
                        $el->insert($i++, '<div class="collapse" id="collapseRowNote'.$row->id.'">');
                        $el->insert($i++, nl2br($row->note));
                        $el->insert($i++, '</div>');
                    }
                return $el;
        });

        $grid->addColumnText('generateStatus', 'Generování JPG')
        	->setSortable()->setSortableResetPagination()
            ->setRenderer(function($row) use ($presenter) {
                $class="btn-primary";
                $el = Html::el("span");
                if($row->emptyFilm){
                        $el->insert(1, Html::el("a")->class("btn btn-secondary")->href($presenter->link("setEmpty!", $row->id, false))->setHtml("<i class='far fa-eye'></i> neprázdný"));
                }
                else{
                    switch($row->generateStatus){
                        case 20:
                            $class = "btn-success";
                            break;
                        case 90:
                            $class = "btn-danger";
                            break;
                    }
                    if(empty($row->branch)){
                        $el->insert(1, Html::el("span")->class("btn btn-warning")->setHtml("Vyberte pobočku"));
                    }
                    else{
                        $el->insert(1, Html::el("span")->class("tabella_ajax btn ".$class)->title($row->genError)->setHtml($presenter->generateStatuses[$row->generateStatus]));
                        if($row->generateStatus!=10){
                            $el->insert(2, Html::el("a")->class("tabella_ajax btn btn-primary")->href($presenter->link("convert!", $row->id))->title("Opakovat generování")->setHtml(Html::el("span")->class("fas fa-sync-alt")));
                        }
                    }
                    if(empty($row->email)){
                        $el->insert(1, Html::el("span")->class("btn btn-secondary")->title("Není vyplněn e-mail zakázky")->setHtml("<i class='far fa-eye-slash'></i> prázdný"));
                    }
                    else{
                        $el->insert(1, Html::el("a")->class("btn btn-danger")->href($presenter->link("setEmpty!", $row->id, true))->setHtml("<i class='far fa-eye-slash'></i> prázdný"));
                    }
                }
                return $el;
        });

        $grid->addColumnText('generated', 'Vygenerované JPG')
        	->setSortable()->setSortableResetPagination()
            ->setRenderer(function($row) use ($presenter) {
				return (int)$row->generated." z ".(int)$row->toGenerate;
        });

        $grid->addColumnText('active', 'Odeslaná')
        	->setSortable()->setSortableResetPagination()
            ->setRenderer(function($row) use ($presenter) {
                if($row->active){
                	return Html::el("a")->class("tabella_ajax")->href($presenter->link("deactivate!", $row->id))->setHtml(html::el("img")->src(FOLDER."/images/active.png")->class("action"));
                }
                else{
                	return Html::el("a")->class("tabella_ajax")->href($presenter->link("activate!", $row->id))->setHtml(html::el("img")->src(FOLDER."/images/deactive.png")->class("action"));
                }
        });

        $grid->addColumnText('tools', 'Nástroje')
            ->setRenderer(function($row) use ($presenter) {
                $photos = $presenter->pageManager->countPhotos($row->id);
                $el = Html::el("span");
                $el->insert(2, html::el("a")->class("btn btn-mini")->href($presenter->link("edit", $row->id))->setHtml(html::el("i")->class("fas fa-edit"))->title(" Upravit"));
                $el->insert(3, " ");
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

    public function handleFilterName($name = null){
            $this->filter->reserver = $name;
            $this->redirect("this");
    }

    public function handleDelete($id){
        $this->projectManager->delete($id);
        $this->redirect("default");
    }

    public function handleSetEmpty($id, $empty){
        $this->projectManager->update(array("emptyFilm"=>$empty), $id);
        if($empty){
            $project = $this->projectManager->find($id);
            $data = array(
                "project" => $project
            );
            $this->sendMailFromTemplate("sendEmptyFilm.latte", $data, $project->email, "Váš film byl prázdný");
        }
        $this->redirect("default");
    }

    public function handleUnlinkFolder($id){
        $this->projectManager->update(array("hash"=>null), $id);
        $this->redirect("default");
    }

    public function handleSendLinks(){
        $toSend = $this->projectManager->getToSend();
        $links = array();
        foreach($toSend as $project){
			if(empty($project->email)){
				$this->flashMessageError("U zakázky ".$project->id." není vyplněn e-mail, e-mail s odkazem nemohl být odeslán.");
			}
			else{
				if(empty($links[$project->email])){
					$links[$project->email] = array();
				}
				$links[$project->email][$project->id] = $project;
			}
        }
        
        //Send email to customer
        foreach($links as $email=>$projects){
        	$data = array(
        		"projects" => $projects
        	);
			$this->sendMailFromTemplate("sendLink.latte", $data, $email, "Váš film je zpracován");
			foreach($projects as $proj){
        		$this->projectManager->update(array("active"=>true), $proj->id);
			}
        }
        
        $this->flashMessage("Odkazy byly odeslány");
        $this->redirect("default");
    }

    public function prepareProjects($date=false){
        $projects = $this->projectManager->getAll();
        if(!empty($this->filter->reserver)){
            $like = "%".$this->filter->reserver."%";
            $projects->whereOr(['name LIKE ?'=>$like, 'surname LIKE ?'=>$like,'email LIKE ?'=>$like,'CONCAT(name, " ",surname) LIKE ?'=>$like]);
        }
        if(!empty($this->filter->branch)){
			$projects->where('(branch = ? OR branch IS NULL)', $this->filter->branch);
        }

        $projects->order("date DESC");
        $this->projects = $projects;
    }

    public function handleConvert($id){
    	$this->projectManager->update(array("generateStatus"=>10, "genError"=>""), $id);
    	/*
    	set_time_limit(-1);
        $project = $this->projectManager->find($id);
        $files = $this->getGfiles($project->hash);
		$jpegs = array();
		$tifs = array();
		foreach($files as $file){
			$ext = pathinfo($file->getName(), PATHINFO_EXTENSION);
			$name = pathinfo($file->getName(), PATHINFO_FILENAME);
			switch(strtolower($ext)){
				case "tif":
				case "tiff":
					$tifs[$name] = $file;
					break;
				default:
					$jpegs[$name] = $file;
					break;
			}
		}

		$converted = 0;
        foreach($tifs as $fileName=>$file){
			if(empty($jpegs[$fileName])){
				$tiffName = $file->getName();
				$jpegName = $this->tiff2jpgName($tiffName);
				$tiffPath = DATA_DIR.'/temp/'.$tiffName;
        		$ft = fopen($tiffPath, 'w');
				//get data of original file
			    fwrite($ft, file_get_contents($file->getWebContentLink()));
			    fclose($ft);

				//convert tiff to jpeg
				$jpegPath = $this->tiff2jpg($tiffPath);

				//upload file to server
				if(!empty($jpegPath)){
					$converted++;
					$this->uploadGfile($jpegName, $jpegPath, $project->hash);
				}
			}

		}
        $this->projectManager->update(array("converted"=>true), $id);
        $this->flashMessage("Celkem TIFF souborů: ".count($tifs).", existujících JPEG: ".count($jpegs).", nových JPEG: $converted");
        */

        $this->redirect("default");
    }

    public function handleImport(){
		set_time_limit(-1);
		$orders = $this->wpManager->getOrders();
		foreach($orders as $order){
			$row = $this->wpManager->getOrderData($order->ID);
			$exists = $this->projectManager->exists($order->ID);
			if(!$exists){
                $productOrderId = $this->wpManager->getProductIdByIdAndOrdrId($order->ID);
                $productAmount = $this->wpManager->getProductAmount($productOrderId);
                //process and add order
				$row->wpId = $order->ID;
				$row->date = new \nette\utils\DateTime();
				$row->street = $row->street." ".$row->address;
				unset($row->address);
				switch($row->pickup){
					case "brno":
						$row->branch = 2;
						break;
					case "ostrava":
						$row->branch = 3;
						break;
					default:
						$row->branch = 1;
						break;
				}
				unset($row->pickup);
				//$row->message = mysqli_real_escape_string($row->message);
                for($i=1;$i<=$productAmount;$i++){
                    $insertedId = $this->projectManager->add($row);
                }

				//send notification
				//$this->sendDemandNotification($insertedId);
			}
		}
		$this->flashMessage("Zakázky byly naimportovány");
	}



}
