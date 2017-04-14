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
 * 门店车位管理
 * @author tina
 *
 */
class StoreStationController extends AdminController {

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
        

        $list   = $this->lists('StoreStation', $map);
        int_to_string($list);
        $this->assign('_list', $list);
//         print_r($list);exit;
        $this->meta_title = '车位信息';
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
    			$this->forbid('StoreStation', $map );
    			break;
    		case 'enabled':
    			$this->resume('StoreStation', $map );
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
    	if(M('StoreStation')->where($map)->delete()){
    		S('DB_CONFIG_DATA',null);
    		//记录行为
    		action_log('delete_store_station','store_station',$id,UID);
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
    		$Store = D('StoreStation');
    		$data = $Store->create();
    		if($data){
    			if($Store->save()){
    				S('DB_CONFIG_DATA',null);
    				//记录行为
    				action_log('update_store','store',$data['id'],UID);
    				$this->success('更新成功', 'Admin/StoreStation/index');
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
    		$data = M('StoreStation')->field(true)->find($id);
    		if(false === $data){
    			$this->error("获取车位信息错误");
    		}
    		$this->assign('data',$data);
    		$this->meta_title = '修改车位信息';
    		$this->display();
    	}
    	
    }
    
    /**
     * 更新
     * @author tina
     */
    public function save(){
    	$res = D('StoreStation')->update();
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
    		//print_r($param);
    		/* 调用注册接口注册用户 */
    		
    			$id = M('StoreStation')->add($param);
    			if($id){
    				// S('DB_CONFIG_DATA',null);
    				//记录行为
    				action_log('add_store_station', 'store_station', $id, UID);
    				$this->success('新增成功', Cookie('__forward__'));
    			} else {
    				$this->error('新增失败');
    			}
    		
    		
    	} else {
    		$this->meta_title = '新增车位';
    		$this->display();
    	}
    }

   
}
