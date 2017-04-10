<?php
namespace Api\Provider\Admin;
use Api\Provider\IndexProvider;
/**
 * Description of ConstantProvider
 */
class ConstantProvider extends IndexProvider
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * 公共配置参数获取
     * @param $param
     */
    public function index($param)
    {
        $version=isset($param['version'])?$param['version']:'1.0.0';
        // 公共配置信息
        $constant=[
            'version'=>$version,
        ];
        //错误信息返回
        //$this->fail('101');
        //成功返回
        $this->success($constant);
    }

}
