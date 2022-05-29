<?php

namespace App\FrontModule\Presenters;

use App\Model;
use Nette;
use TH\Form,
	TH\Translator;
use Nette\Utils\Image;
use BulkGate;
use BulkGate\Sms\SenderSettings;
use FlexibeeClient\FlexibeeClient;
use FlexibeeClient\Registry;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

	 /** @persistent */
	public $lang;

	public $languages;
	public $translator;

    public $breadcrumbs=array();
    public $settings;
    public $basket;
    public $basketD;
    public $basketM;
    public $basketMap;
    public $onlineSale = 0;
    
    public $zones = 8;
    
    public $vatSes;
    
	public $contactCategories = array(50=>"Vedení společnosti", 30=>"Administrativa", 40=>"Dispečink", 10=>"Řidiči", 20=>"Strojníci");
    

    /** @var Model\CommonManager  @inject */
    public $commonManager;
    /** @var Model\ProductManager  @inject */
    public $productManager;
    /** @var Model\CategoryManager  @inject */
    public $categoryManager;
    /** @var Model\PageManager  @inject */
    public $pageManager;
    /** @var Model\ProjectManager  @inject */
    public $projectManager;
    /** @var Model\OrderManager  @inject */
    public $orderManager;
    /** @var Model\OrderStatusManager  @inject */
    public $orderStatusManager;
    /** @var Model\VoucherManager  @inject */
    public $voucherManager;
	/** @var Model\TranslateManager  @inject */
	public $translateManager;
	/** @var Model\UserManager  @inject */
	public $userManager;
    /** @var Model\LanguageManager  @inject */
    public $languageManager;
    /** @var Model\AttributeManager  @inject */
    public $attributeManager;

    /** @var BulkGate\Sms\ISender @inject */
    public $sender;

	public function startup(){
		parent::startup();
		
		$this->lang = 'cz';
        
        $this->template->rootFolder = FOLDER;

        $this->settings = $this->template->settings = $this->commonManager->getSettings();

        // set translator for presenters
        $this->translator = new Translator($this->translateManager,$this->lang);
        $this->template->setTranslator($this->translator);
		$this->languages = $this->translateManager->getLanguages();

        $this->basket = $this->getSession("basket");
        $this->basketD = $this->getSession("basket-demand");
        $this->basketM = $this->getSession("basket-material");
        $this->basketMap = $this->getSession("basket-map");

        $this->template->topPages = $this->pageManager->get()->where("location = ?", 0)->where("active = ?", true)->where("parent IS NULL");
        $this->template->bottomPages = $this->pageManager->get()->where("location = ?", 1)->where("active = ?", true)->where("parent IS NULL");

        \Nette\Application\UI\Form::extensionMethod('addMultiUpload', function(\Nette\Application\UI\Form $form, $name, $label = NULL) {
            $form[$name] = new \Nette\Forms\Controls\MultiUploadControl($label);
            return $form[$name];
        });

        /*
        $this->template->months = array(
            1=>$this->translator->translate("Leden"),
            2=>$this->translator->translate("Únor"),
            3=>$this->translator->translate("Březen"),
            4=>$this->translator->translate("Duben"),
            5=>$this->translator->translate("Květen"),
            6=>$this->translator->translate("Červen"),
            7=>$this->translator->translate("Červenec"),
            8=>$this->translator->translate("Srpen"),
            9=>$this->translator->translate("Září"),
            10=>$this->translator->translate("Říjen"),
            11=>$this->translator->translate("Listopad"),
            12=>$this->translator->translate("Prosinec")
        );
        */
        
        $this->vatSes = $this->getSession("vat");
        if(!isset($this->vatSes->vat)){
			$this->vatSes->vat = false;
        }
        $vat = new \Nette\Utils\ArrayHash();
        $vat->isWith = $this->vatSes->vat;
        $vat->koef = 1;
        if($vat->isWith){
			$vat->koef = 1 + ($this->settings->vat/100);
        }
        $this->template->vat = $vat;


	}
	
	public function handleSetVat($isWith){
		$this->vatSes = $this->getSession("vat");
		$this->vatSes->vat = $isWith;
		$this->redirect("this");
	}

    public function beforeRender(){
        $presenter = $this;
        $this->template->addFilter('thumb', function($image, $width=null, $height=null, $method = "EXACT") use ($presenter){
            return $presenter->thumb($image, $width, $height, $method);
        });
    }


	public function flashMessageError($message){
		$this->flashMessage($message, "alert-danger");
	}

	public function flashMessage($message, $type="alert-success"){
		parent::flashMessage($message, $type);
	}

    protected function isAllowed($role="")
    {
        if (is_array($role)){
            if (!$this->user->isLoggedIn() || (array_search($this->user->identity->role, $role)===false)) {
                $this->flashMessageError("Pro přístup k požadovanému obsahu nemáte dostatečná oprávnění");
                $this->redirect(":Front:Homepage:login");
            }
        }
        else{
            if (!$this->user->isLoggedIn() || (!empty($role) && !$this->user->isInRole($role))) {
                $this->flashMessageError("Pro přístup k požadovanému obsahu nemáte dostatečná oprávnění");
                $this->redirect(":Front:Homepage:login");
            }
        }
    }

    public function addBreadcrumbs($title, $link="")
    {
        $this->breadcrumbs[] = array("title"=>$title, "link"=>$link);
        $this->template->breadcrumbs = $this->breadcrumbs;
    }

    public function actionLogin(){
		$this->template->bodyClassPage = "page";
        $page = new \Nette\Utils\ArrayHash();
        $page->name = "Přihlášení";
        $page->alias = "";
        $page->id = 0;
        $this->template->images = array();
        $this->template->page = $page;
    }
    /** handler for user logout
    *
    * @return void
    */
    public function handleLogout(){

        $this->user->logout();
        $this->flashMessage("Byl jsi odlášen.");
        $this->redirect(":Front:Homepage:page", "odhlaseni");
    }


    public function thumb($image, $width = null, $height = null, $method = "EXACT")
    {
       $methods = array(
           "FIT"=>0,
           "FILL"=>4,
           "EXACT"=>8
       );

       if(empty($image->file)){
       		$imagePath = '../obrazek-neni-k-dispozici.png';
		   //echo "Neexistující obrázek:";
		   //var_dump($image);
		   //die();
       }
       else{
       		$imagePath = $image->file;
       }
       if(($width==null || $height==null) && $method=="EXACT"){
           $method="FIT";
       }
        $uploadDir = BASE_DIR . DIRECTORY_SEPARATOR ."data/original";
        $uploadPath = FOLDER."/data/original";
        $thumbPath = BASE_DIR . DIRECTORY_SEPARATOR ."data/thumb" . DIRECTORY_SEPARATOR;
        $thumbDir = FOLDER."/data/thumb/";
        if(!file_exists($thumbPath)) mkdir($thumbPath, 0777);


        if (!is_file($uploadDir . DIRECTORY_SEPARATOR . $imagePath)) {
            $imagePath = '../obrazek-neni-k-dispozici.png';
        }

        if ($width == null && $height == null) {
            return str_replace('\\', '/', $uploadPath . '/' . $imagePath);
        }

        $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
        if($extension=="svg"){
			return $uploadPath . DIRECTORY_SEPARATOR . $image->file;
        }
        $fileSize = filesize($uploadDir . DIRECTORY_SEPARATOR . $imagePath);

        $thumbImagePath = md5($width . $height . $imagePath . $fileSize) . '.' . $extension;

        if (!file_exists($thumbPath . DIRECTORY_SEPARATOR . $thumbImagePath)) {

            $image = \Nette\Utils\Image::fromFile($uploadDir . DIRECTORY_SEPARATOR . $imagePath);
            $image->resize($width, $height, $methods[$method] | \Nette\Utils\Image::SHRINK_ONLY);

            $image->save($thumbPath . DIRECTORY_SEPARATOR . $thumbImagePath, 100);

        }

        return $thumbDir . "/" . $thumbImagePath;
    }

    public function makeAlias($entity, $name, $id=null){
        $alias = \Nette\Utils\Strings::webalize($name);
        $aliasNumber = 1;
        while($this->commonManager->existsAlias($entity, $alias, $id, $this->lang)){
            $alias = $alias."-".$aliasNumber;
            $aliasNumber++;
        }
        return $alias;
    }

    public function generateString($length){
        $string = "";
        $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
        $maxlength = strlen($possible);

        // set up a counter for how many characters are in the hash
        $i = 0;

        // add random characters to $password until $length is reached
        while ($i < $length) {
            $char = substr($possible, mt_rand(0, $maxlength-1), 1);
            $string .= $char;
            $i++;
        }
        return $string;

    }

    public function handleAddToOrder($album, $id, $name){

        $items = $this->basket->items;
        $items[] = $id;
        $itemNames = $this->basket->itemNames;
        $itemNames[$id] = $name;
        $albums = $this->basket->albums;
		$albumSes = $this->getSession("album");
        $albums[$id] = $album;
        $this->basket->albums = $albums;
        $this->basket->items = $items;
        $this->basket->itemNames = $itemNames;
        $this->flashMessage("Fotka byla přidána do objednávky");
        $this->redirect("this");
    }

    public function handleDuplicateToOrder($fileId){

        $items = $this->basket->items;
        $sizesS = $this->basket->sizes;
        $materialsS = $this->basket->materials;
        $id = $items[$fileId];
        $nextId = max(array_keys($items)) + 1;
        $items[$nextId] = $id;
        $sizesS[$nextId] = "1";
        $materialsS[$nextId] = "1";
        $this->basket->items = $items;
        $this->basket->sizes = $sizesS;
        $this->basket->materials = $materialsS;
        $this->flashMessage("Fotka byla přidána do objednávky");
        $this->redirect("this");
    }

    public function handleAddToOrderMulti($album, array $images, array $names){

        $items = $this->basket->items;
        $albums = $this->basket->albums;
		$albumSes = $this->getSession("album");
		$itemNames = $this->basket->itemNames;
		foreach($images as $imageId=>$image){
        	$items[] = $image;
        	$itemNames[$image] = $names[$imageId];
        	$albums[$image] = $album;
		}
        $this->basket->albums = $albums;
        $this->basket->items = $items;
        $this->basket->itemNames = $itemNames;
        $this->flashMessage("Fotka byla přidána do objednávky");
        $this->redirect("order");
    }

    public function handleRemoveFromOrder($id){

        $items = $this->basket->items;
        $sizesSes = $this->basket->sizes;
        $materialsSes = $this->basket->materials;

        unset($items[$id]);
        if(!empty($sizesSes[$id])){
			unset($sizesSes[$id]);
        }
        if(!empty($materialsSes[$id])){
			unset($materialsSes[$id]);
        }
        $this->basket->items = $items;
        $this->basket->sizes = $sizesSes;
        $this->basket->materials = $materialsSes;
        $this->flashMessage("Fotka byla odebrána z objednávky");
        $this->redirect("this");
    }

    public function handleAddProductToOrder($id){

        $items = $this->basket->products;
        $product = $this->productManager->find($id);
        $product = $this->rowToArray($product);
        if(empty($items[$product->id])){
	        $product->amount = 1;
	        $items[$product->id] = $product;
        }
        else{
			$items[$product->id]->amount += 1;
        }
        $this->basket->products = $items;
        $this->flashMessage("Produkt byl přidán do objednávky");
        $this->redirect("this");
    }

    public function handleRemoveProductFromOrder($id){

        $items = $this->basket->products;
        unset($items[$id]);
        $this->basket->products = $items;
        $this->flashMessage("Produkt byl odebrán z objednávky");
        $this->redirect("this");
    }

    public function handleUpdateOrder(array $sizes, array $materials, array $amounts, array $amountsProducts){

        $products = $this->basket->products;
        $sizesSes = $this->basket->sizes;
        $materialsSes = $this->basket->materials;
        $amountsSes = $this->basket->amounts;

        foreach($sizes as $item=>$size){
			$sizesSes[$item] = $size;
        }
        foreach($materials as $item=>$material){
			$materialsSes[$item] = $material;
        }
        foreach($amounts as $item=>$amount){
			$amountsSes[$item] = $amount;
        }
        foreach($amountsProducts as $item=>$amount){
			$products[$item]->amount = $amount;
        }
        $this->basket->sizes = $sizesSes;
        $this->basket->materials = $materialsSes;
        $this->basket->amounts = $amountsSes;
        $this->basket->products = $products;
    }


    public function sendSMS($number, $text, $orderId=null){
    	$type = SenderSettings\Gate::GATE_TEXT_SENDER;
		$value = 'Siegl';
		$settings = new SenderSettings\StaticSenderSettings($type, $value);

		$this->sender->setSenderSettings($settings);

		$order = $this->orderManager->find($orderId);
		$branch = $this->branchManager->findByCity($order->branch);

        $text = str_replace("[ORDERID]", $orderId, $text);
        $text = str_replace("[BRANCH-NAME]", $branch->name, $text);
        $text = str_replace("[BRANCH-ADDRESS]", $branch->address, $text);
        $text = str_replace("[BRANCH-OPENING]", $branch->opening, $text);

        //$this->sender->send(new BulkGate\Sms\Message($number, $text));
    }

    public function recalculateBasket($basket = "basket"){
        $totalPrice = 0;
        $weightAttr = 3;
        $volumeAttr = 2;
        $totalWeight = 0;
        $totalVolume = 0;
        $zone = $this->$basket->zone;
        $holidays = explode(chr(10), $this->settings->holidays);
        $weekendPrice = 0;
        $betonPrice = 0;
        $materialTons = 0;
        
        $isBetonAdd = false;
		$year = date("Y");
		$startYear = strtotime($year."-01-01");
		$endYear = strtotime($year."-12-31");
		$startRange = strtotime($year."-11-15");
		$endRange = strtotime($year."-03-15");
		
		if(!empty($this->$basket->containers)){
			foreach($this->$basket->containers as $container){
				if(!empty($container->price)){
					$price = $this->productManager->findActualPrice($container->product, $container->type);
					if(empty($zone)){
						$totalPrice += $price->priceFrom;
					}
					else if($price){
						$zoneField = "price".$zone;
						if(!empty($price->$zoneField)){
							$totalPrice += $price->$zoneField;
						}
						else{
							$totalPrice += $price->priceFrom;
						}
					}
					$prod = $this->productManager->find($container->product);
					if($prod){
						foreach(explode("|", $prod->attributes) as $aKey){
							list($key, $val) = explode("-", $aKey);
							if($key==$weightAttr){
								$aVal = $this->attributeManager->findValue($val);
								$totalWeight += (int)$aVal->name;
							}
							if($key==$volumeAttr){
								$aVal = $this->attributeManager->findValue($val);
								$totalVolume += (int)$aVal->name;
							}
						}
					}
				}
				//detect term and test weekend and holiday
				if(!empty($container->term)){
					$dateTime = \Nette\Utils\DateTime::createFromFormat("d/m/Y", $container->term);
					if(array_search($dateTime->format("d.m."), $holidays)!==false){
						$weekendPrice += $this->settings->holidayPrice;
					}
					else if($dateTime->format("N")>=6){
						$weekendPrice += $this->settings->holidayPrice;
					}
					$termU = $dateTime->format("U");
					if(($startYear <= $termU && $termU <= $endRange) || ($startRange <= $termU && $termU <= $endYear)){
						$isBetonAdd = true;
					}
				}
			}
		}

		if(!empty($this->$basket->termFrom)){
			$dateTime = \Nette\Utils\DateTime::createFromFormat("d/m/Y", $this->$basket->termFrom);
			$termU = $dateTime->format("U");
			if(($startYear <= $termU && $termU <= $endRange) || ($startRange <= $termU && $termU <= $endYear)){
				$isBetonAdd = true;
			}
		}
		
		$specialDelivery = false;
		if(!empty($this->$basket->materials)){
			if($basket=="basket"){
				foreach($this->$basket->materials as $material){
					if(!empty($material->priceObj)){
						$totalPrice += $material->priceObj->priceFrom * $material->amount;

						//text beton extra pay
						if($material->product == $this->settings->betonProduct){
							if($isBetonAdd){
								//$m3Amount = round($material->amount * $material->priceObj->koef, 2);
								$betonPrice += $this->settings->betonPrice * $material->amount;
							}
						}
					}
				}
			}
			else if($basket=="basketM"){
				foreach($this->$basket->materials as $material){
					if(!empty($material->priceObj)){
						$totalPrice += $material->priceObj->priceFrom * $material->amount;

						//text beton extra pay
						if($material->product == $this->settings->betonProduct){
							if($isBetonAdd){
								//$m3Amount = round($material->amount * $material->priceObj->koef, 2);
								$betonPrice += $this->settings->betonPrice * $material->amount;
							}
							$materialTons += ($material->amount/$material->priceObj->koef);
						}
						else{
							$materialTons += $material->amount;
						}
						if(in_array($material->priceObj->product, array("10", "14"))){
							$specialDelivery = true;
						}
					}
				}
			}
			else{
				foreach($this->$basket->materials as $materials){
					foreach($materials as $material){
						if(!empty($material->priceObj)){
							$totalPrice += $material->priceObj->priceFrom * $material->amount;

							//text beton extra pay
							if($material->priceObj->product == $this->settings->betonProduct){
								if($isBetonAdd){
									//$m3Amount = round($material->amount * $material->priceObj->koef, 2);
									$betonPrice += $this->settings->betonPrice * $material->amount;
								}
								$materialTons += ($material->amount/$material->priceObj->koef);
							}
							else{
								$materialTons += $material->amount;
							}
							if(in_array($material->priceObj->product, array("10", "14"))){
								$specialDelivery = true;
							}
						}
					}
				}
			}
		}
		
		$deliveryPrice = 0;
		if($materialTons>0 && $materialTons<=6){
			if(!empty($this->$basket->containers[0]->product)){
				$deliveryPrice = 0;
			}
			else if($specialDelivery){
				$deliveryPrice = $this->settings->smallDeliveryS;
			}
			else{
				$deliveryPrice = $this->settings->smallDelivery;
			}
		}
		else if($materialTons>6 && $materialTons<=12){
			if(!empty($this->$basket->containers[0]->product) && $this->$basket->containers[0]->product == $this->settings->bigContainer){
				$deliveryPrice = 0;
			}
			else if($specialDelivery){
				$deliveryPrice = $this->settings->bigDeliveryS;
			}
			else{
				$deliveryPrice = $this->settings->bigDelivery;
			}
		}
		
        $this->$basket->betonPrice = $betonPrice;
        $this->$basket->weekendPrice = $weekendPrice;
        $this->$basket->price = $totalPrice + $weekendPrice;
        $this->$basket->maxWeight = $totalWeight;
        $this->$basket->maxVolume = $totalVolume;
        $this->$basket->materialTons = $materialTons;
        $this->$basket->deliveryPrice = $deliveryPrice;

		$vatKoef = 1 + ($this->settings->vat/100);
        $this->$basket->priceVat = ($this->$basket->price * $vatKoef);
        
        $this->template->$basket = $this->$basket;
        
        $this->redrawControl("orderprice");


    }

    public function correctPrice($order, $online = true){
        if(empty($order->saleBilled) && !empty($order->sale)){
            $sale = $order->sale;
        }
        else{
            $sale = 0;
        }

        $onlineSale = $this->onlineSale;
        if(!$online){
            $onlineSale = 1;
        }

        if($order->price_actual > $order->price){
            return round(($order->price_actual - $order->price - $sale)*$onlineSale);
        }
        else{
            return round(($order->price - $order->voucherSale - $sale)*$onlineSale);
        }
    }

    public function payOrder($orderId){
        $order = $this->orderManager->find($orderId);

        $payment = $this->paymentService->createPayment([
            'sum'         => $order->price,
            'variable'    => $order->id,
            'productName' => "Objednávka tisku",
            'customer' => [
                'firstName'   => $order->name,
                'lastName'    => $order->surname,    // všechna parametry jsou volitelné
                'street'      => NULL,    // pokud některý neuvedete,
                'city'        => NULL,    // použije se prázdný řetězec
                'postalCode'  => null,
                'countryCode' => 'CZE',
                'email'       => $order->email,
                'phoneNumber' => $order->phone,
            ],
        ]);
        $paymentId = time().$order->id;
        $this->paymentService->setSuccessUrl($this->link('//:Front:Homepage:payment', ['pay' => 1, 'id'=>$orderId]));
        $this->paymentService->setFailureUrl($this->link('//:Front:Homepage:payment', ['pay' => 9, 'id'=>$orderId]));

        $storeIdCallback = function ($paymentId) use ($order) {
            $this->orderManager->update(array("paymentId"=>$paymentId), $order->id);
        };

        $response = $this->paymentService->pay($payment, "eu_gp_kb", $storeIdCallback);
        $this->sendResponse($response);
    }

    public function makeInvoice($orderId, $type="FAKTURA/GOPAY"){
        $order = $this->orderManager->find($orderId);
        //$invoices = $this->orderManager->findOrderInvoices($orderId);
        $orderItems = $this->orderManager->findOrderItems($orderId);
        $orderProducts = $this->orderManager->findOrderProducts($orderId);

        $lastNumber = $this->orderManager->getLastInvoiceNumber();
        $invoiceNr = "T".str_pad($lastNumber, 5, "0", STR_PAD_LEFT);

        $client = new FlexibeeClient(FB_HOST, FB_COMPANY, FB_USER, FB_PASS);
        $registry = $client->registry("faktura-vydana");
        $invoiceItems = array();
        foreach($orderItems as $item){
            $invoiceItems[] = [
                "kod"=>'item'.$item->id,
                "nazev"=>"Obrázek ".$item->fileId.", ".$this->sizes[$item->size]["name"].", ".$this->materials[$item->material],
                "typPolozkyK"=>"typPolozky.obecny",
                "mnozMj"=>$item->amount,
                "typCenyDphK"=>"typCeny.sDph",
                "typSzbDphK"=>"typSzbDph.dphZakl",
                "szbDph"=>21,
                "cenaMj"=>$item->price,
            ];
        }
        foreach($orderProducts as $item){
            $invoiceItems[] = [
                "kod"=>'item'.$item->id,
                "nazev"=>$item->name,
                "typPolozkyK"=>"typPolozky.obecny",
                "mnozMj"=>$item->quantity,
                "typCenyDphK"=>"typCeny.sDph",
                "typSzbDphK"=>"typSzbDph.dphZakl",
                "szbDph"=>21,
                "cenaMj"=>$item->price_vat,
            ];
        }
        if($order->voucherSale>0){
            $invoiceItems[] = [
                "kod"=>'itemSale',
                "nazev"=>"Slevový kód ".$order->voucherCode,
                "typPolozkyK"=>"typPolozky.obecny",
                "mnozMj"=>1,
                "typCenyDphK"=>"typCeny.sDph",
                "typSzbDphK"=>"typSzbDph.dphZakl",
                "szbDph"=>21,
                "cenaMj"=>-$order->voucherSale,
            ];
        }
        $invoiceItems[] = [
            "kod"=>'itemDelivery',
            "nazev"=>"Způsob dopravy: ".$order->delivery,
            "typPolozkyK"=>"typPolozky.obecny",
            "mnozMj"=>1,
            "typCenyDphK"=>"typCeny.sDph",
            "typSzbDphK"=>"typSzbDph.dphZakl",
            "szbDph"=>21,
            "cenaMj"=>$order->deliveryPrice,
        ];
        $invoiceItems[] = [
            "kod"=>'itemDelivery',
            "nazev"=>"Způsob platby: ".$order->payment,
            "typPolozkyK"=>"typPolozky.obecny",
            "mnozMj"=>1,
            "typCenyDphK"=>"typCeny.sDph",
            "typSzbDphK"=>"typSzbDph.dphZakl",
            "szbDph"=>21,
            "cenaMj"=>$order->paymentPrice,
        ];
        $now = new \Nette\Utils\DateTime();
        $newInvoice = [
            'kod' => $invoiceNr,
            'datVyst' => $now->format('c'),
            'cisObj' => $order->id,
            'nazFirmy' => $order->name." ".$order->surname,
            'datObj' => $order->date->format('c'),
            'typDokl' => "code:$type",
            'typUcOp' => 'code:DP1-ZBOŽÍ',
            'polozkyFaktury' => $invoiceItems,
        ];
        $result = $registry->callCreate($newInvoice);
        if ($result->isOk()) {
            $returnData = $result->getData();
            $invoiceData = new \Nette\Utils\ArrayHash();
            $invoiceData->date = new \Nette\Utils\DateTime();
            $invoiceData->number = $invoiceNr;
            $invoiceData->orderId = $order->id;
            if(!empty($returnData["results"][0]["id"])){
                $invoiceData->invoiceId = $returnData["results"][0]["id"];
            }
            else{
                var_dump($returnData["results"][0]["errors"]);
                var_dump($newInvoice);
                die();
            }
            $invoiceData->status = addslashes(serialize($returnData));
            $this->orderManager->addInvoice($invoiceData);
            //update billed amount
            if(!empty($returnData["results"])){
                $invoiceId = $returnData["results"][0]["id"];
            }
            else{
                $invoiceId = "none";
            }
            $this->orderManager->updateLastInvoiceNumber($lastNumber);
            //save PDF
            $invoicePdfData = $this->getInvoicePdf($invoiceId);
            $invoiceFile = DATA_DIR."/invoice/".$invoiceId.".pdf";
            $f = fopen($invoiceFile, "w");
            fputs($f, $invoicePdfData);
            fclose($f);

            return $invoiceId;

        }
        else{
            return false;
        }

    }

    public function getInvoicePdf($invoiceId){
        $client = new FlexibeeClient(FB_HOST, FB_COMPANY, FB_USER, FB_PASS);
        $registry = $client->registry("faktura-vydana");
        $registry->setOutputFormat("pdf");
        $result = $registry->callDetail($invoiceId);
        $returnData = $result->getData();
        return $returnData;
    }


    public function getClientObject(){
	    $client = new \Google_Client();
	    $client->setApplicationName('Google Drive API Polagraph');
	    $client->setScopes(\Google_Service_Drive::DRIVE);
	    $client->setAuthConfig(APP_DIR.'/config/credentials.json');
	    $client->setAccessType('offline');
	    $client->setPrompt('select_account consent');
		//$client->setRedirectUri($this->link('//:Admin:Homepage:'));
	    return $client;
    }

	function tiff2jpg($source, $project = null) {
		try{
	        $images = new \Imagick($source);

	        //if you want to delete the original tif
	        unlink($source);

	        foreach ($images as $i => $image) {
	            $image->setImageFormat("jpeg");
	            $jpegPath = $this->tiff2jpgName($source);
	            $image->writeImage($jpegPath);
	            return $jpegPath;
	        }
		}
		catch(\ImagickException $e){
			$this->projectManager->update(array("genError"=>$e->getMessage()), $project);	
			//$this->flashMessageError($e->getMessage());
			return null;
		}
	}

	function tiff2jpgName($name) {
        $name = str_replace(".tif", ".jpeg", $name);
        $name = str_replace(".TIF", ".jpeg", $name);
        $name = str_replace(".tiff", ".jpeg", $name);
        return str_replace(".TIFF", ".jpeg", $name);
	}

	public function sendMailFromTemplate($templateFile, $data, $email, $subject, $attachments = null){
        $template = $this->createTemplate();
        $template->setTranslator($this->translator);
        $presenter = $this;
        $template->addFilter('thumb', function($image, $width=null, $height=null, $method = "EXACT") use ($presenter){
            return $presenter->thumb($image, $width, $height, $method);
        });
        $template->setFile(APP_DIR . '/FrontModule/templates/Mails/'.$templateFile);
        foreach($data as $var=>$vals){
        	$template->$var = $vals;
        }
        $template->settings = $this->settings;

        //send mail
        $mail = new Message;
        $mail->setFrom($this->settings->title." <".$this->settings->email.">")
            ->addTo($email)
            ->setSubject($subject)
            ->setHtmlBody($template);
        if(!empty($attachments)){
        	foreach($attachments as $attName=>$attFile){
				$mail->addAttachment($attName, file_get_contents($attFile));
        	}
        }
        $mailer = new SendmailMailer;
        $mailer->send($mail);
	}

    public function rowToArray($row){
        return Nette\Utils\ArrayHash::from($row->toArray());
    }
    
    public function timeToMinutes($time){
		$delimiters = [':', '.'];
		$str = 'foo! bar? baz.';
		$time = str_replace($delimiters, $delimiters[0], $time); // 'foo. bar. baz.'
		$times = explode($delimiters[0], $time);
		$minutes = 0;
		$hours = (int)$times[0];
		if(!empty($times[1])){
			$minutes = (int)$times[1];
		}
		return (60*$hours) + $minutes;
    }
    

}
