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
use Admin\Enums\StoreStation;
use Admin\Enums\District;

/**
 * 摄像头管理
 * @author tina
 *
 */
class CameraController extends AdminController {

    /**
     * 
     */
    public function index(){
        
    	$district_id       =   I('district_id');
    	$city_id       =   I('city_id');
    	$store_id       =   I('store_id');
    	
    	//step-1: 取出用户的role_key
    	$role_key = session('user_auth.role_key');
    	if(!isset($role_key)) {
    	    //FIXME redirect to login
    	    
    	}
    	
    	//如果不是admin，则取当前用户绑定的大区、城市、门店
    	if($role_key != 'admin') {
    	   $user = M('Member')->where('uid='.UID)->find();
    	   $district_id = $user['district_id'];
    	   $city_id = $user['city_id'];
    	   $store_id = $user['store_id'];
    	    
    	} 
    	
    	
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
    	
        $list   = $this->lists('Camera', $map);
        
        //获取所有车辆信息
        $storeIdList = getFieldMap($list,$field="store_id");
        
        if(isset($storeIdList) && !empty($storeIdList)) {
        	//根据汽车id，批量取出汽车信息
        	$where['id'] = array('in',$storeIdList);
        	$storeList = M('District')->where($where)->select();
        	 
        	//key:id, val:car
        	//$carMap＝array_combine(array_column($carList,"id"), $carList);
        	$storeMap = array();
        	foreach ($storeList as $key => $val) {
        		$storeMap[$val['id']] = $val['name'];
        	}
        	 
        }
        
        foreach ($list as $key=>$val) {
        	//设置汽车信息
        	if(isset($storeMap)) {
        		$list[$key]['store_name'] = $storeMap[$val['store_id']];
        	}
        	 
        	//设置状态对应的名称
        	$disabled_name = StoreStation::$DISTABLED[$val['status']];
        	$list[$key]['disabled_name'] = isset($disabled_name) ? $disabled_name : "";
        	
        	 
        }
        
        //下拉框字典值
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
        $this->assign('_disabled', $role_key == 'admin' ? '' : 'disabled');
        $this->meta_title = '摄像头信息';
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
    			$this->forbid('Camera', $map );
    			break;
    		case 'enabled':
    			$this->resume('Camera', $map );
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
    	if(M('Camera')->where($map)->delete()){
    		S('DB_CONFIG_DATA',null);
    		//记录行为
    		action_log('delete_camera','camera',$id,UID);
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
    		
    		
    		$param = I('post.');
    		
            $Camera = M('Camera');
            
            $where['name'] = array('eq',$param['name']);
            $where['id'] = array('neq', $param['id']);
            
            $data = $Camera->where($where)->find();
            if(isset($data) || !empty($data)) {
                $this->error('摄像头名称已被使用！');
            }
            
            $result = $Camera->where('id='.$param['id'])->save($param);
            if ($result) {
                S('DB_CONFIG_DATA', null);
                // 记录行为
                action_log('update_camera', 'store', $data['id'], UID);
                $this->success('更新成功', 'Admin/Camera/index');
            } else {
                $this->error('未修改任何内容！');
            }
    		
    	} else {
    		if($id == 0) {
    			$this->error('无效的参数');
    		}
    		$data = M('Camera')->field(true)->find($id);
    		if(false === $data){
    			$this->error("获取车位信息错误");
    		}
    		$this->assign('data',$data);
    		
    		$districtService = new DistrictService();
    		$district = $districtService->select($type=1, $pid=0);
    		$city = $districtService->select($type=2, $pid=$data['district_id']);
    		$store = $districtService->select($type=3, $pid=$data['city_id']);
    		
    		$this->assign('_district',$district);
    		$this->assign('_city',$city);
    		$this->assign('_store',$store);
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
    		
    		//查询摄像头名是否已经被使用
    		$where = 'name='.$param['name'];
    		$camera = M('Camera')->where($where)->find();
    		if(isset($camera) || !empty($camera)) {
    		    $this->error('摄像头名称已被使用！');
    		}
    		
    		$id = M('Camera')->add($param);
            if ($id) {
                S('DB_CONFIG_DATA', null);
                // 记录行为
                action_log('add_camera', 'camera', $id, UID);
                $this->success('新增成功', 'Admin/Camera/index');
            } else {
                $this->error('新增失败');
            }
    		
    			
    		
    	} else {
    	    //根据权限获取数据
    	    
    	    
    	    $districtService = new DistrictService();
    	    $district = $districtService->select();
    	    $this->assign('_district',$district);
    	    //$this->assign('store_id',session('store_id'));
    	    
    		$this->meta_title = '新增摄像头';
    		$this->display();
    	}
    }

   
}
