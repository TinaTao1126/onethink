<?php
/* * * * * * * * * * * * * * * * * * * * * *
 * * * * OH, NO BUG NO BUG, OH YEAH! * * * *
 * * * * * * * * * * * * * * * * * * * * * *
 */
namespace Api\Controller;
use Api\Provider\IndexProvider;
use Think\Controller;
/**
 * API服务入口
 * Description of IndexController
 * http://localhost/onethink/Api/index/index.html?debug=1&action=constant&method=index
 */
class IndexController extends Controller{
    /**
     * v1.0.0 接口通用入口
     * @example
     */
    public function index(){
        	//获取REQUEST 参数
        	$param = I("post.");
        	if (empty($param) && I("get.debug") == 1 ) {
        		$param = I("get.");
        	}
        	if(empty($param)) {
        		$json = file_get_contents('php://input');
        		$param =json_decode($json,true);
        	}
        	//print_r($param);exit;
        	//实例化接口服务类
        	$info = new IndexProvider();
        	//接口日志参数
        	$info->apiLog = array(
        			'http_user_agent' => $_SERVER['HTTP_USER_AGENT'], //来源设备信息
        			'remote_addr'     => $_SERVER['REMOTE_ADDR'], //来源IP地址
        			'param'           => json_encode($param), //传入参数
        			'action'          => $param['action'], //调用方法
        			'method'          => $param['method'], //调用方法
        			'start_time_unix' => time(), //时间戳
        	);
        	//根据get参数调用相应的方法
        	$info->method($param['action'],$param['method'], $param);
    }
}
