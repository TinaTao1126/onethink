<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Admin\Service;

class DistrictService{
	

	function __construct() {
		//print "In BaseClass constructor\n";
	}
	
	
	public function select($type=1,$pid=0){
	    $District = M('District');
	    $condition['type'] = $type;
	    $condition['pid'] = $pid;
	    $district = $District->where($condition)->select();
	    return $district;
	}
	
	public function listByIds($ids = array()) {
	    if(empty($ids)) {
	        return array();
	    }
	    $where = array();
	    $where['id'] = array('in',$ids);
	    return M('District')->where($where)->select();
	}
}