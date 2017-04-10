<?php

namespace Admin\Service;
use Admin\Conf\OrderConstant;

class OrderService {
	
	public function addOrder($param){
		
		$result = array("code"=>500);
		$order['car_id'] = $param['car_id'];
		$order['store_id'] = $param['store_id'];
		$order['order_status'] = OrderConstant::$ORDER_STATUS_200;//开单
		$order['creator'] = UID;	//创建人
		$order['create_time'] = time();
		//生成订单号
		$orderNo = date('Ymdhis').rand(1000);
		$order['order_no'] = $orderNo;
		$order['driving_distance'] = $param['driving_distance'];
		$order['store_station_id'] = $param['store_station_id'];
		
		$Order = M('Order');
		$data = $Order->add($order);
		if($data) {
			$result['code'] = 200;
			$result['data'] = $data['id'];
			$result['msg'] = "新增成功";
		} else {
			//print_r($Order->getError());
			$result['msg'] = "新增订单失败";
		}
		return $result;
	}
}

