<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;


/**
 * 订单明细管理
 * @author tina
 *
 */
class OrderItemController extends AdminController {

   
    
    /**
     * 删除
     */
    public function del(){
    	$id = array_unique((array)I('id',0));
    
    	if ( empty($id) ) {
    		$this->error('请选择要操作的数据!');
    	}
    
    	$map = array('id' => array('in', $id) );
    	if(M('OrderItem')->where($map)->delete()){
    		S('DB_CONFIG_DATA',null);
    		//记录行为
    		action_log('delete_order_item','order_item',$id,UID);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败！');
    	}
    }
    
    /**
     * 修改初始化
     * @author tina
     */
    public function edit($id = 0,$car_order_id=0){
    	if(IS_POST) {
    		$OrderItem = D('OrderItem');
    		$data = $OrderItem->create();
    		//print_r($data);exit;
    		if($data){
    			if($OrderItem->save()){
    				S('DB_CONFIG_DATA',null);
    				//记录行为
    				action_log('update_order_item','order_item',$data['id'],UID);
    				$this->success('更新成功', "Admin/Order/edit?id=".$data['car_order_id']);
    			} else {
    				$this->error('无更新');
    			}
    		} else {
    			$this->error($OrderItem->getError());
    		}
    	} else {
    		if($id == 0) {
    			$this->error('无效的参数');
    		}
    		$data = M('OrderItem')->field(true)->find($id);
    		if(false === $data){
    			$this->error("获取项目信息错误");
    		}
    		$this->assign('car_order_id',$car_order_id);
    		$this->assign('data',$data);
    		$this->meta_title = '修改项目信息';
    		$this->display();
    	}
    	
    }
    
    /**
     * 更新
     * @author tina
     */
    public function save(){
    	$res = D('Store')->update();
    	if(!$res){
    		//print_r(D('Store')->getError());exit;
    		$this->error(D('Store')->getError());
    	}else{
    		$this->success($res['id']?'更新成功！':'新增成功！', Cookie('__forward__'));
    	}
    }
    
    
    public function add(){

    	if(IS_POST){
    		$param = I('post.');
    		
    		/* 调用注册接口注册用户 */
    		$OrderItem = D('OrderItem');
    		$data = $OrderItem->create();
    		if($data){
    			$id = $OrderItem->add();
    			if($id){
    				S('DB_CONFIG_DATA',null);
    				//记录行为
    				action_log('add_order_item', 'OrderItem', $id, UID);
    				$this->success('新增成功', Cookie('__forward__'));
    			} else {
    				$this->error('新增失败');
    			}
    		} else {
    			$this->error($OrderItem->getError());
    		}
    		
    	} else {
    		$id = I('get.id');
    		empty($id) && $this->error('参数不能为空！');
    		$data['id'] = $id;
    		$this->assign('data',$data);
    		$this->meta_title = '编辑行为';
    		$this->display();
    		
    	}
    }

   
}