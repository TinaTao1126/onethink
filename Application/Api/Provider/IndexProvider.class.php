<?php
namespace Api\Provider;
use Api\Service\ReturnMsgService;
use Api\Provider\Admin\ConstantProvider;
use Think\Controller;
/**
 * 接口基础类
 */
class IndexProvider extends Controller{

    /**
     * 调用方法
     * @var type 
     */
    protected $method;
    protected $apiArr = [
        //常量
        'constant' =>[
            "provider_name"=>"ConstantProvider",
            "method_list"=>[
                "index" => ['auth'=>false],//'auth'=>true,'noAuthContinue'=>true,'is_paging'=>true
            ]
        ],
        //汽车模块
	    'car' =>[
	    	"provider_name"=>"CarProvider",
	    	"method_list"=>[
	    		"save" => ['auth'=>false],//'auth'=>true,'noAuthContinue'=>true,'is_paging'=>true
	    	]
	    ]
    ];

    /**
     * 接口日志信息
     * @var type 
     */
    public $apiLog = array();

    /**
     * 返回数据的数组,json的数据源
     * @var type 
     */
    protected $result = array();


    /**
     * 方法调用、签名校验 控制方法
     * @param type $method
     * @param type $param 内部调用参数
     * @return type
     */
    public function method($action, $method, $param = array() ) {
        //签名校验
        $this->createSign($param);
        
        //判断是接口否存在
        $ThisClassObj = '';
        if(isset($this->apiArr[$action]['method_list'][$method])){
            	$className=__NAMESPACE__ . '\\Admin\\'.$this->apiArr[$action]['provider_name'];
           $ThisClassObj = new $className();
        }
       
        if (empty($ThisClassObj)) {
            $this->fail(1);
        }
        // 参数验证
        $this->verifyPublicParams($param);
        
        // 登录验证      
        $noAuthContinue=false;
        isset($this->apiArr[$action]['method_list'][$method]['noAuthContinue']) && $noAuthContinue = $this->apiArr[$action]['method_list'][$method]['noAuthContinue'];
        $isLogin_member = $this->apiArr[$action]['method_list'][$method]['auth'];
        if ($isLogin_member) {
        	   $param = $this->checkAuthToken($param,$noAuthContinue);
        }
		//处理是否分页
		$is_paging=false;
        isset($this->apiArr[$action]['method_list'][$method]['is_paging']) && $is_paging = $this->apiArr[$action]['method_list'][$method]['is_paging'];
        //公共参数
        $param=$this->commonParams($param,$is_paging);
        
        $ThisClassObj->$method($param);
    }

    /**
     * 签名验证
     * @param param
     * @return false or true
     */
    protected function createSign($param) {
        return true;
    }

    /**
     * 公共参数验证
     * @param param
     * @return false or true
     */
    protected function verifyPublicParams($param) {
        return true;
    }

    /**
     * 统一返回Json格式数据
     * @return type
     */
    protected function back() {
        $this->apiLog['end_time_unix'] = time(); //结束时间
        $this->apiLog['used_time'] = (($this->apiLog['end_time_unix'] * 10000) - ($this->apiLog['start_time_unix'] * 10000)) / 10000; //总耗时 单位：秒
        $this->apiLog['return_param'] = json_encode($this->result); //返回参数
        $this->ajaxReturn($this->result);
        die;
    }
    
    /**
     * 校验token有效性
     * @param $param
     * @return boolen
     */
	protected function checkAuthToken($param,$noAuthContinue) {
        return $param;
    }
    /**
     * 获取公共参数
     * @param  $param
     */
    protected function commonParams($param,$is_paging) {
        	//处理分页
        	if($is_paging){
        		if (isset($param['page']) && intval($param['page']) > 0) {
        			$page = empty($param['page']) ? 1 : intval($param['page']);
        			$rows_size = empty($param['size']) ? 10 : intval($param['size']);;
        		} else {
        			$page = 1;
        			$rows_size = 10;//默认值
        		}
        		//读取行数
        		$param['size']=$rows_size;
        		//当前页
        		$param['page']=$page;
        	}
        	//用户经纬度
        	if (!isset($param['lat']) || empty($param['lat'])) 
        		$param['lat']=0;//纬度
        	if (!isset($param['lng']) || empty($param['lng']))
        		$param['lng']=0;//经度
        	return $param;
    }

    /**
     * 失败返回
     * @return type
     */
    protected function fail($msgID, $data=array(), $msgDatas=array()) {
        $this->result = ReturnMsgService::getMsg($msgID);
        $this->result['data'] = $data;
        if (!empty($msgDatas)) {
            	foreach ($msgDatas as $index=>$data) {
            		$this->result['msg'] = $data;
            		//$this->result['msg'] = str_replace('{'.$index.'}', $data, $this->result['msg']);
            	}
        }
        $this->back();
    }

    /**
     * 成功返回
     * @return type
     */
    protected function success($data, $msgID=0, $msgDatas=array()) {
        $this->result = ReturnMsgService::getMsg($msgID);
        $this->result['data'] = $data;
//         	if (!empty($msgDatas)) {
//             	foreach ($msgDatas as $index=>$data) {
//             	    $this->result['msg'] = $this->result['msg'];
//             		//$this->result['msg'] = str_replace('{'.$index.'}', $data, $this->result['msg']);
//             	}
//         }
        $this->back();
    }
}
