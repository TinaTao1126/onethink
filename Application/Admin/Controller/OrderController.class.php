<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;
use Admin\Enums\Order;
use Admin\Enums\District;
use Admin\Service\DistrictService;
use Admin\Service\OrderItemService;

/**
 * 汽车管理
 * @author tina
 *
 */
class OrderController extends AdminController {

    /**
     * 客户接待首页
     * @author tina
     */
    public function index(){
    	$order_no       	=   I('order_no');
    	$create_time_begin	=   I('create_time_begin');
    	$create_end      	=   I('create_time_end');
    	$district_id       =   I('district_id');
    	$city_id       =   I('city_id');
    	$store_id       =   I('store_id');
    	$phone       =   I('phone');
    	$map = array();
    	if($store_id > 0) {
    		$map['store_id']=$store_id;
    	}
    	if(isset($order_no) && !empty($order_no)) {
    		$map['order_no']=$order_no;
    	}
    	if(isset($phone) && !empty($phone)) {
    		$map['owner_phone']=$phone;
    	}
    	
        $list   = $this->lists('Order', $map);
        
        //获取所有车辆信息
        $carIdList = getFieldMap($list,$field="car_id");

        if(isset($carIdList) && !empty($carIdList)) {
        	//根据汽车id，批量取出汽车信息
        	$where['id'] = array('in',$carIdList);
        	$carList = M('Car')->where($where)->select();
        	
        	//key:id, val:car
        	//$carMap＝array_combine(array_column($carList,"id"), $carList);
        	$carMap = array();
        	foreach ($carList as $key => $val) {
        		$carMap[$val['id']] = $val;
        	}
        	
        }
        
        foreach ($list as $key=>$val) {
        	//设置汽车信息
        	if(isset($carMap)) {
        		$car = $carMap[$val['car_id']];
        		$list[$key]['car_number'] = $car['car_number'];
        		$list[$key]['car_owner'] = $car['car_owner'];
        	}
        	
        	//设置状态对应的名称
        	$order_status_name = Order::$ORDER_STATUS[$val['order_status']];
        	$list[$key]['order_status_name'] = isset($order_status_name) ? $order_status_name : "";
        	
        	//设置操作人 FIXME
        	$list[$key]['operator'] = 'tina';
        	$list[$key]['station'] = '001';
        	
        }
        
        
        //获取大区数据
        $districtService = new DistrictService();
        $district = $districtService->select(District::$TYPE_DISTRICT, $pid=0);
        $city = $districtService->select(District::$TYPE_CITY, $pid=$district_id);
        $store = $districtService->select(District::$TYPE_STORE, $pid=$city_id);
        
        $this->assign('_list', $list);
        $this->assign('_district',$district);
        $this->assign('_city',$city);
        $this->assign('_store',$store);
        $this->assign('_district_id',$district_id);
        $this->assign('_city_id',$city_id);
        $this->assign('_store_id',$store_id);
        $this->assign('_phone',$phone);
        $this->meta_title = '客户接待';
        $this->display();
    }
    
    /**
     * 结算
     * @param unknown $id
     */
    public function settlement($id){
    	$id = I('id');
    	if ( empty($id) ) {
    		$this->error('请选择要操作的数据!');
    	}
    	
    	//FIXME  判断状态
    	
    	$map = array('id'=>$id);
    	$Order = M('Order');
    	$Order->order_status = Order::$ORDER_STATUS_201;
    	$Order->pay_time = date('Y-m-d H:i:s');
    	$Order->payee = UID;
    	$Order->pay_status = Order::$PAY_STATUS_1;
    	
    	$msg   = array_merge( array( 'success'=>' 结算成功！', 'error'=>'结算失败！', 'url'=>'' ,'ajax'=>IS_AJAX) , (array)$msg );
    	if($Order->where($map)->save()){
    		S('DB_CONFIG_DATA',null);
    		//记录行为
    		action_log('add_order', 'Order', $id, UID);
    		
    		
    		$this->success($msg['success'],$msg['url'],$msg['ajax']);
    		
    	    //$this->success('结算成功', 'Admin/Order/index');
    	} else {
    		$this->error($msg['error']);
    	}
    	
    	
    }
    
    
    /**
     * 删除
     */
    public function del(){
    	$id = array_unique((array)I('id',0));
    
    	if ( empty($id) ) {
    		$this->error('请选择要操作的数据!');
    	}
    
    	$map = array('id' => array('in', $id) );
    	if(M('Order')->where($map)->delete()){
    		S('DB_CONFIG_DATA',null);
    		//记录行为
    		action_log('delete_order','order',$id,UID);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败！');
    	}
    }
    
    
    
    public function printer($id = 0){
        $this->get($id);
    }
    
    public function detail($id = 0){
        $this->get($id);
    }
    
    public function edit($id = 0){
    	$this->get($id);
    }
    
    public function get($id = 0){

    		if($id == 0) {
    			$this->error('无效的参数');
    		}
    		$order = M('Order')->field(true)->find($id);
    		if(false === $order){
    			$this->error("请检查参数，不能获取到有效的数据！");
    		}
    		$car_id = $order['car_id'];
    		$car = M('Car')->field(true)->find($car_id);
    		if(false === $order){
    			$this->error("无法获取到车辆信息！");
    		}
    		$orderItemService = new OrderItemService();
    		$orderItemList = $orderItemService->listByOrderId($order['id']);
    		
    		$car['order_no'] = $order['order_no'];
    		$car['pay_status'] = $order['pay_status'];
    		$car['store_station_id'] = $order['store_station_id'];
    		
    		$this->assign('store_station_id',$order['store_station_id']);
    		$this->assign('car_order_id',$order['id']);
    		$this->assign('data',$car);
    		$this->assign('_list',$orderItemList);
    		
    		$this->getCommonInfo();
    		
    		$this->meta_title = '修改门店信息';
    		$this->display();
    	 
    }
    
    
    /**
     * 获取公共信息
     */
    public function add(){
        //从session获取门店信息
        
        $this->getCommonInfo();
        $this->display();
    }
   
   
    private function getCommonInfo(){
        $store_name = session("user_auth.store_name");
        $store_id = session('user_auth.store_id');
        if(is_null($store_id) || !isset($store_id)) {
        
        	$this->display();
        } else {
        	if(is_null($store_name) || !isset($store_name)) {
        		$Store = M('District')->where('id='.$store_id)->find();
        		$store_name = $Store['name'];
        	}
        
        	session('store_name',$store_name);
        	$this->assign("store_id", $Store['id']);
        	$this->assign("store_name", $store_name);
        }
        
        //获取工位信息
        $where = array(
        		"store_id"=> $store_id,
        		"status"=>0 //   空闲
        );
        $store_station = M('StoreStation')->where($where)->select();
        $this->assign("_store_station", $store_station);
    }
   
    /**
     * 查询新车入场数量
     */
    function count(){
        $count = M('Order')->field('count(1) count')->where('order_status='.Order::$ORDER_STATUS_100)->find();
        //echo $count['count'];
        $this->success($count['count']);
    }
}
