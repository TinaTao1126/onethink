<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: yangweijie <yangweijiester@gmail.com> <code-tech.diandian.com>
// +----------------------------------------------------------------------

namespace Admin\Model;
use Think\Model;

/**
 * 工位
 * @author tina
 *
 */
class StoreStationModel extends Model {

	protected $_validate = array(
		array('name','require','工位号必填'),
	    array('district_id','positive_integer','请选择大区'),
	    array('city_id','positive_integer','请选择城市'),
	    array('store_id','positive_integer','请选择大区'),
	);

	
}