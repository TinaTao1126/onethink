<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;
use Admin\Service\StoreStationService;
use Admin\Enums\StoreStation;

/**
 * 门店车位管理
 * @author tina
 *
 */
class StoreStationController extends AdminController {

    /**
     * 车位管理
     * step-1: 找到当前用户的auth_group role_key是admin、manager、sa
     * step-2: 根据角色限制筛选条件，admin 大区、城市、门店的工位信息均可查看，manager、sa只能查看当前门店工位信息
     * step-3: 
     * @author tina
     */
    public function index(){
        
    	//step-1: 封装查询参数
    	$storeStationService = new StoreStationService();
    	$map = $storeStationService->list_condition();
    	
    	//step-2: 根据条件查询
        $list   = $this->lists('StoreStation', $map);
        
        //step-3 :组装返回结果
        $list = $storeStationService->combine_response($list);
        
        //获取 ［区|城市|门店］ 数据
        $this->setDistrictSelectList($map['district_id'], $map['city_id']);
        
        //缓存条件
        $condition = $storeStationService->cache_condition($map);
        
        $this->assign('_list', $list);
        $this->assign('_condition', $condition);
        
        $this->meta_title = '工位信息';
        $this->display();
    }
    
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
    		$StoreStation = D('StoreStation');
    		$data = $StoreStation->create();
    		if($data){
    			if($StoreStation->save()){
    				S('DB_CONFIG_DATA',null);
    				//记录行为
    				action_log('update_store_station','store_station',$data['id'],UID);
    				$this->success('更新成功', 'Admin/StoreStation/index');
    			} else {
    				$this->error('无更新');
    			}
    		} else {
    			$this->error($StoreStation->getError());
    		}
    	} else {
    		if($id == 0) {
    			$this->error('无效的参数');
    		}
    		$data = M('StoreStation')->field(true)->find($id);
    		if(false === $data){
    			$this->error("获取车位信息错误");
    		}
    		
    		//获取 ［区|城市|门店］ 数据
    		$this->setDistrictSelectList($data['district_id'], $data['city_id']);
    		
    		$this->assign('data',$data);
    		$this->assign('_district_id',$data['district_id']);
    		$this->assign('_city_id',$data['city_id']);
    		$this->assign('_store_id',$data['store_id']);
    		
    		$this->meta_title = '修改车位信息';
    		$this->display();
    	}
    	
    }
    

    public function add(){

    	if(IS_POST){
    		$param = I('post.');
    		$param['creator'] = UID;
    		$param['disabled'] = 1;   //启用
    		
    		$StoreStation = D('StoreStation');
    		$data = $StoreStation->create($param);
    		if($data) {
    		    $id = $StoreStation->add($param);
    		    if($id){
    		    	S('DB_CONFIG_DATA',null);
    		    	//记录行为
    		    	action_log('add_store_station', 'store_station', $id, UID);
    		    	$this->success('新增成功', 'Admin/StoreStation/index');
    		    } else {
    		    	$this->error('新增失败');
    		    }
    		} else {
    		    $this->error($StoreStation->getError());
    		}
    		
    	} else {
    	    //根据权限获取数据
    	    
    	    
    	    //获取 ［区］ 数据
    	    $this->setDistrictSelectList(0, 0);
    	    
    	    //$this->assign('store_id',session('store_id'));
    	    
    		$this->meta_title = '新增工位';
    		$this->display();
    	}
    }

   
}
