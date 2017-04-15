<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;
use Admin\Service\DistrictService;

/**
 * 区域管理
 * @author tina
 *
 */
class DistrictController extends AdminController {

    public function list_by_type($type=1, $pid=0){
        //echo $type.'&$pid='.$pid;exit;
        $districtService = new DistrictService();
        $district = $districtService->select($type,$pid);
        $this->success($district);
    }

   
}
