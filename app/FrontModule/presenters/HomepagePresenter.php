<?php

namespace App\FrontModule\Presenters;

use \TH\Form;
use \Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use Nette\Utils\Html;

final class HomepagePresenter extends HomepageForms
{
    public function actionApprove($id, $branch=1){
    	$this->orderManager->update(array("branch"=>$branch), $id);
		$this->approveOrder($id);
        $this->redirect(":Front:Homepage:page", "");
	}
	
	public function renderSubpages(){
		$this->template->subpages = $this->pageManager->getInSubpagesByParent($this->template->page->id);
	}

    public function renderOrder(){
		$this->template->basket = $this->basket;
		$this->template->address = $this->basket->address;
		$this->template->containers = $this->basket->containers;
		$this->template->materials = $this->basket->materials;
		$this->template->attVals = $this->attributeManager->getAllValues();
		$this->template->zones = $this->commonManager->getActiveZones();
        if(!empty($this->basket->order)){
        	$this["orderForm"]->setDefaults($this->basket->order);
		}
		$this["orderForm"]["type"]->setValue("1");
	}
	
    public function renderOrderMaterial(){
		$this->template->basket = $this->basketM;
		$this->template->address = $this->basketM->address;
		$this->template->containers = $this->basketM->containers;
		$this->template->materials = $this->basketM->materials;
		$this->template->attVals = $this->attributeManager->getAllValues();
		$this->template->zones = $this->commonManager->getActiveZones();
        if(!empty($this->basketM->order)){
        	$this["orderForm"]->setDefaults($this->basketM->order);
		}
		$this["orderForm"]["type"]->setValue("2");
	}
	
    public function renderDemand(){
		$this->template->basket = $this->basketD;
		$this->template->address = $this->basketD->address;
		$this->template->containers = $this->basketD->containers;
		$this->template->materials = $this->basketD->materials;
		$this->template->attVals = $this->attributeManager->getAllValues();
		$this->template->zones = $this->commonManager->getActiveZones();
        if(!empty($this->basketD->order)){
        	$this["orderForm"]->setDefaults($this->basketD->order);
		}
		$this["orderForm"]["type"]->setValue("9");
	}
	
    public function renderDemandMaterial(){
		$this->template->basket = $this->basketD;
		$this->template->address = $this->basketD->address;
		$this->template->containers = $this->basketD->containers;
		$this->template->materials = $this->basketD->materials;
		$this->template->attVals = $this->attributeManager->getAllValues();
		$this->template->zones = $this->commonManager->getActiveZones();
        if(!empty($this->basketD->order)){
        	$this["orderForm"]->setDefaults($this->basketD->order);
		}
		$this["orderForm"]["type"]->setValue("9");
	}
	
	public function handleAddNextContainer($basket="basket"){
		$containers = $this->$basket->containers;
		$container = new \Nette\Utils\ArrayHash();
		$container->amount = 1;
		$containers[] = $container;
		$this->$basket->containers = $containers;
		$this->recalculateBasket($basket);
		$this->redrawControl("orderContainers");
		//$this->redirect("this");
	}

	public function handleSetMaterial($mat){
		$materials = $this->basket->materials;
		$material = new \Nette\Utils\ArrayHash();
		$material->product = $mat;
		$materials[0] = $material;
		$this->basket->materials = $materials;
		$this->recalculateBasket();
		$this->redrawControl("matamount");
		//$this->redirect("this");
	}

	public function handleUnuseMaterial($basket="basketD"){
		$this->$basket->materials = array();
		$this->recalculateBasket($basket);
		//$this->redirect("this");
	}

	public function handleUnuseContainers($basket="basketM"){
		$this->$basket->containers = array();
		$this->recalculateBasket($basket);
		$this->redrawControl("orderContainers");
		//$this->redirect("this");
	}

	public function handleSetMaterialVariant($var){
		$materials = $this->basket->materials;
		$material = $materials[0];
		$material->price = $var;
		$priceObj = $this->productManager->findPrice($var);
		$material->priceObj = $this->rowToArray($priceObj);
		$material->amount = 1;
		/*
		if($material->product==$this->settings->betonProduct){
			$material->amount = 1/$priceObj->koef;
		}
		else{
		}
		*/
		$materials[0] = $material;
		$this->basket->materials = $materials;
		$this->recalculateBasket();
		$this->redrawControl("matamount");
		//$this->redirect("this");
	}
	public function handleSetMaterialVariantD($var, $basket="basketD"){
		$priceObj = $this->rowToArray($this->productManager->findPrice($var));
		$materials = $this->$basket->materials;
		if(!isset($materials[$priceObj->product])){
			$materials[$priceObj->product] = array();
		}
		$material = $materials[$priceObj->product];
		if(!isset($material[$var])){
			$material[$var] = new \Nette\Utils\ArrayHash();
		}
		$variant = $material[$var];
		$variant->priceObj = $priceObj;
		$variant->amount = 1;
		$material[$var] = $variant;
		$materials[$priceObj->product] = $material;
		$this->$basket->materials = $materials;
		$this->recalculateBasket($basket);
		//$this->redrawControl("matamount");
		//$this->redirect("this");
	}
	public function handleUnsetMaterialVariantD($var, $basket="basketD"){
		$priceObj = $this->rowToArray($this->productManager->findPrice($var));
		$materials = $this->$basket->materials;
		if(!isset($materials[$priceObj->product])){
			$materials[$priceObj->product] = array();
		}
		$material = $materials[$priceObj->product];
		if(isset($material[$var])){
			unset($material[$var]);
		}
		if(empty($material)){
			unset($materials[$priceObj->product]);
		}
		else{
			$materials[$priceObj->product] = $material;
		}
		$this->$basket->materials = $materials;
		$this->recalculateBasket($basket);
		//$this->redrawControl("matamount");
		//$this->redirect("this");
	}
	public function handleSetMaterialAmount($amount){
		$materials = $this->basket->materials;
		if(!empty($materials[0])){
			$material = $materials[0];
			$material->amount = $amount;
			$materials[0] = $material;
			$this->basket->materials = $materials;
		}
		$this->recalculateBasket();
		//$this->redirect("this");
	}
	public function handleSetMaterialAmountD($product, $variant, $amount){
		$materials = $this->basketD->materials;
		if(!empty($materials[$product][$variant])){
			$var = $materials[$product][$variant];
			$var->amount = $amount;
			$materials[$product][$variant] = $var;
			$this->basketD->materials = $materials;
		}
		$this->recalculateBasket("basketD");
		//$this->redirect("this");
	}
	public function handleSetMaterialAmountM($product, $variant, $amount){
		$materials = $this->basketM->materials;
		if(!empty($materials[$product][$variant])){
			$var = $materials[$product][$variant];
			$var->amount = $amount;
			$materials[$product][$variant] = $var;
			$this->basketM->materials = $materials;
		}
		$this->recalculateBasket("basketM");
		//$this->redirect("this");
	}
	public function handleSetAddress($basket="basket", $a){
		$this->$basket->address = $a;
		$this->recalculateBasket($basket);
		//$this->redirect("this");
	}
    public function handleSetBasketZone($basket="basket", $z){
		$this->$basket->zone = $z;
		$zoneObj = $this->commonManager->findZone($z);
		if($zoneObj){
			$this->$basket->zoneObj = $this->rowToArray($zoneObj);
		}
		$this->redrawControl("orderContainers");
		$this->recalculateBasket($basket);
    }
    
    public function handleSetBasketNote($basket="basket", $val){
		$this->$basket->description = $val;
    }

	public function handleRemoveFromOrder($index, $basket="basket"){
		$containers = $this->$basket->containers;
		unset($containers[$index]);
		$this->$basket->containers = $containers;
		if(count($containers)==0){
			$this->$basket->materials = null;
		}
		$this->recalculateBasket($basket);
		$this->redrawControl("orderContainers");
		//$this->redirect("this");
	}

    public function handleSetBasketVal($index, $name, $basket="basket", $val){
		$items = $this->$basket->containers;
		$items[$index]->$name = $val;
		if($name=="term"){
			unset($items[$index]->time);
		}
		if(!empty($items[$index]->product) && !empty($items[$index]->type)){
			$price = $this->productManager->findActualPrice($items[$index]->product, $items[$index]->type);
			if($price){
				$items[$index]->price = $this->rowToArray($price);
			}
			else{
				$items[$index]->price = null;
			}
		}
		$this->$basket->containers = $items;
		$this->recalculateBasket($basket);
		if(in_array($name, array("term"))!==false){
			$this->redrawControl("orderContainers");
		}
    }

    public function handleSetOrderVal($name, $basket="basket", $val){
		$this->$basket->$name = $val;
		$this->recalculateBasket($basket);
    }

    public function handleSetMoreToDemand($val){
		if(empty($this->basketD->more)){
			$more = array();
		}
		else{
			$more = $this->basketD->more;
		}
		$more[$val] = true;
		$this->basketD->more = $more;
		//$this->recalculateBasket();
    }

    public function handleUnsetMoreToDemand($val){
		if(empty($this->basketD->more)){
			$more = array();
		}
		else{
			$more = $this->basketD->more;
		}
		unset($more[$val]);
		$this->basketD->more = $more;
		//$this->recalculateBasket();
    }

    public function renderOrder2(){
		$this->template->items = $this->basket->items;
	}

    public function renderSummary(){
		$this->template->items = $this->basket->items;
		$this->template->products = $this->basket->products;
		$this->template->sizesS = $this->basket->sizes;
		$this->template->materialsS = $this->basket->materials;
		$this->template->amountsS = $this->basket->amounts;
		$this->template->borders = $this->basket->borders;
		$this->template->order = $this->basket->order;
    	$totalPhotos = 0;
    	if(!empty($this->basket->amounts)){
            foreach($this->basket->amounts as $amount){
                $totalPhotos += $amount;
            }
        }
    	$this->template->totalPhotos = $totalPhotos;
    	//$this->template->minPhotos = MIN_PHOTOS_TO_PRINT;
	}

    public function actionFinishOrder($p=false){

    	$items = 0;
    	foreach($this->basket->amounts as $amount){
			$items += $amount;
    	}
    	/*
    	if($items<MIN_PHOTOS_TO_PRINT){
			$this->flashMessageError("Objednávka tisku musí obsahovat alespoň ".MIN_PHOTOS_TO_PRINT." fotek");
			$this->redirect("this");
    	}
    	*/

		$this->basket->order->delivery = $this->deliveries[$this->basket->order->delivery];
		$this->basket->order->payment = $this->payments[$this->basket->order->payment];
		$orderId = $this->orderManager->saveOrder($this->basket, $this->sizes);

        if(!empty($this->basket->voucher) && !$this->basket->voucher->unlimited){
            $this->voucherManager->update(array("used"=>new \Nette\Utils\DateTime()), $this->basket->voucher->id);
        }

		$this->basket->remove();

		$order = $this->orderManager->find($orderId);

		$items = $this->orderManager->findOrderItems($orderId);
		$products = $this->orderManager->findOrderProducts($orderId);
		$albums = array();
		foreach($items as $item){
			if(empty($albums[$item->folderId])){
				$album = $this->projectManager->findByHash($item->folderId);
				$albums[$item->folderId] = new \nette\utils\arrayHash();
				$albums[$item->folderId]->branch = $album->branch;
				$albums[$item->folderId]->id = $album->id;
				$albums[$item->folderId]->custom = $album->custom;
				$folder = $this->getGfile($item->folderId);
				$albums[$item->folderId]->folder = $folder->name;
			}
		}

		$data = array(
			"order" => $order,
			"items" => $items,
			"products" => $products,
			"albums" => $albums,
			"branches" => $this->branchesSimple,
			"sizes" => $this->sizes,
			"materials" => $this->materials,
			"deliveries" => $this->deliveries,
			"payments" => $this->payments,
		);

		//TODO generate mail
		$this->sendMailFromTemplate("orderStatus1.latte", $data, $order->email, "Potvrzení objednávky tisku fotek");
		$this->sendMailFromTemplate("orderConfirmEshop.latte", $data, $this->settings->email, "Nová objednáva tisku fotek");
        
        //cleanup user album
        $this->cleanupUserAlbumAfterOrder($orderId);

		if(!$p){
			$this->redirect(":Front:Homepage:page", "dekujeme-za-objednavku");
		}
		else{
			$this->payOrder($orderId);
			//$this->redirect(":Front:Homepage:payment", $orderId);
		}
	}
    
    public function cleanupUserAlbumAfterOrder($orderId){
       $orderedCustomPhotos = $this->orderManager->findOrderCustomPhotos($orderId); 
       foreach($orderedCustomPhotos as $folrderId=>$orderedItems){
           $folderItems = $this->getGfiles($folrderId);
           foreach($folderItems as $folderItem){
            if(array_search($folderItem->getId(), $orderedItems)===false){
                //delete file from folder
                $this->deleteGfolder($folderItem->getId());
            }
           }
       }
       $albumSes = $this->getSession("album");
       $albumSes->album = null;
    }


    public function actionPage($id=""){
        //detect product or category
        $product = $this->productManager->findByAlias($id);
        $category = $this->categoryManager->findByAlias($id);
        if($product && $product->type==1){
			$this->setview("container");
        }
        else if($category){
			if($category->type==1){
				$this->setview("containers");
			}
			else{
				$this->setview("materials");
			}
        }
        else{
	        $page = $this->template->page = $this->pageManager->findByAlias($id);
	        if(!$page){
				$this->redirect(":front:Homepage:page", "stranka-nenalezena");
	        }
	        $this->template->title = $page->title;
	        $this->template->keywords = $page->seo_keywords;
	        $this->template->description = $page->seo_description;
	        $this->setview($page->view);
	        if($page->parent){
	            $parent = $this->pageManager->find($page->parent);
	            //$this->addBreadcrumbs($parent->name, $this->link(":Front:Homepage:page", $parent->alias));
	        }
	        //$this->addBreadcrumbs($page->name, $this->link(":Front:Homepage:page", $id));
	        $this->template->images = $this->pageManager->getPhotos($page->id);
        }
    }
    
    public function renderContainers($id=""){
	    $page = $this->template->page = $this->pageManager->findByLayout(8);
	    $this->template->title = $page->title;
	    $this->template->keywords = $page->seo_keywords;
	    $this->template->description = $page->seo_description;

	    if(!empty($id)){
			$category = $this->categoryManager->findByAlias($id);
		    if($category){
			    $this->template->title = $category->title;
			    $this->template->description = $category->seo_description;
		    }
			if(!empty($category->attVal)){
				$_GET["a1"] = $category->attVal;
			}
	    }
    	
    	$containers = $this->productManager->getByType(1);
    	foreach($_GET as $key=>$val){
			if(!empty($val) && $key[0]=="a"){
				$attr = substr($key, 1, 9);
				if($attr==1){
					$containers->where("id IN (SELECT product FROM product_prices WHERE attributeValue=".$attr." AND priceFrom>0)");
				}
				else{
					$attrPair = $attr."-".$val;
					$containers->where("attributes LIKE '%".$attrPair."%'");
				}
			}
			if($key=="t" && $val=="on"){
				$containers->where("turbo = ?", true);
			}
    	}
    	$this->template->containers = $containers;
    	$this->template->attVals = $this->attributeManager->getAllValues();
    }
    public function renderMaterials($id=""){
	    $page = $this->template->page = $this->pageManager->findByLayout(9);
	    $this->template->title = $page->title;
	    $this->template->keywords = $page->seo_keywords;
	    $this->template->description = $page->seo_description;

	    if(!empty($id)){
			$category = $this->categoryManager->findByAlias($id);
		    if($category){
			    $this->template->title = $category->title;
			    $this->template->description = $category->seo_description;
		    }
	    }

    	$containers = $this->productManager->getByType(2);
    	foreach($_GET as $key=>$val){
			if(!empty($val) && $key[0]=="a"){
				$attr = substr($key, 1, 9);
				if($attr==1){
					$containers->where("id IN (SELECT product FROM product_prices WHERE attributeValue=".$attr." AND priceFrom>0)");
				}
				else{
					$attrPair = $attr."-".$val;
					$containers->where("attributes LIKE '%".$attrPair."%'");
				}
			}
    	}
    	$this->template->containers = $containers;
    	$this->template->attVals = $this->attributeManager->getAllValues();
    }
    public function renderContainer($id){
    	$this->template->bodyClassPage = "container-detail";
    	$this->template->product = $container = $this->productManager->findByAlias($id);
    	$this->template->attVals = $this->attributeManager->getAllValues();
    	$this->template->paVals = $this->getProductAttributeValues($container->attributes);
    	$this->template->mainImg = $this->productManager->getMainPhoto($container->id);
    	$this->template->secondImg = $this->productManager->getSecondPhoto($container->id);
	    $this->template->title = $container->title;
	    $this->template->description = $container->seo_description;
	}
	
    public function getProductAttributeValues($source){
		$values = $this->attributeManager->getAllValues();
		$return = array();
		$pairs = explode("|", $source);
		foreach($pairs as $pair){
			if(!empty($pair)){
				list($attr, $val) = explode("-", $pair);
				$return[$attr] = $values[$val];
			}
		}
		return $return;
    }


	public function actionPayment($id = null, $pay = null, $paymentSessionId=null, $targetGoId=null, $orderNumber=null, $encryptedSignature=null)
	{
        if(!empty($pay)){
            //$this->orderManager->update(array("pay_status"=>5), $id);
            $order = $this->orderManager->findByPaymentId($paymentSessionId);
            if($pay = 1){
                $payment = $this->paymentService->restorePayment([
                    'sum'         => $order->price,
                    'variable'    => $order->id,
                    'productName' => "Objednávka tisku",
                ], [
                    'paymentSessionId'   => $paymentSessionId,
                    'targetGoId'         => $targetGoId,
                    'orderNumber'        => $orderNumber,
                    'encryptedSignature' => $encryptedSignature,
                ]);
                if($payment->isPaid()){
                    $this->orderManager->update(array("paid"=>true), $order->id);
                    //generate invoice
                    $invoiceId = $this->makeInvoice($order->id);
	                //send invoice
	                $invoicePdfData = $this->getInvoicePdf($invoiceId);
	                $template = $this->createTemplate();
	                $template->setFile(APP_DIR . '/FrontModule/templates/Mails/sendInvoice.latte');


	                $mail = new Message;
	                $mail->setFrom($this->settings->email)
	                    ->addTo($order->email)
	                    ->setSubject("Daňový doklad za tisk fotek")
	                    ->setHtmlBody($template);
	                $mail->addAttachment('faktura.pdf', $invoicePdfData);
	                $mailer = new SendmailMailer;
	                $mailer->send($mail);

	                $this->orderManager->updateByInvoiceId(array("sended"=>true), $invoiceId);

                    $this->template->status = 1;
                    $this->redirect("paymentDone");
                }
                else{
                    $this->template->order = $order;
                    $this->template->status = 9;
                }
            }
            else{
                $this->template->order = $order;
                $this->template->status = 9;
            }
        }
        $this->template->order = $this->orderManager->find($id);
	}

    public function handlePayOrder($orderId){
        $this->payOrder($orderId);
    }

    public function createComponentOrderForm(){
        $form = new Form();

        //SYSTEM
        $form ->addHidden("type");
        $form ->addHidden("usertype")->setDefaultValue(0);
        
        //PERSONAL
        $form ->addText("name", "Jméno")
                ->getControlPrototype()->class("form-control");
        $form ->addText("surname", "Příjmení")
                ->getControlPrototype()->class("form-control");
        $form ->addText("email", "E-mail")
                ->getControlPrototype()->class("form-control");
        $form ->addText("phone", "Telefon")
                ->getControlPrototype()->class("form-control");
		
		//PERSONAL valudations
        $form["name"]
        	->addConditionOn($form['usertype'], Form::EQUAL, 1)
        	->addRule(Form::FILLED, "Vyplňte jméno");
        $form["surname"]
        	->addConditionOn($form['usertype'], Form::EQUAL, 1)
        	->addRule(Form::FILLED, "Vyplňte příjmení");
        $form["email"]
        	->addConditionOn($form['usertype'], Form::EQUAL, 1)
        	->addRule(Form::FILLED, "Vyplňte e-mail")
        	->addRule(Form::EMAIL, "Vyplňte platný e-mail");
        $form["phone"]
        	->addConditionOn($form['usertype'], Form::EQUAL, 1)
        	->addRule(Form::FILLED, "Vyplňte telefonní číslo");

        $form ->addTextArea("note", "Poznámka", 30, 5)
                ->getControlPrototype()->class("form-control");

		//different delivery personal
        $form -> addCheckbox("different_delivery", "Fakturační adresa je jiná než adresa přistavení")
                ->getControlPrototype()->class("differentDelivery form-check-input");
        $form["different_delivery"]->getLabelPrototype()->class("form-check-label");

        $form ->addText("delivery_street", "Ulice a číslo popisné")
                ->getControlPrototype()->class("form-control");
        $form["delivery_street"]
        	->addConditionOn($form['different_delivery'], Form::EQUAL, TRUE)
        	->addRule(Form::FILLED, "Vyplňte ulici a číslo popisné");
        $form ->addText("delivery_city", "Město")
                ->getControlPrototype()->class("form-control");
        $form["delivery_city"]
        	->addConditionOn($form['different_delivery'], Form::EQUAL, TRUE)
        	->addRule(Form::FILLED, "Vyplňte město");
        $form ->addText("delivery_zip", "PSČ")
                ->getControlPrototype()->class("form-control");
        $form["delivery_zip"]
        	->addConditionOn($form['different_delivery'], Form::EQUAL, TRUE)
        	->addRule(Form::FILLED, "Vyplňte PSČ");

        //COMPANY
        $form ->addText("ic", "IČ")
                ->getControlPrototype()->class("form-control idField");
        $form ->addText("bussiness_name", "Jméno")
                ->getControlPrototype()->class("form-control");
        $form ->addText("bussiness_surname", "Příjmení")
                ->getControlPrototype()->class("form-control");
        $form ->addText("bussiness_email", "E-mail")
                ->getControlPrototype()->class("form-control");
        $form ->addText("bussiness_phone", "Telefon")
                ->getControlPrototype()->class("form-control");
        $form ->addTextArea("bussiness_note", "Poznámka", 30, 5)
                ->getControlPrototype()->class("form-control");
        $form ->addText("bussiness_company", "Název firmy")
                ->getControlPrototype()->class("form-control");

		//COMPANY valudations
        $form["ic"]
        	->addConditionOn($form['usertype'], Form::EQUAL, 0)
        	->addRule(Form::FILLED, "Vyplňte IČ");
        $form["bussiness_name"]
        	->addConditionOn($form['usertype'], Form::EQUAL, 0)
        	->addRule(Form::FILLED, "Vyplňte jméno");
        $form["bussiness_surname"]
        	->addConditionOn($form['usertype'], Form::EQUAL, 0)
        	->addRule(Form::FILLED, "Vyplňte příjmení");
        $form["bussiness_email"]
        	->addConditionOn($form['usertype'], Form::EQUAL, 0)
        	->addRule(Form::FILLED, "Vyplňte e-mail")
        	->addRule(Form::EMAIL, "Vyplňte platný e-mail");
        $form["bussiness_phone"]
        	->addConditionOn($form['usertype'], Form::EQUAL, 0)
        	->addRule(Form::FILLED, "Vyplňte telefonní číslo");


		//different delivery company
        $form -> addCheckbox("bussiness_different_delivery", "Fakturační adresa je jiná než adresa přistavení")
                ->getControlPrototype()->class("differentDeliveryBussiness form-check-input");
        $form["bussiness_different_delivery"]->getLabelPrototype()->class("form-check-label");

        $form ->addText("bussiness_delivery_street", "Ulice a číslo popisné")
                ->getControlPrototype()->class("form-control");
        $form["bussiness_delivery_street"]
        	->addConditionOn($form['bussiness_different_delivery'], Form::EQUAL, TRUE)
        	->addRule(Form::FILLED, "Vyplňte ulici a číslo popisné");
        $form ->addText("bussiness_delivery_city", "Město")
                ->getControlPrototype()->class("form-control");
        $form["bussiness_delivery_city"]
        	->addConditionOn($form['bussiness_different_delivery'], Form::EQUAL, TRUE)
        	->addRule(Form::FILLED, "Vyplňte město");
        $form ->addText("bussiness_delivery_zip", "PSČ")
                ->getControlPrototype()->class("form-control");
        $form["bussiness_delivery_zip"]
        	->addConditionOn($form['bussiness_different_delivery'], Form::EQUAL, TRUE)
        	->addRule(Form::FILLED, "Vyplňte PSČ");


        $form->addSubmit("submit", "Odeslat objednávku")->getControlPrototype()->class("btn btn-primary btn-arrow btn-white");

        $form->onSuccess[] = [$this, 'saveOrder'];

        return $form;
    }


    /** callback for page form
    *
    * @param Form data from page form
    * @return void
    */
    public function saveOrder(Form $form){
        $values = $form->getValues();
        
        $basketfield = "basket";
		if($values->type == 9){
			$basketfield = "basketD";
		}
		if($values->type == 2){
			$basketfield = "basketM";
		}

        if($values->type==1 && count($this->basket->containers)==0){
			$form->addError("Objednávka neobsahuje žádné kontejnery");
        }

        if($form->isValid()){
            try{
                $values->date = new \nette\utils\DateTime();
				/*
				if(!empty($values->payment)){
					$values->paymentPrice = (!empty($this->paymentPrices[$values->payment])?$this->paymentPrices[$values->payment]:0);
				}
				if(!empty($values->delivery)){
					$values->deliveryPrice = (!empty($this->deliveryPrices[$values->delivery])?$this->deliveryPrices[$values->delivery]:0);
				}
				*/
                unset($values->usertype);
                $this->$basketfield->order = $values;

				$orderId = $this->orderManager->saveOrder($this->$basketfield, $this->productManager, $this->settings);

				$this->$basketfield->remove();

				$order = $this->orderManager->find($orderId);

				$products = $this->orderManager->findOrderProducts($orderId);

				$data = array(
					"order" => $order,
					"products" => $products,
					"beton" => $this->settings->betonProduct,
					//"deliveries" => $this->deliveries,
					//"payments" => $this->payments,
				);

				//TODO generate mail
				//$this->sendMailFromTemplate("orderStatus1.latte", $data, $order->email, "Potvrzení objednávky kontejneru");
				if($values->type==1){
					$this->sendMailFromTemplate("orderConfirmEshop.latte", $data, $this->settings->email, "Nová objednáva kontejneru");
					if(!empty($values->email)){
						$this->sendMailFromTemplate("orderConfirmEshop.latte", $data, $values->email, "Nová objednáva kontejneru");
					}
					if(!empty($values->bussiness_email)){
						$this->sendMailFromTemplate("orderConfirmEshop.latte", $data, $values->bussiness_email, "Nová objednáva kontejneru");
					}
				}
				if($values->type==2){
					$this->sendMailFromTemplate("orderMatConfirmEshop.latte", $data, $this->settings->email, "Nová objednáva materiálu");
					if(!empty($values->email)){
						$this->sendMailFromTemplate("orderMatConfirmEshop.latte", $data, $values->email, "Nová objednáva materiálu");
					}
					if(!empty($values->bussiness_email)){
						$this->sendMailFromTemplate("orderMatConfirmEshop.latte", $data, $values->bussiness_email, "Nová objednáva materiálu");
					}
				}
				if($values->type==9){
					$this->sendMailFromTemplate("demandConfirmEshop.latte", $data, $this->settings->email, "Nová poptávka");
					if(!empty($values->email)){
						$this->sendMailFromTemplate("demandConfirmEshop.latte", $data, $values->email, "Nová poptávka");
					}
					if(!empty($values->bussiness_email)){
						$this->sendMailFromTemplate("demandConfirmEshop.latte", $data, $values->bussiness_email, "Nová poptávka");
					}
				}

				if($values->type==9){
                	$this->redirect(":Front:Homepage:page", "dekujeme-za-poptavku");
				}
				else{
                	$this->redirect(":Front:Homepage:page", "dekujeme-za-objednavku");
				}
            }
            catch(DibiDriverException $e){
                $this->flashMessage($e->getMessage(), "error");
            }
        }
    }
    
    public function createComponentAddToOrderForm(){
        $form = new Form();

        $form ->addHidden("isContainer", "");
        
        $form->onSuccess[] = [$this, 'createOrder'];

        return $form;
    }


    /** callback for page form
    *
    * @param Form data from page form
    * @return void
    */
    public function createOrder(Form $form){
        $values = $form->getValues();
	   	$request = $this->getHttpRequest();
	   	$amounts = $request->getPost("amounts");
	   	$prices = $request->getPost("prices");
        if($values->isContainer=="1"){
	        $items = array();
	        foreach($prices as $priceId=>$value){
				if($value=="on"){
					$amount = $amounts[$priceId];
					for($i=1;$i<=$amount;$i++){
						$price = $this->productManager->findPrice($priceId);
						$item = new \Nette\Utils\ArrayHash();
						$item->type = $price->attributeValue;
						$item->product = $price->product;
						$item->price = $this->rowToArray($price);
						$items[] = $item;
					}
				}
	        }
	        $this->basket->containers = $items;
            
			$this->recalculateBasket();

            $containerOrderPage = $this->pageManager->findByLayout(11);
            $this->redirect(":Front:Homepage:page", $containerOrderPage->alias);
        }
        if($values->isContainer=="2"){
	        $items = array();
	        foreach($prices as $priceId=>$value){
				if($value=="on"){
					$amount = $amounts[$priceId];
					$price = $this->productManager->findPrice($priceId);
	        		if(empty($items[$price->product])){
						$items[$price->product] = array();
	        		}
					$item = new \Nette\Utils\ArrayHash();
					$item->amount = $amount;
					$item->priceObj = $this->rowToArray($price);
					$items[$price->product][$priceId] = $item;
				}
	        }
	        $this->basketM->materials = $items;
            
			$this->recalculateBasket();

            $containerOrderPage = $this->pageManager->findByLayout(15);
            $this->redirect(":Front:Homepage:page", $containerOrderPage->alias);
        }
    }
    
    public function createComponentUpload(){
        $form = new Form();
        
        $form ->addMultiUpload("img", "Soubor obrázku")
            ->setRequired(true)
            ->addRule(Form::MIME_TYPE, 'Požadovaný soubor musí být ve formátu JPG nebo TIFF.', 'image/jpeg,image/tiff')
            //->addRule(Form::MAX_LENGTH, 'Najednou lze nahrát maximálně %d souborů', 10)
            //->addRule(Form::IMAGE, 'Soubor musí být JPG, JPEG, PNG nebo GIF.')
            ;
        $form ->addSubmit("submit", "Nahrát")
            ->getControlPrototype()->class("btn btn-primary");

        $form->onSuccess[] = [$this, 'savePhoto'];


        return $form;
    }
    
    public function actionUploadFile(){
    	
    	$albumSes = $this->getSession("album");
    	if(empty($albumSes->album)){
			$this->createAlbum();
    	}

		$fileTypes = array('jpg','jpeg','tif','tiff'); // Allowed file extensions

		$verifyToken = md5('unique_salt' . $_POST['timestamp']);

		if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
			$tempFile   = $_FILES['Filedata']['tmp_name'];

			// Validate the filetype
			$fileParts = pathinfo($_FILES['Filedata']['name']);
			$ext= strtolower($fileParts['extension']);
			if (in_array($ext, $fileTypes)) {
				// Save the file
		        $name = $this->generateString(15);
		        $fileName = "usr_".$name.".".$ext;
		        $albumSes = $this->getSession("album");
		        $album = $albumSes->album;
		        $gFile = $this->uploadGfile($fileName, $tempFile, $album);
		        if($ext=="tif" || $ext=="tiff"){
					$this->generateJpegForTif($gFile->getId(), $album);
		        }
				echo 1;

			} else {

				// The file type wasn't allowed
				echo 'Invalid file type.';

			}
		}
		$this->terminate();
    }
    
    public function generateJpegForTif($fileId, $album){
		$file = $this->getGfile($fileId);
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
			$this->uploadGfile($jpegName, $jpegPath, $album);
		}
    }

    public function savePhoto(Form $form){
        $values = $form->getValues();

        if ($form->isValid()) {
            // submitted and valid
            $values = $form->getValues();
            /*
             * Kontrola, zda-li byl obrazek skutecne nahran
             */
            if(!empty($values['img'])){
                foreach($values['img'] as $image){
                    if ($image->isOk()) {
                        $ext = pathinfo($image->getSanitizedName(), PATHINFO_EXTENSION);
                        $name = $this->generateString(15);
                        $fileName = $name.".".$ext;
                        $tempFile = BASE_DIR."/data/temp/".$fileName;
                        $image->move($tempFile);

                        //resize and move
                        //$bigImage = Image::fromFile($tempFile);
                        //$bigImage->resize(1600, 1200, Image::SHRINK_ONLY);
                        //$bigImage->save($tempFile);

                        $albumSes = $this->getSession("album");
                        $album = $albumSes->album;
                        $this->uploadGfile($fileName, $tempFile, $album);

                        if(file_exists($tempFile))
                            unlink($tempFile);

                    }
                }
            }
        }
        $this->redirect("this");

    }

    public function createComponentVoucherForm(){
        $form = new Form($this->lang);

        //$form->getElementPrototype()->class("ajax");
        $form ->addText("voucher", "Vložte kód kupónu")
                ->getControlPrototype()->class("form-control")->placeholder("Vložte kód kupónu");

        $form->addSubmit("submit", "Použít slevový kupón")->getControlPrototype()->class("btn btn-info");

        if(!empty($this->basket->details)){
            $form->setDefaults($this->basket->details);
        }

        $form->onSuccess[] = [$this, 'useVoucher'];

        return $form;
    }

    /** callback for contact form
    *
    * @param Form data from contact form
    * @return void
    */
    public function useVoucher(Form $form, $values){

        $exists = $this->voucherManager->findByCode($values->voucher);
        $now = new \nette\utils\DateTime();
        if(!$exists){
            $form["voucher"]->addError("Slevový kód není platný");
        }
        elseif(!empty($exists->validTo) && $exists->validTo<$now){
            $form["voucher"]->addError("Slevový kód již není platný");
        }
        elseif(!$exists->unlimited && !empty($exists->used)){
            $form["voucher"]->addError("Slevový kód již byl použit");
        }
        else{
            $exists = \Nette\Utils\ArrayHash::from($exists->toArray());
            $this->basket->voucher = $exists;
            $this->flashMessage("Slevový kód byl úspěšně použit");
        	$this->redirect("this");
        }

        //$this->recalculateBasket();


    }

    public function handleDownloadZip($id, $format="JPEG", array $images){
    	$album = $this->projectManager->find($id);
    	$files = $this->getGfiles($album->hash);
		$toZip = array();
		foreach($files as $file){
			$ext = pathinfo($file->getName(), PATHINFO_EXTENSION);
			$name = pathinfo($file->getName(), PATHINFO_FILENAME);
			if( ($format=="TIF" && in_array(strtolower($ext), array("tif", "tiff"))) || ($format=="JPEG" && in_array(strtolower($ext), array("jpg", "jpeg")))){
				if(in_array($file->getId(), $images)){
					$toZip[$name] = $file;
				}
			}
		}

		$dir = DATA_DIR.DIRECTORY_SEPARATOR."temp".DIRECTORY_SEPARATOR;
        $zipname = "fotografie_".$format."_".$id.".zip";
		if(file_exists($dir . $zipname)){
            unlink($dir . $zipname);
		}
        $zip = new \ZipArchive;
        $zip->open($dir . $zipname, \ZipArchive::CREATE);
        foreach ($toZip as $file) {
			$content = $this->getGfileMedia($file->getId());
            $f = fopen($dir.$file->getName(), "w");
            fputs($f, $content);
            fclose($f);
            $zip->addFile($dir.$file->getName(),$file->getName());
            //$zip->addFromString($content,$file->getName());
		}
        $zip->close();
        foreach ($toZip as $file) {
			if(file_exists($dir.$file->getName())){
            	unlink($dir.$file->getName());
			}
		}

		if(file_exists($dir . $zipname)){
	        $ret = array("url"=>$this->link("//:Front:Homepage:page") . "data/temp/". $zipname);
	        die(json_encode($ret));
	        //$response = new \Nette\Application\Responses\FileResponse($dir . $zipname, $zipname, 'application/zip', false);
	        //$this->sendResponse($response);
		}
		$this->terminate();
    }
    
    public function handleMergeRows(){
		$items = $this->basket->items;
		$sizesS = $this->basket->sizes;
		$materialsS = $this->basket->materials;
		$amountsS = $this->basket->amounts;
		$exists = array();
		
		foreach($items as $key=>$fileId){
			$unique = $fileId."-".$sizesS[$key]."-".$materialsS[$key];
			if(!isset($exists[$unique])){
				$exists[$unique] = $key;
			}
			else{
				$amountsS[$exists[$unique]] += $amountsS[$key];
				unset($items[$key]);
				unset($sizesS[$key]);
				unset($materialsS[$key]);
				unset($amountsS[$key]);
			}
		}
		
		$this->basket->items = $items;
		$this->basket->sizes = $sizesS;
		$this->basket->materials = $materialsS;
		$this->basket->amounts = $amountsS;

		$this->redirect("order2");
    }
    
    public function actionAres($ico){
        define('ARES','http://wwwinfo.mfcr.cz/cgi-bin/ares/darv_bas.cgi?ico=');
        $file = @file_get_contents(ARES.$ico);
        if ($file) $xml = @simplexml_load_string($file);
        $a = array();
        if ($xml) {
         $ns = $xml->getDocNamespaces();
         $data = $xml->children($ns['are']);
         $el = $data->children($ns['D'])->VBAS;
         if (strval($el->ICO) == $ico) {
          $a['ico']     = strval($el->ICO);
          $a['dic']     = strval($el->DIC);
          $a['firma']     = strval($el->OF);
          $a['ulice']    = strval($el->AA->NU).' '.strval($el->AA->CO);
          $a['mesto']    = strval($el->AA->N);
          $a['psc']        = strval($el->AA->PSC);
          $a['zeme']    = strval($el->AA->NS);
          $a['stav']     = 'ok';
         } else
          $a['stav']     = 'IČ firmy nebylo nalezeno';
        } else
         $a['stav']     = 'Databáze ARES není dostupná';
        $this->payload->data = $a;
        $this->sendPayload();
        $this->terminate();
    }

    

}
