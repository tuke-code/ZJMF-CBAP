<?php 

use think\facade\Route;

$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

# 前台,可登录不可登录的接口
Route::group('console/v1',function (){

	// 订购页面
    Route::get('product/:id/mf_dcim/order_page', "\\server\\mf_dcim\\controller\\home\\CloudController@orderPage");
    Route::get('product/:id/mf_dcim/image', "\\server\\mf_dcim\\controller\\home\\CloudController@imageList");
    Route::post('product/:id/mf_dcim/duration', "\\server\\mf_dcim\\controller\\home\\CloudController@getAllDurationPrice");
    Route::get('product/:id/mf_dcim/line/:line_id', "\\server\\mf_dcim\\controller\\home\\CloudController@lineConfig");
    Route::get('product/:id/mf_dcim/data_center', "\\server\\mf_dcim\\controller\\home\\CloudController@dataCenterSelect");
    Route::get('product/:id/mf_dcim/package', "\\server\\mf_dcim\\controller\\home\\CloudController@packageIndex");

    // vnc
    Route::get('mf_dcim/:id/vnc', "\\server\\mf_dcim\\controller\\home\\CloudController@vncPage");

})->allowCrossDomain([
    'Access-Control-Allow-Origin'        => $origin,
    'Access-Control-Allow-Credentials'   => 'true',
    'Access-Control-Max-Age'             => 600,
])->middleware(\app\http\middleware\Check::class);

// 前台需要登录的接口
Route::group('console/v1',function (){
    
	Route::post('product/:id/mf_dcim/validate_settle', "\\server\\mf_dcim\\controller\\home\\CloudController@validateSettle");
	Route::get('mf_dcim', "\\server\\mf_dcim\\controller\\home\\CloudController@list");
	Route::get('mf_dcim/:id', "\\server\\mf_dcim\\controller\\home\\CloudController@detail");
	Route::get('mf_dcim/:id/part', "\\server\\mf_dcim\\controller\\home\\CloudController@detailPart");
	Route::get('mf_dcim/:id/status', "\\server\\mf_dcim\\controller\\home\\CloudController@status");
	Route::post('mf_dcim/:id/on', "\\server\\mf_dcim\\controller\\home\\CloudController@on");
	Route::post('mf_dcim/:id/off', "\\server\\mf_dcim\\controller\\home\\CloudController@off");
	Route::post('mf_dcim/:id/reboot', "\\server\\mf_dcim\\controller\\home\\CloudController@reboot");
	Route::post('mf_dcim/:id/vnc', "\\server\\mf_dcim\\controller\\home\\CloudController@vnc");
	Route::post('mf_dcim/:id/reset_password', "\\server\\mf_dcim\\controller\\home\\CloudController@resetPassword");
	Route::post('mf_dcim/:id/rescue', "\\server\\mf_dcim\\controller\\home\\CloudController@rescue");
	Route::post('mf_dcim/:id/reinstall', "\\server\\mf_dcim\\controller\\home\\CloudController@reinstall");
	Route::get('mf_dcim/:id/chart', "\\server\\mf_dcim\\controller\\home\\CloudController@chart");
	Route::get('mf_dcim/:id/flow', "\\server\\mf_dcim\\controller\\home\\CloudController@flowDetail");
	Route::get('mf_dcim/:id/log', "\\server\\mf_dcim\\controller\\home\\CloudController@log");
	Route::get('mf_dcim/:id/image/check', "\\server\\mf_dcim\\controller\\home\\CloudController@checkHostImage");
	Route::post('mf_dcim/:id/image/order', "\\server\\mf_dcim\\controller\\home\\CloudController@createImageOrder");
	Route::get('mf_dcim/:id/remote_info', "\\server\\mf_dcim\\controller\\home\\CloudController@remoteInfo");
	Route::get('mf_dcim/:id/ip', "\\server\\mf_dcim\\controller\\home\\CloudController@ipList");
	Route::get('mf_dcim/:id/ip_num', "\\server\\mf_dcim\\controller\\home\\CloudController@calIpNumPrice");
	Route::post('mf_dcim/:id/ip_num/order', "\\server\\mf_dcim\\controller\\home\\CloudController@createIpNumOrder");
	Route::get('mf_dcim/:id/common_config', "\\server\\mf_dcim\\controller\\home\\CloudController@calCommonConfigPrice");
	Route::post('mf_dcim/:id/common_config/order', "\\server\\mf_dcim\\controller\\home\\CloudController@createCommonConfigOrder");

})->allowCrossDomain([
    'Access-Control-Allow-Origin'        => $origin,
    'Access-Control-Allow-Credentials'   => 'true',
    'Access-Control-Max-Age'             => 600,
])
->middleware(\app\http\middleware\CheckHome::class)
->middleware(\app\http\middleware\ParamFilter::class)
->middleware(\server\mf_dcim\middleware\CheckAuthMiddleware::class);

# 后台,可登录不可登录的接口
Route::group(DIR_ADMIN . '/v1',function (){
    
    // Route::get('mf_dcim/:id/vnc', "\\server\\mf_dcim\\controller\\admin\\CloudController@vncPage");

})->allowCrossDomain([
    'Access-Control-Allow-Origin'        => $origin,
    'Access-Control-Allow-Credentials'   => 'true',
    'Access-Control-Max-Age'             => 600,
])->middleware(\app\http\middleware\Check::class);

// 后台接口
Route::group(DIR_ADMIN . '/v1',function (){
    
    // 周期
	Route::post('mf_dcim/duration', "\\server\\mf_dcim\\controller\\admin\\DurationController@create");
	Route::get('mf_dcim/duration', "\\server\\mf_dcim\\controller\\admin\\DurationController@list");
	Route::put('mf_dcim/duration/:id', "\\server\\mf_dcim\\controller\\admin\\DurationController@update");
	Route::delete('mf_dcim/duration/:id', "\\server\\mf_dcim\\controller\\admin\\DurationController@delete");
	
	// 型号配置
	Route::post('mf_dcim/model_config', "\\server\\mf_dcim\\controller\\admin\\ModelConfigController@create");
	Route::get('mf_dcim/model_config', "\\server\\mf_dcim\\controller\\admin\\ModelConfigController@list");
	Route::put('mf_dcim/model_config/:id', "\\server\\mf_dcim\\controller\\admin\\ModelConfigController@update");
	Route::delete('mf_dcim/model_config/:id', "\\server\\mf_dcim\\controller\\admin\\ModelConfigController@delete");
	Route::get('mf_dcim/model_config/:id', "\\server\\mf_dcim\\controller\\admin\\ModelConfigController@index");

	// 数据中心
	Route::post('mf_dcim/data_center', "\\server\\mf_dcim\\controller\\admin\\DataCenterController@create");
	Route::get('mf_dcim/data_center', "\\server\\mf_dcim\\controller\\admin\\DataCenterController@list");
	Route::put('mf_dcim/data_center/:id', "\\server\\mf_dcim\\controller\\admin\\DataCenterController@update");
	Route::delete('mf_dcim/data_center/:id', "\\server\\mf_dcim\\controller\\admin\\DataCenterController@delete");
	Route::get('mf_dcim/data_center/select', "\\server\\mf_dcim\\controller\\admin\\DataCenterController@dataCenterSelect");

	// 配置限制
	Route::post('mf_dcim/config_limit', "\\server\\mf_dcim\\controller\\admin\\ConfigLimitController@create");
	Route::get('mf_dcim/config_limit', "\\server\\mf_dcim\\controller\\admin\\ConfigLimitController@list");
	Route::put('mf_dcim/config_limit/:id', "\\server\\mf_dcim\\controller\\admin\\ConfigLimitController@update");
	Route::delete('mf_dcim/config_limit/:id', "\\server\\mf_dcim\\controller\\admin\\ConfigLimitController@delete");

	// 操作系统分类
	Route::post('mf_dcim/image_group', "\\server\\mf_dcim\\controller\\admin\\ImageController@imageGroupCreate");
	Route::get('mf_dcim/image_group', "\\server\\mf_dcim\\controller\\admin\\ImageController@imageGroupList");
	Route::put('mf_dcim/image_group/:id', "\\server\\mf_dcim\\controller\\admin\\ImageController@imageGroupUpdate");
	Route::delete('mf_dcim/image_group/:id', "\\server\\mf_dcim\\controller\\admin\\ImageController@imageGroupDelete");
	Route::put('mf_dcim/image_group/order', "\\server\\mf_dcim\\controller\\admin\\ImageController@imageGroupOrder");

	// 操作系统
	Route::post('mf_dcim/image', "\\server\\mf_dcim\\controller\\admin\\ImageController@imageCreate");
	Route::get('mf_dcim/image', "\\server\\mf_dcim\\controller\\admin\\ImageController@imageList");
	Route::put('mf_dcim/image/:id', "\\server\\mf_dcim\\controller\\admin\\ImageController@imageUpdate");
	Route::delete('mf_dcim/image/:id', "\\server\\mf_dcim\\controller\\admin\\ImageController@imageDelete");
	Route::get('mf_dcim/image/sync', "\\server\\mf_dcim\\controller\\admin\\ImageController@imageSync");
	Route::put('mf_dcim/image/:id/enable', "\\server\\mf_dcim\\controller\\admin\\ImageController@toggleImageEnable");

	// 其他设置
	Route::put('mf_dcim/config', "\\server\\mf_dcim\\controller\\admin\\ConfigController@save");
	Route::get('mf_dcim/config', "\\server\\mf_dcim\\controller\\admin\\ConfigController@index");

	// 线路
	Route::post('mf_dcim/line', "\\server\\mf_dcim\\controller\\admin\\LineController@create");
	Route::put('mf_dcim/line/:id', "\\server\\mf_dcim\\controller\\admin\\LineController@update");
	Route::delete('mf_dcim/line/:id', "\\server\\mf_dcim\\controller\\admin\\LineController@delete");
	Route::get('mf_dcim/line/:id', "\\server\\mf_dcim\\controller\\admin\\LineController@index");

	Route::post('mf_dcim/line/:id/line_bw', "\\server\\mf_dcim\\controller\\admin\\LineController@lineBwCreate");
	Route::get('mf_dcim/line_bw/:id', "\\server\\mf_dcim\\controller\\admin\\LineController@lineBwIndex");
	Route::put('mf_dcim/line_bw/:id', "\\server\\mf_dcim\\controller\\admin\\LineController@lineBwUpdate");
	Route::delete('mf_dcim/line_bw/:id', "\\server\\mf_dcim\\controller\\admin\\LineController@lineBwDelete");

	Route::post('mf_dcim/line/:id/line_flow', "\\server\\mf_dcim\\controller\\admin\\LineController@lineFlowCreate");
	Route::get('mf_dcim/line_flow/:id', "\\server\\mf_dcim\\controller\\admin\\LineController@lineFlowIndex");
	Route::put('mf_dcim/line_flow/:id', "\\server\\mf_dcim\\controller\\admin\\LineController@lineFlowUpdate");
	Route::delete('mf_dcim/line_flow/:id', "\\server\\mf_dcim\\controller\\admin\\LineController@lineFlowDelete");

	Route::post('mf_dcim/line/:id/line_defence', "\\server\\mf_dcim\\controller\\admin\\LineController@lineDefenceCreate");
	Route::get('mf_dcim/line_defence/:id', "\\server\\mf_dcim\\controller\\admin\\LineController@lineDefenceIndex");
	Route::put('mf_dcim/line_defence/:id', "\\server\\mf_dcim\\controller\\admin\\LineController@lineDefenceUpdate");
	Route::delete('mf_dcim/line_defence/:id', "\\server\\mf_dcim\\controller\\admin\\LineController@lineDefenceDelete");

	Route::post('mf_dcim/line/:id/line_ip', "\\server\\mf_dcim\\controller\\admin\\LineController@lineIpCreate");
	Route::get('mf_dcim/line_ip/:id', "\\server\\mf_dcim\\controller\\admin\\LineController@lineIpIndex");
	Route::put('mf_dcim/line_ip/:id', "\\server\\mf_dcim\\controller\\admin\\LineController@lineIpUpdate");
	Route::delete('mf_dcim/line_ip/:id', "\\server\\mf_dcim\\controller\\admin\\LineController@lineIpDelete");

	// 周期比例
	Route::get('mf_dcim/duration_ratio', "\\server\\mf_dcim\\controller\\admin\\DurationController@indexDurationRatio");
	Route::put('mf_dcim/duration_ratio', "\\server\\mf_dcim\\controller\\admin\\DurationController@saveDurationRatio");
	Route::post('mf_dcim/duration_ratio/fill', "\\server\\mf_dcim\\controller\\admin\\DurationController@fillDurationRatio");

	// 硬件配置
	Route::get('mf_dcim/cpu', "\\server\\mf_dcim\\controller\\admin\\OptionController@cpuList");
	Route::get('mf_dcim/cpu/:id', "\\server\\mf_dcim\\controller\\admin\\OptionController@cpuIndex");
	Route::post('mf_dcim/cpu', "\\server\\mf_dcim\\controller\\admin\\OptionController@cpuCreate");
	Route::put('mf_dcim/cpu/:id', "\\server\\mf_dcim\\controller\\admin\\OptionController@cpuUpdate");
	Route::delete('mf_dcim/cpu/:id', "\\server\\mf_dcim\\controller\\admin\\OptionController@cpuDelete");

	Route::get('mf_dcim/memory', "\\server\\mf_dcim\\controller\\admin\\OptionController@memoryList");
	Route::get('mf_dcim/memory/:id', "\\server\\mf_dcim\\controller\\admin\\OptionController@memoryIndex");
	Route::post('mf_dcim/memory', "\\server\\mf_dcim\\controller\\admin\\OptionController@memoryCreate");
	Route::put('mf_dcim/memory/:id', "\\server\\mf_dcim\\controller\\admin\\OptionController@memoryUpdate");
	Route::delete('mf_dcim/memory/:id', "\\server\\mf_dcim\\controller\\admin\\OptionController@memoryDelete");

	Route::get('mf_dcim/disk', "\\server\\mf_dcim\\controller\\admin\\OptionController@diskList");
	Route::get('mf_dcim/disk/:id', "\\server\\mf_dcim\\controller\\admin\\OptionController@diskIndex");
	Route::post('mf_dcim/disk', "\\server\\mf_dcim\\controller\\admin\\OptionController@diskCreate");
	Route::put('mf_dcim/disk/:id', "\\server\\mf_dcim\\controller\\admin\\OptionController@diskUpdate");
	Route::delete('mf_dcim/disk/:id', "\\server\\mf_dcim\\controller\\admin\\OptionController@diskDelete");

	// 灵活机型
	Route::get('mf_dcim/package', "\\server\\mf_dcim\\controller\\admin\\PackageController@list");
	Route::get('mf_dcim/package/:id', "\\server\\mf_dcim\\controller\\admin\\PackageController@index");
	Route::post('mf_dcim/package', "\\server\\mf_dcim\\controller\\admin\\PackageController@create");
	Route::put('mf_dcim/package/:id', "\\server\\mf_dcim\\controller\\admin\\PackageController@update");
	Route::delete('mf_dcim/package/:id', "\\server\\mf_dcim\\controller\\admin\\PackageController@delete");
	Route::put('mf_dcim/package/:id/hidden', "\\server\\mf_dcim\\controller\\admin\\PackageController@updateHidden");
	

})->allowCrossDomain([
    'Access-Control-Allow-Origin'        => $origin,
    'Access-Control-Allow-Credentials'   => 'true',
    'Access-Control-Max-Age'             => 600,
])->middleware(\app\http\middleware\CheckAdmin::class);
