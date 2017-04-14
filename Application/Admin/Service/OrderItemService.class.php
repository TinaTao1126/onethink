<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Admin\Service;
use Admin\Conf\OrderConstant;

class OrderItemService{
	

	function __construct() {
		//print "In BaseClass constructor\n";
	}
	
	
	public function listByOrderId($order_id){
	    $orderItemList = M('OrderItem')->field(true)->where(array("car_order_id"=>$order_id))->select();
	    
	    foreach ($orderItemList as $key=>$orderItem) {
	    	$orderItemList[$key]['item_type_name'] = OrderConstant::$ITEM_TYPE[$orderItem['item_type']];
	    	$hour_price = isset($orderItem['hour_price']) ? $orderItem['hour_price'] : 0;
	    	$item_price = isset($orderItem['item_price']) ? $orderItem['item_price'] : 0;
	    	$item_num = isset($orderItem['item_num']) ? $orderItem['item_num'] : 0;
	    	$orderItemList[$key]['total_price'] = $hour_price + $item_price * $item_num;
	    		
	    }
	    return $orderItemList;
	}
}