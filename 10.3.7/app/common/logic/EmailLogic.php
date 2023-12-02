<?php 
namespace app\common\logic;

use think\facade\Db;
use app\admin\model\PluginModel;
use app\common\model\NoticeSettingModel;
use app\common\model\EmailTemplateModel;
use app\admin\model\EmailLogModel;
use app\common\model\ConfigurationModel;
/**
 * @title 邮件发送
 * @desc 邮件发送
 * @use app\common\logic\EmailLogic
 */
class EmailLogic
{
    /**
     * 时间 2022-05-19
     * @title 基础发送
     * @desc 基础发送
     * @author xiong
     * @version v1
     * @param array
     * @param string param.email_name - 邮件插件标识名 required 
     * @param string param.email - 邮箱 required
     * @param string param.subject - 邮件标题 required
     * @param string param.message - 邮件内容 required
     * @param string param.attachments - 邮件附件
     * @param array param.template_param - 参数替换
     */
    public function sendBase($param)
    {
		$data = [
			'email' => $param['email'],
			'subject' => $this->paramStrReplace($param['subject'],$param['template_param']),
			'message' => $this->paramStrReplace($param['message'],$param['template_param']),
			'attachments' => $param['attachments'],
			'email_name' => $param['email_name'],
		];		
		if(empty($param['email'])){
			return ['status'=>400, 'msg'=>lang('email_cannot_be_empty'),'data'=>$data];//邮箱不能为空
		}
		$mail_methods = $this->mailMethods('send',$data);
		if($mail_methods['status'] == 'success'){
			return ['status'=>200, 'msg'=>lang('send_mail_success'), 'data'=>$data];//邮件发送成功
		}else{
			return ['status'=>400, 'msg'=>lang('send_mail_error').' : '.$mail_methods['msg'], 'data'=>$data];//邮件发送失败
		}
    }
    /**
     * 时间 2022-05-19
     * @title 发送
     * @desc 发送
     * @author xiong
     * @version v1
     * @param string param.email - 邮箱 required
     * @param string param.name - 动作名称 required
     * @param int param.client_id - 客户id
     * @param int param.host_id - 主机id
     * @param int param.order_id - 订单id
     * @param array param.template_param - 参数
     */
    public function send($param)
    {
		//读取发送动作
		$index_setting = (new NoticeSettingModel())->indexSetting($param['name']);
		//产品开通中
		if($param['name']=='host_pending'){
			if(empty($param['host_id'])){
				return ['status'=>400, 'msg'=>lang('id_error')];
			}
			$index_host = Db::name('host')->field('id,product_id,server_id,name,notes,first_payment_amount,renew_amount,billing_cycle,billing_cycle_name,billing_cycle_time,active_time,due_time,status,client_id,suspend_reason')->find($param['host_id']);
			if(empty($index_host)){
				return ['status'=>400, 'msg'=>lang('host_is_not_exist')];
			}
			$index_product = Db::name('product')->find($index_host['product_id']);
			if(empty($index_product)){
				return ['status'=>400, 'msg'=>lang('product_is_not_exist')];
			}
			if($index_product['creating_notice_mail'] && $index_product['creating_notice_mail_api']>0 && $index_product['creating_notice_mail_template']>0){
				$plugin = Db::name('plugin')->field('id,name')->find($index_product['creating_notice_mail_api']);
				$index_setting['email_enable'] = 1;
				$index_setting['email_name'] = $plugin['name'];
				$index_setting['email_template'] = $index_product['creating_notice_mail_template'];
			}

		}
		//产品开通成功
		if($param['name']=='host_active'){
			if(empty($param['host_id'])){
				return ['status'=>400, 'msg'=>lang('id_error')];
			}
			$index_host = Db::name('host')->field('id,product_id,server_id,name,notes,first_payment_amount,renew_amount,billing_cycle,billing_cycle_name,billing_cycle_time,active_time,due_time,status,client_id,suspend_reason')->find($param['host_id']);
			if(empty($index_host)){
				return ['status'=>400, 'msg'=>lang('host_is_not_exist')];
			}
			$index_product = Db::name('product')->find($index_host['product_id']);
			if(empty($index_product)){
				return ['status'=>400, 'msg'=>lang('product_is_not_exist')];
			}
			if($index_product['created_notice_mail'] && $index_product['created_notice_mail_api']>0 && $index_product['created_notice_mail_template']>0){
				$plugin = Db::name('plugin')->field('id,name')->find($index_product['created_notice_mail_api']);
				$index_setting['email_enable'] = 1;
				$index_setting['email_name'] = $plugin['name'];
				$index_setting['email_template'] = $index_product['created_notice_mail_template'];
			}
		}
		if(empty($index_setting['name'])){
			return ['status'=>400, 'msg'=>lang('send_wrong_action_name')];//动作名称错误
		}
		if($index_setting['email_enable'] == 0){
			return ['status'=>400, 'msg'=>lang('send_mail_action_not_enabled')];//邮件动作发送未开启
		}
		if(isset($param['email_name'])){
			$index_setting['email_name'] = $param['email_name'];
		}
		if(empty($index_setting['email_name'])){
			return ['status'=>400, 'msg'=>lang('send_mail_interface_not_set')];//邮件发送接口未设置
		}
		if(isset($param['email_template'])){
			$index_setting['email_template'] = $param['email_template'];
		}
		if($index_setting['email_template'] == 0){
			return ['status'=>400, 'msg'=>lang('send_mail_template_not_set')];//邮件发送模板未设置
		}			
		$index_mail_template = (new EmailTemplateModel())->indexEmailTemplate($index_setting['email_template']);
		if(empty($index_mail_template)){
			return ['status'=>400, 'msg'=>lang('email_template_is_not_exist')];//邮件模板不存在
		}			
		
		
		$template_param = $client = $order = $host = [];
		//全局参数
		$setting = ['website_name','website_url'];
		$configuration=configuration($setting);
		$system = [
			'system_website_name'=>$configuration['website_name'],
			'system_website_url'=>$configuration['website_url'],
			'send_time'=>date('Y-m-d H:i:s'),//发送时间
		];
		$client_id = 0;
		//订单
        if(!empty($param['order_id'])){
			$index_order = Db::name('order')->field('id,type,amount,create_time,status,gateway_name gateway,credit,client_id')->find($param['order_id']);
			if(empty($index_order)){
				return ['status'=>400, 'msg'=>lang('order_is_not_exist')];
			}
			$order = [
				'order_id' => $index_order['id'],
				'order_create_time' => $index_order['create_time'],
				'order_amount' => $index_order['amount'],
			];	
			if(isset($param['client_id']) && !empty($param['client_id'])){
				$client_id = $param['client_id'];
			}else{
				$client_id = $param['client_id'] = $index_order['client_id'];
			}
		}
		//产品
        if(!empty($param['host_id'])){	
			$index_host = Db::name('host')->field('id,product_id,server_id,name,notes,first_payment_amount,renew_amount,billing_cycle,billing_cycle_name,billing_cycle_time,active_time,due_time,status,client_id,suspend_reason')->find($param['host_id']);
			if(empty($index_host)){
				return ['status'=>400, 'msg'=>lang('host_is_not_exist')];
			}
			$index_product = Db::name('product')->field('id,name')->find($index_host['product_id']);
			if(empty($index_product)){
				return ['status'=>400, 'msg'=>lang('product_is_not_exist')];
			}
			//获取自动化设置
			$config=(new ConfigurationModel())->cronList();
			$host = [
				'product_name' => $index_product['name'] .'-'.$index_host['name'],
				'product_marker_name' => $index_host['name'],
				'product_first_payment_amount' => $index_host['first_payment_amount'],
				'product_renew_amount' => $index_host['renew_amount'],
				'product_binlly_cycle' => $index_host['billing_cycle'],
				'product_active_time' => date("Y-m-d H:i:s", $index_host['active_time']),
				'product_due_time' => date("Y-m-d H:i:s", $index_host['due_time']),
				'product_suspend_reason' => $index_host['suspend_reason'],
				'renewal_first' => $config['cron_due_renewal_first_day'],
				'renewal_second' => $config['cron_due_renewal_second_day'],
			];	
			if(isset($param['client_id']) && !empty($param['client_id'])){
				$client_id = $param['client_id'];
			}else{
				$client_id = $param['client_id'] = $index_host['client_id'];
			}		
		}
		//客户
        if(!empty($param['client_id'])){
			$index_client = Db::name('client')->field('id,username,email,phone_code,phone,company,country,address,language,notes,status,create_time register_time,last_login_time,last_login_ip,credit')->find($param['client_id']);
			if(empty($index_client)){
				return ['status'=>400, 'msg'=>lang('client_is_not_exist')];
			}
			if($index_client['username']){
				$account = $index_client['username'];
			}else if($index_client['phone']){
				$account = $index_client['phone_code'].$index_client['phone'];
			}else if($index_client['email']){
				$account = $index_client['email'];
			}	
			
			$client = [
				'client_register_time' => date("Y-m-d H:i:s", $index_client['register_time']),
				'client_username' => $index_client['username'],
				'client_email' => $index_client['email'],
				'client_phone' => $index_client['phone_code'].$index_client['phone'],
				'client_company' => $index_client['company'],
				'client_last_login_time' => date("Y-m-d H:i:s", $index_client['last_login_time']),
				'client_last_login_ip' => $index_client['last_login_ip'],
				'account' => $account,
			];
			$client_id = $param['client_id'];
			$param['email'] = $param['email'] ?? '';	
			$param['email'] = !empty($param['email']) ? $param['email'] : $index_client['email'];
		}	
		
		if(!empty($param['template_param'])) $template_param = $param['template_param'];
		$template_param=array_merge($system,$client,$order,$host,$template_param);
		$data = [
			'email' => $param['email']??'',
			'subject' => $index_mail_template['subject'],
			'message' => $index_mail_template['message'],
			'attachments' => $index_mail_template['attachments'],
			'email_name' => $index_setting['email_name'],
			'template_param' => $template_param,
		];

		// 发送前hook
		$send = true;
		$result_hook = hook('before_email_send', ['param' => $param, 'data' => $data]); // name:动作名称send:true发送false取消发送data:发送数据
		$result_hook = array_values(array_filter($result_hook ?? []));
		foreach ($result_hook as $key => $value) {
			if(isset($value['send']) && $value['send']===false){
				$send = false;
				break;
			}
		}
		if($send===false){
			return ['status'=>400, 'msg'=>lang('email_cancel_send')];//邮件取消发送
		}
		if(isset($param['admin_id'])){
			$admin_id = $param['admin_id'];
		}

		$send_result = $this->sendBase($data);	
		$log = [       
            'subject' => $send_result['data']['subject'] ?? '',
            'message' => $send_result['data']['message'] ?? '',
            'status' => ($send_result['status'] == 200)?1:0,
			'fail_reason' =>($send_result['status'] == 200)?'':$send_result['msg'],			
			'to' =>$data['email'],			
            'rel_id' => $admin_id ?? $client_id,
            'type' => isset($admin_id) ? 'admin' : 'client',
			'ip' =>  empty($param['ip'])?'':$param['ip'],
			'port' =>  empty($param['port'])?'':$param['port'],			
        ];
		(new EmailLogModel())->createEmailLog($log);
		unset($send_result['data']);	
		return $send_result;
    }
	//邮件接口调用
	private function mailMethods($cmd,$param)
	{
		//邮件接口判断
		$mail = (new PluginModel())->pluginList(['module'=>'mail']);				
		$mail_status = array_column($mail['list'],"status","name");
		if(empty($mail_status[$param['email_name']])){
			return ['status'=>400, 'msg'=>lang('send_mail_interface_is_not_exist')];//邮件接口不存在
		}else if($mail_status[$param['email_name']]==0){
			return ['status'=>400, 'msg'=>lang('send_mail_interface_is_disabled')];//邮件接口已禁用
		}else if($mail_status[$param['email_name']]==3){
			return ['status'=>400, 'msg'=>lang('send_mail_interface_not_installed_')];//邮件接口未安装
		}
		//提交到接口
		
		$class = get_plugin_class($param['email_name'],'mail');
		if (!class_exists($class)) {
			return ['status'=>400, 'msg'=>lang('send_mail_interface_is_not_exist')];//邮件接口不存在
		}
		$methods = get_class_methods($class)?:[];
		if(!in_array($cmd,$methods)){
			return ['status'=>400, 'msg'=>lang('send_mail_interface_not_supported')];//邮件接口不支持
		}
		$mail_class = new $class();
		$config = $mail_class->getConfig();
		//发送
		$data=[
			'email' => $param['email'],
			'subject' => $param['subject'],
			'content' => htmlspecialchars_decode($param['message']),
			'attachments' => $param['attachments'],
			'config' => $config?:[],
		];
		return $mail_class->$cmd($data);
		
		
	}
	//参数替换
	private function paramStrReplace($content,$param)
	{
		foreach($param as $k=>$v){
		$content=str_replace('{'.$k.'}',$v,$content);
		}
		return $content;
	}
}