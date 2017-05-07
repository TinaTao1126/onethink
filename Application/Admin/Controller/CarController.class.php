<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;


use Admin\Service\CarService;
use Admin\Service\OrderService;
/**
 * 汽车管理
 * @author tina
 *
 */
class CarController extends AdminController {

    /**
     * 用户管理首页
     * @author tina
     */
    public function index(){
    	$district_id       =   I('district_id');
    	$city_id       =   I('city_id');
    	$store_id       =   I('store_id');
    	$map = array();
    	if($district_id > 0) {
    		$map['district_id']=$district_id;
    	}
    	if($city_id > 0) {
    		$map['city_id']=$city_id;
    	}
    	if($store_id > 0) {
    		$map['store_id']=$store_id;
    	}
        

        $list   = $this->lists('Car', $map);
        int_to_string($list);
        $this->assign('_list', $list);
//         print_r($list);exit;
        $this->meta_title = '门店信息';
        $this->display();
    }
    
    public function changeStatus($method=null){
    	$id = array_unique((array)I('id',0));
       	$id = is_array($id) ? implode(',',$id) : $id;
    	if ( empty($id) ) {
    		$this->error('请选择要操作的数据!');
    	}
    	$map['id'] =   array('in',$id);
    	switch ( strtolower($method) ){
    		case 'disabled':
    			$this->forbid('Store', $map );
    			break;
    		case 'enabled':
    			$this->resume('Store', $map );
    			break;
    		default:
    			$this->error('参数非法');
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
    	if(M('Store')->where($map)->delete()){
    		S('DB_CONFIG_DATA',null);
    		//记录行为
    		action_log('delete_store','store',$id,UID);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败！');
    	}
    }
    
    /**
     * 修改汽车信息
     * @author tina
     */
    public function edit($id = 0){
    	if(IS_POST) {
    	    //print_r(I('post.'));
    	    
    	    $carService = new CarService();
    	    $car_id = $carService->editCar();
    	    //修改订单信息
    	    if($car_id > 0) {
    	        $param = I('post.');
    	    	$param['car_id'] = $car_id;
    	    	$orderService = new OrderService();
    	    	$order_id = $orderService->editOrder($param);
    	    	$response = array(
    	    			'car_id'=>$car_id,
    	    			'order_id'=>$order_id,
    	    	);
    	    }else{
    			$this->error('保存车辆信息异常！');
    		}
    	    
    		if(empty($response)) {
    			$this->error('生成订单异常！');
    		} else {
    			$this->success($response);
    		}
    	} 
    	
    }
    
    public function get(){
        $car_number = I('car_number');
        if(empty($car_number)) {
            return null;
        } 
        $where =array('car_number'=>$car_number);
        $car = M('Car')->where($where)->find();
        $this->success($car);
    }
    
   
    
    /**
     * 开单操作，填写完汽车信息，保存时进行以下操作
     * 1）保存汽车信息，返回car_id,
     * 2）保存订单信息，返回订单id
     * 
     */
    public function add(){
		
    	if(IS_POST){
    		$param = I('post.');
    		
    		//echo 'edit';
    		//添加汽车信息
    		$carService = new CarService();
    		$car_id = $carService->editCar();
    		
    		//生成订单信息
    		if($car_id > 0) {
    			$param['car_id'] = $car_id;
    			$orderService = new OrderService();
    			$order_id = $orderService->addOrder($param);
    			$response = array(
    					'car_id'=>$car_id,
    					'order_id'=>$order_id,
    			);
    		} else{
    		    $this->error('保存车辆信息异常！');
    		}
    		
    		if(empty($response)) {
    		    $this->error('生成订单异常！');
    		} else {
    		    $this->success($response);
    		}
    		
    	} else {
    		$this->meta_title = '开单';
    		$this->display();
    	}
    }

   
}
