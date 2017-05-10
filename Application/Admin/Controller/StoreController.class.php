<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;
use Admin\Service\DistrictService;
use Admin\Service\StoreService;
use Admin\Enums\Store;
use Admin\Enums\District;

/**
 * 门店管理
 * @author tina
 *
 */
class StoreController extends AdminController {

    /**
     * 用户管理首页
     * @author tina
     */
    public function index(){
    	$storeService = new StoreService();
    	$map = $storeService->list_condition();
        
        
        $list   = $this->lists('Store', $map);
        
        //获取所有门店的大区id和城市id
        $district_ids = getFieldMap($list,$field="district_id");
        $city_ids = getFieldMap($list, $field='city_id');
        $ids = array_merge($district_ids,$city_ids);
       
        
        if(isset($ids) && !empty($ids)) {
            
        	//根据汽车id，批量取出汽车信息
            $districtService = new DistrictService();
            $districtList = $districtService->listByIds($ids);
        	 
            //php 5.4 array_combine(array_column($districtList,'id'),$districtList);
        	$districtMap = array();
        	foreach ($districtList as $key => $val) {
        		$districtMap[$val['id']] = $val['name'];
        	}
        }
        
       
        foreach ($list as $key => $store) {
            $list[$key]['district_name'] = $districtMap[$store['district_id']];
            $list[$key]['city_name'] = $districtMap[$store['city_id']];
            $list[$key]['status_name'] = Store::$STATUS[$store['status']];
        }
        
    	//获取 ［区|城市］ 数据
    	$this->setDistrictSelectList($map['district_id'], 0);
    	
    	//缓存条件
    	$condition = $storeService->cache_condition($map);
        
        $this->assign('_list', $list);
        $this->assign('_condition', $condition);
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

    	$id = is_array($id) ? implode(',',$id) : $id;
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
     * 修改初始化
     * @author tina
     */
    public function edit($id = 0){
    	if(IS_POST) {
    		
    		$store = M('Store')->where('id='. I('post.id'))->find();
    		if(!isset($store)) {
    		    $this->error('未找到门店信息！');
    		}
    		$Store = D('Store');
    		$data = $Store->create();
    		
    		if($data){
    			if($Store->save()){
    			    
    			    //修改字段值
    			    $district['name'] = $data['name'];
    			    $district['pid'] = $data['city_id'];
    			    M('District')->where('id='.$store['store_id'])->save($district);
    			    
    				S('DB_CONFIG_DATA',null);
    				//记录行为
    				action_log('update_store','store',$data['id'],UID);
    				$this->success('更新成功', 'Admin/Store/index');
    			} else {
    				$this->error('无更新');
    			}
    		} else {
    			$this->error($Store->getError());
    		}
    	} else {
    		if($id == 0) {
    			$this->error('无效的参数');
    		}
    		$data = M('Store')->field(true)->find($id);
    		if(false === $data){
    			$this->error("获取门店信息错误");
    		}
    		
    		//获取 ［区|城市］ 数据
    		$this->setDistrictSelectList($data['district_id'], 0);
    		
    		$this->assign('data',$data);
    		$this->assign('_district_id',$data['district_id']);
    		$this->assign('_city_id',$data['city_id']);
    		$this->meta_title = '修改门店信息';
    		$this->display();
    	}
    	
    }
    
  
    /**
     * 新增门店
     */  
    public function add(){

    	if(IS_POST){
    	    $param = I('post.');
    	    //step-1: 先保存district字典表中
    	    $district['type'] = District::$TYPE_STORE;
    	    $district['name'] = $param['name'];
    	    $district['pid'] = $param['city_id'];
    	    $store_id = M('District')->add($district);
    	    
    		//step-2: 保存门店信息
    		$Store = M('Store');
    		$param['store_id'] = $store_id;
    		$id = $Store->add($param);
    		
    		if($id){
    		  
    		  //step-3: 记录日志
    		  S('DB_CONFIG_DATA',null);
    	      action_log('add_store', 'Store', $id, UID);
    		  $this->success('新增成功', 'Admin/Store/index');
    			
    		} else {
    			$this->error($Store->getError());
    		}
    		
    	} else {
    		$this->meta_title = '新增门店';
    		
    		//获取大区数据
    		$this->setDistrictSelectList(0, 0);
    		
    		$this->display();
    	}
    }

   
}
