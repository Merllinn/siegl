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

    public function renderOrder(){
		$this->template->items = $this->basket->items;
		$this->template->products = $this->basket->products;
		$this->template->itemNames = $this->basket->itemNames;
		$this->template->sizesS = $this->basket->sizes;
		$this->template->materialsS = $this->basket->materials;
		$this->template->amountsS = $this->basket->amounts;
		$this->template->borders = $this->basket->borders;
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
        $page = $this->template->page = $this->pageManager->findByAlias($id);
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
    
    public function renderContainers(){
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
    public function renderMaterials(){
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
    public function actionContainer($id){
    	$this->template->product = $container = $this->productManager->findByAlias($id);
    	$this->template->attVals = $this->attributeManager->getAllValues();
    	$this->template->paVals = $this->getProductAttributeValues($container->attributes);
    	$this->template->mainImg = $this->productManager->getMainPhoto($container->id);
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

        $form ->addText("name", "Jméno *")
                ->getControlPrototype()->class("form-control");
        $form["name"]->addRule(Form::FILLED, "Vyplňte jméno");
        $form ->addText("surname", "Příjmení *")
                ->getControlPrototype()->class("form-control");
        $form["surname"]->addRule(Form::FILLED, "Vyplňte příjmení");
        $form ->addText("email", "E-mail")
                ->getControlPrototype()->class("form-control");
        $form["email"]->setRequired(true)->addRule(Form::EMAIL, "Vyplňte e-mail");
        $form ->addText("phone", "Telefonní číslo *")
                ->getControlPrototype()->class("form-control");
        $form["phone"]->setRequired(true)->addRule(Form::FILLED, "Vyplňte telefonní číslo");
        $form ->addText("street", "Ulice a číslo popisné *")
                ->getControlPrototype()->class("form-control");
        $form["street"]->addRule(Form::FILLED, "Vyplňte ulici a číslo popisné");
        $form ->addText("city", "Město *")
                ->getControlPrototype()->class("form-control");
        $form["city"]->addRule(Form::FILLED, "Vyplňte město");
        $form ->addText("zip", "PSČ *")
                ->getControlPrototype()->class("form-control");
        $form["zip"]->addRule(Form::FILLED, "Vyplňte PSČ");

        $form ->addTextArea("note", "Poznámka", 30, 2)
                ->getControlPrototype()->class("form-control");

        $form -> addCheckbox("different_delivery", "Přejete si odeslat zboží na jinou adresu?")
                ->getControlPrototype()->class("differentDelivery form-check-input");
        $form["different_delivery"]->getLabelPrototype()->class("form-check-label");

        $form ->addText("delivery_name", "Jméno")
                ->getControlPrototype()->class("form-control");
        $form["delivery_name"];
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

		$deliveries = array();
		foreach($this->deliveries as $id=>$name){
			$price = !empty($this->deliveryPrices[$id])?$this->deliveryPrices[$id]:0;
			$deliveries[$id] = $name." - ".$price." Kč";
		}
        $form->addRadioList("delivery", "", $deliveries)
                ->getControlPrototype()->class("form-check-input");
        $form["delivery"]->addRule(Form::FILLED, "Vyberte způsob dopravy");

		$payments = array();
		foreach($this->payments as $id=>$name){
			$price = !empty($this->paymentPrices[$id])?$this->paymentPrices[$id]:0;
			$payments[$id] = $name." - ".$price." Kč";
		}
        $form->addRadioList("payment", "", $payments)
                ->getControlPrototype()->class("form-check-input");
        $form["payment"]->addRule(Form::FILLED, "Vyberte způsob platby");

        $form -> addCheckbox("framing", "Pošlete mi nabídku rámování")
                ->getControlPrototype()->class("form-check-input");
        $form["different_delivery"]->getLabelPrototype()->class("form-check-label");

        $form->addSubmit("submit", "Dokončit objednávku")->getControlPrototype()->class("btn btn-primary");

        $form->onSuccess[] = [$this, 'saveOrder'];

        if(!empty($this->basket->order)){
        	$form->setDefaults($this->basket->order);
		}

        return $form;
    }


    /** callback for page form
    *
    * @param Form data from page form
    * @return void
    */
    public function saveOrder(Form $form){
        $values = $form->getValues();

        if($form->isValid()){
            try{
                $values->date = new \nette\utils\DateTime();
				if(!empty($values->payment)){
					$values->paymentPrice = (!empty($this->paymentPrices[$values->payment])?$this->paymentPrices[$values->payment]:0);
				}
				if(!empty($values->delivery)){
					$values->deliveryPrice = (!empty($this->deliveryPrices[$values->delivery])?$this->deliveryPrices[$values->delivery]:0);
				}
                $this->basket->order = $values;
                $this->redirect(":Front:Homepage:summary");
            }
            catch(DibiDriverException $e){
                $this->flashMessage($e->getMessage(), "error");
            }
        }
    }
    
    public function handleSetBorder($itemId, $set=true){
		$borders = $this->basket->borders;
		$borders[$itemId] = $set;
		$this->basket->borders = $borders;		
		$this->redirect("this");
    }

    public function handleSetBorders(array $items, $set=true){
		$borders = $this->basket->borders;
		foreach($items as $itemId){
			$borders[$itemId] = $set;
		}
		$this->basket->borders = $borders;		
		$this->redirect("this");
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

}
