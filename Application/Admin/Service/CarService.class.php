<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Admin\Service;


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
		//id

//car_lit
//car_year
//car_desc
//car_vin
//car_engine_num
//remark
//member_id
//create_time
		$carInfo = array(
			'car_number' => $PlateResult['license'],
			'car_color'=> $PlateResult['carColor'],
			'model_type'=> $Result['CarType'] 
		);
		$data['carInfo'] = $carInfo;
		$data['imageFile'] = $Result['imageFile']; 
		
		
		return $data;
	}
	
	public function uploadImage($base64Code){
		$date = date('Y/m/d');
		
		$fileName = time().'_'.rand(1,1000);
		$uploadDir = self::IMAGEDIR.$date;
		$uploadFile = $uploadDir.'/'.$fileName;
		//echo $uploadDir."****";
		if(!file_exists($uploadDir)){
			//echo "not exists";
			$isSuccess = mkdir($uploadDir,0777,true);
// 			if(!$isSuccess) {
// 				echo 'failure';
// 			}
		}
		//echo file_exists_case($uploadDir);
		//echo $uploadFile;exit;
		//保存图片
		try{
			$fileName = save_base64_image($base64Code,$uploadFile);
			//echo $fileName;exit;
			$this->uploadOne($fileName);
		} catch (Exception $e) {
			//记录日志 TODO
			//Log::write($message);
		}
	}
	
	public function uploadOne($fileName){
// 		$file = $_FILES['qiniu_file'];
		$file = array(
				'name'=>'file',
				'fileName'=>$fileName,
				'fileBody'=>file_get_contents($fileName)
		);
		$config = array();
		try{
			$this->uploadToQiniu($file);
		}catch (Exception $e) {
			print_r($e);exit;
		} 
		//$result = $this->qiniu->upload($config, $file);
		if($result){
			$this->success('上传成功','', $result);
		}else{
// 			$this->error('上传失败','', array(
// 					'error'=>$this->qiniu->error,
// 					'errorStr'=>$this->qiniu->errorStr
// 			));
		}
		exit;
	}
	
	public function uploadToQiniu($file){
		$config = array(
				'accessKey'=>'dalSQLfpzok2VmcFGRgT6LO-KGhncJlwRzDeKlSN',
				'secrectKey'=>'pYx_nlDpVL4TS32ENYU5M3SwTy9dB17aMReyFlpc',
				'bucket'=>'cartest',
				'domain'=>'oo379l7i5.bkt.clouddn.com',
		);
		$qiniu = new QiniuStorage($config);
		$qiniu->upload(array(),$file);
		
		
// 		$upload = new Upload(C("qiniu"));
// 		$info = $upload->uploadOne($file);
// 		print_r($info);
	}
	
	/**
	 * 添加车辆信息，如果存在，则修改，不存在，则添加
	 * @return 
	 */
	public function editCar() {
		$param   =   I('post.');
		//print_r($param);exit;
		$Car = M('Car');
		//$Car->car_number=$param['car_number'];
		$where = array('car_number'=>$param['car_number']);
		$data = $Car->where($where)->find();
		$response = array("code"=>500);
		print_r($data);exit;
		if(isset($data) && !empty($data)) {
			//echo 'edit';
			//如果存在，则修改否则添加
			$result = $Car->save();
			if($result) {
				S('DB_CONFIG_DATA',null);
				//记录行为
				action_log('edit_car', 'Car', $data['id'], UID);
			}
			
			$response['code'] = 200;
			$response['data'] = $data['id'];
			$response['msg'] = " 修改成功";
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
				print_r($Car->getDbError());
				$response['msg'] = '新增失败！';
			}
		
		} 
		//print_r($response);exit;
		return $response;
	}
}