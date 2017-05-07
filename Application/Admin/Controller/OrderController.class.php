<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;
use Admin\Enums\Order;
use Admin\Enums\District;
use Admin\Service\DistrictService;
use Admin\Service\OrderItemService;
use Admin\Service\OrderService;
use Admin\Enums\OrderItem;

/**
 * 汽车管理
 * @author tina
 *
 */
class OrderController extends AdminController {

    /**
     * 客户接待首页
     * @author tina
     */
    public function index(){
        //print_r(session("user_auth"));
        //封装参数
        $orderService = new OrderService();
        $map = $orderService->list_condition();
    	
        //只显示当天未结算的和等待开单的订单
    	$map['order_status'] = array('in', Order::$ORDER_STATUS_ACTIVE);
    	$list   = $this->lists('Order', $map);
    	
    	//组装返回数据
        $orderService = new OrderService();
        $list = $orderService->combine_response($list);
        
        //获取 ［区|城市|门店］ 数据
        $districtService = new DistrictService();
        $district = $districtService->select(District::$TYPE_DISTRICT, $pid=0);
        $city = $districtService->select(District::$TYPE_CITY, $pid=$map['district_id']);
    	$store = $districtService->select(District::$TYPE_STORE, $pid=$map['city_id']);
        
        $this->assign('_list', $list);
        $this->assign('_district',$district);
        $this->assign('_city',$city);
        $this->assign('_store',$store);
        $this->assign('_condition', $orderService->cache_condition($map));
        $this->meta_title = '客户接待';
        $this->display();
    }
    
    
    /**
     * 历史结算单
     * @author tina
     */
    public function history(){
        $orderService = new OrderService();
        $map = $orderService->list_condition();

    	
    	$map['order_status'] = array('in', Order::$ORDER_STATUS_HISTORY);
    	$list   = $this->lists('Order', $map);
    	 
    	//组装返回数据
    	$list = $orderService->combine_response($list);
    
    	//获取 ［区|城市|门店］ 数据
    	$districtService = new DistrictService();
    	$district = $districtService->select(District::$TYPE_DISTRICT, $pid=0);
    	$city = $districtService->select(District::$TYPE_CITY, $pid=$map['district_id']);
    	$store = $districtService->select(District::$TYPE_STORE, $pid=$map['city_id']);
    
    	$this->assign('_list', $list);
    	$this->assign('_district',$district);
    	$this->assign('_city',$city);
    	$this->assign('_store',$store);
    	$condition = $orderService->cache_condition($map);
    	$this->assign('_condition', $condition);
    	$this->meta_title = '结算单';
    	$this->display();
    }
    
    /**
     * 结算
     * @param unknown $id
     */
    public function settle(){
        //print_r(I('post.'));
    	$id = I('id');
    	if (empty($id) ) {
    		$this->error('请选择要操作的数据!');
    	}
    	
    	//检查参数
    	$map = array('id'=>$id);
    	$Order = M('Order');
    	$order = $Order->where($map)->find();
    	if(empty($order)){
    	    $this->error('未找到订单!');
    	}
    	
    	if($order['pay_status'] > 0){
    		$this->error('订单已结算！');
    	}
    	
    	$pay_amount = I('pay_amount');
    	$pay_amount = isset($pay_amount) ? ($pay_amount == 0 ? null : $pay_amount) : null;
    	
    	
    	$Order->order_status = Order::$ORDER_STATUS_201;
    	$Order->pay_time = date('Y-m-d H:i:s');
    	$Order->payee = UID;
    	$Order->pay_amount = empty($pay_amount) ? $order['purchase_amount'] : $pay_amount;
    	$Order->pay_remark = I('pay_remark');
    	$Order->pay_status = Order::$PAY_STATUS_1;
    	
        //$msg   = array_merge( array( 'success'=>'结算成功！', 'error'=>'结算失败！', 'url'=>'' ,'ajax'=>IS_AJAX) , (array)$msg );
    	if($Order->where($map)->save()){
    		S('DB_CONFIG_DATA',null);
    		//记录行为
    		action_log('add_order', 'Order', $id, UID);
    		
    		
    		//$this->success($msg['success'],$msg['url'],$msg['ajax']);
    		
    	    $this->success('结算成功', 'Admin/Order/index');
    	} else {
    		$this->error($msg['error']);
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
    	if(M('Order')->where($map)->delete()){
    		S('DB_CONFIG_DATA',null);
    		//记录行为
    		action_log('delete_order','order',$id,UID);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败！');
    	}
    }
    
    
    public function detail($id = 0){
        $this->get($id, $type="detail");
    }
    
    public function edit($id = 0){
    	$this->get($id, $type='edit');
    }
    
    public function get($id = 0, $type){

		if($id == 0) {
			$this->error('无效的参数');
		}
		$order = M('Order')->field(true)->find($id);
		if(false === $order){
			$this->error("请检查参数，不能获取到有效的数据！");
		}
		$car_id = $order['car_id'];
		$car = M('Car')->field(true)->find($car_id);
		if(false === $order){
			$this->error("无法获取到车辆信息！");
		}
		$orderItemService = new OrderItemService();
		$orderItemList = $orderItemService->listByOrderId($order['id']);
		
		$car['order_no'] = $order['order_no'];
		$car['pay_status'] = $order['pay_status'];
		$car['store_station_id'] = $order['store_station_id'];
		
		$this->assign('store_station_id',$order['store_station_id']);
		$this->assign('car_order_id',$order['id']);
		$this->assign('data',$car);
		$this->assign('isPrint',empty(I('print')) ? 0 : 1);
		$this->assign('_list',$orderItemList);
		
		$this->getCommonInfo();
		
		if($type == 'detail') {
		    $meta_title = empty(I('print')) ? "订单详情" : "打印订单";
		} else {
		    $meta_title = $order['order_status'] == 100 ? "开单" : "编辑订单";
		}
		
        $this->meta_title=$meta_title;   
        $this->display();
    }
    
    
    /**
     * 获取公共信息
     */
    public function add(){
        //从session获取门店信息
        
        $this->getCommonInfo();
        $this->display();
    }
   
   
    private function getCommonInfo(){
        $store_name = session("user_auth.store_name");
        $store_id = session('user_auth.store_id');
        if(is_null($store_id) || !isset($store_id)) {
        
        	$this->display();
        } else {
        	if(is_null($store_name) || !isset($store_name)) {
        		$Store = M('District')->where('id='.$store_id)->find();
        		$store_name = $Store['name'];
        	}
        
        	session('store_name',$store_name);
        	$this->assign("store_id", $Store['id']);
        	$this->assign("store_name", $store_name);
        }
        
        //获取工位信息
        $where = array(
        		"store_id"=> $store_id,
        		"status"=>0 //   空闲
        );
        $store_station = M('StoreStation')->where($where)->select();
        $this->assign("_store_station", $store_station);
    }
   
    /**
     * 查询新车入场数量
     */
    function count(){
        $count = M('Order')->field('count(1) count')->where('order_status='.Order::$ORDER_STATUS_100)->find();
        //echo $count['count'];
        $this->success($count['count']);
    }
    
    /**
     * 报表
     */
    public function report(){
 
           
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
    public function report_detail(){
    
//     		$Order = M('Order');
//     		$sql = '
//             select store_id, DATE_FORMAT( create_time, "%Y-%m-%d" ) date ,sum(purchase_amount) total_amount,count(1) order_nums
//             from onethink_order
//             group by store_id,DATE_FORMAT( create_time, "%Y-%m-%d" ) ';
//     		$list = $Order->query($sql);
    
//     		$data = array();
    
//     		$legend = array();
//     		$series = array();
//     		$xAxis = array();
//     		$order_nums_arr = array();
    
//     		$dateList = getFieldMap($list,$field="date");
    
//     		//echo json_encode($dateList).'12345';
    
//     		foreach ($list as $key=>$val) {
//     			$legend[] = $val['store_id'];
//     			$order_nums_arr[$val['store_id']][$val['date']] = $val['order_nums'];
    
//     		}
    
//     		foreach ($order_nums_arr as $key=> $val) {
    
//     			$notFoundDateList = array_diff($dateList, array_keys($val));
    
//     			if(!empty($notFoundDateList)) {
//     				foreach ($notFoundDateList as $date) {
//     					$val[$date] = '0';
//     				}
//     			}
    
//     			ksort($val);
    
//     			$series[] = array(
//     					'name'=>$key,
//     					'type'=>'line',
//     					'data'=>array_values($val)
//     			);
//     		}
//     		$data['legend'] = array_unique($legend);
//     		$data['series'] = $series;
//     		$data['xAxis'] = array_unique($dateList);
    
//     		$this->success($data);
    	
        $param = I('post.');
        $where = 'where store_id <> 0';
        //显示列
        
        
        if(!empty($param['item_type'])) {
            $where = ' and item_type='.$param['item_type'];
        } else {
            $show_columns = OrderItem::$ITEM_TYPE;
        }
        

		$Order = M('Order');
		$sql = '
        select store_id, district_id, item_type,  sum(purchase_amount) total_amount
        from onethink_order `order`
	    left join onethink_order_item order_item on `order`.id=order_item.car_order_id '.
	    $where.
        ' group by store_id, item_type';
		
		//echo $sql;
		$list = $Order->query($sql);
        
		
        $data = array();
        
        foreach ($list as $key => $val) {
            if($val['item_type'] == 1)  {
                $val['total_cleaning_amount'] = $val['total_amount'];
            }elseif ($val['item_type'] == 2){
                $val['total_repair_amount'] = $val['total_amount'];
            }elseif ($val['item_type'] == 3){
                $val['total_maintain_amount'] = $val['total_amount'];
            }elseif ($val['item_type'] == 4){
                $val['total_beauty_amount'] = $val['total_amount'];
            }
        	
            if(in_array($val['item_type'], OrderItem::$ALL_ITEM_TYPE_ID)) {
                $total_amount = (empty($val['total_cleaning_amount']) ? 0 : $val['total_cleaning_amount'])
                    + (empty($val['total_repair_amount']) ? 0 : $val['total_repair_amount'])
                    + (empty($val['total_maintain_amount']) ? 0 : $val['total_maintain_amount'])
                    + (empty($val['total_beauty_amount']) ? 0 : $val['total_beauty_amount']);
                
                $data[] = $val;
            }
            
        }
        
        
        $this->assign('_list', $data);
        $this->meta_title = "报表统计";
        $this->display();
        
    
    }
}
