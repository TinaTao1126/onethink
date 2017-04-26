<?php
namespace Api\Provider\Admin;
use Api\Provider\IndexProvider;
use Admin\Service\CarService;
use Admin\Service\OrderService;
use Admin\Service\CameraService;
use Admin\Enums\Order;
use Think\Exception; 

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
        //step－1: 解析封装数据
        $carService = new CarService();
        try{
        	$data = $carService->encapsuleData($param);
        } catch (Exception $e) {
        	
        	$this->fail(600, array(), array('msg'=>$e->getMessage()));
        }
        
        if(!isset($data)) {
        	$this->fail(600, array(), array('msg'=>"服务器异常"));
        }
        
        
        $carInfo = $data['carInfo'];
        
        //step-2-1: 检查是否能获取到摄像头门店id
        $cameraService = new CameraService();
        $camera = $cameraService->select_by_name($data['deviceName']);
        if(!isset($camera)) {
        	$this->fail('600', $data=array(), $msg=array("无法找到摄像头的门店信息！"));
        }
        
        //step-2-2: 检查车牌信息是否已经存在
        if(isset($carInfo['car_number'])) {
        	$carInDb = M('Car')->where(array('car_number'=>$carInfo['car_number']))->find();
        	if(!empty($carInDb)) {
        		//TODO
        		$this->fail('600', $data=array(), $msg=array("车辆信息已经存在！"));
        	} 
        } else {
        	$this->fail('600', $data=array(), $msg=array("无车牌信息！"));
        }
        
        //step-3 :保存图片
        try{
        	$url = $carService->uploadImage($data['imageFile']);
        } catch (\Exception $e) {
        	//记录日志 TODO
        	//Log::write($message);
        }

        //step-3: 保存汽车信息
        $carInfo['img_url'] = isset($url) ? $url : '';
        
        $carId = M('Car')->add($carInfo);
               
        //step-4: 生成临时订单
        if(isset($carId) && !empty($carId)) {
            $order = $carInfo;
            $order['car_id'] = $carId;
            $order['order_status'] = Order::$ORDER_STATUS_100;//新车入场
            $order['store_id'] = $camera['store_id']; 
            
            $orderService = new OrderService();
            $result = $orderService->addOrder($order);
            $response['data'] = array(
            		'car_id'=>$carId,
            		'order_id'=>$result['data'],
            );
            
            $this->success($response);
        } else {
            $this->fail('600', $data=array(), $msg=array("保存车辆信息失败！"));
        }
    }

}
