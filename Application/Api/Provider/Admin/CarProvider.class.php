<?php
namespace Api\Provider\Admin;
use Api\Provider\IndexProvider;
use Admin\Service\CarService;
use Admin\Service\CameraService;
use Admin\Enums\Camera;
use Think\Exception; 
use Think\Log;

/**
 * Description of ConstantProvider
 */
class CarProvider extends IndexProvider
{
    
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * 公共配置参数获取
     * @param $param
     */
    public function save($param)
    {
        //echo APP_PATH; exit;
        Log::writeInfo('start to execute save in CarProvider, param:' .json_encode($param),'car');
        //step－1: 解析封装数据
        $carService = new CarService();
        Log::writeInfo('step-0: parse param', 'car');
        try{
        	$data = $carService->encapsuleData($param);
        } catch (Exception $e) {
        	$this->fail(600, array(), array('msg'=>$e->getMessage()));
        }
        
        if(!isset($data)) {
        	$this->fail(600, array(), array('msg'=>"解析数据失败！"));
        }
        
        
        $carInfo = $data['carInfo'];
        
        //step-2-0: 检查车牌信息是否存在
        Log::writeInfo('step-1: check car_number', 'car');
        if(empty($carInfo['car_number'])) {
            Log::writeInfo('car_number empty!', 'car');
        	$this->fail('600', $data=array(), $msg=array("无车牌信息！"));
        }
         
        //step-2-1: 检查是否能获取到摄像头门店id
        $cameraService = new CameraService();
        $camera = $cameraService->select_by_name($data['deviceName']);
        Log::writeInfo('step-2: find store_id by camera name, deviceName:'.$data['deviceName'].', result:'.json_encode($camera), 'car');
        if(empty($camera)) {
        	$this->fail('600', $data=array(), $msg=array("无法找到摄像头的门店信息！"));
        }
        
        $data['store_id'] = $camera['store_id'];
        $data['camera_id'] = $camera['id'];
        
        //step-2: 根据摄像头类型来选择以下操作
        if($camera['type'] == Camera::$TYPE_IN) {
            Log::writeInfo('step-3: call CarService.carIn', 'car');
            //入口摄像头，新车入场
            try{
                $response = $carService->carIn($data);
            } catch (Exception $e) {
        	   $this->fail(600, array(), array('msg'=>$e->getMessage()));
            }
                
            $this->success($response);
            
        } elseif($camera['type'] == Camera::$TYPE_OUT) {
            Log::writeInfo('step-3: call CarService.carOut', 'car');
            //出口摄像头，汽车离场
            $response = $carService->carOut($data);
            if(empty($response)) {
            	$this->fail('600', $data=array(), $msg=array("保存车辆信息失败！"));
            } else {
            	$this->success($response);
            }
        }
        
    }

}
