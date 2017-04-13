<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;
use Admin\Conf\OrderConstant;
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
    	$map = array();
    	if(isset($order_no) && !empty($order_no)) {
    		$map['order_no']=$order_no;
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
        	$order_status_name = OrderConstant::$ORDER_STATUS[$val['order_status']];
        	$list[$key]['order_status_name'] = isset($order_status_name) ? $order_status_name : "";
        	
        	//设置操作人 FIXME
        	$list[$key]['operator'] = 'tina';
        	$list[$key]['station'] = '001';
        	
        }
        $this->assign('_list', $list);
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
    	$Order->order_status = OrderConstant::$ORDER_STATUS_201;
    	$Order->pay_time = time();
    	$Order->payee = UID;
    	$Order->pay_status = OrderConstant::$PAY_STATUS_1;
    	
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
    
    /**
     * 修改初始化
     * @author tina
     */
    public function edit($id = 0){
    	if(IS_POST) {
    		$Store = D('Store');
    		$data = $Store->create();
    		if($data){
    			if($Store->save()){
    				S('DB_CONFIG_DATA',null);
    				//记录行为
    				action_log('update_store','store',$data['id'],UID);
    				$this->success('更新成功', Cookie('__forward__'));
    			} else {
    				$this->error('更新失败');
    			}
    		} else {
    			$this->error($Store->getError());
    		}
    	} else {
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
    		$orderItemList = $orderItemService->listByOrderId($order["id"]);
    		
    		
  			$this->assign('car_order_id',$order['id']);
    		$this->assign('data',$car);
    		$this->assign('_list',$orderItemList);
        	
    		$this->meta_title = '修改门店信息';
    		$this->display();
    	}
    	
    }
    
    public function printer($id = 0){
        $this->get($id);
    }
    
    public function detail($id = 0){
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
    		
    		$this->assign('car_order_id',$order['id']);
    		$this->assign('data',$car);
    		$this->assign('_list',$orderItemList);
    		$this->meta_title = '修改门店信息';
    		$this->display();
    	 
    }
    
  
   
   
  
   
}
