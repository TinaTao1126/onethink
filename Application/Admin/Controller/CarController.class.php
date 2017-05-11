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
     * 启用／禁用
     * @param string $method
     */
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
     * 修改汽车信息
     * @author tina
     */
    public function edit($id = 0){
    	if(IS_POST) {
    	    $param = I('post.');
    	    
    	    $carService = new CarService();
    	    try{
    	    	$car_id = $carService->editCar();
    	    } catch (\Exception $e) {
    	    	$this->error($e->getMessage());
    	    }
    	        	    
    	    //修改订单信息
    	    if($car_id > 0) {
    	    	$param['car_id'] = $car_id;
    	    	
    	    	$orderService = new OrderService();
    	    	$order_id = $orderService->editOrder($param);
    	    	
    	    	//返回结果
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
    
    /**
     * 根据车牌获取车辆信息
     * @return NULL
     */
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
    		
    		//添加汽车信息
    		$carService = new CarService();
    		try{
    		  $car_id = $carService->editCar();
    		} catch (\Exception $e) {
    		    $this->error($e->getMessage());
    		}
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
    		
    	} 
    }

   
}
