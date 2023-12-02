{include file="header"}
<!-- 统计图表 -->
<!-- 页面独有样式 -->
<link rel="stylesheet" href="/{$template_catalog}/template/{$themes}/css/cloudTop.css">
<link rel="stylesheet" href="/{$template_catalog}/template/{$themes}/css/cloudDetail.css">
</head>

<body>
    <!-- mounted之前显示 -->
    <div id="mainLoading">
        <div class="ddr ddr1"></div>
        <div class="ddr ddr2"></div>
        <div class="ddr ddr3"></div>
        <div class="ddr ddr4"></div>
        <div class="ddr ddr5"></div>
    </div>
    <div class="template">
        <el-container>
            <aside-menu></aside-menu>
            <el-container>
                <top-menu></top-menu>
                <el-main>
                    <!-- 自己的东西 -->
                    <div class="main-card">
                        <div class="card-top">
                            <div class="top-operation">
                                <div class="operation-row1">

                                    <img @click="goBack" class="back-img" src="/{$template_catalog}/template/{$themes}/img/finance/back.png" />
                                    <span class="top-product-name">{{hostData.product_name}}</span>
                                    <div class="host-status" :class="hostData.status">{{hostData.status_name}}</div>
                                    <span class="top-area">
                                        <img class="country-img" :src="'/upload/common/country/' + cloudData.data_center.iso +'.png'">
                                        <span class="country">{{cloudData.data_center.country_name}}</span>
                                        <span class="city">{{cloudData.data_center.city}}</span>
                                    </span>
                                </div>
                                <div class="operation-row2">
                                    <div class="row2-left">
                                        <span class="name">{{hostData.name}}</span>
                                        <span class="ip">{{cloudData.ip}}</span>
                                    </div>
                                    <div class="row2-right">
                                        <!-- 停用-->
                                        <span class="refund">
                                            <span class="refund-status" v-if="refundData && refundData.status != 'Cancelled' && refundData.status != 'Reject'">{{refundStatus[refundData.status]}}</span>
                                            <span class="refund-stop-btn" v-if="refundData && refundData.status=='Pending'" @click="quitRefund">取消停用</span>
                                            <span class="refund-btn" @click="showRefund" v-if="!refundData || (refundData && (refundData.status=='Reject')) || (refundData && (refundData.status=='Cancelled'))">申请停用</span>
                                        </span>
                                        <!-- 控制台 -->
                                        <img class="console-img" src="/{$template_catalog}/template/{$themes}/img/cloudDetail/console.png" title="前往控制台" @click="doGetVncUrl" v-show="status != 'operating'">
                                        <!-- 开关机 -->
                                        <span class="on-off">
                                            <el-popover placement="bottom" v-model="onOffvisible" trigger="click">
                                                <div class="sure-remind">
                                                    <span class="text">是否确认 </span>
                                                    <span class="status">{{status == 'on'?'关机':'开机'}} </span>
                                                    <span>?</span>
                                                    <!-- 关机确认 -->
                                                    <span class="sure-btn" v-if="status == 'on'" @click="doPowerOff">确认</span>
                                                    <!-- 开机确认 -->
                                                    <span class="sure-btn" v-else @click="doPowerOn">确认</span>
                                                </div>
                                                <img :src="'/{$template_catalog}/template/{$themes}/img/cloudDetail/'+status+'.png'" :title="statusText" v-show="status != 'operating'" slot="reference">
                                            </el-popover>
                                            <i class="el-icon-loading" title="操作中" v-show="status == 'operating'"></i>
                                        </span>

                                        <!-- 重启 -->
                                        <span class="restart">
                                            <el-popover placement="bottom" v-model="rebotVisibel" trigger="click">
                                                <div class="sure-remind">
                                                    <span class="text">是否确认</span>
                                                    <span class="status">重启</span>
                                                    <span>?</span>
                                                    <span class="sure-btn" @click="doReboot">确认</span>
                                                </div>
                                                <img src="/{$template_catalog}/template/{$themes}/img/cloudDetail/restart.png" title="重启" v-show="status != 'operating'" slot="reference">
                                            </el-popover>

                                            <i class="el-icon-loading" title="操作中" v-show="status == 'operating'"></i>
                                        </span>

                                        <!-- 救援模式 -->
                                        <img class="fault" src="/{$template_catalog}/template/{$themes}/img/cloudDetail/fault.png" v-show="isRescue && status != 'operating'" title="救援模式">
                                    </div>
                                </div>
                                <div class="operation-row3">
                                    <!-- 有备注 -->
                                    <span class="yes-notes" v-if="hostData.notes" @click="doEditNotes">
                                        <i class="el-icon-edit notes-icon"></i>
                                        <span class="notes-text">{{hostData.notes}}</span>
                                    </span>
                                    <!-- 无备注 -->
                                    <span class="no-notes" v-else @click="doEditNotes">
                                        {{lang.cloud_add_notes + ' +'}}
                                    </span>

                                </div>
                            </div>
                            <div class="refund-msg">
                                <!-- 停用成功 -->
                                <div class="refund-success" v-if="refundData && refundData.status == 'Suspending'">
                                    (产品于{{refundData.create_time | formateTime}}申请 {{refundData.type=='Expire'?'到期退款':'立即退款'}}，于 {{refundData.type=='Expire'?'到期后':'通过当天24点后'}} 自动删除)
                                </div>
                                <!-- 停用失败 -->
                                <div class="refund-fail" v-if="refundData && refundData.status == 'Reject'">
                                    (产品于{{refundData.create_time | formateTime}}申请 {{refundData.type=='Expire'?'到期退款':'立即退款'}} 失败，

                                    <el-popover placement="top-start" trigger="hover">
                                        <span>{{refundData.reject_reason}}</span>
                                        <span class="reason-text" slot="reference">查看原因</span>
                                    </el-popover>

                                    )
                                </div>
                            </div>
                            <div class="top-msg">
                                <!-- 实例信息 -->
                                <div class="msg-l">
                                    <div class="system-t">
                                        <img class="os-img" :src="'/plugins/server/common_cloud/view/img/' + cloudData.image.image_group_name + '.png'" alt="">
                                        <span class="os">
                                            <span class="os-text">{{lang.cloud_os}}</span>
                                            <span class="os-name">{{cloudData.image.name}}</span>
                                        </span>
                                        <div class="btn-port">
                                            <div class="re-btn" @click="showReinstall">{{lang.cloud_os_btn}}</div>
                                            <div class="port">默认端口:{{rescueStatusData.port}}</div>
                                        </div>


                                        <div class="user-name-pass">
                                            <div class="user-name">用户名：
                                                <span> {{rescueStatusData.username}} </span>
                                            </div>
                                            <div class="user-pass">密码：
                                                <span v-show="isShowPass"> {{rescueStatusData.password}} </span>
                                                <span v-show="!isShowPass"> {{passHidenCode}} </span>
                                                <img class="eyes" :src="isShowPass?'/{$template_catalog}/template/{$themes}/img/cloud/pass-show.png':'/{$template_catalog}/template/{$themes}/img/cloud/pass-hide.png'" @click="isShowPass=!isShowPass" />
                                            </div>
                                        </div>

                                    </div>
                                    <div class="system-b">
                                        <div class="b-item">
                                            <div class="item-label">CPU</div>
                                            <div class="item-val">{{cloudData.package.cpu + '核'}}</div>
                                        </div>
                                        <div class="b-item">
                                            <div class="item-label">{{lang.cloud_memery}}</div>
                                            <div class="item-val">{{(cloudData.package.memory/1024).toFixed(2) + 'G'}}</div>
                                        </div>
                                        <div class="b-item">
                                            <div class="item-label">{{lang.cloud_bw}}</div>
                                            <div class="item-val">{{cloudData.package.out_bw + 'Mbps'}}</div>
                                        </div>
                                        <div class="b-item">
                                            <div class="item-label">{{lang.cloud_disk}}</div>
                                            <div class="item-val">{{cloudData.package.system_disk_size + 'G'}}</div>
                                        </div>
                                    </div>
                                </div>
                                <!-- 付款信息 -->
                                <div class="msg-r">
                                    <div class="r-t">
                                        <div class="r-t-l">
                                            <span class="r-t-l-text">{{lang.cloud_pay_title}}</span>
                                        </div>
                                        <!-- 续费 -->
                                        <div class="r-t-r">
                                            <span>是否自动续费：</span>
                                            <el-switch v-model="isShowPayMsg" active-color="#0052D9">
                                            </el-switch>
                                            <el-popover placement="top" trigger="hover">
                                                <div class="sure-remind">
                                                    开启自动续费后，即将到期时不再发送续费通知，而是检测余额是否充足，余额充足时将自动续费
                                                </div>
                                                <div class="help" slot="reference">?</div>
                                            </el-popover>
                                        </div>
                                    </div>
                                    <div class="r-b">

                                        <div class="row">
                                            <div class="row-l">
                                                <div class="label">{{lang.cloud_due_time}}:</div>
                                                <div class="value" :class="isRead?'red':''">{{hostData.due_time | formateTime}}</div>
                                            </div>
                                            <div class="row-r">
                                                <div class="label">{{lang.cloud_creat_time}}:</div>
                                                <div class="value">{{hostData.active_time | formateTime}}</div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="row-l">
                                                <div class="label">{{lang.cloud_pay_style}}:</div>
                                                <div class="value">{{hostData.billing_cycle_name + '付'}}</div>
                                            </div>
                                            <div class="row-r">
                                                <div class="label">{{lang.cloud_first_pay}}:</div>
                                                <div class="value">{{commonData.currency_prefix + hostData.first_payment_amount + commonData.currency_suffix}}</div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="row-l">
                                                <div class="label">{{lang.cloud_re_text}}:</div>
                                                <div class="value">{{commonData.currency_prefix + hostData.renew_amount + commonData.currency_suffix}}</div>
                                                <span class="renew-btn" @click="showRenew" v-if="!refundData || (refundData && refundData.status=='Cancelled') || (refundData && refundData.status=='Reject')">{{lang.cloud_re_btn}}</span>
                                                <span class="renew-btn-disable" v-else>{{lang.cloud_re_btn}}</span>
                                            </div>
                                            <div class="row-r">
                                                <div class="label">{{lang.cloud_code}}:</div>
                                                <div class="value" :title="codeString">{{codeString?codeString:'--'}}</div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <el-tabs class="tabs" v-model="activeName" @tab-click="handleClick">
                                <el-tab-pane label="统计图表" name="1">
                                    <el-select class="time-select" v-model="chartSelectValue" @change="chartSelectChange">
                                        <el-option value='1' label="过去24H"></el-option>
                                        <el-option value='2' label="过去3天"></el-option>
                                        <el-option value='3' label="过去7天"></el-option>
                                    </el-select>

                                    <div class="echart-main">
                                        <!-- cpu用量图 -->
                                        <div id="cpu-echart" class="my-echart" v-loading="echartLoading1"></div>
                                        <!-- 网络带宽 -->
                                        <div id="bw-echart" class="my-echart" v-loading="echartLoading2"></div>
                                        <!-- 磁盘IO -->
                                        <div id="disk-io-echart" class="my-echart" v-loading="echartLoading3"></div>
                                        <!-- 内存用量 -->
                                        <div id="memory-echart" class="my-echart" v-loading="echartLoading4"></div>
                                    </div>
                                </el-tab-pane>
                                <el-tab-pane label="管理" name="2">
                                    <!-- 管理 -->
                                    <div class="manage-content">
                                        <!-- 第一行 -->
                                        <el-row>
                                            <el-col :span="8">
                                                <div class="manage-item">
                                                    <div class="item-top">
                                                        <el-select v-model="powerStatus">
                                                            <el-option v-for="item in powerList" :key="item.id" :value="item.value" :label="item.label"></el-option>
                                                        </el-select>
                                                        <div class="item-top-btn" @click="toChangePower" v-loading="loading1">
                                                            确定
                                                        </div>
                                                    </div>
                                                    <div class="item-bottom">
                                                        <div class="bottom-row">对您的实例进行电源操作</div>
                                                    </div>
                                                </div>
                                            </el-col>
                                            <el-col :span="8">
                                                <div class="manage-item">
                                                    <div class="item-top-btn" @click="getVncUrl" v-loading="loading2">
                                                        控制台
                                                    </div>
                                                    <div class="item-bottom">
                                                        <div class="bottom-row">通过实体显示器与鼠标键盘控制您的实例</div>
                                                        <div class="bottom-row">即使实例没有网络也能控制</div>
                                                    </div>
                                                </div>
                                            </el-col>
                                            <el-col :span="8">
                                                <div class="manage-item">
                                                    <div class="item-top-btn" @click="showRePass">
                                                        重置密码
                                                    </div>
                                                    <div class="item-bottom">
                                                        <div class="bottom-row">如您忘记密码或密码无法进入</div>
                                                        <div class="bottom-row">可强制修改您的实例的root/administrator密码</div>
                                                    </div>
                                                </div>
                                            </el-col>

                                        </el-row>
                                        <!-- 第二行 -->
                                        <el-row>
                                            <el-col :span="8">
                                                <div class="manage-item">
                                                    <div class="item-top-btn" @click="showRescueDialog" v-if="!isRescue">
                                                        救援模式
                                                    </div>
                                                    <div class="item-top-btn" @click="showQuitRescueDialog" v-else>
                                                        退出救援模式
                                                    </div>
                                                    <div class="item-bottom">
                                                        <div class="bottom-row">如实例系统损坏无法启动</div>
                                                        <div class="bottom-row">可进入临时救援系统进行修复或数据拷贝</div>
                                                    </div>
                                                </div>
                                            </el-col>
                                            <el-col :span="8">
                                                <div class="manage-item">
                                                    <div class="item-top-btn" @click="showReinstall">
                                                        重装系统
                                                    </div>
                                                    <div class="item-bottom">
                                                        <div class="bottom-row">快速更换实例为其他操作系统</div>
                                                    </div>
                                                </div>
                                            </el-col>
                                            <el-col :span="8">
                                                <div class="manage-item">
                                                    <div class="item-top-btn" @click="showUpgrade">
                                                        升降级
                                                    </div>
                                                    <div class="item-bottom">
                                                        <div class="bottom-row">升降级配置</div>
                                                    </div>
                                                </div>
                                            </el-col>
                                        </el-row>
                                        <!-- 第三行 -->
                                        <el-row>
                                            <el-col :span="8">
                                                <div class="manage-item">
                                                    <div class="item-top-btn" style="background: #eee;cursor: not-allowed;color: #999;">
                                                        设置启动项
                                                    </div>
                                                    <div class="item-bottom">
                                                        <div class="bottom-row">可设置您的实例从ISO或本地硬盘中启动</div>
                                                    </div>
                                                </div>
                                            </el-col>
                                            <el-col :span="8">
                                                <div class="manage-item">
                                                    <div class="item-top-btn" style="background: #eee;cursor: not-allowed;color: #999;">
                                                        挂载ISO
                                                    </div>
                                                    <div class="item-bottom">
                                                        <div class="bottom-row">挂载ISO文件，用于安装系统或者提供Virto驱动</div>
                                                    </div>
                                                </div>
                                            </el-col>
                                            <el-col :span="8">
                                                <div class="manage-item">
                                                    <div class="item-top-btn" style="background: #eee;cursor: not-allowed;color: #999;">
                                                        删除实例
                                                    </div>
                                                    <div class="item-bottom">
                                                        <div class="bottom-row">不再使用该实例，彻底销毁并删除所有数据该操作不可逆</div>
                                                    </div>
                                                </div>
                                            </el-col>
                                        </el-row>
                                    </div>
                                </el-tab-pane>
                                <el-tab-pane label="磁盘" name="3">
                                    <div class="disk-top-operation">
                                        <el-button v-if="diskList.length != 0" type="primary" class="btn1" @click="showExpansion">扩容</el-button>
                                        <el-button type="primary" class="btn2">指南</el-button>
                                    </div>
                                    <div class="no-disk" v-if="diskList.length == 0">
                                        <span class="text">没有磁盘</span>
                                        <span class="text2">您尚未订购任何数据盘，立刻购买磁盘</span>
                                    </div>
                                    <div class="yes-disk" v-else>
                                        <div class="main-table">
                                            <el-table v-loading="diskLoading" :data="diskList" style="width: 100%">
                                                <el-table-column prop="name" label="磁盘名称" width="300" align="left">
                                                </el-table-column>
                                                <el-table-column prop="create_time" width="300" label="操作时间" align="left">
                                                </el-table-column>
                                                <el-table-column prop="size" label="容量" min-width="400" align="left" :show-overflow-tooltip="true">
                                                    <template slot-scope="scope">
                                                        <span>{{scope.row.size + 'G'}}</span>
                                                    </template>
                                                </el-table-column>
                                            </el-table>
                                        </div>
                                    </div>
                                    <span class="buy-btn" @click="showDg">订购磁盘</span>
                                </el-tab-pane>
                                <el-tab-pane label="网络" name="4">
                                    <div class="net">
                                        <div class="title">公网IP</div>
                                        <div class="main_table">
                                            <el-table v-loading="netLoading" :data="netDataList" style="width: 100%;margin-bottom: .2rem;">
                                                <el-table-column prop="ip" label="IP地址" min-width="200" align="left">
                                                </el-table-column>
                                                <el-table-column prop="gateway" width="400" label="网关" align="left">
                                                </el-table-column>
                                                <el-table-column prop="subnet_mask" width="400" label="掩码" align="left">
                                                </el-table-column>
                                            </el-table>
                                            <pagination :page-data="netParams" @sizechange="netSizeChange" @currentchange="netCurrentChange">
                                            </pagination>
                                        </div>
                                        <div class="title">网络流量</div>
                                        <div class="flow-content">
                                            <div class="flow-item">
                                                <div class="flow-label">当月流量</div>
                                                <div class="flow-value">{{flowData.total}}</div>
                                            </div>
                                            <div class="flow-item">
                                                <div class="flow-label">剩余流量</div>
                                                <div class="flow-value">{{flowData.leave}}</div>
                                            </div>
                                            <div class="flow-item">
                                                <div class="flow-label">流量归零时间</div>
                                                <div class="flow-value">{{flowData.reset_flow_date}}</div>
                                            </div>
                                        </div>

                                        <el-select class="time-select" v-model="chartSelectValue" @change="chartSelectChange">
                                            <el-option value='1' label="过去24H"></el-option>
                                            <el-option value='2' label="过去3天"></el-option>
                                            <el-option value='3' label="过去7天"></el-option>
                                        </el-select>
                                        <div class="echart-main">
                                            <!-- 网络带宽 -->
                                            <div id="bw2-echart" class="my-echart" v-loading="echartLoading2"></div>
                                        </div>

                                    </div>
                                </el-tab-pane>
                                <el-tab-pane label="备份与快照" name="5">
                                    <!-- 备份 -->
                                    <!-- 启用备份 -->
                                    <div class="main-content" v-if="cloudData.backup_num > 0">
                                        <div class="content-top">
                                            <div class="top-title">备份</div>
                                            <div class="top-btn" @click="showCreateBs('back')">立即备份</div>
                                        </div>
                                        <div class="main-table">
                                            <el-table v-loading="backLoading" :data="dataList1" style="width: 100%;margin-bottom: .2rem;">
                                                <el-table-column prop="name" label="备份名称" width="300" align="left">
                                                </el-table-column>
                                                <el-table-column prop="create_time" width="300" label="生成时间" align="left">
                                                    <template slot-scope="scope">
                                                        {{scope.row.create_time | formateTime}}
                                                    </template>
                                                </el-table-column>
                                                <el-table-column prop="notes" label="备注" min-width="400" align="left" :show-overflow-tooltip="true">
                                                </el-table-column>
                                                <el-table-column prop="type" label="操作" width="150" align="left">
                                                    <template slot-scope="scope">
                                                        <el-popover placement="top-start" trigger="hover">
                                                            <div class="operation">
                                                                <div class="operation-item" @click="showhyBs('back',scope.row)">还原</div>
                                                                <div class="operation-item" @click="showDelBs('back',scope.row)">删除</div>
                                                            </div>
                                                            <span class="more-operation" slot="reference">
                                                                <div class="dot"></div>
                                                                <div class="dot"></div>
                                                                <div class="dot"></div>
                                                            </span>
                                                        </el-popover>
                                                    </template>
                                                </el-table-column>
                                            </el-table>
                                        </div>
                                    </div>
                                    <!-- 没有启用备份 -->
                                    <div class="no-bs no-back" v-else>
                                        <span class="no-bs-title">备份</span>
                                        <div class="no-bs-content">
                                            <span class="text">您尚未开启备份功能</span>
                                            <div class="btn" @click="openBs('back')">立刻开启</div>
                                        </div>
                                    </div>
                                    <!-- 快照 -->
                                    <div class="main-content" v-if="cloudData.snap_num > 0">
                                        <div class="content-top">
                                            <div class="top-title">快照</div>
                                            <div class="top-btn" @click="showCreateBs('snap')">生成快照</div>
                                        </div>
                                        <div class="main-table">
                                            <el-table v-loading="snapLoading" :data="dataList2" style="width: 100%;margin-bottom: .2rem;">
                                                <el-table-column prop="name" label="快照名称" width="300" align="left">
                                                </el-table-column>
                                                <el-table-column prop="create_time" width="300" label="生成时间" align="left">
                                                    <template slot-scope="scope">
                                                        {{scope.row.create_time | formateTime}}
                                                    </template>
                                                </el-table-column>
                                                <el-table-column prop="notes" label="备注" min-width="400" align="left" :show-overflow-tooltip="true">
                                                </el-table-column>
                                                <el-table-column prop="type" label="操作" width="150" align="left">
                                                    <template slot-scope="scope">
                                                        <el-popover placement="top-start" trigger="hover">
                                                            <div class="operation">
                                                                <div class="operation-item" @click="showhyBs('snap',scope.row)">还原</div>
                                                                <div class="operation-item" @click="showDelBs('snap',scope.row)">删除</div>
                                                            </div>
                                                            <span class="more-operation" slot="reference">
                                                                <div class="dot"></div>
                                                                <div class="dot"></div>
                                                                <div class="dot"></div>
                                                            </span>
                                                        </el-popover>
                                                    </template>
                                                </el-table-column>
                                            </el-table>
                                        </div>
                                    </div>
                                    <div class="no-bs no-snap" v-else>
                                        <span class="no-bs-title">快照</span>
                                        <div class="no-bs-content">
                                            <span class="text">您尚未开启快照功能</span>
                                            <div class="btn" @click="openBs('snap')">立刻开启</div>
                                        </div>
                                    </div>
                                </el-tab-pane>
                                <el-tab-pane label="日志" name="6">
                                    <div class="log">
                                        <div class="main_table">
                                            <el-table v-loading="logLoading" :data="logDataList" style="width: 100%;margin-bottom: .2rem;">
                                                <el-table-column prop="id" label="序号" width="400" align="left">
                                                </el-table-column>
                                                <el-table-column prop="create_time" width="400" label="操作时间" align="left">
                                                    <template slot-scope="scope">
                                                        <span>{{scope.row.create_time | formateTime}}</span>
                                                    </template>
                                                </el-table-column>
                                                <el-table-column prop="description" label="操作详情" min-width="400" align="left" :show-overflow-tooltip="true">
                                                </el-table-column>
                                            </el-table>
                                            <pagination :page-data="logParams" @sizechange="logSizeChange" @currentchange="logCurrentChange">
                                            </pagination>
                                        </div>
                                    </div>
                                </el-tab-pane>
                            </el-tabs>


                            <!-- 修改备注弹窗 -->
                            <div class="notes-dialog">
                                <el-dialog width="6.2rem" :visible.sync="isShowNotesDialog" :show-close=false @close="notesDgClose">
                                    <div class="dialog-title">
                                        {{hostData.notes?'修改备注':'添加备注'}}
                                    </div>
                                    <div class="dialog-main">
                                        <div class="label">备注</div>
                                        <el-input class="notes-input" v-model="notesValue"></el-input>
                                    </div>
                                    <div class="dialog-footer">
                                        <div class="btn-ok" @click="subNotes">提交</div>
                                        <div class="btn-no" @click="notesDgClose">取消</div>
                                    </div>
                                </el-dialog>
                            </div>

                            <!-- 重装系统弹窗 -->
                            <div class="reinstall-dialog">
                                <el-dialog width="6.2rem" :visible.sync="isShowReinstallDialog" :show-close=false @close="reinstallDgClose">
                                    <div class="dialog-title">
                                        重装系统
                                    </div>
                                    <div class="dialog-main">
                                        <div class="label">操作系统</div>
                                        <div class="os-content">
                                            <!-- 镜像组选择框 -->
                                            <el-select class="os-select os-group-select" v-model="reinstallData.osGroupId" @change="osSelectGroupChange">
                                                <img class="os-group-img" :src="osIcon" slot="prefix" alt="">
                                                <el-option v-for="item in osData" :key='item.id' :value="item.id" :label="item.name">
                                                    <div class="option-label">
                                                        <img class="option-img" :src="'/plugins/server/common_cloud/view/img/' + item.name + '.png'" alt="">
                                                        <span class="option-text">{{item.name}}</span>
                                                    </div>
                                                </el-option>
                                            </el-select>
                                            <!-- 镜像实际选择框 -->
                                            <el-select class="os-select" v-model="reinstallData.osId" @change="osSelectChange">
                                                <el-option v-for="item in osSelectData" :key="item.id" :value="item.id" :label="item.name +'-' + commonData.currency_prefix + item.price"></el-option>
                                            </el-select>
                                        </div>
                                        <div class="label">密码</div>
                                        <div class="pass-content">
                                            <el-select class="pass-select" v-model="reinstallData.type">
                                                <el-option value="pass" label="密码"></el-option>

                                            </el-select>
                                            <el-popover placement="top-start" width="200" trigger="hover" content="1、长度6位及以上 2、只能输入大写字母、小写字母、数字、~!@#$&* ()_ -+=| {}[];:<>?,./中的特殊符号3、不能以“/”开头4、必须包含小写字母a~z，大写字母A~Z,字母0-9">
                                                <i class="el-icon-question help-icon" slot="reference"></i>
                                            </el-popover>
                                            <el-input class="pass-input" v-model="reinstallData.password" placeholder="请输入内容" v-show="reinstallData.type=='pass'">
                                                <div class="pass-btn" slot="suffix" @click="autoPass">随机生成</div>
                                            </el-input>
                                            <div class="key-select" v-show="reinstallData.type=='key'">
                                                <el-select v-model="reinstallData.key">
                                                    <el-option v-for="item in sshKeyData" :key="item.id" :value="item.id"></el-option>
                                                </el-select>
                                            </div>
                                        </div>
                                        <div class="label">端口</div>
                                        <el-input class="pass-input" v-model="reinstallData.port" placeholder="请输入内容">
                                            <div class="pass-btn" slot="suffix" @click="autoPort">随机生成</div>
                                        </el-input>

                                        <div class="pay-img" v-show="isPayImg">
                                            当前系统为付费系统，{{commonData.currency_prefix + payMoney + '/次'}}
                                            <span class="buy-btn" @click="payImg">立即购买</span>
                                        </div>

                                        <div class="alert">
                                            <el-alert :title="errText" type="error" show-icon :closable="false" v-show="errText">
                                            </el-alert>

                                            <div class="remind" v-show="!errText">请妥善保存当前密码，该密码不会二次出现</div>
                                        </div>
                                    </div>
                                    <div class="dialog-footer">
                                        <div class="btn-ok" @click="doReinstall">提交</div>
                                        <div class="btn-no" @click="reinstallDgClose">取消</div>
                                    </div>
                                </el-dialog>
                            </div>


                            <!-- 续费弹窗 -->
                            <div class="renew-dialog">
                                <el-dialog width="6.9rem" :visible.sync="isShowRenew" :show-close=false @close="renewDgClose">
                                    <div class="dialog-title">
                                        续费
                                    </div>
                                    <div class="dialog-main">
                                        <div class="renew-content">
                                            <div class="renew-item" :class="renewActiveId==item.id?'renew-active':''" v-for="item in renewPageData" :key="item.id" @click="renewItemChange(item)">
                                                <div class="item-top">{{item.billing_cycle}}</div>
                                                <div class="item-bottom">{{commonData.currency_prefix + item.price}}</div>
                                                <i class="el-icon-check check" v-show="renewActiveId==item.id"></i>
                                            </div>
                                        </div>
                                        <div class="pay-content">
                                            <div class="pay-price">
                                                <div class="text">合计</div>
                                                <div class="money">{{commonData.currency_prefix + renewParams.price}}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dialog-footer">
                                        <div class="btn-ok" @click="subRenew">确认续费</div>
                                        <div class="btn-no" @click="renewDgClose">取消</div>
                                    </div>
                                </el-dialog>
                            </div>

                            <!-- 停用弹窗（删除实例） -->
                            <div class="refund-dialog">
                                <el-dialog width="6.8rem" :visible.sync="isShowRefund" :show-close=false @close="refundDgClose">
                                    <div class="dialog-title">
                                        退款
                                    </div>
                                    <div class="dialog-main">
                                        <div class="label">产品信息</div>
                                        <div class="host-content">
                                            <div class="host-item">
                                                <div class="left">产品配置:</div>
                                                <div class="right">
                                                    <div class="right-row">
                                                        <div class="right-row-item">{{cloudData.package.cpu}} 核CPU</div>
                                                        <div class="right-row-item">{{(cloudData.package.memory/1024).toFixed(2) + 'G 内存'}}</div>
                                                        <div class="right-row-item">{{cloudData.package.system_disk_size}} GB 存储容量</div>
                                                        <div class="right-row-item">{{cloudData.package.out_bw}} 宽带</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="host-item">
                                                <div class="left">订购时间:</div>
                                                <div class="right">{{refundPageData.host.create_time | formateTime}}</div>
                                            </div>
                                            <div class="host-item">
                                                <div class="left">订购金额:</div>
                                                <div class="right">{{commonData.currency_prefix + refundPageData.host.first_payment_amount}}</div>
                                            </div>
                                        </div>
                                        <div class="label">停用原因</div>
                                        <el-select v-if="refundPageData.reason_custom == 0" v-model="refundParams.suspend_reason" multiple>
                                            <el-option v-for="item in refundPageData.reasons" :key="item.id" :value="item.id" :label="item.content"></el-option>
                                        </el-select>
                                        <el-input v-else v-model="refundParams.suspend_reason"></el-input>
                                        <div class="label">停用时间</div>
                                        <el-select v-model="refundParams.type">
                                            <el-option value="Expire" label="到期"></el-option>
                                            <el-option value="Immediate" label="立即"></el-option>
                                        </el-select>
                                        <div class="label">退款金额</div>
                                        <div class="amount-content">{{commonData.currency_prefix}}{{refundParams.type=='Immediate'?refundPageData.host.amount:'0.00'}}</div>
                                    </div>
                                    <div class="dialog-footer">
                                        <div class="btn-ok" @click="subRefund">确认退款</div>
                                        <div class="btn-no" @click="refundDgClose">取消</div>
                                    </div>
                                </el-dialog>
                            </div>

                            <!-- 重置密码弹窗 -->
                            <div class="repass-dialog">
                                <el-dialog width="6.8rem" :visible.sync="isShowRePass" :show-close=false @close="rePassDgClose">
                                    <div class="dialog-title">
                                        重置密码
                                        <span class="second-title">如您忘记密码，可直接输入新密码进行破解</span>
                                    </div>
                                    <div class="dialog-main">
                                        <div class="label">
                                            密码
                                            <el-popover placement="top-start" width="200" trigger="hover" content="1、长度6位及以上 2、只能输入大写字母、小写字母、数字、~!@#$&* ()_ -+=| {}[];:<>?,./中的特殊符号3、不能以“/”开头4、必须包含小写字母a~z，大写字母A~Z,字母0-9">
                                                <i class="el-icon-question" slot="reference"></i>
                                            </el-popover>

                                        </div>
                                        <el-input class="pass-input" v-model="rePassData.password" placeholder="请输入内容">
                                            <div class="pass-btn" slot="suffix" @click="autoPass">随机生成</div>
                                        </el-input>

                                        <div class="alert" v-show="powerStatus=='off'">
                                            <div class="title">当前操作需要实例在关机状态下进行</div>
                                            <div class="row"><span class="dot"></span> 为了避免数据丢失，实例将关机中断您的业务，请仔细确认。</div>
                                            <div class="row"><span class="dot"></span> 强制关机可能会导致数据丢失或文件系统损坏，您也可以主动关机后再进行操作
                                            </div>
                                        </div>
                                        <el-checkbox v-model="rePassData.checked" v-show="powerStatus=='off'">同意强制关机</el-checkbox>
                                        <div class="alert-err-text">
                                            <el-alert :title="errText" type="error" show-icon :closable="false" v-show="errText">
                                            </el-alert>
                                        </div>
                                    </div>
                                    <div class="dialog-footer">
                                        <div class="btn-ok" @click="rePassSub" v-loading="loading5">提交</div>
                                        <div class="btn-no" @click="rePassDgClose">取消</div>
                                    </div>
                                </el-dialog>
                            </div>

                            <!-- 救援系统弹窗 -->
                            <div class="rescue-dialog">
                                <el-dialog width="6.8rem" :visible.sync="isShowRescue" :show-close=false @close="rescueDgClose">
                                    <div class="dialog-title">
                                        救援系统
                                        <span class="second-title">当您系统损坏时，可进入救援模式，您的系统盘将会挂载作为数据盘</span>
                                    </div>
                                    <div class="dialog-main">
                                        <div class="label">救援系统类型</div>
                                        <el-select class="os-select" v-model="rescueData.type">
                                            <img class="os-img" :src="'/plugins/server/common_cloud/view/img/' + (rescueData.type==1?'Windows':'Linux') + '.png'" slot="prefix" alt="">
                                            <el-option value="1" label="Windows">
                                                <div class="os-item">
                                                    <img class="item-os-img" src="/plugins/server/common_cloud/view/img/Windows.png" alt="">
                                                    <span class="item-os-text">Windows</span>
                                                </div>
                                            </el-option>
                                            <el-option value="2" label="Linux">
                                                <div class="os-item">
                                                    <img class="item-os-img" src="/plugins/server/common_cloud/view/img/Linux.png" alt="">
                                                    <span class="item-os-text">Linux</span>
                                                </div>
                                            </el-option>
                                        </el-select>
                                        <div class="label">临时密码</div>
                                        <el-input class="pass-input" v-model="rescueData.password" placeholder="请输入内容">
                                            <div class="pass-btn" slot="suffix" @click="autoPass">随机生成</div>
                                        </el-input>
                                        <div class="alert">
                                            <el-alert :title="errText" type="error" show-icon :closable="false" v-show="errText">
                                            </el-alert>
                                            <div class="remind" v-show="!errText">请妥善保存当前密码，该密码不会二次使用</div>
                                        </div>
                                    </div>
                                    <div class="dialog-footer">
                                        <div class="btn-ok" @click="rescueSub" v-loading="loading3">提交</div>
                                        <div class="btn-no" @click="rescueDgClose">取消</div>
                                    </div>
                                </el-dialog>
                            </div>

                            <!-- 确认退出救援模式弹窗 -->
                            <div class="quitRescu">
                                <el-dialog width="6.8rem" :visible.sync="isShowQuit" :show-close=false @close="quitDgClose">
                                    <div class="dialog-title">
                                        退出救援模式
                                    </div>
                                    <div class="dialog-main">
                                        您将退出救援模式请确认操作
                                    </div>
                                    <div class="dialog-footer">
                                        <div class="btn-ok" @click="reQuitSub">确认</div>
                                        <div class="btn-no" @click="quitDgClose">取消</div>
                                    </div>
                                </el-dialog>
                            </div>

                            <!-- 升降级弹窗 -->
                            <div class="upgrade-dialog">
                                <el-dialog width="6.8rem" :visible.sync="isShowUpgrade" :show-close=false @close="upgradeDgClose">
                                    <div class="dialog-title">
                                        升降级
                                    </div>
                                    <div class="dialog-main">
                                        <div class="package package-now">
                                            <div class="label">当前配置</div>
                                            <div class="package-main">
                                                <span class="day-row">当前配置剩余天数：<span class="day">{{hostData.due_time |
                                                    formateDueDay}}</span> 天</span>
                                                <span class="money">{{commonData.currency_prefix +
                                                cloudData.first_payment_amount + '/' +
                                                hostData.billing_cycle_name}}</span>
                                                <div class="package-line"></div>
                                                <div class="description" v-html="cloudData.package.description"></div>
                                            </div>
                                        </div>
                                        <div class="package">
                                            <div class="label">可切换配置</div>
                                            <el-select class="package-select" v-model="upgradePackageId" @change="upgradeSelectChange">
                                                <el-option v-for="item in upgradeList" :key="item.id" :value="item.id" :label="item.name"></el-option>
                                            </el-select>
                                            <div class="package-main package-upgrade">
                                                <span class="money">{{commonData.currency_prefix}}{{changeUpgradeData.money}}/{{changeUpgradeData.duration}}</span>
                                                <div class="package-line"></div>
                                                <div class="description" v-html="changeUpgradeData.description"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert">
                                        <el-alert :title="errText" type="error" show-icon :closable="false" v-show="errText">
                                        </el-alert>
                                    </div>
                                    <div class="dialog-footer">
                                        <span class="money-text">
                                            切换所需费用：
                                        </span>
                                        <span class="money-num">{{commonData.currency_prefix + upPrice}}</span>
                                        <div class="btn-ok" @click="upgradeSub" v-loading="loading4">提交</div>
                                        <div class="btn-no" @click="upgradeDgClose">取消</div>
                                    </div>
                                </el-dialog>
                            </div>

                            <!-- 订购磁盘弹窗 -->
                            <div class="dg-dialog">
                                <el-dialog width="9.12rem" :visible.sync="isShowDg" :show-close=false @close="dgClose">
                                    <div class="dialog-title">
                                        订购磁盘
                                    </div>
                                    <!-- 当前配置磁盘 -->
                                    <div class="disk-card disk-old" v-if="oldDiskList.length > 0">
                                        <div class="disk-label">当前配置磁盘</div>
                                        <div class="disk-main-card">
                                            <div class="old-disk-item" v-for="item in oldDiskList" :key="item.id">
                                                <span class="disk-item-text">{{item.name}}</span>
                                                <span class="disk-item-size">{{item.size + 'G'}}</span>
                                                <i class="el-icon-remove-outline del" @click="delOldSize(item.id)"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- 新增磁盘 -->
                                    <div class=" disk-card disk-new">
                                        <div class="disk-label">新增磁盘</div>
                                        <div class="disk-main-card">
                                            <div class="new-disk-item" v-for="item in moreDiskData" :key="item.id">
                                                <span class="item-name">数据盘{{item.index}}</span>
                                                <span class="item-min-size">{{configData.disk_min_size}}G</span>
                                                <el-slider :step="10" :min="configData.disk_min_size" :max="configData.disk_max_size" v-model="item.size"></el-slider>
                                                <span class="item-max-size">{{configData.disk_max_size}}G</span>
                                                <i class="el-icon-remove-outline del" @click="delMoreDisk(item.id)"></i>
                                                <i class="el-icon-circle-plus-outline add" v-show="item.index === moreDiskData.length" @click="addMoreDisk"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dialog-footer">
                                        <span class="text">订购所需费用:</span>
                                        <span class="num">{{commonData.currency_prefix}}{{moreDiskPrice}}</span>
                                        <div class="btn-ok" @click="toCreateDisk">提交</div>
                                        <div class="btn-no" @click="dgClose">取消</div>
                                    </div>
                                </el-dialog>
                            </div>

                            <!-- 扩容磁盘弹窗 -->
                            <div class="kr-dialog">
                                <el-dialog width="8.41rem" :visible.sync="isShowExpansion" :show-close=false @close="krClose">
                                    <div class="dialog-title">
                                        扩容磁盘
                                    </div>
                                    <div class="dialog-main">
                                        <div class="label">当前配置磁盘容量</div>
                                        <div class="disk-main-card">
                                            <div class="old-disk-item" v-for="item in oldDiskList" :key="item.id">
                                                <span class="disk-item-text" :title="item.name">{{item.name}}</span>
                                                <span class="disk-item-size">{{item.size + 'G'}}</span>
                                                <el-slider :step="10" :min="Number(item.minSize)" :max="configData.disk_max_size" v-model="item.size"></el-slider>
                                                <span class="item-max-size">{{configData.disk_max_size}}G</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dialog-footer">
                                        <span class="text">扩容所需费用:</span>
                                        <span class="num">{{commonData.currency_prefix}}{{expansionDiskPrice}}</span>
                                        <div class="btn-ok" @click="subExpansion">提交</div>
                                        <div class="btn-no" @click="krClose">取消</div>
                                    </div>
                                </el-dialog>
                            </div>

                            <!-- 创建备份/快照弹窗 -->
                            <div class="create-bs-dialog">
                                <el-dialog width="6.8rem" :visible.sync="isShwoCreateBs" :show-close=false @close="bsCgClose">
                                    <div class="dialog-title">
                                        {{isBs?'创建备份':'创建快照'}}
                                    </div>
                                    <div class="title-second-text">
                                        建议您关闭实例后{{isBs?'创建备份':'创建快照'}}，确保一致性避免出现问题
                                    </div>
                                    <div class="dialog-main">
                                        <div class="label">磁盘</div>
                                        <el-select v-model="createBsData.disk_id">
                                            <el-option v-for="item in diskList" :key="item.id" :value="item.id" :label="item.name"></el-option>
                                        </el-select>
                                        <div class="label">备注</div>
                                        <el-input v-model="createBsData.name"></el-input>
                                        <div class="alert">
                                            <el-alert :title="errText" type="error" show-icon :closable="false" v-show="errText">
                                            </el-alert>
                                        </div>
                                    </div>
                                    <div class="dialog-footer">
                                        <div class="btn-ok" @click="subCgBs" v-loading="cgbsLoading">立即创建</div>
                                        <div class="btn-no" @click="bsCgClose">取消</div>
                                    </div>
                                </el-dialog>
                            </div>
                            <!-- 还原备份、快照弹窗 -->
                            <div class="restore-dialog">
                                <el-dialog width="6.8rem" :visible.sync="isShowhyBs" :show-close=false @close="bshyClose">
                                    <div class="dialog-title">
                                        恢复
                                    </div>
                                    <div class="dialog-main">
                                        <span>您将恢复实例{{restoreData.cloud_name}}的磁盘</span>
                                        <span>{{restoreData.time | formateTime}}创建的{{isBs?'备份':'快照'}}</span>
                                        <span>当前的所有数据丢失，请确认您需要恢复{{isBs?'备份':'快照'}}</span>
                                    </div>
                                    <div class="dialog-footer">
                                        <div class="btn-ok" @click="subhyBs" v-loading="loading3">立即恢复</div>
                                        <div class="btn-no" @click="bshyClose">取消</div>
                                    </div>
                                </el-dialog>
                            </div>
                            <!-- 删除备份、快照弹窗 -->
                            <div class="del-dialog">
                                <el-dialog width="6.8rem" :visible.sync="isShowDelBs" :show-close=false @close="delBsClose">
                                    <div class="dialog-title">
                                        <i class="el-icon-warning-outline"></i>
                                        {{isBs?'删除备份':'删除快照'}}
                                    </div>
                                    <div class="dialog-main">
                                        <span class="row-1">{{isBs?'删除备份：':'删除快照：'}}{{delData.name}}</span>
                                        <span class="row-2">您将删除实例{{delData.cloud_name}}的磁盘{{delData.time | formateTime}}创建的{{isBs?'备份':'快照'}}，删除后不可恢复</span>
                                    </div>
                                    <div class="dialog-footer">
                                        <div class="btn-ok" @click="subDelBs" v-loading="loading4">确认删除</div>
                                        <div class="btn-no" @click="delBsClose">取消</div>
                                    </div>
                                </el-dialog>
                            </div>

                            <!-- 开启备份/快照弹窗 -->
                            <div class="bs-dialog">
                                <el-dialog width="6.2rem" :visible.sync="isShowOpenBs" :show-close=false @close="bsopenDgClose">
                                    <div class="dialog-title">
                                        {{isBs?'开启备份':'开启快照'}}
                                    </div>
                                    <div class="dialog-main">
                                        <!-- 备份下拉框 -->
                                        <el-select v-model="bsData.backNum" v-show="isBs">
                                            <el-option v-for="item in configData.backup_option" :key="item.id" :value="item.num" :label="item.num + '个备份' + commonData.currency_prefix + item.price + '/' + bsData.duration"></el-option>
                                        </el-select>
                                        <!-- 快照下拉框 -->
                                        <el-select v-model="bsData.snapNum" v-show="!isBs">
                                            <el-option v-for="item in configData.snap_option" :key="item.id" :value="item.num" :label="item.num + '个快照' + commonData.currency_prefix + item.price  + '/' + bsData.duration"></el-option>
                                        </el-select>
                                        <span class="num">开启创建{{isBs?bsData.backNum:bsData.snapNum}}个{{isBs?'备份':'快照'}}</span>
                                        <span class="price">{{commonData.currency_prefix + bsData.money}}</span>
                                    </div>
                                    <div class="dialog-footer">
                                        <div class="btn-ok" @click="bsopenSub">提交</div>
                                        <div class="btn-no" @click="bsopenDgClose">取消</div>
                                    </div>
                                </el-dialog>
                            </div>

                            <pay-dialog ref="topPayDialog" @payok="paySuccess" @paycancel="payCancel"></pay-dialog>
                        </div>
                    </div>
                </el-main>
            </el-container>
        </el-container>
    </div>
    <!-- =======页面独有======= -->
    <script src="/{$template_catalog}/template/{$themes}/api/common.js"></script>
    <script src="/{$template_catalog}/template/{$themes}/api/cloud.js"></script>
    <script src="https://cdn.bootcdn.net/ajax/libs/echarts/5.3.3/echarts.min.js"></script>
    <script src="/{$template_catalog}/template/{$themes}/js/cloudDetail.js"></script>
    <script src="/{$template_catalog}/template/{$themes}/utils/util.js"></script>
    <script src="/{$template_catalog}/template/{$themes}/components/payDialog/payDialog.js"></script>
    <script src="/{$template_catalog}/template/{$themes}/components/pagination/pagination.js"></script>
    {include file="footer"}