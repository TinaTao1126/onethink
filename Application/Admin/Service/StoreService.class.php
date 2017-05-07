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


class StoreService{
	
    /**
     * 根据角色显示查询条件
     */
	public function list_condition(){
	    $district_id       =   I('district_id');
	    $city_id       =   I('city_id');
	    $store_name       =   I('name');
	     
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
	    if(!empty($store_name)) {
	        $map['store_name'] = $store_name;
	    	$map['name']=array('like', '%'.$store_name.'%');
	    }
	    
	    //根据权限筛选条件
	    if(!empty(session('user_auth.role_key')) && session('user_auth.role_key') != RoleKey::$ADMIN){
	    	$map['district_id']=session('user_auth.district_id');
	    	$map['city_id']=session('user_auth.city_id');
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
				'_name'=>$map['store_name'],
				'_disabled'=>$map['select_disabled']
		);
		return $condition;
	}
}