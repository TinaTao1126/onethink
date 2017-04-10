<?php
namespace Api\Provider\Admin;
use Api\Provider\IndexProvider;
use Admin\CarService;
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
//         $version=isset($param['version'])?$param['version']:'1.0.0';
//         // 公共配置信息
//         $constant=[
//             'version'=>$version,
//         ];
//         //错误信息返回
//         //$this->fail('101');
//         //成功返回
//         $this->success($constant);

        //echo 123;exit;
        $carService = new CarService();
        try{
        	$data = $carService->encapsuleData($param);
        } catch (Exception $e) {
        	
        	$this->fail(600, array(), array('msg'=>$e->getMessage()));
        }
        
        if(!isset($data)) {
        	$this->fail(600, array(), array('msg'=>"服务器异常"));
        }
        
        //保存图片
//         try{
//         	$carService->uploadImage($data['imageFile']);
//         } catch (Exception $e) {
//         	//记录日志 TODO
//         	//Log::write($message);
//         }

        // 保存汽车信息
        $carInfo = $data['carInfo'];
        //print_r($carInfo);exit;
        if(isset($carInfo['car_number'])) {
        	$carInDb = M('Car')->where(array('car_number'=>$carInfo['car_number']))->find();
        	if(!empty($carInDb)) {
        		//TODO
        		$this->fail('600', $data=array(), $msg=array("车辆信息已经存在！"));
        
        	}
        }
        
        //FIXME 加上异常
        $car = M('Car')->add($carInfo);
        $this->success($carInfo);
    }

}
