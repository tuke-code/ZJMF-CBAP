<?php
namespace reserver\mf_finance\controller\home;

use app\common\model\HostModel;
use app\common\model\MenuModel;
use app\common\model\OrderItemModel;
use app\common\model\OrderModel;
use app\common\model\ProductUpgradeProductModel;
use app\common\model\UpgradeModel;
use app\common\model\UpstreamProductModel;
use reserver\mf_finance\logic\RouteLogic;
use reserver\mf_finance\model\SystemLogModel;
use reserver\mf_finance\validate\HostValidate;
use think\facade\Cache;

/**
 * @title 魔方财务(自定义配置)-前台
 * @desc 魔方财务(自定义配置)-前台
 * @use reserver\mf_finance\controller\home\CloudController
 */
class CloudController
{
    /**
     * 时间 2023-02-06
     * @title 获取订购页面配置
     * @desc 获取订购页面配置
     * @url /console/v1/product/:id/remf_finance/order_page
     * @method  GET
     * @author wyh
     * @version v1
     * @param   int id - 商品ID require
     *
     */
    public function orderPage(){
        $param = request()->param();

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByProduct($param['id']);

            $postData = [
                'pid' => $RouteLogic->upstream_product_id,
                'billingcycle' => $param['billingcycle']??''
            ];

            $result = $RouteLogic->curl( 'cart/set_config', $postData, 'GET');
            if ($result['status']==200){
                $cycles = [];
                foreach ($result['product']['cycle'] as $item){
                    if ($item['billingcycle']!='ontrial'){
                        unset($item['product_price'],$item['setup_fee']);
                        $cycles[] = $item;
                    }
                }
                $result['product']['cycle'] = $cycles;
            }
            /*if($result['status'] == 200){
                // 计算价格倍率
                if(isset($result['option'])){
                    foreach($result['option'] as $k=>$v){
                        if($v['pricing']>0){
                            $result['option'][$k]['pricing'] = bcmul($v['pricing'], $RouteLogic->price_multiple);
                        }
                    }
                }
                if (isset($result['product']['cycle'])){
                    foreach ($result['product']['cycle'] as $k1=>$v1){
                        $result['product']['cycle'][$k1]['product_price'] = bcmul($v1['product_price'], $RouteLogic->price_multiple);
                    }
                }
            }*/
        }catch(\Exception $e){
            $result = json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_act_exception') . $e->getMessage()]);
        }
        return json($result);
    }
    /**
     * 时间 2023-02-06
     * @title 获取订购页面配置
     * @desc 获取订购页面配置(层级联动)
     * @url /console/v1/product/:id/remf_finance/link
     * @method  GET
     * @author wyh
     * @version v1
     * @param   int id - 商品ID require
     * @param   int cid - 配置项ID require
     * @param   int sub_id - 子项ID require
     *
     */
    public function link(){
        $param = request()->param();

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByProduct($param['id']);

            $postData = [
                'pid' => $RouteLogic->upstream_product_id,
                'cid' => $param['cid'],
                'sub_id' => $param['sub_id'],
                'billingcycle' => $param['billingcycle']??''
            ];

            $result = $RouteLogic->curl( 'link_list', $postData, 'GET');
            /*if($result['status'] == 200){
                // 计算价格倍率
                if(isset($result['option'])){
                    foreach($result['option'] as $k=>$v){
                        if($v['pricing']>0){
                            $result['option'][$k]['pricing'] = bcmul($v['pricing'], $RouteLogic->price_multiple);
                        }
                    }
                }
                if (isset($result['product']['cycle'])){
                    foreach ($result['product']['cycle'] as $k1=>$v1){
                        $result['product']['cycle'][$k1]['product_price'] = bcmul($v1['product_price'], $RouteLogic->price_multiple);
                    }
                }
            }*/
        }catch(\Exception $e){
            $result = json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_act_exception') . $e->getMessage()]);
        }
        return json($result);
    }


    /**
     * 时间 2022-06-29
     * @title 获取实例详情
     * @desc 获取实例详情
     * @url /console/v1/remf_finance/:id
     * @method  GET
     * @author hh
     * @version v1
     * @param   int $id - 产品ID
     * @return host_data:基础数据@
     * @host_data  ordernum:订单id
     * @host_data  productid:产品id
     * @host_data  serverid:服务器id
     * @host_data  regdate:产品开通时间
     * @host_data  domain:主机名
     * @host_data  payment:支付方式
     * @host_data  firstpaymentamount:首付金额
     * @host_data  firstpaymentamount_desc:首付金额
     * @host_data  amount:续费金额
     * @host_data  amount_desc:续费金额
     * @host_data  billingcycle:付款周期
     * @host_data  billingcycle_desc:付款周期
     * @host_data  nextduedate:到期时间
     * @host_data  nextinvoicedate:下次帐单时间
     * @host_data  dedicatedip:独立ip
     * @host_data  assignedips:附加ip
     * @host_data  ip_num:IP数量
     * @host_data  domainstatus:产品状态
     * @host_data  domainstatus_desc:产品状态
     * @host_data  username:服务器用户名
     * @host_data  password:服务器密码
     * @host_data  suspendreason:暂停原因
     * @host_data  auto_terminate_end_cycle:是否到期取消
     * @host_data  auto_terminate_reason:取消原因
     * @host_data  productname:产品名
     * @host_data  groupname:产品组名
     * @host_data  bwusage:当前使用流量
     * @host_data  bwlimit:当前使用流量上限(0表示不限)
     * @host_data  os:操作系统
     * @host_data  port:端口
     * @host_data  remark:备注
     * @return config_options:可配置选项@
     * @config_options  name:配置名
     * @config_options  sub_name:配置项值
     * @return custom_field_data:自定义字段@
     * @custom_field_data  fieldname:字段名
     * @custom_field_data  value:字段值
     * @return download_data:可下载数据@
     * @download_data  id:文件id
     * @title  id:文件标题
     * @down_link  id:下载链接
     * @location  id:文件名
     * @return module_button:模块按钮@
     * @module_button  type:default:默认,custom:自定义
     * @module_button  type:func:函数名
     * @module_button  type:name:名称
     * @return module_client_area:模块页面输出
     * @return hook_output:钩子在本页面的输出，数组，循环显示的html
     * @return dcim.flowpacket:当前产品可购买的流量包@
     * @dcim.flowpacket  id:流量包ID
     * @dcim.flowpacket  name:流量包名称
     * @dcim.flowpacket  price:价格
     * @dcim.flowpacket  sale_times:销售次数
     * @dcim.flowpacket  stock:库存(0不限)
     * @return dcim.auth:服务器各种操作权限控制(on有权限off没权限)
     * @return dcim.area_code:区域代码
     * @return dcim.area_name:区域名称
     * @return dcim.os_group:操作系统分组@
     * @dcim.os_group  id:分组ID
     * @dcim.os_group  name:分组名称
     * @dcim.os_group  svg:分组svg号
     * @return dcim.os:操作系统数据@
     * @dcim.os  id:操作系统ID
     * @dcim.os  name:操作系统名称
     * @dcim.os  ostype:操作系统类型(1windows0linux)
     * @dcim.os  os_name:操作系统真实名称(用来判断具体的版本和操作系统)
     * @dcim.os  group_id:所属分组ID
     * @return  flow_packet_use_list:流量包使用情况@
     * @flow_packet_use_list  name:流量包名称
     * @flow_packet_use_list  capacity:流量包大小
     * @flow_packet_use_list  price:价格
     * @flow_packet_use_list  pay_time:支付时间
     * @flow_packet_use_list  used:已用流量
     * @flow_packet_use_list  used:已用流量
     * @return  host_cancel: 取消请求数据,空对象
     */
    public function detail(){
        $param = request()->param();

        $HostValidate = new HostValidate();
        if (!$HostValidate->scene('auth')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($HostValidate->getError())]);
        }

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByHost($param['id']);

            $result = $RouteLogic->curl( 'host/header', ['host_id'=>$RouteLogic->upstream_host_id], 'GET');
            if ($result['status']==200){
                if (isset($result['data']['host_data'])){
                    /*$host_data = [
                        "dedicatedip" => $result['data']['host_data']['dedicatedip'],
                        "domain" => $result['data']['host_data']['domain'],
                        "domainstatus" => $result['data']['host_data']['domainstatus'],
                    ];
                    $result['data']['host_data'] = $host_data;*/
                    unset(
                        $result['data']['host_data']['amount'],
                        $result['data']['host_data']['amount_desc'],
                        $result['data']['host_data']['firstpaymentamount'],
                        $result['data']['host_data']['firstpaymentamount_desc'],
                        $result['data']['host_data']['order_amount'],
                        $result['data']['host_data']['upstream_price_value']
                    );
                }
            }
        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_act_exception')]);
        }
        if ($result['status']!=200){
            $result['status'] = 400;
        }
        return json($result);
    }

    /**
     * 时间 2022-06-22
     * @title 开机
     * @desc 开机
     * @url /console/v1/remf_finance/:id/on
     * @method  POST
     * @author hh
     * @version v1
     * @param   int id - 产品ID require
     */
    public function on()
    {
        $param = request()->param();

        $HostModel = HostModel::find($param['id']);
        if(empty($HostModel) || $HostModel['client_id'] != get_client_id() ){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_host_not_found')]);
        }

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByHost($param['id']);

            $postData = [
                'id' => $RouteLogic->upstream_host_id,
                'func' => 'on',
                'os' => $param['os']??'',
                'code' => $param['code']??'',
                'is_api' => true
            ];

            $result = $RouteLogic->curl( 'provision/default', $postData, 'POST');
            if($result['status'] == 200){
                $description = lang_plugins('res_mf_finance_log_host_start_boot_success', [
                    '{hostname}' => $HostModel['name'],
                ]);
            }else{
                $description = lang_plugins('res_mf_finance_log_host_start_boot_fail', [
                    '{hostname}' => $HostModel['name'],
                ]);
            }
            active_log($description, 'host', $HostModel['id']);
        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_act_exception')]);
        }
        return json($result);
    }

    /**
     * 时间 2022-06-22
     * @title 关机
     * @desc 关机
     * @url /console/v1/remf_finance/:id/off
     * @method  POST
     * @author hh
     * @version v1
     * @param   int id - 产品ID require
     */
    public function off()
    {
        $param = request()->param();

        $HostModel = HostModel::find($param['id']);
        if(empty($HostModel) || $HostModel['client_id'] != get_client_id() ){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_host_not_found')]);
        }

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByHost($param['id']);

            $postData = [
                'id' => $RouteLogic->upstream_host_id,
                'func' => 'off',
                'os' => $param['os']??'',
                'code' => $param['code']??'',
                'is_api' => true
            ];

            $result = $RouteLogic->curl( 'provision/default', $postData, 'POST');
            if($result['status'] == 200){
                $description = lang_plugins('res_mf_finance_log_host_start_off_success', [
                    '{hostname}' => $HostModel['name'],
                ]);
            }else{
                $description = lang_plugins('res_mf_finance_log_host_start_off_fail', [
                    '{hostname}' => $HostModel['name'],
                ]);
            }
            active_log($description, 'host', $HostModel['id']);
        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_act_exception')]);
        }
        return json($result);
    }

    /**
     * 时间 2022-06-22
     * @title 重启
     * @desc 重启
     * @url /console/v1/remf_finance/:id/reboot
     * @method  POST
     * @author hh
     * @version v1
     * @param   int id - 产品ID require
     */
    public function reboot()
    {
        $param = request()->param();

        $HostModel = HostModel::find($param['id']);
        if(empty($HostModel) || $HostModel['client_id'] != get_client_id() ){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_host_not_found')]);
        }

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByHost($param['id']);

            $postData = [
                'id' => $RouteLogic->upstream_host_id,
                'func' => 'reboot',
                'os' => $param['os']??'',
                'code' => $param['code']??'',
                'is_api' => true
            ];

            $result = $RouteLogic->curl( 'provision/default', $postData, 'POST');
            if($result['status'] == 200){
                $description = lang_plugins('res_mf_finance_log_host_start_reboot_success', [
                    '{hostname}' => $HostModel['name'],
                ]);
            }else{
                $description = lang_plugins('res_mf_finance_log_host_start_reboot_fail', [
                    '{hostname}' => $HostModel['name'],
                ]);
            }
            active_log($description, 'host', $HostModel['id']);
        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_act_exception')]);
        }
        return json($result);
    }

    /**
     * 时间 2022-06-29
     * @title 获取控制台地址
     * @desc 获取控制台地址
     * @url /console/v1/remf_finance/:id/vnc
     * @method  POST
     * @author hh
     * @version v1
     * @return  string data.url - 控制台地址
     */
    public function vnc()
    {
        $param = request()->param();

        $HostModel = HostModel::find($param['id']);
        if(empty($HostModel) || $HostModel['client_id'] != get_client_id() ){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_host_not_found')]);
        }

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByHost($param['id']);

            $postData = [
                'id' => $RouteLogic->upstream_host_id,
                'func' => 'vnc',
                'os' => $param['os']??'',
                'code' => $param['code']??'',
                'is_api' => true
            ];
  
            $result = $RouteLogic->curl( 'provision/default', $postData, 'POST');
            if($result['status'] == 200){
                $cache = $result['data'];
                //unset($cache['url']);

                Cache::set('remf_finance_vnc_'.$param['id'], $cache, 30*60);
                if(!isset($param['more']) || $param['more'] != 1){
                    // 不获取更多信息
                    $result['data'] = [];
                }
                // 转到当前res模块
                /*if (isset($cache['token'])){
                    $result['data']['url'] = request()->domain().'/console/v1/remf_finance/'.$param['id'].'/vnc?tmp_token='.$cache['token'];
                }else{
                    $result['data']['url'] = request()->domain().'/console/v1/remf_finance/'.$param['id'].'/vnc';
                }*/
                $result['data']['url'] = $cache['url'];

            }

        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_act_exception') . $e->getMessage()]);
        }
        return json($result);
    }

    /**
     * 时间 2022-06-24
     * @title 获取实例状态
     * @desc 获取实例状态
     * @url /console/v1/remf_finance/:id/status
     * @method  GET
     * @author hh
     * @version v1
     * @param   int id - 产品ID require
     * @return  string data.status - 实例状态(on=开机,off=关机,operating=操作中,fault=故障)
     * @return  string data.desc - 实例状态描述
     */
    public function status()
    {
        $param = request()->param();

        $HostModel = HostModel::find($param['id']);
        if(empty($HostModel) || $HostModel['client_id'] != get_client_id() ){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_host_not_found')]);
        }

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByHost($param['id']);

            $postData = [
                'id' => $RouteLogic->upstream_host_id,
                'func' => 'status',
                'os' => $param['os']??'',
                'code' => $param['code']??'',
                'is_api' => true
            ];

            $res = $RouteLogic->curl( 'provision/default', $postData, 'POST');
            if($res['status'] == 200){
                if(in_array($res['data']['status'], ['task','process','cold_migrate','hot_migrate'])){
                    $status = [
                        'status' => 'operating',
                        'desc'   => lang_plugins('res_mf_finance_operating'),
                    ];
                }else if(in_array($res['data']['status'], ['on','waiting'])){
                    $status = [
                        'status' => 'on',
                        'desc'   => lang_plugins('res_mf_finance_on'),
                    ];
                }else if(in_array($res['data']['status'], ['off'])){
                    $status = [
                        'status' => 'off',
                        'desc'   => lang_plugins('res_mf_finance_off')
                    ];
                }else{
                    $status = [
                        'status' => 'fault',
                        'desc'   => lang_plugins('res_mf_finance_fault'),
                    ];
                }
            }else{
                $status = [
                    'status' => 'fault',
                    'desc'   => lang_plugins('res_mf_finance_fault'),
                ];
            }

            $result = [
                'status' => 200,
                'msg'    => lang_plugins('success_message'),
                'data'   => $status,
            ];
        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_act_exception')]);
        }
        return json($result);
    }

    /**
     * 时间 2022-06-24
     * @title 重置密码
     * @desc 重置密码
     * @url /console/v1/remf_finance/:id/reset_password
     * @method  POST
     * @author hh
     * @version v1
     * @param   int id - 产品ID require
     * @param   string password - 新密码 require
     */
    public function resetPassword()
    {
        $param = request()->param();

        $HostModel = HostModel::find($param['id']);
        if(empty($HostModel) || $HostModel['client_id'] != get_client_id() ){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_host_not_found')]);
        }

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByHost($param['id']);

            $postData = [
                'id' => $RouteLogic->upstream_host_id,
                'func' => 'crack_pass',
                'password' => $param['password']??'',
                'is_api' => true
            ];

            $result = $RouteLogic->curl( 'provision/default', $postData, 'POST');
            if($result['status'] == 200){
                $description = lang_plugins('res_mf_finance_log_host_start_reset_password_success', [
                    '{hostname}' => $HostModel['name'],
                ]);
            }else{
                $description = lang_plugins('res_mf_finance_log_host_start_reset_password_success', [
                    '{hostname}' => $HostModel['name'],
                ]);
            }
            active_log($description, 'host', $HostModel['id']);
        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_act_exception')]);
        }
        return json($result);
    }

    /**
     * 时间 2022-06-24
     * @title 救援模式
     * @desc 救援模式
     * @url /console/v1/remf_finance/:id/rescue
     * @method  POST
     * @author hh
     * @version v1
     * @param   int id - 产品ID require
     * @param   int type - 指定救援系统类型(1=windows,2=linux) require
     * @param   int temp_pass - 临时密码 require
     */
    public function rescue()
    {
        $param = request()->param();

        $HostModel = HostModel::find($param['id']);
        if(empty($HostModel) || $HostModel['client_id'] != get_client_id() ){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_host_not_found')]);
        }

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByHost($param['id']);

            $postData = [
                'id' => $RouteLogic->upstream_host_id,
                'func' => 'rescue_system',
                'system' => $param['type']??'',
                'temp_pass' => $param['temp_pass']??'',
                'is_api' => true
            ];

            $result = $RouteLogic->curl( 'provision/default', $postData, 'POST');
            if($result['status'] == 200){
                $description = lang_plugins('res_mf_finance_log_host_start_rescue_success', [
                    '{hostname}' => $HostModel['name'],
                ]);
            }else{
                $description = lang_plugins('res_mf_finance_log_host_start_rescue_fail', [
                    '{hostname}' => $HostModel['name'],
                ]);
            }
            active_log($description, 'host', $HostModel['id']);
        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_act_exception')]);
        }
        return json($result);
    }

    /**
     * 时间 2022-06-30
     * @title 重装系统
     * @desc 重装系统
     * @url /console/v1/remf_finance/:id/reinstall
     * @method  POST
     * @author hh
     * @version v1
     * @param   int id - 产品ID require
     * @param   int os - 重装系统的操作系统id require
     * @param   int port - 端口 require
     */
    public function reinstall()
    {
        $param = request()->param();

        $HostModel = HostModel::find($param['id']);
        if(empty($HostModel) || $HostModel['client_id'] != get_client_id() ){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_host_not_found')]);
        }

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByHost($param['id']);

            $postData = [
                'id' => $RouteLogic->upstream_host_id,
                'func' => 'reinstall',
                'os' => $param['os']??'',
                'port' => $param['port']??'',
                'is_api' => true
            ];

            $result = $RouteLogic->curl( 'provision/default', $postData, 'POST');
            if($result['status'] == 200){
                $description = lang_plugins('res_mf_finance_log_host_start_reinstall_success', [
                    '{hostname}' => $HostModel['name'],
                ]);
            }else{
                $description = lang_plugins('res_mf_finance_log_host_start_reinstall_fail', [
                    '{hostname}' => $HostModel['name'],
                ]);
            }
            active_log($description, 'host', $HostModel['id']);
        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_act_exception')]);
        }
        return json($result);
    }

    /**
     * 时间 2022-06-30
     * @title 硬关机
     * @desc 硬关机
     * @url /console/v1/remf_finance/:id/hard_off
     * @method  POST
     * @author hh
     * @version v1
     * @param   int id - 产品ID require
     */
    public function hardOff()
    {
        $param = request()->param();

        $HostModel = HostModel::find($param['id']);
        if(empty($HostModel) || $HostModel['client_id'] != get_client_id() ){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_host_not_found')]);
        }

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByHost($param['id']);

            $postData = [
                'id' => $RouteLogic->upstream_host_id,
                'func' => 'hard_off',
                'is_api' => true
            ];

            $result = $RouteLogic->curl( 'provision/default', $postData, 'POST');
            if($result['status'] == 200){
                $description = lang_plugins('res_mf_finance_log_host_hard_off_success', [
                    '{hostname}' => $HostModel['name'],
                ]);
            }else{
                $description = lang_plugins('res_mf_finance_log_host_hard_off_fail', [
                    '{hostname}' => $HostModel['name'],
                ]);
            }
            active_log($description, 'host', $HostModel['id']);
        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_act_exception')]);
        }
        return json($result);
    }

    /**
     * 时间 2022-06-30
     * @title 硬重启
     * @desc 硬重启
     * @url /console/v1/remf_finance/:id/hard_reboot
     * @method  POST
     * @author hh
     * @version v1
     * @param   int id - 产品ID require
     */
    public function hardReboot()
    {
        $param = request()->param();

        $HostModel = HostModel::find($param['id']);
        if(empty($HostModel) || $HostModel['client_id'] != get_client_id() ){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_host_not_found')]);
        }

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByHost($param['id']);

            $postData = [
                'id' => $RouteLogic->upstream_host_id,
                'func' => 'hard_reboot',
                'is_api' => true
            ];

            $result = $RouteLogic->curl( 'provision/default', $postData, 'POST');
            if($result['status'] == 200){
                $description = lang_plugins('res_mf_finance_log_host_hard_reboot_success', [
                    '{hostname}' => $HostModel['name'],
                ]);
            }else{
                $description = lang_plugins('res_mf_finance_log_host_hard_reboot_fail', [
                    '{hostname}' => $HostModel['name'],
                ]);
            }
            active_log($description, 'host', $HostModel['id']);
        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_act_exception')]);
        }
        return json($result);
    }

    /**
     * 时间 2020-07-06
     * @title 获取模块图表数据
     * @desc 获取模块图表数据
     * @url /console/v1/remf_finance/:id/chart
     * @method  GET
     * @author hh
     * @version v1
     * @param   .name:type type:string require:1 default: other: desc:module_chart里面的type:比如：cpu/dist/memory/flow
     * @param   .name:select type:string require:0 default: other: desc:module_chart里面的select的value
     * @param   .name:start type:int require:0 default: desc:开始毫秒时间戳
     * @param   .name:end type:int require:0 default: desc:结束毫秒时间戳
     * @return  unit:单位
     * @return  chart_type:line线性图
     * @return  list:图表数据@
     * @list  time:时间
     * @value  value:值
     * @return  label:对应list鼠标over显示内容
     */
    public function chart()
    {
        $param = request()->param();

        $HostModel = HostModel::find($param['id']);
        if(empty($HostModel) || $HostModel['client_id'] != get_client_id() ){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_host_not_found')]);
        }

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByHost($param['id']);

            $postData = [
                'type' => $param['type']??'',
                'select' => $param['select']??'',
                'start' => $param['start']??'',
                'end' => $param['end']??'',
                'is_api' => true
            ];

            $result = $RouteLogic->curl( '/provision/chart/'.$RouteLogic->upstream_host_id, $postData, 'GET');
        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_act_exception')]);
        }
        return json($result);
    }

    /**
     * 时间 2022-09-26
     * @title 获取商品配置所有周期价格
     * @desc 获取商品配置所有周期价格
     * @url /console/v1/product/:id/remf_finance/duration
     * @method  GET
     * @author wyh
     * @version v1
     * @param   int id - 商品ID require
     * @return object duration - 周期
     * @return float duration.product_price - 价格
     * @return float duration.setup_fee - 初装费
     * @return string duration.billingcycle - 周期
     * @return string duration.billingcycle_zh - 周期
     * @return string duration.pay_ontrial_cycle - 试用
     */
    public function cartConfigoption()
    {
        $param = request()->param();

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByProduct($param['id']);

            unset($param['id']);

            $postData = [
                'pid' => $RouteLogic->upstream_product_id,
                'billingcycle' => $param['billingcycle']??''
            ];

            $result = $RouteLogic->curl( 'cart/set_config', $postData, 'GET');
            if($result['status'] == 200){
                // 计算价格倍率
                foreach($result['product']['cycle'] as $k=>$v){
                    if($v['product_price'] > 0){
                        # 固定利润
                        if ($RouteLogic->profit_type==1){
                            $result['product']['cycle'][$k]['product_price'] = bcadd($v['product_price'], $RouteLogic->profit_percent*100);
                        }else{
                            $result['product']['cycle'][$k]['product_price'] = bcmul($v['product_price'], $RouteLogic->price_multiple);
                        }

                    }
                    if($v['setup_fee'] > 0){
                        # 固定利润
                        if ($RouteLogic->profit_type==1){
                            $result['product']['cycle'][$k]['setup_fee'] = bcadd($v['setup_fee'], 0);
                        }else{
                            $result['product']['cycle'][$k]['setup_fee'] = bcmul($v['setup_fee'], $RouteLogic->price_multiple);
                        }
                    }
                }

                $cycles = [];
                foreach ($result['product']['cycle'] as $item){
                    if ($item['billingcycle']!='ontrial'){
                        //unset($item['product_price'],$item['setup_fee']);
                        $cycles[] = $item;
                    }
                }
                $result['product']['cycle'] = $cycles;

                $res = [
                    'status' => 200,
                    'msg' => $result['msg'],
                    'data' => [
                        'duration' => $result['product']['cycle']??[]
                    ]
                ];
                return json($res);
            }else{
                return json($result);
            }
        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_act_exception')]);
        }
    }

    /**
     * 时间 2023-02-09
     * @title 产品列表
     * @desc 产品列表
     * @url /console/v1/remf_finance
     * @method  GET
     * @author hh
     * @version v1
     * @param   int page 1 页数
     * @param   int limit - 每页条数
     * @param   string orderby - 排序(id,due_time,status)
     * @param   string sort - 升/降序
     * @param   string keywords - 关键字搜索,搜索套餐名称/主机名/IP
     * @param   string param.status - 产品状态(Unpaid=未付款,Pending=开通中,Active=已开通,Suspended=已暂停,Deleted=已删除)
     * @param   int param.m - 菜单ID
     * @return  array data.list - 列表数据
     * @return  int data.list[].id - 产品ID
     * @return  string data.list[].name - 产品标识
     * @return  string data.list[].status - 产品状态(Unpaid=未付款,Pending=开通中,Active=已开通,Suspended=已暂停,Deleted=已删除)
     * @return  int data.list[].due_time - 到期时间
     * @return  int data.list[].active_time - 开通时间
     * @return  string data.list[].product_name - 商品名称
     */
    public function list(){
        $param = request()->param();

        $result = [
            'status' => 200,
            'msg'    => lang_plugins('success_message'),
            'data'   => [
                'list'  => [],
                'count' => [],
            ]
        ];

        $clientId = get_client_id();
        if(empty($clientId)){
            return json($result);
        }

        $where = [];
        if(isset($param['m']) && !empty($param['m'])){
            // 菜单,菜单里面必须是下游商品
            $MenuModel = MenuModel::where('menu_type', 'res_module')
                ->where('module', 'mf_finance')
                ->where('id', $param['m'])
                ->find();
            if(!empty($MenuModel) && !empty($MenuModel['product_id'])){
                $MenuModel['product_id'] = json_decode($MenuModel['product_id'], true);
                if(!empty($MenuModel['product_id'])){
                    $upstreamProduct = UpstreamProductModel::whereIn('product_id', $MenuModel['product_id'])->where('res_module', 'mf_finance')->find();
                    if(!empty($upstreamProduct)){
                        $where[] = ['h.product_id', 'IN', $MenuModel['product_id'] ];
                    }
                }
            }
        }else{
            //return json($result);
        }

        $param['page'] = isset($param['page']) ? ($param['page'] ? (int)$param['page'] : 1) : 1;
        $param['limit'] = isset($param['limit']) ? ($param['limit'] ? (int)$param['limit'] : config('idcsmart.limit')) : config('idcsmart.limit');
        $param['sort'] = isset($param['sort']) ? ($param['sort'] ?: config('idcsmart.sort')) : config('idcsmart.sort');
        $param['orderby'] = isset($param['orderby']) && in_array($param['orderby'], ['id','due_time','status']) ? $param['orderby'] : 'id';
        $param['orderby'] = 'h.'.$param['orderby'];

        $where[] = ['h.client_id', '=', $clientId];
        $where[] = ['h.status', '<>', 'Cancelled'];
        if(isset($param['status']) && !empty($param['status'])){
            if($param['status'] == 'Pending'){
                $where[] = ['h.status', 'IN', ['Pending','Failed']];
            }else if(in_array($param['status'], ['Unpaid','Active','Suspended','Deleted'])){
                $where[] = ['h.status', '=', $param['status']];
            }
        }
        if(isset($param['keywords']) && $param['keywords'] !== ''){
            $where[] = ['h.name', 'LIKE', '%'.$param['keywords'].'%'];
        }

        // 获取子账户可见产品
        $res = hook('get_client_host_id', ['client_id' => get_client_id(false)]);
        $res = array_values(array_filter($res ?? []));
        foreach ($res as $key => $value) {
            if(isset($value['status']) && $value['status']==200){
                $hostId = $value['data']['host'];
            }
        }
        if(isset($hostId) && !empty($hostId)){
            $where[] = ['h.id', 'IN', $hostId];
        }

        $count = HostModel::alias('h')
            ->leftJoin('product p', 'h.product_id=p.id')
            ->join('upstream_product up', 'p.id=up.product_id AND up.res_module="mf_finance"')
            ->where($where)
            ->count();

        $host = HostModel::alias('h')
            ->field('h.id,h.name,h.status,h.active_time,h.due_time,p.name product_name,h.client_notes')
            ->leftJoin('product p', 'h.product_id=p.id')
            ->join('upstream_product up', 'p.id=up.product_id AND up.res_module="mf_finance"')
            ->where($where)
            ->withAttr('status', function($val){
                return $val == 'Failed' ? 'Pending' : $val;
            })
            ->limit($param['limit'])
            ->page($param['page'])
            ->order($param['orderby'], $param['sort'])
            ->group('h.id')
            ->select()
            ->toArray();

        $result['data']['list']  = $host;
        $result['data']['count'] = $count;
        return json($result);
    }

    /**
     * 时间 2020-08-06
     * @title 获取自定义内容
     * @desc 获取自定义内容
     * @url /console/v1/remf_finance/:id/custom/content
     * @method  POST
     * @author hh
     * @param   .name:id type:int require:1 default: other: desc:hostid
     * @param   .name:key type:string require:1 default: other: desc:module_client_area里面的key:比如security_groups,setting
     * @param   .name:api_url type:string require:1 default: other: desc:替换原来模板内的接口地址
     * @return  html:html内容
     */
    public function postClientAreaContent()
    {
        $param = request()->param();

        $HostModel = HostModel::find($param['id']);
        if(empty($HostModel) || $HostModel['client_id'] != get_client_id() ){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_host_not_found')]);
        }

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByHost($param['id']);

            $postData = [
                'id' => $RouteLogic->upstream_host_id,
                'key' => $param['key']??'',
                'api_url' => $param['api_url']??''
            ];

            $result = $RouteLogic->curl( 'zjmf_api/provision/custom/content', $postData, 'POST');
        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_act_exception')]);
        }
        return json($result);
    }

    /**
     * 时间 2020-07-02
     * @title 执行自定义模块方块
     * @desc 执行自定义模块方块
     * @url /console/v1/remf_finance/:id/custom
     * @method  POST
     * @author hh
     * @version v1
     * @param   .name:func type:string require:1 default: other: desc:执行的方法
     * @return  [type] [description]
     */
    public function customFunc()
    {
        $param = request()->param();

        $HostModel = HostModel::find($param['id']);
        if(empty($HostModel) || $HostModel['client_id'] != get_client_id() ){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_host_not_found')]);
        }

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByHost($param['id']);

            $postData = [
                'id' => $RouteLogic->upstream_host_id,
                'key' => $param['key']??'',
                'api_url' => $param['api_url']??''
            ];

            $result = $RouteLogic->curl( 'zjmf_api/provision/custom/content', $postData, 'POST');
        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_act_exception')]);
        }
        return json($result);
    }

    /**
     * @time 2020-07-12
     * @title 获取用量信息
     * @description 获取用量信息
     * @url /console/v1/remf_finance/:id/trafficusage
     * @method  GET
     * @author huanghao
     * @version v1
     * @param   .name:id type:int require:1 desc:host ID
     * @param   .name:start type:string require:0 desc:开始日期(YYYY-MM-DD)
     * @param   .name:end type:string require:0 desc:结束日期(YYYY-MM-DD)
     * @return  0:流量数据@
     * @0  time:横坐标值
     * @0  value:纵坐标值(单位Mbps)
     */
    public function trafficusage()
    {
        $param = request()->param();

        $HostModel = HostModel::find($param['id']);
        if(empty($HostModel) || $HostModel['client_id'] != get_client_id() ){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_host_not_found')]);
        }

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByHost($param['id']);

            $postData = [
                'id' => $RouteLogic->upstream_host_id,
                'start' => $param['start']??'',
                'end' => $param['end']??''
            ];

            $result = $RouteLogic->curl( 'host/trafficusage', $postData, 'GET');

        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_act_exception')]);
        }
        return json($result);
    }

    // 获取快照信息
    public function snapshot()
    {
        $param = request()->param();

        $HostModel = HostModel::find($param['id']);
        if(empty($HostModel) || $HostModel['client_id'] != get_client_id() ){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_host_not_found')]);
        }

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByHost($param['id']);

            $UpstreamProductModel = new UpstreamProductModel();
            $upstream = $UpstreamProductModel->where('product_id',$HostModel['product_id'])->find();
            $api_id  = $upstream['supplier_id'];
            $key = 'api_auth_login_' . AUTHCODE . '_' . $api_id;

            $jwt = idcsmart_cache($key);

            $postData = [
                'id' => $RouteLogic->upstream_host_id,
                'key' => 'snapshot', // 这个含备份信息
                //'api_url' => request()->domain() . request()->rootUrl() . '/provision/custom/' . $id,
                'jwt' => $jwt,
                'v10' => true
            ];

            $result = $RouteLogic->curl( 'provision/custom/content', $postData, 'GET');

        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_act_exception')]);
        }
        return json($result);
    }

    // 创建快照
    public function snapshotPost(){
        $param = request()->param();

        $HostModel = HostModel::find($param['id']);
        if(empty($HostModel) || $HostModel['client_id'] != get_client_id() ){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_host_not_found')]);
        }

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByHost($param['id']);

            $postData = [
                'id' => $param['disk_id']??0,
                'name' => $param['name']??'',
                'func' => 'createSnap'
            ];

            $res = $RouteLogic->curl( 'provision/custom/'.$RouteLogic->upstream_host_id, $postData,'POST');
            if($res['status'] == 200){
                // 创建成功
                $result = [
                    'status' => 200,
                    'msg'    => lang_plugins('start_create_snapshot_success')
                ];

                $description = lang_plugins('res_mf_finance_log_host_start_create_snap_success', [
                    '{hostname}'=>$HostModel['name'],
                    '{name}'=>$param['name']
                ]);
            }else{
                $result = [
                    'status' => 400,
                    'msg'    => lang_plugins('start_create_snapshot_failed')
                ];

                $description = lang_plugins('res_mf_finance_log_host_start_create_snap_fail', [
                    '{hostname}'=>$HostModel['name'],
                    '{name}'=>$param['name']
                ]);
            }
            active_log($description, 'host', $HostModel['id']);
        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_act_exception')]);
        }
        return json($result);
    }

    // 恢复快照
    public function snapshotPut(){
        $param = request()->param();

        $HostModel = HostModel::find($param['id']);
        if(empty($HostModel) || $HostModel['client_id'] != get_client_id() ){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_host_not_found')]);
        }

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByHost($param['id']);

            $postData = [
                //'id' => $param['disk_id']??0,
                //'name' => $param['name']??'',
                'id' => $param['snapshot_id']??0,
                'func' => 'restoreSnap'
            ];

            $res = $RouteLogic->curl( 'provision/custom/'.$RouteLogic->upstream_host_id, $postData,'POST');
            if($res['status'] == 200){
                // 还原成功,更新密码,端口信息
                $result = [
                    'status' => 200,
                    'msg'    => lang_plugins('start_snapshot_restore_success')
                ];


                $description = lang_plugins('res_mf_finance_log_host_start_snap_restore_success', [
                    '{hostname}'=>$HostModel['name'],
                    '{name}'=>$param['snapshot_id']
                ]);
            }else{
                $result = [
                    'status' => 400,
                    'msg'    => lang_plugins('start_snapshot_restore_failed')
                ];

                $description = lang_plugins('res_mf_finance_log_host_start_snap_restore_fail', [
                    '{hostname}'=>$HostModel['name'],
                    '{name}'=>$param['snapshot_id']
                ]);
            }
            active_log($description, 'host', $HostModel['id']);
        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_act_exception')]);
        }
        return json($result);
    }

    // 删除快照
    public function snapshotDelete(){
        $param = request()->param();

        $HostModel = HostModel::find($param['id']);
        if(empty($HostModel) || $HostModel['client_id'] != get_client_id() ){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_host_not_found')]);
        }

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByHost($param['id']);

            $postData = [
                'id' => $param['snapshot_id']??0,
                //'name' => $param['name']??'',
                'func' => 'deleteSnap'
            ];

            $res = $RouteLogic->curl( 'provision/custom/'.$RouteLogic->upstream_host_id, $postData,'POST');

            if($res['status'] == 200){
                $result = [
                    'status' => 200,
                    'msg'    => lang_plugins('delete_snapshot_success')
                ];

                $description = lang_plugins('res_mf_finance_log_host_delete_snap_success', [
                    '{hostname}'=>$HostModel['name'],
                    '{name}'=>$param['snapshot_id']
                ]);
            }else{
                $result = [
                    'status' => 400,
                    'msg'    => lang_plugins('delete_snapshot_failed')
                ];

                $description = lang_plugins('res_mf_finance_log_host_delete_snap_fail', [
                    '{hostname}'=>$HostModel['name'],
                    '{name}'=>$param['snapshot_id']
                ]);
            }
            active_log($description, 'host', $HostModel['id']);
        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_act_exception')]);
        }
        return json($result);
    }

    // 获取备份信息
    public function backup()
    {
        $param = request()->param();

        $HostModel = HostModel::find($param['id']);
        if(empty($HostModel) || $HostModel['client_id'] != get_client_id() ){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_host_not_found')]);
        }

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByHost($param['id']);

            $UpstreamProductModel = new UpstreamProductModel();
            $upstream = $UpstreamProductModel->where('product_id',$HostModel['product_id'])->find();
            $api_id  = $upstream['supplier_id'];
            $key = 'api_auth_login_' . AUTHCODE . '_' . $api_id;

            $jwt = idcsmart_cache($key);

            $postData = [
                'id' => $RouteLogic->upstream_host_id,
                'key' => 'snapshot', // 这个含备份信息
                //'api_url' => request()->domain() . request()->rootUrl() . '/provision/custom/' . $id,
                'jwt' => $jwt,
                'v10' => true
            ];

            $result = $RouteLogic->curl( 'provision/custom/content', $postData, 'GET');

        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_act_exception')]);
        }
        return json($result);
    }


    // 创建备份
    public function backupPost(){
        $param = request()->param();

        $HostModel = HostModel::find($param['id']);
        if(empty($HostModel) || $HostModel['client_id'] != get_client_id() ){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_host_not_found')]);
        }

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByHost($param['id']);

            $postData = [
                'id' => $param['disk_id']??0,
                'name' => $param['name']??'',
                'func' => 'createBackup'
            ];

            $res = $RouteLogic->curl( 'provision/custom/'.$RouteLogic->upstream_host_id, $postData,'POST');
            if($res['status'] == 200){
                // 创建成功
                $result = [
                    'status' => 200,
                    'msg'    => lang_plugins('start_create_backup_success')
                ];

                $description = lang_plugins('res_mf_finance_log_host_start_create_backup_success', [
                    '{hostname}'=>$HostModel['name'],
                    '{name}'=>$param['disk_id']
                ]);
            }else{
                $result = [
                    'status' => 400,
                    'msg'    => lang_plugins('start_create_backup_failed')
                ];

                $description = lang_plugins('res_mf_finance_log_host_start_create_backup_fail', [
                    '{hostname}'=>$HostModel['name'],
                    '{name}'=>$param['disk_id']
                ]);
            }
            active_log($description, 'host', $HostModel['id']);

        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_act_exception')]);
        }
        return json($result);
    }

    // 恢复备份
    public function backupPut(){
        $param = request()->param();

        $HostModel = HostModel::find($param['id']);
        if(empty($HostModel) || $HostModel['client_id'] != get_client_id() ){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_host_not_found')]);
        }

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByHost($param['id']);

            $postData = [
                'id' => $param['backup_id']??0,
                'func' => 'restoreBackup'
            ];

            $res = $RouteLogic->curl( 'provision/custom/'.$RouteLogic->upstream_host_id, $postData,'POST');

            if($res['status'] == 200){
                $result = [
                    'status' => 200,
                    'msg'    => lang_plugins('start_backup_restore_success')
                ];

                $description = lang_plugins('res_mf_finance_log_host_start_backup_restore_success', [
                    '{hostname}'=>$HostModel['name'],
                    '{name}'=>$param['backup_id']
                ]);
            }else{
                $result = [
                    'status' => 400,
                    'msg'    => lang_plugins('start_backup_restore_failed')
                ];

                $description = lang_plugins('res_mf_finance_log_host_start_backup_restore_fail', [
                    '{hostname}'=>$HostModel['name'],
                    '{name}'=>$param['backup_id']
                ]);
            }
            active_log($description, 'host', $HostModel['id']);
        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_act_exception')]);
        }
        return json($result);
    }

    // 删除备份
    public function backupDelete(){
        $param = request()->param();

        $HostModel = HostModel::find($param['id']);
        if(empty($HostModel) || $HostModel['client_id'] != get_client_id() ){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_host_not_found')]);
        }

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByHost($param['id']);

            $postData = [
                'id' => $param['backup_id']??0,
                'func' => 'deleteBackup'
            ];

            $res = $RouteLogic->curl( 'provision/custom/'.$RouteLogic->upstream_host_id, $postData,'POST');
            if($res['status'] == 200){
                $result = [
                    'status' => 200,
                    'msg'    => lang_plugins('delete_backup_success')
                ];

                $description = lang_plugins('res_mf_finance_log_host_delete_backup_success', [
                    '{hostname}'=>$HostModel['name'],
                    '{name}'=>$param['backup_id']
                ]);
            }else{
                $result = [
                    'status' => 400,
                    'msg'    => lang_plugins('delete_backup_failed')
                ];

                $description = lang_plugins('res_mf_finance_log_host_delete_backup_fail', [
                    '{hostname}'=>$HostModel['name'],
                    '{name}'=>$param['backup_id']
                ]);
            }
            active_log($description, 'host', $HostModel['id']);
        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_act_exception')]);
        }
        return json($result);
    }

    // 远程信息
    public function remoteInfo(){
        $param = request()->param();

        $HostModel = HostModel::find($param['id']);
        if(empty($HostModel) || $HostModel['client_id'] != get_client_id() ){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_host_not_found')]);
        }

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByHost($param['id']);

            $postData = [
                'func' => 'remoteInfo'
            ];

            $result = $RouteLogic->curl( 'provision/custom/'.$RouteLogic->upstream_host_id, $postData,'POST');

        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_act_exception')]);
        }
        return json($result);
    }

    // 退出救援
    public function exitRescue(){
        $param = request()->param();

        $HostModel = HostModel::find($param['id']);
        if(empty($HostModel) || $HostModel['client_id'] != get_client_id() ){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_host_not_found')]);
        }

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByHost($param['id']);

            $postData = [
                'id'   => $RouteLogic->upstream_host_id,
                'func' => 'exitRescue',
            ];

            $result = $RouteLogic->curl( 'provision/button', $postData,'POST');

        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_act_exception')]);
        }
        return json($result);
    }

    // 磁盘
    public function disk(){
        $param = request()->param();

        $HostModel = HostModel::find($param['id']);
        if(empty($HostModel) || $HostModel['client_id'] != get_client_id() ){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_host_not_found')]);
        }

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByHost($param['id']);

            $postData = [
                'type' => 'disk',
                'select' => $param['select']??'',
                'start' => $param['start']??'',
                'end' => $param['end']??'',
                'is_api' => true
            ];

            $result = $RouteLogic->curl( 'provision/chart/'.$RouteLogic->upstream_host_id, $postData,'GET');

        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_act_exception')]);
        }
        return json($result);
    }

    // 日志
    public function log(){
        $param = request()->param();

        $SystemLogModel = new SystemLogModel();
        $result = $SystemLogModel->systemLogList($param);
        return json($result);
    }

    // 流量
    public function flowDetail(){
        $param = request()->param();

        $HostModel = HostModel::find($param['id']);
        if(empty($HostModel) || $HostModel['client_id'] != get_client_id() ){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_host_not_found')]);
        }

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByHost($param['id']);

            $postData = [
                'type' => 'flow',
                'select' => $param['select']??'',
                'start' => $param['start']??'',
                'end' => $param['end']??'',
                'is_api' => true
            ];

            $result = $RouteLogic->curl( 'provision/chart/'.$RouteLogic->upstream_host_id, $postData,'GET');

        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_act_exception')]);
        }
        return json($result);
    }

    /**
     * 时间 2023-02-06
     * @title 升降级配置页面
     * @desc 升降级配置页面
     * @url /console/v1/remf_finance/:id/upgrade_config
     * @method  GET
     * @author wyh
     * @version v1
     * @param int id - 产品ID require
     * @return array host - 配置数据
     */
    public function upgradeConfig(){
        $param = request()->param();

        $HostModel = HostModel::find($param['id']);
        if(empty($HostModel) || $HostModel['client_id'] != get_client_id() ){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_host_not_found')]);
        }

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByHost($param['id']);

            $postData = [
                'hid' => $RouteLogic->upstream_host_id,
            ];

            $result = $RouteLogic->curl( 'upgrade/index/'.$RouteLogic->upstream_host_id, $postData,'GET');

        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_act_exception')]);
        }
        return json($result);
    }

    /**
     * 时间 2023-02-06
     * @title 升降级配置计算价格
     * @desc 升降级配置计算价格
     * @url /console/v1/remf_finance/:id/sync_upgrade_config_price
     * @method  POST
     * @author wyh
     * @version v1
     * @param int id - 产品ID require
     * @param array configoption - 配置信息{"配置ID":"子项ID"} require
     * @return float price - 价格
     */
    public function syncUpgradeConfigPrice(){
        $param = request()->param();

        $HostModel = HostModel::find($param['id']);
        if(empty($HostModel) || $HostModel['client_id'] != get_client_id() ){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_dcim_host_not_found')]);
        }

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByHost($param['id']);

            $postData = [
                'hid' => $RouteLogic->upstream_host_id,
                'configoption' => $param['configoption']??[]
            ];

            $result = $RouteLogic->curl( 'upgrade/upgrade_config_post', $postData,'POST');
            if ($result['status']==200){
                $result = $RouteLogic->curl( 'upgrade/upgrade_config_page', ['hid' => $RouteLogic->upstream_host_id],'GET');
                if ($result['status']==200){
                    $res['status'] = 200;
                    $res['msg'] = $result['msg'];
                    //$res['data'] = $result['data'];
                    if ($RouteLogic->profit_type==1){
                        $res['data']['price'] = bcadd(($result['data']['subtotal']??0)-($result['data']['saleproducts']??0), $RouteLogic->getProfitPercent()*100,2);
                    }else{
                        $res['data']['price'] = bcmul(($result['data']['subtotal']??0)-($result['data']['saleproducts']??0), (1+$RouteLogic->getProfitPercent()),2);
                    }

                    if ($res['data']['price']<0){
                        $res['data']['price'] = bcsub(0,0,2);
                    }
                    //$res['data']['price'] = $result['data']['subtotal']??bcsub(0,0,2);
                    return json($res);
                }
            }

        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_dcim_act_exception')]);
        }
        return json($result);
    }

    /**
     * 时间 2023-02-06
     * @title 升降级配置结算
     * @desc 升降级配置结算
     * @url /console/v1/remf_finance/:id/upgrade_config
     * @method  POST
     * @author wyh
     * @version v1
     * @param int id - 产品ID require
     * @return int id - 订单ID
     */
    public function upgradeConfigPost(){
        $param = request()->param();

        $HostModel = HostModel::find($param['id']);
        if(empty($HostModel) || $HostModel['client_id'] != get_client_id() ){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_host_not_found')]);
        }
        $RouteLogic = new RouteLogic();
        $RouteLogic->routeByHost($param['id']);

        $res = $RouteLogic->curl( 'upgrade/upgrade_config_page', ['hid' => $RouteLogic->upstream_host_id],'GET');
        if ($res['status']!=200){
            return json($res);
        }
        $description = "";
        foreach ($res['data']['alloption'] as $item){
            $option_type = $item['option_type'];
            if ($option_type == 15 ||$option_type == 16 || $option_type == 17 || $option_type == 18 || $option_type == 19){
                $description .= $item['option_name'] . ":" . ($item['old_qty']??0) . "=>" . (($item['qty']??0).($item['unit']??"")) . "\n";
            }else{
                $description .= $item['option_name'] . ":" . ($item['old_suboption_name']??'') . "=>" . $item['suboption_name'] . "\n";
            }
        }
        $res['data']['description'] = $description;


        $OrderModel = new OrderModel();

        $res['data']['subtotal'] = (isset($res['data']['subtotal']) && $res['data']['subtotal']>0)?$res['data']['subtotal']:bcsub(0,0,2);
        $res['data']['total'] = (isset($res['data']['total']) && $res['data']['total']>0)?$res['data']['total']:bcsub(0,0,2);

        $data = [
            'host_id'     => $param['id']??0,
            'client_id'   => get_client_id(),
            'type'        => 'upgrade_config',
            'amount'      => $RouteLogic->profit_type==1?bcadd(($result['data']['subtotal']??0)-($result['data']['saleproducts']??0),$RouteLogic->getProfitPercent()*100,2):bcmul(($result['data']['subtotal']??0)-($result['data']['saleproducts']??0), (1+$RouteLogic->getProfitPercent()),2),//$res['data']['subtotal'],
            'description' => $res['data']['description'],
            'price_difference' => $RouteLogic->profit_type==1?bcadd(($res['data']['total']??0)-($result['data']['saleproducts']??0),$RouteLogic->getProfitPercent()*100,2):bcmul(($res['data']['total']??0)-($result['data']['saleproducts']??0), (1+$RouteLogic->getProfitPercent()),2),//$res['data']['total'],
            'renew_price_difference' => $RouteLogic->profit_type==1?bcadd(($res['data']['total']??0)-($result['data']['saleproducts']??0),$RouteLogic->getProfitPercent()*100,2):bcmul(($res['data']['total']??0)-($result['data']['saleproducts']??0), (1+$RouteLogic->getProfitPercent()),2),// $res['data']['total'],
            'upgrade_refund' => 0,
            'config_options' => [
                //'configoption' => $param['configoption']??[],
                // 取接口返回的配置
                'configoption' => $res['data']['configoptions']??[],
            ]
        ];

        $result = $OrderModel->createOrder($data);

        return json($result);
    }

    /**
     * 时间 2023-02-06
     * @title 升降级商品
     * @desc 升降级商品
     * @url /console/v1/remf_finance/:id/upgrade_product
     * @method  GET
     * @author wyh
     * @version v1
     * @param int id - 产品ID require
     * @return object old_host - 原产品数据
     * @return array host - 可升降级的商品数组
     * @return int host[].pid - 商品ID
     * @return string host[].host - 商品名称
     * @return array host[].cycle - 周期
     * @return float host[].cycle[].price - 价格
     * @return string host[].cycle[].billingcycle - 周期
     * @return string host[].cycle[].billingcycle_zh - 周期
     */
    public function upgradeProduct(){
        $param = request()->param();

        $HostModel = HostModel::find($param['id']);
        if(empty($HostModel) || $HostModel['client_id'] != get_client_id() ){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_host_not_found')]);
        }

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByHost($param['id']);

            $productId = $HostModel['product_id'];

            // 可升降级商品
            $ProductUpgradeProductModel = new ProductUpgradeProductModel();
            $upgradeProductIds = $ProductUpgradeProductModel->where('product_id',$productId)->column('upgrade_product_id');
            // 对应的上游商品ID
            $UpstreamProductModel = new UpstreamProductModel();
            $upstreamProductIds = $UpstreamProductModel->whereIn('product_id',$upgradeProductIds)->column('upstream_product_id');

            $postData = [
                'need_pids' => $upstreamProductIds??[], // [4,5]
            ];

            $result = $RouteLogic->curl( 'upgrade/upgrade_product/'.$RouteLogic->upstream_host_id, $postData,'GET');

        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_act_exception')]);
        }
        return json($result);
    }

    /**
     * 时间 2023-02-06
     * @title 升降级商品计算价格
     * @desc 升降级商品计算价格
     * @url /console/v1/remf_finance/:id/sync_upgrade_product_price
     * @method  POST
     * @author wyh
     * @version v1
     * @param int id - 产品ID require
     * @param int product_id - 新商品ID require
     * @param string cycle - 周期,传billingcycle的值 require
     * @return float price - 价格
     */
    public function syncUpgradeProductPrice(){
        $param = request()->param();

        $HostModel = HostModel::find($param['id']);
        if(empty($HostModel) || $HostModel['client_id'] != get_client_id() ){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_dcim_host_not_found')]);
        }

        try{
            $RouteLogic = new RouteLogic();
            $RouteLogic->routeByHost($param['id']);

            // 可升降级商品
            $productId = $HostModel['product_id'];
            $ProductUpgradeProductModel = new ProductUpgradeProductModel();
            $upgradeProductIds = $ProductUpgradeProductModel->where('product_id',$productId)->column('upgrade_product_id');
            // 对应的上游商品ID
            $UpstreamProductModel = new UpstreamProductModel();
            $upstreamProductIds = $UpstreamProductModel->whereIn('product_id',$upgradeProductIds)->column('upstream_product_id');
            if (!in_array($param['product_id']??0,$upstreamProductIds)){
                throw new \Exception("商品不可升降级");
            }

            $postData = [
                'hid' => $RouteLogic->upstream_host_id,
                'pid' => $param['product_id']??0,
                'billingcycle' => $param['cycle']??""
            ];

            $result = $RouteLogic->curl( 'upgrade/upgrade_product_post', $postData,'POST');
            if ($result['status']==200){
                $result = $RouteLogic->curl( 'upgrade/upgrade_product_page', ['hid' => $RouteLogic->upstream_host_id],'GET');
                if ($result['status']==200){
                    $res['status'] = 200;
                    $res['msg'] = $result['msg'];
                    $upstream = $UpstreamProductModel->where('upstream_product_id',$param['product_id']??0)
                        ->where('supplier_id',$RouteLogic->supplier_id)
                        ->find();
                    // 以新商品利润计算
                    if ($RouteLogic->profit_type==1){
                        $res['data']['price'] = bcadd($result['data']['amount_total']??0, $upstream['profit_percent'],2);
                    }else{
                        $res['data']['price'] = bcmul($result['data']['amount_total']??0, (1+$upstream['profit_percent']),2);
                    }

                    if ($res['data']['price']<0){
                        $res['data']['price'] = bcsub(0,0,2);
                    }
                    return json($res);
                }
            }

        }catch(\Exception $e){
            return json(['status'=>400, 'msg'=>$e->getMessage()/*lang_plugins('res_mf_finance_dcim_act_exception')*/]);
        }
        return json($result);
    }

    /**
     * 时间 2023-02-06
     * @title 升降级商品结算
     * @desc 升降级商品结算
     * @url /console/v1/remf_finance/:id/upgrade_product
     * @method  POST
     * @author wyh
     * @version v1
     * @param int id - 产品ID require
     * @return int id - 订单ID
     */
    public function upgradeProductPost(){
        $param = request()->param();

        $host = HostModel::find($param['id']);
        if(empty($host) || $host['client_id'] != get_client_id() ){
            return json(['status'=>400, 'msg'=>lang_plugins('res_mf_finance_host_not_found')]);
        }
        $RouteLogic = new RouteLogic();
        $RouteLogic->routeByHost($param['id']);

        $res = $RouteLogic->curl( 'upgrade/upgrade_product_page', ['hid' => $RouteLogic->upstream_host_id],'GET');
        if ($res['status']!=200){
            return json($res);
        }

        // 上游商品ID
        $upstreamProductId = $res['data']['new_pid'];

        // 对应的本地商品ID
        $UpstreamProductModel = new UpstreamProductModel();
        $upstream = $UpstreamProductModel->where('upstream_product_id',$upstreamProductId)
            ->where('supplier_id',$RouteLogic->supplier_id)
            ->find();
        if (empty($upstream)){
            return json(['status'=>400,'msg'=>"商品不可升降级"]);
        }

        // 以新商品利润计算
        if ($RouteLogic->profit_type==1){
            $amount = bcadd($res['data']['amount_total']??0, $upstream['profit_percent'],2);
        }else{
            $amount = bcmul($res['data']['amount_total']??0, (1+$upstream['profit_percent']),2);
        }

        $amount = $amount>0?$amount:bcsub(0,0,2);

        // 自定义升降级产品订单逻辑
        $OrderModel = new OrderModel();
        $result = $OrderModel->createUpgradeOrder([
            'host_id' => $param['id'],
            'client_id' => get_client_id(),
            'upgrade_refund' => 0, # 不支持退款
            'product' => [
                'product_id' => $upstream['product_id'],
                'price' =>  $amount,
                'config_options' => [
                    'new_pid' => $upstreamProductId,//上游商品
                    'cycle' => $res['data']['billingcycle']??"",
                    'configoption' => [] //使用默认配置
                ]
            ]
        ]);

        return json($result);
    }

}
