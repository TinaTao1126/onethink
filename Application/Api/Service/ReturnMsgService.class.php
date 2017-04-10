<?php
namespace Api\Service;
/**
 * 接口返回值 服务Service
 */
class ReturnMsgService {
    /**
     * 提示代码信息
     * @var type 
     */
	public  static $msgType = array(
		//公共提示：
		"0" => array("true", ""),
		
	    "1" => array("false", "方法不存在"),
		"600" => array("false", "缺少必要参数"),
		"102" => array("false", "参数格式错误"),
	    
	    "200" => array("true", "操作成功"),
	    "500" => array("false", "操作失败"),
	);
    /**
     * 获取提示信息 外部
     * @param type $type
     * @return type
     */
    static public function getMsg($type) {
        $msg = array();
        $msg['code'] = $type;
        $msg['success'] = self::$msgType[$type][0];
        $msg['msg'] = self::$msgType[$type][1];
        $msg['data'] = null;
        return $msg;
    }
}
