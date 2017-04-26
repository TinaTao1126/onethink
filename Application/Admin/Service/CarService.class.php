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
			
			throw_exception("请求参数不全");
		}
		$AlarmInfoPlate = $param['AlarmInfoPlate'];
		
		if(!isset($AlarmInfoPlate['result'])) {
			throw_exception("请求参数不全");
		}
		$Result = $AlarmInfoPlate['result'];
		
		if(!isset($Result['PlateResult'])) {
			throw_exception("请求参数不全");
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
		} catch (\Exception $e) {
			//记录日志 TODO
			//Log::write($message);
		}
	}
	
	private function save_base64_image($base64Code, $image_file){
	    header('Content-type:text/html;charset=utf-8');
	    
	    //保存base64字符串为图片
	    //匹配出图片的格式
	    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64Code, $result)){
	    	$type = $result[2];
	    	$new_file = $image_file.'.'.$type;
	    	//echo $new_file;exit;
	    	if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64Code)))){
	    		//echo '新文件保存成功：', $new_file;
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
	 * 添加车辆信息，如果存在，则修改，不存在，则添加
	 * @return 
	 */
	public function editCar() {
		$param   =   I('post.');
		$Car = M('Car');
		$where = array('car_number'=>$param['car_number']);
		$data = $Car->where($where)->find();
		$response = array("code"=>500);
		if(isset($data) && !empty($data)) {
			//如果存在，则修改否则添加
			$DCar = M('Car');
			if($DCar->save($param, array('where'=>'id='.$data['id']))) {
				S('DB_CONFIG_DATA',null);
				//记录行为
				action_log('edit_car', 'Car', $data['id'], UID);
			}else {
			    $response['msg'] = $DCar->getError();
			    return $response;
			}
			
			$response['code'] = 200;
			$response['data'] = $data['id'];
			$response['msg'] = " 修改成功";
			//print_r($response);
		} else {
			//echo 'add';
			//不存在则新增
			$id = M('Car')->add($param);
			if($id){
				S('DB_CONFIG_DATA',null);
				//记录行为
				action_log('add_car', 'Car', $id, UID);
				$response['code'] = 200;
				$response['data'] = $id;
				$response['msg'] = "新增成功";
			} else {
				//print_r($Car->getDbError());
				$response['msg'] = '新增失败！';
			}
		
		} 
		return $response;
	}
}