<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Admin\Service;
use Admin\Enums\Order;
use Think\Exception;

class OrderItemService{
	

	function __construct() {
		//print "In BaseClass constructor\n";
	}
	
	/**
	 * 列举订单详情
	 * @param unknown $order_id
	 * @return number
	 */
	public function listByOrderId($order_id){
	    $orderItemList = M('OrderItem')->field(true)->where(array("car_order_id"=>$order_id))->select();
	    
	    foreach ($orderItemList as $key=>$orderItem) {
	    	$orderItemList[$key]['item_type_name'] = Order::$ITEM_TYPE[$orderItem['item_type']];
	    	$hour_price = isset($orderItem['hour_price']) ? $orderItem['hour_price'] : 0;
	    	$item_price = isset($orderItem['item_price']) ? $orderItem['item_price'] : 0;
	    	$item_num = isset($orderItem['item_num']) ? $orderItem['item_num'] : 0;
	    	$orderItemList[$key]['total_price'] = $hour_price + $item_price * $item_num;
	    		
	    }
	    return $orderItemList;
	}
	
	/**
	 * 修改订单详情
	 *     > 已结算的订单不可修改
	 */
	public function editOrderItem(){
	    $param = I("post.");
	    $order =  M('Order')->where(array("id"=>$param['car_order_id']))->find();
	    if(!isset($order)) {
	        throw new \Exception("非法操作，未找到订单！");
	    }
	    if($order['pay_status'] == Order::$PAY_STATUS_1){
	        throw new \Exception("已结算的订单不允许修改！");
	    }
	    
	    $OrderItem = D('OrderItem');
	    $data = $OrderItem->create();
	    if($data){
	        $data = $OrderItem->save();
	    	if($data){
	    		S('DB_CONFIG_DATA',null);
	    		//记录行为
	    		action_log('update_order_item','order_item',$data['id'],UID);
	    	} 
	    } 
	    return $data;
	}
}