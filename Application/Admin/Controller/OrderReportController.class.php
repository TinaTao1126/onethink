<?php
namespace Admin\Controller;
use Admin\Enums\Order;
use Admin\Enums\District;
use Admin\Service\DistrictService;
use Admin\Service\OrderItemService;
use Admin\Service\OrderReportService;
use Admin\Service\OrderService;
use Admin\Service\CarService;
use Admin\Enums\OrderItem;
use Admin\Enums\RoleKey;

class OrderReportController extends AdminController {
    
    /**
     * 报表
     */
    public function index(){
    	 
    	$districtService = new DistrictService();
    	$distirct = $districtService->selectStoreGroupByDistrict();
    	// echo json_encode($distirct);
    	 
    	$this->assign('_list', $distirct);
    	$this->assign('_item_type', OrderItem::$ITEM_TYPE);
    	$this->meta_title = "报表统计";
    	$this->display();
    
    }
    
    /**
     * 报表详情页
     */
    public function detail(){
    
        $orderReportService = new OrderReportService();
    	$where = $orderReportService->encapsule_condition();
    	
    
        //查询sql    
    	$Order = M('Order');
    	$sql = '
        select store_id, district_id, item_type,  sum(purchase_amount) total_amount
        from onethink_order `order`
	    left join onethink_order_item order_item on `order`.id=order_item.car_order_id '.
    	    $where.
    	    ' group by store_id, item_type';
    
    	//echo $sql;
    	$list = $Order->query($sql);
    
    
    	$storeIdList = getFieldMap($list,$field="store_id");
    	$districtIdList = getFieldMap($list,$field="district_id");
    
    	$idList = array_merge($storeIdList, $districtIdList);
    
    
    	$districtService = new DistrictService();
    
    	if(!empty($idList)) {
    		$where['id'] = array('in',$idList);
    		$districtList = $districtService->listByIds($idList);
    
    
    		$districtMap = array();
    		foreach ($districtList as $key => $val) {
    			$districtMap[$val['id']] = $val;
    		}
    
    	}
    
    	//返回结果
    	$data = array();
    
    	$order_item_keys = array_keys(OrderItem::$ITEM_TYPE);
    		
    	//组装数据
    	foreach ($list as $key => $val) {
           
    	    
    		if(in_array($val['item_type'], $order_item_keys))  {
    		    $property_name = OrderItem::$ITEM_TYPE[$val['item_type']]['en_name'];
    		    $val['total_'.$property_name]  =  $val['total_amount'];
    		    unset($val['total_amount']);
    		}
    		
    		if(!empty($districtMap)) {
    			$district = $districtMap[$val['district_id']];
    			$store = $districtMap[$val['store_id']];
    			$val['district_name'] = $district['name'];
    			$val['store_name'] = $store['name'];
    		}
    
    		if(array_key_exists($val['store_id'], $data)) {
    			$data[$val['store_id']] = array_merge($val, $data[$val['store_id']]);
    		} else {
    			$data[$val['store_id']] = $val;
    		}
    
    	}
    	
    	//计算总金额
    	foreach ($data as $key=>$val) {
    	    $_keys = array_keys($val);
    	    
    	    $total_amount = 0;
    	    foreach ($_keys as $val_key) {
    	        if($this->startwith($val_key, 'total_')) {
    	            
    	            $total_amount += (float)$val[$val_key];
    	        }
    	    }
    	    
    	    $data[$key]['total_amount'] = $total_amount;
    	}
    
    	$this->assign('condition', I('post.'));
    	$this->assign('_list', $data);
    	$this->meta_title = "报表统计";
    	$this->display();
    
    
    }
    
    public function chart(){
		
        $orderReportService = new OrderReportService();
        $where = $orderReportService->encapsule_condition();
         
        
        //查询sql
        $Order = M('Order');
        $sql = '
        select store_id, district_id, item_type,  sum(purchase_amount) total_amount
        from onethink_order `order`
	    left join onethink_order_item order_item on `order`.id=order_item.car_order_id '.
        	    $where.
        	    ' group by store_id, item_type';
        
        //echo $sql;
        $list = $Order->query($sql);
        
        
        $storeIdList = getFieldMap($list,$field="store_id");
        $districtIdList = getFieldMap($list,$field="district_id");
        
        $idList = array_merge($storeIdList, $districtIdList);
        
        
        $districtService = new DistrictService();
        
        if(!empty($idList)) {
        	$where['id'] = array('in',$idList);
        	$districtList = $districtService->listByIds($idList);
        
        
        	$districtMap = array();
        	foreach ($districtList as $key => $val) {
        		$districtMap[$val['id']] = $val;
        	}
        
        }

		$data = array();

		$legend = array();
		$series = array();
		$xAxis = array();
		$order_nums_arr = array();

		$dateList = getFieldMap($list,$field="date");

		//echo json_encode($dateList).'12345';

	    		foreach ($list as $key=>$val) {
			$legend[] = $val['store_id'];
			$order_nums_arr[$val['store_id']][$val['date']] = $val['order_nums'];

		}

		foreach ($order_nums_arr as $key=> $val) {

			$notFoundDateList = array_diff($dateList, array_keys($val));

			if(!empty($notFoundDateList)) {
				foreach ($notFoundDateList as $date) {
	    					$val[$date] = '0';
				}
			}

			ksort($val);

			$series[] = array(
					'name'=>$key,
					'type'=>'line',
					'data'=>array_values($val)
			);
		}
		$data['legend'] = array_unique($legend);
		$data['series'] = $series;
		$data['xAxis'] = array_unique($dateList);

		$this->success($data);
    }
    
    function startwith($str,$pattern) {
    	if(strpos($str,$pattern) === 0)
    		return true;
    	else
    		return false;
    }
}

