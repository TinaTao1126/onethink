<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Admin\Service;
use Think\Upload;
use Admin\Service\OrderService;
use Admin\Enums\Order;
use Admin\Enums\Camera;
use Think\Exception;
use Think\Log;

class CarService{
	
	const IMAGEDIR = "/Applications/MAMP/htdocs/onethink/image/";

	function __construct() {
		//print "In BaseClass constructor\n";
	}
	/**
	 * 验证并且组装数据
	 * @param unknown $param
	 * @return unknown
	 */
	public function encapsuleData($param){
		//echo 123;exit;
		if(!isset($param['AlarmInfoPlate'])) {
			E("请求参数不全");
		}
		$AlarmInfoPlate = $param['AlarmInfoPlate'];
		
		if(!isset($AlarmInfoPlate['result'])) {
			E("请求参数不全");
		}
		$Result = $AlarmInfoPlate['result'];
		
		if(!isset($Result['PlateResult'])) {
			E("请求参数不全");
		}
		$PlateResult = $Result['PlateResult'];
		$data = array();

		$carInfo = array(
			'car_number' => $PlateResult['license'],
			'car_color'=> $PlateResult['carColor'],
			'model_type'=> $Result['CarType'] 
		);
		$data['deviceName'] = $AlarmInfoPlate['deviceName'];
		$data['carInfo'] = $carInfo;
		$data['imageFile'] = $Result['imageFile']; 
		
		
		return $data;
	}
	
	
	public function uploadImage($base64Code){
		$date = date('Y/m/d');
		
		$fileName = time().'_'.rand(1,1000);
		$uploadDir = self::IMAGEDIR.$date;
		$uploadFile = $uploadDir.'/'.$fileName;
		if(!file_exists($uploadDir)){
			$isSuccess = mkdir($uploadDir,0777,true);
		}

		//保存图片
		try{
			$fileName = $this->save_base64_image($base64Code,$uploadFile);
			//echo $fileName;exit;
			$file = array(
					'name'=>$fileName,
					'tmp_name'=>$fileName,
					'fileBody'=>file_get_contents($fileName)
			);
			$url = $this->uploadToQiniu($file);
			
			//上传成功删除本地服务器文件
			unlink($fileName);
			
			return $url;
		} catch (Exception $e) {
			//记录日志 
			Log::write($message);
		}
	}
	
	private function save_base64_image($base64Code, $image_file){
	    header('Content-type:text/html;charset=utf-8');
	    
	    //保存base64字符串为图片
	    //匹配出图片的格式
	    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64Code, $result)){
	    	$type = $result[2];
	    	$new_file = $image_file.'.'.$type;
	    	if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64Code)))){
	    		return $new_file;
	    	}
	    }
	}
	

	public function uploadToQiniu($file){
		
		/* 调用文件上传组件上传文件 */
		try{
		  $uploader = new Upload(C('PICTURE_UPLOAD'), C('PICTURE_UPLOAD_DRIVER'),C('UPLOAD_QINIU_CONFIG'));
		  $info   = $uploader->uploadOne($file);
		  return $info['url'];
		} catch(Exception $e) {
		    
		    return null;
		}
	    

	}
	
	/**
	 * 新车入场
	 */
	public function carIn($data){
	    Log::writeInfo('start to excute carIn in CarService', 'car');
	    $carInfo = $data['carInfo'];
	    //step-2-2: 检查车牌信息是否已经存在
	    $carInDb = M('Car')->where(array('car_number'=>$carInfo['car_number']))->find();
	    Log::writeInfo('step-1: find car by car_number, param:'.$carInfo['car_number'].', result:'.json_encode($carInDb), 'car');
	    if(!empty($carInDb)) {
	       E("车牌信息已经存在！");
	    }
	     
	    $carOutIn = array(
	    	'type' => Camera::$TYPE_IN,
	       'camera_id' => $data['camera_id'],
	        'car_number' => $carInfo['car_number']
	        
	    );
	    $result = M('CarOutIn')->add($carOutIn);
	    Log::writeInfo('step-2: save car_out_in record, param:'.json_encode($carOutIn).', result:'.$result, 'car');
	    
	    //step-1 :保存图片
	    try{
	        Log::writeInfo('step-3: start to upload image', 'car');
	    	$url = $this->uploadImage($data['imageFile']);
	    } catch (\Exception $e) {
	    	//记录日志 TODO
	    	//Log::write($message);
	    }
	    
	    //step-2: 保存汽车信息
	    $carInfo['img_url'] = isset($url) ? $url : '';
	    
	    $carId = M('Car')->add($carInfo);
	    Log::writeInfo('step-4: save car, param:'.json_encode($carInfo).', result:'.$carId, 'car');
	     
	    //step-3: 生成临时订单
	    Log::writeInfo('step-5: start to create temp order', 'car');
	    if(isset($carId) && !empty($carId)) {
	    	$order = $carInfo;
	    	$order['car_id'] = $carId;
	    	$order['order_status'] = Order::$ORDER_STATUS_0;//新车入场
	    	$order['store_id'] = $data['store_id'];
	    	$order['district_id'] = $data['district_id'];
	    	$order['city_id'] = $data['city_id'];
	    
	    	Log::writeInfo('step-5-1: order:'.json_encode($order), 'car');
	    	$orderService = new OrderService();
	    	$order_id = $orderService->addOrder($order);
	    	Log::writeInfo('step-5-2: succeed to create temp order, result:'.$order_id, 'car');
	    	$response['data'] = array(
	    			'car_id'=>$carId,
	    			'order_id'=>$order_id,
	    	);
	    
	    	return $response;
	   // 	$this->success($response);
	    } else {
	        return null;
	    //	$this->fail('600', $data=array(), $msg=array("保存车辆信息失败！"));
	    }
	    
	    return $response;
	}
	
	
	/**
	 * 汽车离场
	 * 将已经结算的订单
	 * 
	 */
	public function carOut($data){
	    $carInfo = $data['carInfo'];
	    
	    $carOutIn = array(
	    		'type' => Camera::$TYPE_OUT,
	    		'camera_id' => $data['camera_id'],
	    		'car_number' => $carInfo['car_number']
	    		 
	    );
	    M('CarOutIn')->add($carOutIn);
	    
	    //step-0: 查询车牌信息
	    $Car = M('Car')->where(array('car_number'=>$carInfo['car_number']))->find();
	    
	    //step-1: 判断车辆信息是否存在
	    if(!empty($Car)) {
	        $where['car_id'] = array('eq', $Car['id']);
	        $where['order_status'] = array('neq', Order::$ORDER_STATUS_200);
	    	$OrderList = M('Order')->where($where)->select();
	    	// print_r($OrderList);
	    	if(!empty($OrderList)) {
	    	    
	    	    //$idList = getFieldMap($OrderList, $field="id");
	    	    foreach ($OrderList as $key => $value) {
	    	    	$idList[] = $value['id'];
	    	    }
	    	    $data['order_status'] = Order::$ORDER_STATUS_300;
	    	    $where['id'] = array('in', $idList);
	    	    $response = M('Order')->where($where)->save($data);
	    	    
	    	    return $response;
	    	}
	    	
	    } 
	}
	
	
	/**
	 * 添加车辆信息，如果存在，则修改，不存在，则添加
	 * @return 
	 */
	public function editCar() {
		$param   =   I('post.');
		$Car = D('Car');
		$data = $Car->create($param);
		//print_r($Car->getError());
		if($data) {
		    $where = array('id'=>$param['id']);
		    $car = $Car->where($where)->find();
		    
		    if(!empty($car)) {
		    	//如果存在，则修改否则添加
		    	if($Car->save($param, array('where'=>'id='.$car['id']))) {
		    		S('DB_CONFIG_DATA',null);
		    		//记录行为
		    		action_log('edit_car', 'car', $car['id'], UID);
		    	}
		    		
		    	return $car['id'];
		    } else {
		    	//不存在则新增
		    	$id = M('Car')->add($param);
		    	if($id){
		    		S('DB_CONFIG_DATA',null);
		    		//记录行为
		    		action_log('add_car', 'Car', $id, UID);
		    		return $id;
		    	} else {
		    		return 0;
		    	}
		    
		    }
		} else {
            E($Car->getError());
		}
		
	}
	
	
	public function getCarMap($carIdList){
	    if(!empty($carIdList)) {
	    	//根据汽车id，批量取出汽车信息
	    	$where['id'] = array('in',$carIdList);
	    	$carList = M('Car')->where($where)->select();
	    
	    	//key:id, val:car
	    	//$carMap＝array_combine(array_column($carList,"id"), $carList);
	    	$carMap = array();
	    	foreach ($carList as $key => $val) {
	    		$carMap[$val['id']] = $val;
	    	}
	       return $carMap;
	    }
	    
	}
}