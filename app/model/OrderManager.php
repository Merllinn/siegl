<?php

namespace App\Model;

use Nette;


/**
 * Orders management.
 */
final class OrderManager
{
	use Nette\SmartObject;

	const
        BASE = 'orders',
        ITEMS = 'order_items',
        PRODUCTS = 'order_products',
        INVOICES = 'order_invoice',
        PAYS = 'order_pay',
        STATUS_TABLE = 'order_status',
		NUMBER_TABLE = 'invoice_number';


	/** @var Nette\Database\Context */
	private $database;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

    public function get()
    {
        return $this->database->table(self::BASE);
    }
    public function getAll()
    {
        return $this->get()
            ->select("*, concat(name,' ',surname) AS customer");
    }
    public function getEmails()
    {
        return $this->get()
            ->select("email")
            ->group("email")
            ->order("email");
    }
    public function getItems()
    {
        return $this->database->table(self::ITEMS);
    }

    public function getProducts()
    {
        return $this->database->table(self::PRODUCTS);
    }

    public function getInvoices()
    {
        return $this->database->table(self::INVOICES);
    }

    public function add($values)
    {
        return $this->get()->insert($values);
    }

    public function update($values, $id)
    {
        $this->get()
        ->where("id", $id)
        ->update($values);
    }
    public function updatePrice($id)
    {
        $order = $this->get()
        ->where("id", $id);
        $order->update(array("price"=>$order->fetch()->price_actual));
    }

    public function delete($id)
    {
        $this->get()
        ->where("id", $id)
        ->delete();
    }

    public function find($id)
    {
        return $this->get()
            ->where("id", $id)
            ->fetch();
    }

    public function findByPaymentId($id)
    {
        return $this->get()
            ->where("paymentId", $id)
            ->fetch();
    }

    public function findOrderItems($order)
    {
        return $this->getItems()
            ->where("orderId = ?", $order);
    }

    public function findOrderProducts($order)
    {
        return $this->getProducts()
            ->where("order_id = ?", $order);
    }

    public function findOrderItemsOrdered($order)
    {
        return $this->getItems()
            ->where("orderId = ?", $order)
            ->order("folderId")
            ->order("size")
            ->order("material")
            ->order("fileName")
            ->order("border");
    }

    public function findOrderItemsToBill($order)
    {
        return $this->getItems()
            ->select("*, (quantity-billed) AS billable")
            ->where("quantity != billed")
            ->where("orderId = ?", $order);
    }

    public function addItem($values)
    {
        return $this->getItems()->insert($values);
    }

    public function updateItem($values, $id)
    {
        $this->getItems()
        ->where("id", $id)
        ->update($values);
    }

    public function deleteItem($id)
    {
        $this->getItems()
        ->where("id", $id)
        ->delete();
    }

    public function findItem($id)
    {
        return $this->getItems()
            ->where("id", $id)
            ->fetch();
    }

    public function addProduct($values)
    {
        return $this->getProducts()->insert($values);
    }

    public function updateProduct($values, $id)
    {
        $this->getProducts()
        ->where("id", $id)
        ->update($values);
    }

    public function deleteProduct($id)
    {
        $this->getProducts()
        ->where("id", $id)
        ->delete();
    }

    public function findProduct($id)
    {
        return $this->getProducts()
            ->where("id", $id)
            ->fetch();
    }

    public function findOrderInvoices($order)
    {
        return $this->getInvoices()
            ->where("orderId = ?", $order);
    }

    public function findOrderUnsendedInvoices($order)
    {
        return $this->getInvoices()
            ->where("orderId = ?", $order)
            ->where("sended = ?", false);
    }

    public function addInvoice($values)
    {
        return $this->getInvoices()->insert($values);
    }

    public function updateInvoice($values, $id)
    {
        $this->getInvoices()
        ->where("id", $id)
        ->update($values);
    }

    public function updateByInvoiceId($values, $id)
    {
        $this->getInvoices()
        ->where("invoiceId", $id)
        ->update($values);
    }

    public function deleteInvoice($id)
    {
        $this->getInvoices()
        ->where("id", $id)
        ->delete();
    }

    public function findInvoice($id)
    {
        return $this->getInvoices()
            ->where("id", $id)
            ->fetch();
    }

    public function saveOrder($ses, $pm, $settings){

        $this->database->beginTransaction();

        //save order
		$order = $ses->order;
		$order->date = new \Nette\Utils\DateTime();
		$order->street = $ses->address;
        //resolve company
        $mapArray = array(
        	"bussiness_name" => "name",
        	"bussiness_surname"=>"surname",
        	"bussiness_email"=>"email",
        	"bussiness_phone"=>"phone",
        	"bussiness_note"=>"note",
        	"bussiness_company"=>"company_name",
        	"bussiness_different_delivery"=>"different_delivery",
        	"bussiness_delivery_street"=>"delivery_street",
        	"bussiness_delivery_city"=>"delivery_city",
        	"bussiness_delivery_zip"=>"delivery_zip",
        );
        foreach($mapArray as $source=>$target){
        	if(!empty($order->$source)){
				$order->$target = $order->$source;
        	}
        	unset($order->$source);
			
        }
        $order->price = $ses->price;
        $order->price_vat = ($ses->price * (1 + ($settings->vat/100)));
        $order->weekendPrice = $ses->weekendPrice;
        $order->betonPrice = $ses->betonPrice;
        if($order->type==9){
			$order->description = $ses->description;
			$order->termFrom = $ses->termFrom;
			$order->termTo = $ses->termTo;
			$order->weekends = $ses->weekends;
        }
        
        //save order
        $orderId = $this->add($order);
        
        $containers = $ses->containers;
        $materials = $ses->materials;

        //$totalPice = 0;

        //save containers
        if(!empty($containers)){
	        foreach($containers as $container){
	            $product = $pm->find($container->product);
	            $price = $pm->findPrice($container->price->id);
	            $itemData = array(
	                "order_id"		=>$orderId,
	                "products_id"   =>$container->product,
	                "type"			=>1,
	                "name"          =>$product->name." - ".$price->ref("attributeValue")->name,
	            );
	            if($order->type==1){
					$itemData["term"] = $container->term . " " . $container->time;
					$itemData["quantity"] = 1;
					$itemData["price"] = $price->priceFrom;
					$itemData["price_vat"] = $price->priceFrom * (1 + ($settings->vat/100));
	            }
	            if($order->type==9){
					$itemData["quantity"] = $container->amount;
	            }
	            $this->addProduct($itemData);
	            //$totalPice += $price->priceFrom;
	        }
        }
        //save containers
        if(!empty($materials)){
	        foreach($materials as $material){
	            if($order->type==1){
		            $product = $pm->find($material->product);
		            $price = $pm->findPrice($material->priceObj->id);
		            $itemData = array(
		                "order_id"		=>$orderId,
		                "products_id"   =>$material->product,
		                "quantity"      =>$material->amount,
		                "type"			=>2,
		                "name"          =>$product->name." - ".$price->ref("attributeValue")->name,
		                "price"         =>$price->priceFrom,
		                "price_vat"     =>$price->priceFrom * (1 + ($settings->vat/100)),
		            );
		            $this->addProduct($itemData);
				}
	            if($order->type==9){
		            foreach($material as $variant){
			            $product = $pm->find($variant->priceObj->product);
			            $price = $pm->findPrice($variant->priceObj->id);
			            $itemData = array(
			                "order_id"		=>$orderId,
			                "products_id"   =>$variant->priceObj->product,
			                "quantity"      =>$variant->amount,
			                "type"			=>2,
			                "name"          =>$product->name." - ".$price->ref("attributeValue")->name,
			            );
			            $this->addProduct($itemData);
		            }
				}
	            //$totalPice += $price->priceFrom * $material->amount;
	        }
        }
        //$totalPice += $order->paymentPrice;
        //$totalPice += $order->deliveryPrice;
		//$updateData = array("price"=>$totalPice);
        //$this->update($updateData, $orderId);

        $this->database->commit();

        return $orderId;

    }

    public function getStatuses(){
        return $this->database->table(self::STATUS_TABLE)
            ->order("id")
            ->fetchPairs("id", "name");
    }

    public function getStatusesFull(){
        $statuses = array();
        $ret = $this->database->table(self::STATUS_TABLE)
            ->order("id");
        foreach($ret as $row){
        	$statuses[$row->id] = $row;
        }
        return $statuses;
    }

    public function getStatusesAll(){
        $ret = $this->database->table(self::STATUS_TABLE)
            ->order("id");
        $statuses = array();
        foreach($ret as $row){
        	$statuses[$row->id] = $row;
        }
        return $statuses;
    }

    public function getByDate($start, $end, $branch = null){
        $orders = $this->get()
                ->where("`from` <= ?", $end)
                ->where("`to` >= ?", $start);
        if(!empty($branch)){
            $orders->where("branch = ?", $branch);
        }
        $output = array();
        foreach($orders as $order){
            $cameras = $this->findOrderCameras($order->id);
            while($order->from<=$order->to){
                $date = $order->from->format("j-n-Y");
                foreach($cameras as $camera){
                    $camera = $camera->camera;
                    if(empty($output[$date])){
                        $output[$date] = array();
                    }
                    if(empty($output[$date][$camera])){
                        $output[$date][$camera] = array();
                    }
                    $output[$date][$camera][] = $order;
                }
                $order->from->modify("+1 day");
            }
        }
        return $output;
    }

    public function getToInform(){
        $date = new \nette\utils\DateTime();
        $date->modify("+2 day");
        $ret = $this->get()
                ->where("from = ?", $date->format('Y-m-d'));
        return $ret;
    }

    public function getLastInvoiceNumber()
    {
        $last = $this->database->table(self::NUMBER_TABLE)->where("id", 1)->fetch();
        if(!$last){
            $this->database->table(self::NUMBER_TABLE)->insert(array("id"=>1, "number"=>0));
            $last = $this->database->table(self::NUMBER_TABLE)->where("id", 1)->fetch();
        }
        $lastNumber = $last->number+1;
        return $lastNumber;

    }

    public function updateLastInvoiceNumber($number)
    {
        $last = $this->database->table(self::NUMBER_TABLE)->where("id", 1)->fetch();
        $last->update(array("number"=>$number));

    }

    public function findSumsByType($branch = null)
    {
        $ret = $this->getPays()
            ->select("type, SUM(price) AS totalPrice");
        if(!empty($branch)){
			$ret->where("orderId IN (SELECT id FROM orders WHERE branch = ?)", $branch);
        }
        return $ret->group("type");
    }


    public function getPays()
    {
        return $this->database->table(self::PAYS);
    }

    public function addPay($values)
    {
        return $this->getPays()->insert($values);
    }

    public function findOrderPayments($order)
    {
        return $this->getPays()
            ->where("orderId = ?", $order)
            ->order("date DESC")
            ->fetchAll();
    }

    public function getAllPayments()
    {
        return $this->getPays()
            ->order("date DESC")
            //->fetchAll()
            ;
    }



    public function findOrderCustomPhotos($orderId)
    {
        $items = $this->getItems()
            ->select("fileId, folderId")
            ->where("orderId = ?", $orderId)
            ->where("folderId IN (SELECT hash FROM projects WHERE custom=1)")
            ->order("folderId");
            
        $ret = array();
        foreach($items as $item){
            if(empty($ret[$item->folderId])){
               $ret[$item->folderId] = array(); 
            }
            $ret[$item->folderId][] = $item->fileId;
        }
        return $ret;
    }





}

