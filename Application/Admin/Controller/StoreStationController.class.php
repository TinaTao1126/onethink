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
use Admin\Service\StoreStationService;
use Admin\Enums\StoreStation;
use Admin\Enums\District;

/**
 * 门店车位管理
 * @author tina
 *
 */
class StoreStationController extends AdminController {

    /**
     * 车位管理
     * step-1: 找到当前用户的auth_group role_key是admin、manager、sa
     * step-2: 根据角色限制筛选条件，admin 大区、城市、门店的工位信息均可查看，manager、sa只能查看当前门店工位信息
     * step-3: 
     * @author tina
     */
    public function index(){
        
    	
    	
    	$storeStationService = new StoreStationService();
    	$map = $storeStationService->list_condition();
    	
        $list   = $this->lists('StoreStation', $map);
        
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
        $city = $districtService->select(District::$TYPE_CITY, $pid=$map['district_id']);
    	$store = $districtService->select(District::$TYPE_STORE, $pid=$map['city_id']);
        
        $condition = $storeStationService->cache_condition($map);
        
        $this->assign('_list', $list);
        $this->assign('_district',$district);
        $this->assign('_city',$city);
        $this->assign('_store',$store);
        $this->assign('_condition', $condition);
        
        $this->meta_title = '车位信息';
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
    			$this->forbid('StoreStation', $map );
    			break;
    		case 'enabled':
    			$this->resume('StoreStation', $map );
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
    	if(M('StoreStation')->where($map)->delete()){
    		S('DB_CONFIG_DATA',null);
    		//记录行为
    		action_log('delete_store_station','store_station',$id,UID);
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
    		$Store = D('StoreStation');
    		$data = $Store->create();
    		if($data){
    			if($Store->save()){
    				S('DB_CONFIG_DATA',null);
    				//记录行为
    				action_log('update_store','store',$data['id'],UID);
    				$this->success('更新成功', 'Admin/StoreStation/index');
    			} else {
    				$this->error('更新失败');
    			}
    		} else {
    			$this->error($Store->getError());
    		}
    	} else {
    		if($id == 0) {
    			$this->error('无效的参数');
    		}
    		$data = M('StoreStation')->field(true)->find($id);
    		if(false === $data){
    			$this->error("获取车位信息错误");
    		}
    		$this->assign('data',$data);
    		
    		$districtService = new DistrictService();
    		$district = $districtService->select($type=1, $pid=0);
    		$this->assign('_district',$district);
    		$district = $districtService->select($type=2, $pid=$data['district_id']);
    		$this->assign('_city',$district);
    		$district = $districtService->select($type=3, $pid=$data['city_id']);
    		$this->assign('_store',$district);
    		$this->assign('_district_id',$data['district_id']);
    		$this->assign('_city_id',$data['city_id']);
    		$this->assign('_store_id',$data['store_id']);
    		
    		$this->meta_title = '修改车位信息';
    		$this->display();
    	}
    	
    }
    
    /**
     * 更新
     * @author tina
     */
    public function save(){
    	$res = D('StoreStation')->update();
    	if(!$res){
    		//print_r(D('Store')->getError());exit;
    		$this->error(D('Store')->getError());
    	}else{
    		$this->success($res['id']?'更新成功！':'新增成功！', Cookie('__forward__'));
    	}
    }
    

    public function add(){

    	if(IS_POST){
    		$param = I('post.');
    		$param['creator'] = UID;
    		$param['disabled'] = 1;   //启用
    		print_r($param);exit;
    		$id = M('StoreStation')->add($param);
    		if($id){
    				S('DB_CONFIG_DATA',null);
    				//记录行为
    				action_log('add_store_station', 'store_station', $id, UID);
    				$this->success('新增成功', 'Admin/StoreStation/index');
    			} else {
    				$this->error('新增失败');
    			}
    		
    		
    	} else {
    	    //根据权限获取数据
    	    
    	    
    	    $districtService = new DistrictService();
    	    $district = $districtService->select();
    	    $this->assign('_district',$district);
    	    //$this->assign('store_id',session('store_id'));
    	    
    		$this->meta_title = '新增工位';
    		$this->display();
    	}
    }

   
}
