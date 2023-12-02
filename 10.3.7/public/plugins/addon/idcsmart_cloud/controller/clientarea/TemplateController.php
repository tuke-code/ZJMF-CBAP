<?php
namespace addon\idcsmart_cloud\controller\clientarea;

use app\event\controller\PluginBaseController;
use addon\idcsmart_cloud\logic\IdcsmartCloudLogic;

/**
 * @title 模板管理
 * @desc 模板管理
 * @use addon\idcsmart_cloud\controller\clientarea\TemplateController
 */
class TemplateController extends PluginBaseController
{ 
    /**
     * 时间 2022-07-12
     * @title 模板列表
     * @desc 模板列表
     * @url /console/v1/template
     * @method  GET
     * @author theworld
     * @version v1
     * @return  array data.list -  列表数据 
     * @return  int data.list[].id - 模板ID
     * @return  string data.list[].name - 模板名称
     * @return  int data.list[].create_time - 创建时间
     * @return  string data.list[].host_id - 实例ID
     * @return  string data.list[].host_name - 实例名称
     * @return  string data.list[].ip - 实例IP
     * @return  string data.list[].country - 国家
     * @return  string data.list[].country_code - 国家代码
     * @return  string data.list[].city - 城市
     * @return  string data.list[].area - 区域
     * @return  int data.count - 总条数
     */
    public function list(){
        $param = request()->param();

        try{
            $IdcsmartCloudLogic = new IdcsmartCloudLogic();

            $result = $IdcsmartCloudLogic->templateList($param);
            return json($result);
        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>$e->getMessage()]);
        }
    }

}