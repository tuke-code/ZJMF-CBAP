<?php
namespace app\admin\controller;

use app\common\logic\ModuleLogic;

/**
 * @title 模块管理
 * @desc 模块管理
 * @use app\admin\controller\ModuleController
 */
class ModuleController extends AdminBaseController
{
	/**
	 * 时间 2022-05-27
	 * @title 模块列表
	 * @desc 模块列表
	 * @url /admin/v1/module
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @return array list - 模块列表
     * @return string list[].name - 模块类型
     * @return string list[].display_name - 模块名称
	 */
	public function moduleList()
	{
		$ModuleLogic = new ModuleLogic();

        $data = $ModuleLogic->getModuleList();

        $result = [
            'status' => 200,
            'msg' => lang('success_message'),
            'data' => [
            	'list' => $data
            ]
        ];
        return json($result);
	}

	/**
	 * 时间 2022-06-13
	 * @title 后台模块自定义方法
	 * @desc 后台模块自定义方法
	 * @url /admin/v1/module/:module/:controller/:method
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param string module - 模块名称 require
	 * @param string func - 方法名称 require
	 */
	public function customFunction()
	{
		$param = $this->request->param();

		$ModuleLogic = new ModuleLogic();

        $result = $ModuleLogic->customAdminFunction($param['module'], $param);
        return $result;
	}

} 


