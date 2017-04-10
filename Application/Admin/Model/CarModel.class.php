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
 * 汽车模型
 * @author tina
 *
 */
class CarModel extends Model {

	protected $_validate = array(
		array('car_number','require','车牌号必填'), 
		array('car_owner','require','车主姓名必填'),
		array('owner_phone','require','车主电话必填'),
	);

	
}