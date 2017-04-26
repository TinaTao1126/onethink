<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Admin\Service;

class CameraService{
	

	function __construct() {
		//print "In BaseClass constructor\n";
	}
	
	
	public function select_by_name($name=''){
	    if(empty($name)) {
	        return null;
	    }
	    
	    $where = array(
	    	'name'=>$name
	    );
	    return M('Camera')->where($where)->find();
	}
	
	
}