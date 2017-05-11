<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;
use Admin\Service\CameraService;

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
        //step-1: 组装查询条件
    	$cameraService = new CameraService();
    	$map = $cameraService->list_condition();
    	
    	//step-2: 根据条件查询记录
        $list   = $this->lists('Camera', $map);
        
        //step-3: 封装返回结果
        $list = $cameraService->combine_response($list);
        
        //下拉框字典值
        //获取 ［区|城市|门店］ 数据
        $this->setDistrictSelectList($map['district_id'], $map['city_id']);
        
        //缓存条件
        $this->assign('_condition', $cameraService->cache_condition($map));
        $this->assign('_list', $list);
        
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
    		
    		//step-1: 验证参数
    		$Camera = D('Camera');
    		$data = $Camera->create($param);
    		if($data) {
    		    $where['name'] = array('eq',$param['name']);
    		    $where['id'] = array('neq', $param['id']);
    		    
    		    //step-2: 验证摄像头名称是否被占用
    		    $camera = M('Camera')->where($where)->find();
    		    if(!empty($camera)) {
    		    	$this->error('摄像头名称已被使用！');
    		    }
    		    
    		    //step-3: 修改摄像头信息
    		    $id = $Camera->save($param);
    		    if ($id) {
    		    	S('DB_CONFIG_DATA', null);
    		    	// 记录行为
    		    	action_log('update_camera', 'camera', $camera['id'], UID);
    		    	$this->success('更新成功', 'Admin/Camera/index');
    		    } else {
    		    	$this->error('未修改任何内容！');
    		    }
    		} else {
    		    $this->error($Camera->getError());
    		}
    	
    	} else {
    		if($id == 0) {
    			$this->error('无效的参数');
    		}
    		
    		//获取摄像头信息
    		$data = M('Camera')->field(true)->find($id);
    		if(false === $data){
    			$this->error("获取摄像头信息错误");
    		}
    		
    		//获取 ［区|城市|门店］ 数据
    		$this->setDistrictSelectList($data['district_id'], $data['city_id']);
    		
    		//设置返回值
    		$this->assign('data',$data);
    		$this->assign('_district_id',$data['district_id']);
    		$this->assign('_city_id',$data['city_id']);
    		$this->assign('_store_id',$data['store_id']);
    		
    		$this->meta_title = '修改摄像头';
    		$this->display();
    	}
    	
    }
    
   

    public function add(){

    	if(IS_POST){
    		$param = I('post.');
    		$param['creator'] = UID;
    		$param['disabled'] = 1;   //启用
    		
    		//step-1: 查询摄像头名是否已经被使用
    		$where['name'] = $param['name'];
    		$camera = M('Camera')->where($where)->find();
    		if(!empty($camera)) {
    		    $this->error('摄像头名称已被使用！');
    		}
    		
    		//step-2: 验证字段
    		$Camera = D('Camera');
    		$data = $Camera->create($param);
    		
    		if($data) {
    		    //step-3: 保存摄像头信息
    		    $id = $Camera->add($param);
    		    if ($id) {
    		    	S('DB_CONFIG_DATA', null);
    		    	// 记录行为
    		    	action_log('add_camera', 'camera', $id, UID);
    		    	$this->success('新增成功', 'Admin/Camera/index');
    		    } else {
    		    	$this->error('新增失败');
    		    }
    		} else {
    			$this->error($Camera->getError());
    		}
    		
    	} else {
    	    //根据权限获取数据
    	    
    	    
    	    //获取 ［区］ 数据
    	    $this->setDistrictSelectList(0, 0);
    	    //$this->assign('store_id',session('store_id'));
    	    
    		$this->meta_title = '新增摄像头';
    		$this->display();
    	}
    }

   
}
