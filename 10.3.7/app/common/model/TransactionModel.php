<?php
namespace app\common\model;

use think\db\Query;
use think\Model;
use think\Db;
use app\admin\model\PluginModel;

/**
 * @title 交易流水模型
 * @desc 交易流水模型
 * @use app\common\model\TransactionModel
 */
class TransactionModel extends Model
{
	protected $name = 'transaction';

    // 设置字段信息
    protected $schema = [
        'id'                    => 'int',
        'order_id'              => 'string',
        'client_id'             => 'int',
        'amount'                => 'float',
        'gateway'               => 'string',
        'gateway_name'          => 'string',
        'transaction_number'    => 'string',
        'create_time'           => 'int',
    ];

    /**
     * 时间 2022-05-17
     * @title 交易流水列表
     * @desc 交易流水列表
     * @author theworld
     * @version v1
     * @param string param.keywords - 关键字,搜索范围:交易流水号,订单ID,用户名称,邮箱,手机号
     * @param int param.client_id - 用户ID
     * @param int param.order_id - 订单ID
     * @param string param.amount - 金额
     * @param string param.gateway - 支付方式
     * @param int param.start_time - 开始时间
     * @param int param.end_time - 结束时间
     * @param int param.page - 页数
     * @param int param.limit - 每页条数
     * @param string param.orderby - 排序 id
     * @param string param.sort - 升/降序 asc,desc
     * @return array list - 交易流水
     * @return int list[].id - 交易流水ID 
     * @return float list[].amount - 金额
     * @return string list[].gateway - 支付方式
     * @return string list[].transaction_number - 交易流水号
     * @return int list[].client_id - 用户ID 
     * @return string list[].client_name - 用户名称 
     * @return string list[].email - 邮箱 
     * @return string list[].phone_code - 国际电话区号 
     * @return string list[].phone - 手机号 
     * @return string list[].company - 公司 
     * @return int list[].order_id - 订单ID 
     * @return int list[].create_time - 创建时间
     * @return string list[].type - 订单类型new新订单renew续费订单upgrade升降级订单artificial人工订单
     * @return array list[].hosts - 产品
     * @return int list[].hosts[].id - 产品ID
     * @return string list[].hosts[].name - 商品名称
     * @return array list[].descriptions - 描述
     * @return int count - 交易流水总数
     */
    public function transactionList($param)
    {
        // 获取当前应用
        $app = app('http')->getName();
        if($app=='home'){
            $param['client_id'] = get_client_id();
            if(empty($param['client_id'])){
                return ['list' => [], 'count' => 0];
            }
            $param['order_id'] = isset($param['order_id']) ? intval($param['order_id']) : 0;
        }else{
            $param['client_id'] = isset($param['client_id']) ? intval($param['client_id']) : 0;
            $param['order_id'] = isset($param['order_id']) ? intval($param['order_id']) : 0;
        }

        $param['keywords'] = $param['keywords'] ?? '';
        $param['orderby'] = isset($param['orderby']) && in_array($param['orderby'], ['id', 'amount', 'create_time']) ? 't.'.$param['orderby'] : 't.id';
        $param['amount'] = $param['amount'] ?? '';
        $param['gateway'] = $param['gateway'] ?? '';
        $param['start_time'] = intval($param['start_time'] ?? 0);
        $param['end_time'] = intval($param['end_time'] ?? 0);

        $where = function (Query $query) use ($param){
            if(!empty($param['order_id'])){
                $query->where('t.order_id', $param['order_id']);
            }
            if(!empty($param['client_id'])){
                $query->where('t.client_id', $param['client_id']);
            }
            if(!empty($param['keywords'])){
                $query->where('t.transaction_number|t.order_id|c.username|c.email|c.phone', 'like', "%{$param['keywords']}%");
            }
            if(!empty($param['amount'])){
                $query->where('t.amount', 'like', "%{$param['amount']}%");
            }
            if(!empty($param['gateway'])){
                $query->where('t.gateway', $param['gateway']);
            }
            if(!empty($param['start_time']) && !empty($param['end_time'])){
                $query->where('t.create_time', '>=', $param['start_time'])->where('t.create_time', '<=', $param['end_time']);
            } 
            if(!empty($param['type'])){
                $query->where('o.type', $param['type']);
            }
        };
        $count = $this->alias('t')
            ->field('t.id')
            ->leftjoin('client c', 'c.id=t.client_id')
            ->leftjoin('order o', 'o.id=t.order_id')
            ->where($where)
            ->count();
        $transactions = $this->alias('t')
            ->field('t.id,t.amount,t.gateway_name gateway,t.transaction_number,t.client_id,c.username client_name,c.email,c.phone_code,c.phone,c.company,t.order_id,t.create_time,o.type')
            ->leftjoin('client c', 'c.id=t.client_id')
            ->leftjoin('order o', 'o.id=t.order_id')
            ->where($where)
            ->limit($param['limit'])
            ->page($param['page'])
            ->order($param['orderby'], $param['sort'])
            ->select()
            ->toArray();

        //获取交易流水对应的产品
        $orderId = array_column($transactions, 'order_id');

        $orderItems = OrderItemModel::alias('oi')
        	->field('oi.order_id,h.id,p.name,oi.description')
        	->leftjoin('host h',"h.id=oi.host_id")
        	->leftjoin('product p',"p.id=oi.product_id")
        	->whereIn('oi.order_id', $orderId)
        	->select()
            ->toArray();
        $hosts = [];
        $descriptions = [];
        foreach ($orderItems as $key => $orderItem) {
        	if(!empty($orderItem['name'])){
        		$hosts[$orderItem['order_id']][] = ['id' => $orderItem['id'], 'name' => $orderItem['name']];
        	}
        	if(!empty($orderItem['description'])){
                $descriptions[$orderItem['order_id']][] = $orderItem['description'];
            }
        }

        foreach ($transactions as $key => $transaction) {
        	$transactions[$key]['hosts'] = $hosts[$transaction['order_id']] ?? [];
            $transactions[$key]['descriptions'] = $descriptions[$transaction['order_id']] ?? [];

            // 前台接口去除字段
            if($app=='home'){
                unset($transactions[$key]['client_id'], $transactions[$key]['client_name'], $transactions[$key]['email'], $transactions[$key]['phone_code'], $transactions[$key]['phone'], $transactions[$key]['company']);
            }
        }

        return ['list' => $transactions, 'count' => $count];
    }

	/**
     * 时间 2022-05-17
     * @title 新增交易流水
     * @desc 新增交易流水
     * @author theworld
     * @version v1
     * @param float param.amount - 金额 required
     * @param string param.gateway - 支付方式 required
     * @param string param.transaction_number - 交易流水号 required
     * @param int param.client_id - 用户ID required
     * @return int status - 状态码,200成功,400失败
     * @return string msg - 提示信息
     */
    public function createTransaction($param)
    {
    	//验证用户ID
    	$client = ClientModel::find($param['client_id']);
        if (empty($client)){
            return ['status'=>400, 'msg'=>lang('client_is_not_exist')];
        }

        //验证支付方式
    	$gateway = PluginModel::where('module', 'gateway')->where('name', $param['gateway'])->find();
        if (empty($gateway)){
            return ['status'=>400, 'msg'=>lang('gateway_is_not_exist')];
        }
        $gateway['config'] = json_decode($gateway['config'],true);
        $gateway['title'] =  (isset($gateway['config']['module_name']) && !empty($gateway['config']['module_name']))?$gateway['config']['module_name']:$gateway['title'];

	    $this->startTrans();
		try {
	    	$transaction = $this->create([
	    		'amount' => $param['amount'],
	    		'gateway' => $param['gateway'],
	    		'gateway_name' => $gateway['title'],
	    		'transaction_number' => $param['transaction_number'] ?? '',
	    		'client_id' => $param['client_id'],
                'create_time' => time()
	    	]);

            # 记录日志
            active_log(lang('admin_add_transaction', ['{admin}'=>request()->admin_name, '{client}'=>'client#'.$client->id.'#'.$client->username.'#', '{transaction}'=>'#'.$transaction->id]), 'transaction', $transaction->id);

	        $this->commit();
		} catch (\Exception $e) {
		    // 回滚事务
		    $this->rollback();
		    return ['status' => 400, 'msg' => lang('create_fail')];
		}

		hook('after_transaction_create',['id'=>$transaction->id,'customfield'=>$param['customfield']??[]]);

    	return ['status' => 200, 'msg' => lang('create_success')];
    }

    /**
     * 时间 2022-10-11
     * @title 编辑交易流水
     * @desc 编辑交易流水
     * @author theworld
     * @version v1
     * @param float param.id - 交易流水ID required
     * @param float param.amount - 金额 required
     * @param string param.gateway - 支付方式 required
     * @param string param.transaction_number - 交易流水号 required
     * @param int param.client_id - 用户ID required
     * @return int status - 状态码,200成功,400失败
     * @return string msg - 提示信息
     */
    public function updateTransaction($param)
    {
        //验证交易流水ID
        $transaction = $this->find($param['id']);
        if (empty($transaction)){
            return ['status'=>400, 'msg'=>lang('transaction_is_not_exist')];
        }

        //验证用户ID
        $client = ClientModel::find($param['client_id']);
        if (empty($client)){
            return ['status'=>400, 'msg'=>lang('client_is_not_exist')];
        }

        //验证支付方式
        $gateway = PluginModel::where('module', 'gateway')->where('name', $param['gateway'])->find();
        if (empty($gateway)){
            return ['status'=>400, 'msg'=>lang('gateway_is_not_exist')];
        }
        $gateway['config'] = json_decode($gateway['config'],true);
        $gateway['title'] =  (isset($gateway['config']['module_name']) && !empty($gateway['config']['module_name']))?$gateway['config']['module_name']:$gateway['title'];

        $this->startTrans();
        try {
            # 日志详情
            $description = [];
            if ($transaction['amount'] != $param['amount']){
                $description[] = lang('old_to_new',['{old}'=>lang('transaction_amount').$transaction['amount'], '{new}'=>$param['amount']]);
            }
            if ($transaction['gateway'] != $param['gateway']){
                $oldGateway = PluginModel::where('module', 'gateway')->where('name', $transaction['gateway'])->find();
                $description[] = lang('old_to_new',['{old}'=>lang('transaction_gateway').($oldGateway['title'] ?? ''), '{new}'=>$gateway['title']]);
            }
            if ($transaction['transaction_number'] != $param['transaction_number']){
                $description[] = lang('old_to_new',['{old}'=>lang('transaction_transaction_number').$transaction['transaction_number'], '{new}'=>$param['transaction_number']]);
            }
            if ($transaction['client_id'] != $param['client_id']){
                $oldClient = ClientModel::find($transaction['client_id']);
                $description[] = lang('old_to_new',['{old}'=>lang('transaction_client').($oldClient['username'] ?? ''), '{new}'=>$client['username']]);
            }
            
            $description = implode(',', $description);

            $transaction = $this->update([
                'amount' => $param['amount'],
                'gateway' => $param['gateway'],
                'gateway_name' => $gateway['title'],
                'transaction_number' => $param['transaction_number'] ?? '',
                'client_id' => $param['client_id'],
            ], ['id' => $param['id']]);

            # 记录日志
            active_log(lang('admin_edit_transaction', ['{admin}'=>request()->admin_name, '{client}'=>'client#'.$client->id.'#'.$client->username.'#', '{transaction}'=>'#'.$transaction->id, '{description}'=>$description]), 'transaction', $transaction->id);

            $this->commit();
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return ['status' => 400, 'msg' => lang('update_fail')];
        }

        return ['status' => 200, 'msg' => lang('update_success')];
    }

    /**
     * 时间 2022-05-17
     * @title 删除交易流水
     * @desc 删除交易流水
     * @author theworld
     * @version v1
     * @param int id - 交易流水ID required
     * @return int status - 状态码,200成功,400失败
     * @return string msg - 提示信息
     */
    public function deleteTransaction($id)
    {
    	//验证交易流水ID
    	$transaction = $this->find($id);
    	if (empty($transaction)){
            return ['status'=>400, 'msg'=>lang('transaction_is_not_exist')];
        }

    	$this->startTrans();
		try {

            $client = ClientModel::find($transaction->client_id);
            if(empty($client)){
                $clientName = '#'.$transaction->client_id;
            }else{
                $clientName = 'client#'.$client->id.'#'.$client->username.'#';
            }
            # 记录日志
            active_log(lang('admin_delete_transaction', ['{admin}'=>request()->admin_name, '{client}'=>$clientName, '{transaction}'=>'#'.$transaction->id]), 'transaction', $transaction->id);

			$this->destroy($id);
		    $this->commit();
		} catch (\Exception $e) {
		    // 回滚事务
		    $this->rollback();
		    return ['status' => 400, 'msg' => lang('delete_fail')];
		}

        hook('after_transaction_delete',['id'=>$transaction->id]);

    	return ['status' => 200, 'msg' => lang('delete_success')];
    }
}
