<?php

namespace Admin\Service;
use Admin\Enums\Order;

class OrderService {
	
	public function addOrder($param){
		
		$result = array("code"=>500);
		$order['car_id'] = $param['car_id'];
		$order['store_id'] = $param['store_id'];
		$order['order_status'] = Order::$ORDER_STATUS_200;//开单
		$order['creator'] = UID;	//创建人
		$order['create_time'] = date('Y-m-d H:i:s');
		//生成订单号
		$orderNo = date('Ymdhis').rand(1000);
		$order['order_no'] = $orderNo;
		$order['driving_distance'] = $param['driving_distance'];
		$order['store_station_id'] = $param['store_station_id'];
		
		$Order = M('Order');
		$id = $Order->add($order);
		if($id) {
			$result['code'] = 200;
			$result['data'] = $id;
			$result['msg'] = "新增成功";
		} else {
			//print_r($Order->getError());
			$result['msg'] = "新增订单失败";
		}
		return $result;
	}
	
	/**
	 * 更新各种金额
	 * @param unknown $id
	 */
	public function updateAmount($id) {
	    $order = M('Order')->where('id='.$id)->select();
	    $orderItemList = M('OrderItem')->where('car_order_id='.$id)->select();
	    if(empty($orderItemList)) {
	        return;
	    }
	    $total_price = 0;
	    foreach ($orderItemList as $key => $orderItem) {
	        $orderItemList[$key]['item_type_name'] = Order::$ITEM_TYPE[$orderItem['item_type']];
	        $hour_price = isset($orderItem['hour_price']) ? $orderItem['hour_price'] : 0;
	        $item_price = isset($orderItem['item_price']) ? $orderItem['item_price'] : 0;
	        $item_num = isset($orderItem['item_num']) ? $orderItem['item_num'] : 0;
	        $total_price = $total_price + $hour_price + $item_price * $item_num;
	    }
	     
	    $order['purchase_amount'] = $total_price;
	    M("Order")->where('id='.$id)->save($order);
	}
}

