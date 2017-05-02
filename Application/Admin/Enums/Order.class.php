<?php

namespace Admin\Enums;

class Order {
	
	/**
	 * 
	 * @var "100"=>'新车入场',
	 	"101"=>'未开单',
		@var "200"=>'未结算', 
		"201"=>'已结算',
		@var "300"=>'离场',
		@var "400"=>'订单取消',
	 */
	public static  $ORDER_STATUS=array(
		"100"=>'新车入场',//未开单
		"101"=>'未开单',//未开单
		"200"=>'未结算', //已开单，未结算
		"201"=>'已结算',
		"300"=>'离场',
		"400"=>'订单取消',
	);
	
	/**
	 * @var 客户接待显示的状态
	 */
	public static $ORDER_STATUS_ACTIVE = array(100, 101, 200);
	
	/**
	 * @var 历史订单状态
	 * 
	 */
	public static $ORDER_STATUS_HISTORY=array(201, 300, 400);
	
	/**
	 * 
	 * @var 新车入场，未开单
	 */
	public static $ORDER_STATUS_100 = 100;
	/**
	 *  @var 未开单
	 */
	public static $ORDER_STATUS_101 = 101;
	/**
	 *
	 * @var 已开单，未结算
	 */
	public static $ORDER_STATUS_200 = 200;
	/**
	 *
	 * @var 已结算
	 */
	public static $ORDER_STATUS_201 = 201;
	/**
	 *
	 * @var 离场
	 */
	public static $ORDER_STATUS_300 = 300;
	/**
	 *
	 * @var 订单取消
	 */
	public static $ORDER_STATUS_400 = 400;
	
	/**
	 * 支付状态
	 * @var 未支付
	 */
	public static $PAY_STATUS_0 = 0;	//未支付
	/**
	 * 支付状态
	 * @var 已支付
	 */
	public static $PAY_STATUS_1 = 1;	//已支付
	
	
	public static $ITEM_TYPE = array(
		3=>"美容",
	    1=>"维修",
	    2=>"保养",
	    4=>"检查"
	);
	
}

