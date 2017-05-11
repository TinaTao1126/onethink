<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Admin\Service;
use Admin\Enums\RoleKey;
use Admin\Enums\StoreStation;


class StoreStationService{
	
    /**
     * 根据角色显示查询条件
     */
	public function list_condition(){
	    $district_id       =   I('district_id');
	    $city_id       =   I('city_id');
	    $store_id       =   I('store_id');
	     
	    //step-1: 取出用户的role_key
	    $role_key = session('user_auth.role_key');
	    if(empty($role_key)) {
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
				'_disabled'=>$map['select_disabled']
		);
		return $condition;
	}
	
	public function combine_response($list){
	    //获取所有车辆信息
	    $storeIdList = getFieldMap($list,$field="store_id");
	    
	    if(isset($storeIdList) && !empty($storeIdList)) {
	    	//根据汽车id，批量取出汽车信息
	    	$where['id'] = array('in',$storeIdList);
	    	$storeList = M('District')->where($where)->select();
	    
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
	    return $list;
	}
}