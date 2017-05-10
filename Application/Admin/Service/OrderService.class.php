<?php

namespace Admin\Service;
use Admin\Enums\Order;
use Admin\Enums\RoleKey;
use Admin\Service\CarService;

class OrderService {
    
    /**
     * 封装订单列表查询参数
     * @param unknown $param
     */
    public function list_condition() {
        $order_no       	=   I('order_no');
        $start_time	=   I('start_time');
        $end_time      	=   I('end_time');
        $district_id       =   I('district_id');
        $city_id       =   I('city_id');
        $store_id       =   I('store_id');
        $phone       =   I('phone');
        $owner_name = I('owner_name');
        $map = array();
        
        if(isset($district_id) && $district_id > 0) {
        	$map['district_id']=$district_id;
        }
        if(isset($city_id) && $city_id > 0) {
        	$map['city_id']=$city_id;
        }
        if(isset($store_id) && $store_id > 0) {
        	$map['store_id']=$store_id;
        }
        if(!empty($order_no)) {
        	$map['order_no']=$order_no;
        }
        if(!empty($phone)) {
        	$map['owner_phone']=$phone;
        }
        if(!empty($owner_name)) {
        	$map['car_owner']=$owner_name;
        }
        if(!empty($start_time) && !empty($end_time)) {
        	$map['create_time'] = array('BETWEEN', array($start_time,  $end_time.' 23:59:59'));
        } elseif(!empty($start_time) && empty($end_time)) {
        	$map['create_time'] = array('egt', $start_time);
        } elseif(empty($start_time) && !empty($end_time)) {
        	$map['create_time'] = array('elt', $end_time.' 23:59:59');
        }
        
        $map['start_time']=$start_time;
        $map['end_time']=$end_time;
        
        //根据权限筛选条件
        if(!empty(session('user_auth.role_key')) && session('user_auth.role_key') != RoleKey::$ADMIN){
            $map['district_id']=session('user_auth.district_id');
            $map['city_id']=session('user_auth.city_id');
            $map['store_id']=session('user_auth.store_id');
            $map['select_disabled'] = 'disabled';
        }
        
        
        
        return $map;
    }
    
    /**
     * 缓存条件
     */
    public function cache_condition($map){
        $condition = array(
        		'_district_id'=>$map['district_id'],
        		'_city_id'=>$map['city_id'],
        		'_store_id'=>$map['store_id'],
        		'_phone'=>$map['owner_phone'],
                '_owner_name'=>$map['car_owner'],
        		'_start_time'=>$map['start_time'],
        		'_end_time'=>$map['end_time'],
                '_disabled'=>$map['select_disabled']
        );
        return $condition;
    }
    
    
    /**
     * 封装返回结果
     * @param unknown $list
     * @return Ambigous <string, unknown>
     */
    public function combine_response($list) {
        
        
        //获取所有车辆信息
        $carIdList = getFieldMap($list,$field="car_id");
        
        $carService = new CarService();
        $carMap = $carService->getCarMap($carIdList);
        
        //大区信息
        $districtIdList = getFieldMap($list,$field="district_id");
        $districtService = new DistrictService();
        if(!empty($districtIdList)) {
        	$where['id'] = array('in',$districtIdList);
        	$districtList = $districtService->listByIds($districtIdList);
        
        	$districtMap = array();
        	foreach ($districtList as $key => $val) {
        		$districtMap[$val['id']] = $val;
        	}
        
        }
        
        //城市信息
        $cityIdList = getFieldMap($list,$field="city_id");
        if(!empty($cityIdList)) {
        	$where['id'] = array('in',$cityIdList);
        	$cityList = $districtService->listByIds($cityIdList);
        
        	$cityMap = array();
        	foreach ($cityList as $key => $val) {
        		$cityMap[$val['id']] = $val;
        	}
        
        }
        
        //门店信息
        $storeIdList = getFieldMap($list,$field="store_id");
        if(!empty($storeIdList)) {
        	$where['id'] = array('in',$storeIdList);
        	$storeList = $districtService->listByIds($storeIdList);
        
        
        	$storeMap = array();
        	foreach ($storeList as $key => $val) {
        		$storeMap[$val['id']] = $val;
        	}
        
        }
        
        //获取所有工位信息
        $stationIdList = getFieldMap($list,$field="store_station_id");
        if(!empty($stationIdList)) {
        	//根据汽车id，批量取出汽车信息
        	$where['id'] = array('in',$stationIdList);
        	$stationList = M('StoreStation')->where($where)->select();
        
        	 
        	//key:id, val:car
        	//$carMap＝array_combine(array_column($carList,"id"), $carList);
        	$stationMap = array();
        	foreach ($stationList as $key => $val) {
        		$stationMap[$val['id']] = $val;
        	}
        
        }
        
        $creatorIdList = getFieldMap($list,$field="creator");
        
        if(!empty($creatorIdList)) {
        	//根据会员id，批量取出会员
        	$where['uid'] = array('in',$creatorIdList);
        	$memberList = M('Member')->where($where)->select();
        	//print_r($memberList);exit;
        	$memberMap = array();
        	foreach ($memberList as $key => $val) {
        		$memberMap[$val['uid']] = $val;
        	}
        
        }
        
        foreach ($list as $key=>$val) {
        	 
        	if(!empty($stationMap)) {
        		$station = $stationMap[$val['store_station_id']];
        		$list[$key]['station_name'] = $station['name'];
        	}
        	 
        	//设置汽车信息
        	if(!empty($carMap)) {
        		$car = $carMap[$val['car_id']];
        		$list[$key]['car_number'] = $car['car_number'];
        		$list[$key]['car_owner'] = $car['car_owner'];
        	}
        	 
        	//设置操作人
        	//print_r($memberMap);
        	if(!empty($memberMap)) {
        		$member = $memberMap[$val['creator']];
        		$list[$key]['creator_name'] = $member['nickname'];
        	}
        	
        	if(!empty($districtMap)) {
        		$district = $districtMap[$val['district_id']];
        		$list[$key]['district_name'] = $district['name'];
        	}
        	 
        	if(!empty($cityMap)) {
        		$city = $cityMap[$val['city_id']];
        		$list[$key]['city_name'] = $city['name'];
        	}
        	
        	if(!empty($storeMap)) {
        		$store = $storeMap[$val['store_id']];
        		$list[$key]['store_name'] = $store['name'];
        	}
        	
        	//设置状态对应的名称
        	$order_status_name = Order::$ORDER_STATUS[$val['order_status']];
        	$list[$key]['order_status_name'] = isset($order_status_name) ? $order_status_name : "";
        	 
        }
        
       // print_r($list);
        return $list;
    }
	
    /**
     * sa手动开单
     * @param unknown $param
     * @return unknown
     */
	public function addOrder($param){
		
		$result = array("code"=>500);
		$order['car_id'] = $param['car_id'];
		$order['district_id'] = !empty(session('user_auth.district_id')) ? session('user_auth.district_id') : 0;
		$order['city_id'] = !empty(session('user_auth.city_id')) ? session('user_auth.city_id') : 0;
		$order['store_id'] = !empty(session('user_auth.store_id')) ? session('user_auth.store_id') : 0;
		$order['order_status'] = isset($param['order_status']) ? $param['order_status'] : Order::$ORDER_STATUS_200;//开单
		$order['creator'] = UID;	//创建人
		$order['create_time'] = date('Y-m-d H:i:s');
		//生成订单号
		$orderNo = date('Ymdhis').rand(1000);
		$order['order_no'] = $orderNo;
		$order['driving_distance'] = $param['driving_distance'];
		$order['store_station_id'] = isset($param['store_station_id']) ? $param['store_station_id'] : 0;
		$order['car_owner'] = $param['car_owner'];
		$order['owner_phone'] = $param['owner_phone'];
		$order['store_id'] = $param['store_id'];
		$order['city_id'] = $param['city_id'];
		$order['district_id'] = $param['district_id'];
		
		
		$Order = M('Order');
		$id = $Order->add($order);
// 		if($id) {
// 			$result['code'] = 200;
// 			$result['data'] = $id;
// 			$result['msg'] = "新增成功";
// 		} else {
// 			//print_r($Order->getError());
// 			$result['msg'] = "新增订单失败";
// 		}
		return $id;
	}
	
	public function editOrder($param){
	    $result = array("code"=>500);
	    $Car = M('Order');
	    $where = array('id'=>$param['order_id']);
	    $data = $Car->where($where)->find();
	    
	    if(!isset($data) || empty($data)) {
	        $result['msg'] = "订单未找到";;
	        return $result;
	    }
	    
	    $order['district_id'] = !empty(session('user_auth.district_id')) ? session('user_auth.district_id') : 0;
	    $order['city_id'] = !empty(session('user_auth.city_id')) ? session('user_auth.city_id') : 0;
	    $order['store_id'] = !empty(session('user_auth.store_id')) ? session('user_auth.store_id') : 0;
		$order['car_id'] = $param['car_id'];
		$order['order_status'] = Order::$ORDER_STATUS_200;//开单
		$order['creator'] = UID;	//创建人
		$order['create_time'] = date('Y-m-d H:i:s');
		$order['driving_distance'] = $param['driving_distance'];
		$order['store_station_id'] = isset($param['store_station_id']) ? $param['store_station_id'] : 0;
		$order['car_owner'] = $param['car_owner'];
		$order['owner_phone'] = $param['owner_phone'];
	
		$Order = M('Order');
		$id = $Order->save($order,$options=array('where'=>'id='.$param['order_id']));

		return $id;
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
	
	/**
	 * 订单保存参数
	 */
	public function orderSaveParam(){
	    
	}
}

