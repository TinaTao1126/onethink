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
	
	
	public function selectStoreGroupByDistrict() {
	    $District = M('District');
	    $sql = 'select d.id district_id, d.name district_name, s.id store_id, s.name store_name from onethink_district d
	    inner join onethink_district c on c.pid=d.id and c.type=2 and d.type=1
	    inner join onethink_district s on s.pid=c.id and s.type=3';
	   
	    $list = $District->query($sql);
	    
	    
	    $data = array();
	    foreach ($list as $key=>$row) {
	        
	        if(array_key_exists($row['district_id'], $data)) {
	            $data[$row['district_id']][] = array("store_id"=>$row['store_id'], "store_name"=>$row['store_name']);
	            $data[$row['district_id']]['district_name'] = $row['district_name'];
	        } else {
	            $data[$row['district_id']][] = array("store_id"=>$row['store_id'], "store_name"=>$row['store_name']);
	        }
	        
	    }
	    return $data;
	    
	}
	
}