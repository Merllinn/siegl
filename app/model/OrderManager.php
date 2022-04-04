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

    public function saveOrder($ses, $sizesList){

        $this->database->beginTransaction();

        //save order
		$order = $ses->order;
		$order->date = new \Nette\Utils\DateTime();
        $orderId = $this->add($order);

        $items = $ses->items;
        $products = $ses->products;
        $itemNames = $ses->itemNames;
        $sizes = $ses->sizes;
        $materials = $ses->materials;
        $amounts = $ses->amounts;
        $albums = $ses->albums;
        $borders = $ses->borders;

        $totalPice = 0;

        //save items
        foreach($items as $itemId => $item){
            $itemData = array(
                "orderId"		=>$orderId,
                "fileId"     	=>$item,
                "fileName"     	=>$itemNames[$item],
                "folderId"     	=>$albums[$item],
                "amount"        =>$amounts[$itemId],
                "size"          =>$sizes[$itemId],
                "material"      =>$materials[$itemId],
                "border"      	=>(empty($borders[$itemId])?false:true),
                "price"         =>$sizesList[$sizes[$itemId]]["price"],
                "priceTotal"    =>$sizesList[$sizes[$itemId]]["price"]*$amounts[$itemId],
            );
            $this->addItem($itemData);
            $totalPice += ($sizesList[$sizes[$itemId]]["price"]*$amounts[$itemId]);
        }
        //save products
        foreach($products as $itemId => $item){
            $itemData = array(
                "order_id"		=>$orderId,
                "products_id"   =>$itemId,
                "quantity"      =>$item->amount,
                "name"          =>$item->name,
                "price"         =>$item->price,
                "price_vat"     =>$item->price_vat,
            );
            $this->addProduct($itemData);
            $totalPice += $item->price_vat*$item->amount;
        }
        $totalPice += $order->paymentPrice;
        $totalPice += $order->deliveryPrice;
		$updateData = array("price"=>$totalPice);
        if(!empty($ses->voucher)){
			$updateData["voucherCode"] = $ses->voucher->code;
			if(!empty($ses->voucher->sale)){
				$updateData["voucherSale"] = $ses->voucher->sale;
			}
			elseif(!empty($ses->voucher->salePercent)){
				$updateData["voucherSale"] = $totalPice*($ses->voucher->salePercent/100);
			}
			$updateData["voucherSale"] = round(min($totalPice, $updateData["voucherSale"]));
			$updateData["price"] -= $updateData["voucherSale"];
        }
        $this->update($updateData, $orderId);

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

