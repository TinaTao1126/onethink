<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Admin\Service;
use Admin\Enums\Order;
use Admin\Enums\District;
use Admin\Service\DistrictService;
use Admin\Service\OrderItemService;
use Admin\Service\OrderService;
use Admin\Service\CarService;
use Admin\Enums\OrderItem;
use Admin\Enums\RoleKey;

class OrderReportService{
	

	function __construct() {
		//print "In BaseClass constructor\n";
	}
	
	/**
	 * 封装查询条件
	 * @param unknown $param
	 */
	public function encapsule_condition(){
	    $param = I('post.');
	    
	    $where = 'where store_id <> 0';
	    //显示列
	    
	    
	    if(!empty($param['item_type'])) {
	    	$where .= ' and item_type='.$param['item_type'];
	    } else {
	    	$show_columns = OrderItem::$ITEM_TYPE;
	    }
	    
	    if (!empty($param['start_time'])) {
	    	$where .= ' and order.create_time>=\''. $param['start_time'].'\'';
	    }
	    
	    if (!empty($param['end_time'])) {
	    	$where .= ' and order.create_time<=\''. $param['end_time'].' 23:59:59\'';
	    }
	    
	    if(!empty($param['ids'])) {
	    	$ids = array_unique((array)I('ids',0));
	    
	    	$ids = is_array($ids) ? implode(',',$ids) : $ids;
	    	$where .= ' and store_id in ('.$ids.')';
	    }
	    
	    return $where;
	}
	
	
}