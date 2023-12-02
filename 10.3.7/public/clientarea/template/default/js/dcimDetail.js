(function (window, undefined) {
    var old_onload = window.onload
    window.onload = function () {
        const template = document.getElementsByClassName('template')[0]
        Vue.prototype.lang = window.lang
        new Vue({
            components: {
                asideMenu,
                topMenu,
                payDialog,
                pagination,
            },
            created() {
                // 获取产品id
                this.id = location.href.split('?')[1].split('=')[1]
                // this.id = 5315
                // 获取通用信息
                this.getCommonData()
                // 获取产品详情
                this.getHostDetail()
                // 获取实例详情
                this.getCloudDetail()

                // 获取ssh列表
                // this.getSshKey()
                // 获取实例状态
                this.getCloudStatus()
                // 获取产品停用信息
                this.getRefundMsg()
                // 优惠码信息
                this.getPromoCode()
                // 获取救援模式状态
                this.getRemoteInfo()
                this.getstarttime(1)
            },
            mounted() {
                // 统计图表相关
                this.getBwList()
            },
            updated() {

            },
            destroyed() {

            },
            data() {
                return {
                    commonData: {},
                    // 实例id
                    id: null,
                    // 产品id
                    product_id: 0,
                    // 实例状态
                    status: 'operating',
                    // 实例状态描述
                    statusText: '',
                    // 是否救援系统
                    isRescue: false,
                    // 产品详情
                    hostData: {
                        status: "Active"
                    },
                    // 实例详情
                    cloudData: {
                        data_center: {
                            iso: 'CN'
                        },
                        image: {
                            icon: ''
                        },
                        package: {
                            cpu: ''
                        },
                        iconName: 'Windows'
                    },
                    // 是否显示支付信息
                    isShowPayMsg: false,
                    imgBaseUrl: '',
                    // 是否显示添加备注弹窗
                    isShowNotesDialog: false,
                    // 备份输入框内容
                    notesValue: '',
                    // 显示重装系统弹窗
                    isShowReinstallDialog: false,
                    // 重装系统弹窗内容
                    reinstallData: {
                        image_id: null,
                        password: null,
                        ssh_key_id: null,
                        port: null,
                        osGroupId: null,
                        osId: null,
                        type: 'pass'
                    },
                    // 镜像数据
                    osData: [],
                    // 镜像版本选择框数据
                    osSelectData: [],
                    // 镜像图片地址
                    osIcon: '',
                    // Shhkey列表
                    sshKeyData: [],
                    // 错误提示信息
                    errText: '',
                    // 镜像是否需要付费
                    isPayImg: false,
                    payMoney: 0,
                    onOffvisible: false,
                    rebotVisibel: false,
                    codeString: '',
                    // 停用信息
                    refundData: {

                    },
                    // 停用状态
                    refundStatus: {
                        Pending: "待审核",
                        Suspending: "待停用",
                        Suspend: "停用中",
                        Suspended: "已停用",
                        Refund: "已退款",
                        Reject: "审核驳回",
                        Cancelled: "已取消"
                    },
                    // 停用相关
                    // 是否显示停用弹窗
                    isShowRefund: false,
                    // 停用页面信息
                    refundPageData: {
                        host: {
                            create_time: 0,
                            first_payment_amount: 0
                        }
                    },
                    // 停用页面参数
                    refundParams: {
                        host_id: 0,
                        suspend_reason: null,
                        type: 'Expire'
                    },

                    // 续费
                    // 显示续费弹窗
                    isShowRenew: false,
                    // 续费页面信息
                    renewPageData: [],
                    // 续费参数
                    renewParams: {
                        id: 0,
                        billing_cycle: '',
                        price: 0
                    },
                    renewActiveId: '',
                    renewOrderId: 0,
                    isShowRefund: false,
                    hostStatus: {
                        Unpaid: { text: "未付款", color: "#F64E60", bgColor: "#FFE2E5" },
                        Pending: { text: "开通中", color: "#3699FF", bgColor: "#E1F0FF" },
                        Active: { text: "正常", color: "#1BC5BD", bgColor: "#C9F7F5" },
                        suspended: { text: "已暂停", color: "#F0142F", bgColor: "#FFE2E5" },
                        Deleted: { text: "已删除", color: "#9696A3", bgColor: "#F2F2F7" },
                        Failed: { text: "开通中", color: "#FFA800", bgColor: "#FFF4DE" }
                    },
                    isRead: false,
                    isShowPass: false,
                    passHidenCode: "",
                    rescueData: {},
                    activeName: "1",

                    // 统计图表相关
                    chartSelectValue: "1",
                    echartLoading: false,
                    echartStartTime: '',

                    // 管理相关
                    // 开关机状态
                    powerStatus: 'on',
                    powerList: [
                        {
                            id: 1,
                            label: '开机',
                            value: "on"
                        },
                        {
                            id: 2,
                            label: '关机',
                            value: "off"
                        },
                        {
                            id: 3,
                            label: '重启',
                            value: "rebot"
                        },
                        {
                            id: 4,
                            label: '强制重启',
                            value: "hardRebot"
                        },
                        {
                            id: 5,
                            label: '强制关机',
                            value: "hardOff"
                        },
                    ],
                    loading1: false,
                    loading2: false,
                    loading3: false,
                    loading4: false,
                    loading5: false,
                    loading6: false,
                    loading7: false,
                    loading8: false,
                    loading9: false,
                    // 是否显示电源操作确认弹窗
                    isShowPowerChange: false,
                    powerTitle: "",
                    powerType: "",
                    // 重置密码弹窗
                    isShowRePass: false,
                    // 重置密码弹窗数据
                    rePassData: {
                        password: '',
                        checked: false
                    },
                    // 救援模式
                    // 救援模式弹窗数据
                    rescueData: {
                        type: "1",
                        password: ''
                    },
                    // 是否显示救援模式弹窗
                    isShowRescue: false,

                    // 网络开始
                    netDataList: [],
                    netParams: {
                        page: 1,
                        limit: 20,
                        pageSizes: [20, 50, 100],
                        total: 200,
                        orderby: 'id',
                        sort: 'desc',
                        keywords: '',
                    },
                    // 网络流量
                    flowData:{},
                    // 日志开始
                    logDataList: [],
                    logParams: {
                        page: 1,
                        limit: 20,
                        pageSizes: [20, 50, 100],
                        total: 200,
                        orderby: 'id',
                        sort: 'desc',
                        keywords: '',
                    },
                }
            },
            filters: {
                formateTime(time) {
                    if (time && time !== 0) {
                        return formateDate(time * 1000)
                    } else {
                        return "--"
                    }
                }
            },
            methods: {
                // tab切换
                handleClick() {
                    switch (this.activeName) {
                        // 统计图表
                        case "1":
                            this.getstarttime(1)
                            this.getBwList()
                            break;
                        // 管理
                        case "2":
                            break;
                        // 网络
                        case "3":
                            this.getIpList()
                            this.doGetFlow()
                            break
                        // 日志
                        case "4":
                            this.getLogList()
                        default:
                            break;
                    }
                },
                // 获取通用配置
                getCommonData() {
                    getCommon().then(res => {
                        if (res.data.status === 200) {
                            this.commonData = res.data.data
                            localStorage.setItem('common_set_before', JSON.stringify(res.data.data))
                            document.title = this.commonData.website_name + '-产品详情'
                        }
                    })
                },
                // 获取产品详情
                getHostDetail() {
                    const params = {
                        id: this.id
                    }
                    hostDetail(params).then(res => {
                        if (res.data.status === 200) {
                            this.hostData = res.data.data.host

                            this.hostData.status_name = this.hostStatus[res.data.data.host.status].text

                            // 判断下次缴费时间是否在十天内
                            if (((this.hostData.due_time * 1000) - new Date().getTime()) / (24 * 60 * 60 * 1000) <= 10) {
                                this.isRead = true
                            }


                            this.product_id = this.hostData.product_id
                            // 获取镜像数据
                            this.getImage()
                        }
                    })
                },
                // 获取实例详情
                getCloudDetail() {
                    const params = {
                        id: this.id
                    }
                    cloudDetail(params).then(res => {
                        if (res.data.status === 200) {
                            this.cloudData = res.data.data
                            this.$emit('getclouddetail', this.cloudData)
                        }
                    })
                },
                // 关闭备注弹窗
                notesDgClose() {
                    this.isShowNotesDialog = false
                },
                // 显示 修改备注 弹窗
                doEditNotes() {
                    this.isShowNotesDialog = true
                    this.notesValue = this.hostData.notes
                },
                // 修改备注提交
                subNotes() {
                    const params = {
                        id: this.id,
                        notes: this.notesValue
                    }
                    editNotes(params).then(res => {
                        if (res.data.status === 200) {
                            // 重新拉取产品详情
                            this.getHostDetail()
                            this.$message({
                                message: '修改成功',
                                type: 'success'
                            });
                            this.isShowNotesDialog = false
                        }
                    }).catch(err => {
                        this.$message.error(err.data.msg);
                    })
                },
                // 返回产品列表页
                goBack() {
                    location.href = "dcimList.htm"
                },
                // 关闭重装系统弹窗
                reinstallDgClose() {
                    this.isShowReinstallDialog = false
                },
                // 展示重装系统弹窗
                showReinstall() {
                    this.errText = ''
                    this.reinstallData.password = null
                    this.reinstallData.key = null
                    this.reinstallData.port = null
                    this.isShowReinstallDialog = true
                },
                // 提交重装系统
                doReinstall() {
                    let isPass = true
                    const data = this.reinstallData

                    if (!data.osId) {
                        isPass = false
                        this.errText = "请选择操作系统"
                        return false
                    }

                    if (!data.port) {
                        isPass = false
                        this.errText = "请输入端口号"
                    }

                    if (data.type == 'pass') {
                        if (!data.password) {
                            isPass = false
                            this.errText = "请输入密码"
                            return false
                        }
                    } else {
                        if (!data.key) {
                            isPass = false
                            this.errText = "请选择SSHKey"
                            return false
                        }
                    }

                    if (isPass) {
                        this.errText = ""
                        let params = {
                            id: this.id,
                            image_id: data.osId,
                            port: data.port
                        }

                        if (data.type == 'pass') {
                            params.password = data.password
                        } else {
                            params.ssh_key_id = data.key
                        }


                        // 调用重装系统接口
                        reinstall(params).then(res => {
                            if (res.data.status == 200) {
                                this.$message.success(res.data.msg)
                                this.isShowReinstallDialog = false
                                this.getCloudStatus()

                            }
                        }).catch(err => {
                            this.errText = err.data.msg
                        })
                    }

                },
                // 检查产品是否购买过镜像
                doCheckImage() {
                    const params = {
                        id: this.id,
                        image_id: this.reinstallData.osId
                    }
                    checkImage(params).then(res => {
                        if (res.data.status === 200) {
                            this.payMoney = res.data.data.price
                            if (this.payMoney > 0) {
                                this.isPayImg = true
                            } else {
                                this.isPayImg = false
                            }
                        }
                    })
                },
                // 购买镜像
                payImg() {
                    const params = {
                        id: this.id,
                        image_id: this.reinstallData.osId
                    }
                    imageOrder(params).then(res => {
                        if (res.data.status === 200) {
                            const orderId = res.data.data.id
                            const amount = this.payMoney
                            this.$refs.topPayDialog.showPayDialog(orderId, amount)
                        }
                    })
                },
                // 获取镜像数据
                getImage() {
                    const params = {
                        id: this.product_id
                    }
                    image(params).then(res => {
                        if (res.data.status === 200) {
                            this.osData = res.data.data.list
                            this.osSelectData = this.osData[0].image
                            this.reinstallData.osGroupId = this.osData[0].id
                            this.osIcon = "/plugins/server/common_cloud/view/img/" + this.osData[0].name + '.png'
                            this.reinstallData.osId = this.osData[0].image[0].id
                            this.doCheckImage()
                        }
                    })
                },
                // 镜像分组改变时
                osSelectGroupChange(e) {
                    this.osData.map(item => {
                        if (item.id == e) {
                            this.osSelectData = item.image
                            this.osIcon = "/plugins/server/common_cloud/view/img/" + item.name + '.png'
                            this.reinstallData.osId = item.image[0].id
                            this.doCheckImage()
                        }
                    })
                },
                // 镜像版本改变时
                osSelectChange(e) {
                    this.doCheckImage()
                },
                // 随机生成密码
                autoPass() {
                    let pass = randomCoding(1) + 0 + genEnCode(9, 1, 1, 0, 1, 0)
                    // 重装系统
                    this.reinstallData.password = pass
                    // 重置密码
                    this.rePassData.password = pass
                    // 救援系统密码
                    this.rescueData.password = pass
                },
                // 随机生成port
                autoPort() {
                    this.reinstallData.port = genEnCode(3, 1, 0, 0, 0, 0)
                },
                // 获取SSH秘钥列表
                getSshKey() {
                    const params = {
                        page: 1,
                        limit: 1000,
                        orderby: "id",
                        sort: "desc"
                    }
                    sshKey(params).then(res => {
                        if (res.data.status === 200) {
                            this.sshKeyData = res.data.data.list
                        }
                    })
                },
                // 获取实例状态
                getCloudStatus() {
                    const params = {
                        id: this.id
                    }
                    cloudStatus(params).then(res => {
                        if (res.status === 200) {
                            this.status = res.data.data.status
                            this.statusText = res.data.data.desc
                            const e = this.status
                            if (this.status == 'operating') {
                                this.getCloudStatus()
                            } else {
                                if (e == 'on') {
                                    this.powerList = [
                                        {
                                            id: 2,
                                            label: '关机',
                                            value: "off"
                                        },
                                        {
                                            id: 5,
                                            label: '强制关机',
                                            value: "hardOff"
                                        },
                                        {
                                            id: 3,
                                            label: '重启',
                                            value: "rebot"
                                        },
                                        {
                                            id: 4,
                                            label: '强制重启',
                                            value: "hardRebot"
                                        },
                                    ]
                                    this.powerStatus = 'on'
                                } else if (e == 'off') {
                                    this.powerList = [
                                        {
                                            id: 1,
                                            label: '开机',
                                            value: "on"
                                        },
                                        {
                                            id: 3,
                                            label: '重启',
                                            value: "rebot"
                                        },
                                        {
                                            id: 4,
                                            label: '强制重启',
                                            value: "hardRebot"
                                        },

                                    ]
                                    this.powerStatus = 'off'
                                } else {
                                    this.powerList = [
                                        {
                                            id: 1,
                                            label: '开机',
                                            value: "on"
                                        },
                                        {
                                            id: 2,
                                            label: '关机',
                                            value: "off"
                                        },
                                        {
                                            id: 3,
                                            label: '重启',
                                            value: "rebot"
                                        },
                                        {
                                            id: 4,
                                            label: '强制重启',
                                            value: "hardRebot"
                                        },
                                        {
                                            id: 5,
                                            label: '强制关机',
                                            value: "hardOff"
                                        },
                                    ]
                                }
                                this.$emit('getstatus', res.data.data.status)
                            }
                        }
                    }).catch(err => {
                        this.getCloudStatus()
                    })
                },
                // 获取救援模式状态
                getRemoteInfo() {
                    const params = {
                        id: this.id
                    }

                    remoteInfo(params).then(res => {
                        if (res.data.status === 200) {
                            this.rescueData = res.data.data

                            const length = this.rescueData.password.length
                            for (let i = 0; i < length; i++) {
                                this.passHidenCode += "*"
                            }
                            this.isRescue = (res.data.data.rescue == 1)
                            this.$emit('getrescuestatus', this.isRescue)
                        }
                    })
                },
                getVncUrl() {
                    this.loading4 = true
                    this.doGetVncUrl()
                },
                // 控制台点击
                doGetVncUrl() {
                    const params = {
                        id: this.id
                    }
                    vncUrl(params).then(res => {
                        if (res.data.status === 200) {
                            window.open(res.data.data.url);
                        }
                        this.loading4 = false
                    }).catch(err => {
                        this.loading4 = false
                        this.$message.error(err.data.msg)
                    })
                },
                // 开机
                doPowerOn() {
                    this.onOffvisible = false
                    const params = {
                        id: this.id
                    }
                    powerOn(params).then(res => {
                        if (res.data.status === 200) {
                            this.$message.success("开机发起成功!")
                            this.status = 'operating'
                            this.getCloudStatus()
                            this.loading1 = false
                        }
                    }).catch(err => {
                        this.loading1 = false
                        this.$message.error(err.data.msg)
                    })
                },
                // 关机
                doPowerOff() {
                    this.onOffvisible = false
                    const params = {
                        id: this.id
                    }
                    powerOff(params).then(res => {
                        if (res.data.status === 200) {
                            this.$message.success("关机发起成功!")
                            this.status = 'operating'
                            this.getCloudStatus()
                            this.loading2 = false
                        }
                    }).catch(err => {
                        this.loading2 = false
                        this.$message.error(err.data.msg)
                    })
                },
                // 重启
                doReboot() {
                    this.rebotVisibel = false
                    const params = {
                        id: this.id
                    }
                    reboot(params).then(res => {
                        if (res.data.status === 200) {
                            this.$message.success("重启发起成功!")
                            this.status = 'operating'
                            this.getCloudStatus()
                            this.loading3 = false
                        }
                    }).catch(err => {
                        this.loading3 = false
                        this.$message.error(err.data.msg)
                    })
                },
                // 强制关机
                doHardOff() {
                    const params = {
                        id: this.id
                    }
                    hardOff(params).then(res => {
                        if (res.data.status === 200) {
                            this.$message.success("强制重启发起成功!")
                            this.status = 'operating'
                            this.getCloudStatus()
                            this.loading4 = false
                        }
                    }).catch(err => {
                        this.loading4 = false
                        this.$message.error(err.data.msg)
                    })
                },
                // 强制重启
                doHardReboot() {
                    const params = {
                        id: this.id
                    }
                    hardReboot(params).then(res => {
                        if (res.data.status === 200) {
                            this.$message.success("强制重启发起成功!")
                            this.status = 'operating'
                            this.getCloudStatus()
                            this.loading5 = false
                        }
                    }).catch(err => {
                        this.loading5 = false
                        this.$message.error(err.data.msg)
                    })
                },
                // 获取产品停用信息
                getRefundMsg() {
                    const params = {
                        id: this.id
                    }
                    refundMsg(params).then(res => {
                        if (res.data.status === 200) {
                            this.refundData = res.data.data.refund
                        }
                    }).catch(err => {
                        this.refundData = null
                    })
                },
                // 支付成功回调
                paySuccess(e) {
                    if (e == this.renewOrderId) {
                        // 刷新实例详情
                        this.getHostDetail()
                        return true
                    }
                    // 重新检查当前选择镜像是否购买
                    this.doCheckImage()


                },
                // 取消支付回调
                payCancel(e) {
                    console.log(e);
                },
                // 获取优惠码信息
                getPromoCode() {
                    const params = {
                        id: this.id
                    }
                    promoCode(params).then(res => {
                        if (res.data.status === 200) {
                            let codes = res.data.data.promo_code
                            let code = ''
                            codes.map(item => {
                                code += item + ","
                            })
                            code = code.slice(0, -1)
                            this.codeString = code
                        }
                    })
                },
                // 显示续费弹窗
                showRenew() {
                    // 获取续费页面信息
                    const params = {
                        id: this.id,
                    }
                    renewPage(params).then(res => {
                        if (res.data.status === 200) {
                            this.renewPageData = res.data.data.host

                            this.renewActiveId = this.renewPageData[0].id
                            this.renewParams.billing_cycle = this.renewPageData[0].billing_cycle
                            this.renewParams.price = this.renewPageData[0].price
                            this.isShowRenew = true

                        }
                    }).catch(err => {
                        this.$message.error(err.data.msg)
                    })

                },
                // 续费弹窗关闭
                renewDgClose() {
                    this.isShowRenew = false
                },
                // 续费提交
                subRenew() {
                    const params = {
                        id: this.id,
                        billing_cycle: this.renewParams.billing_cycle,
                        customfield: {
                            promo_code: []
                        }
                    }
                    renew(params).then(res => {
                        if (res.data.status === 200) {
                            this.isShowRenew = false
                            this.renewOrderId = res.data.data.id
                            const orderId = res.data.data.id
                            const amount = this.renewParams.price
                            this.$refs.topPayDialog.showPayDialog(orderId, amount)
                        }
                    })
                },
                // 续费周期点击
                renewItemChange(item) {
                    this.renewActiveId = item.id
                    this.renewParams.billing_cycle = item.billing_cycle
                    this.renewParams.price = item.price
                },
                // 取消停用
                quitRefund() {
                    const params = {
                        id: this.refundData.id
                    }
                    cancel(params).then(res => {
                        if (res.data.status == 200) {
                            this.$message.success("取消停用成功")
                            this.getRefundMsg()
                        }
                    }).catch(err => {
                        this.$message.error(err.data.msg)
                    })
                },
                // 关闭停用
                refundDgClose() {

                },
                // 删除实例点击
                showRefund() {
                    const params = {
                        host_id: this.id
                    }
                    // refundMsg(params).then(res => {
                    //     if (res.data.status === 200) {
                    //         console.log(res);
                    //     }
                    // })
                    // 获取停用页面信息
                    refundPage(params).then(res => {
                        if (res.data.status == 200) {
                            this.refundPageData = res.data.data
                            if (this.refundPageData.allow_refund === 0) {
                                this.$message.warning("不支持退款")
                            } else {
                                this.isShowRefund = true
                            }
                        }
                    })
                },
                // 关闭停用弹窗
                refundDgClose() {
                    this.isShowRefund = false
                },
                // 停用弹窗提交
                subRefund() {
                    const params = {
                        host_id: this.id,
                        suspend_reason: this.refundParams.suspend_reason,
                        type: this.refundParams.type
                    }
                    if (!params.suspend_reason) {
                        this.$message.error("请选择停用原因")
                        return false
                    }
                    if (!params.type) {
                        this.$message.error("请选择停用时间")
                        return false
                    }

                    refund(params).then(res => {
                        if (res.data.status == 200) {
                            this.$message.success("停用申请成功！")
                            this.isShowRefund = false
                            this.getRefundMsg()
                        }
                    }).catch(err => {
                        this.$message.error(err.data.msg)
                    })
                },

                // 统计图表开始
                chartSelectChange(e) {
                    // 计算开始时间
                    this.getstarttime(e)
                    // 重新拉取图表数据
                    this.getBwList()
                },
                // 获取网络宽度
                getBwList() {
                    this.echartLoading = true
                    const params = {
                        id: this.id,
                        start_time: this.echartStartTime,
                    }
                    chartList(params).then(res => {
                        if (res.data.status === 200) {
                            const list = res.data.data.list

                            let xAxis = []
                            let yAxis = []
                            let yAxis2 = []

                            list.forEach(item => {
                                xAxis.push(formateDate(item.time * 1000))
                                yAxis.push(item.in_bw.toFixed(2))
                                yAxis2.push(item.out_bw.toFixed(2));
                            });

                            const options = {
                                title: {
                                    text: '网络宽带',
                                },
                                tooltip: {
                                    show: true,
                                    trigger: "axis",
                                },
                                grid: {
                                    left: '5%',
                                    right: '4%',
                                    bottom: '5%',
                                    containLabel: true
                                },
                                xAxis: {
                                    type: "category",
                                    boundaryGap: false,
                                    data: xAxis,
                                },
                                yAxis: {
                                    type: "value",
                                },
                                series: [
                                    {
                                        name: "进带宽(bps)",
                                        data: yAxis,
                                        type: "line",
                                        areaStyle: {},
                                    },
                                    {
                                        name: "出带宽(bps)",
                                        data: yAxis2,
                                        type: "line",
                                        areaStyle: {},
                                    },
                                ],
                            }

                            var bwChart = echarts.init(document.getElementById('bw-echart'));
                            var bw2Chart = echarts.init(document.getElementById('bw2-echart'));
                            bwChart.setOption(options);
                            bw2Chart.setOption(options);
                        }
                        this.echartLoading = false
                    }).catch(err => {
                        this.echartLoading = false
                    })
                },
                //时间转换
                getstarttime(type) {
                    // 1: 过去24小时 2：过去三天 3：过去七天
                    // let nowtime = parseInt(new Date().getTime() / 1000);
                    // if (type == 1) {
                    //     this.echartStartTime = nowtime - 24 * 60 * 60;
                    // } else if (type == 2) {
                    //     this.echartStartTime = nowtime - 24 * 60 * 60 * 3;
                    // } else if (type == 3) {
                    //     this.echartStartTime = nowtime - 24 * 60 * 60 * 7;
                    // }
                },
                // 管理开始
                // 显示电源操作确认弹窗
                showPowerDialog(type) {
                    if (type == 'on') {
                        this.powerTitle = "开启"
                    }
                    if (type == 'off') {
                        this.powerTitle = "关闭"
                    }
                    if (type == 'rebot') {
                        this.powerTitle = "重启"
                    }
                    if (type == 'hardOff') {
                        this.powerTitle = "强制关机"
                    }
                    if (type == 'hardRebot') {
                        this.powerTitle = "强制重启"
                    }
                    this.powerType = type
                    this.isShowPowerChange = true
                },
                // 进行开关机等操作
                toChangePower() {
                    const type = this.powerType
                    if (type == 'on') {
                        this.doPowerOn()
                        this.loading1 = true
                    }
                    if (type == 'off') {
                        this.doPowerOff()
                        this.loading2 = true
                    }
                    if (type == 'rebot') {
                        this.doReboot()

                        this.loading3 = true
                    }
                    if (type == 'hardOff') {
                        this.doHardOff()
                        this.loading4 = true
                    }
                    if (type == 'hardRebot') {
                        this.doHardReboot()
                        this.loading5 = true
                    }
                    this.isShowPowerChange = false
                },
                // 关闭电源操作确认弹窗
                powerDgClose() {
                    this.isShowPowerChange = false
                },
                // 重置密码 开始
                // 重置密码点击
                showRePass() {
                    this.errText = ''
                    this.rePassData = {
                        password: '',
                        checked: false
                    }
                    this.isShowRePass = true

                },
                rePassDgClose() {
                    this.isShowRePass = false
                },
                // 重置密码提交
                rePassSub() {
                    const data = this.rePassData
                    let isPass = true
                    if (!data.password) {
                        isPass = false
                        this.errText = "请输入密码"
                        return false
                    }

                    if (!data.checked && this.powerStatus == 'on') {
                        isPass = false
                        this.errText = "请勾选同意关机"
                        return false
                    }

                    if (isPass) {
                        this.loading6 = true
                        this.errText = ''
                        const params = {
                            id: this.id,
                            password: data.password
                        }

                        if (this.powerStatus == 'on') {
                            const params1 = {
                                id: this.id
                            }
                            powerOff(params1).then(res => {
                                this.getCloudStatus()
                                resetPassword(params).then(res => {
                                    if (res.data.status === 200) {
                                        this.$message.success("重置密码成功")
                                        this.isShowRePass = false

                                    }
                                    this.loading6 = false
                                }).catch(error => {
                                    this.errText = error.data.msg
                                    this.loading6 = false
                                })
                            }).catch(error => {
                                this.$message.error(error.data.msg)
                            })
                        } else {
                            resetPassword(params).then(res => {
                                if (res.data.status === 200) {
                                    this.$message.success("重置密码成功")
                                    this.isShowRePass = false
                                }
                                this.loading6 = false
                            }).catch(error => {
                                this.errText = error.data.msg
                                this.loading6 = false
                            })
                        }
                    }

                },
                // 救援系统 相关
                // 救援模式点击
                showRescueDialog() {
                    this.errText = ''
                    this.rescueData = {
                        type: "1",
                        password: ''
                    }
                    this.isShowRescue = true
                },
                // 关闭救援模式弹窗
                rescueDgClose() {
                    this.isShowRescue = false
                },
                // 救援模式提交按钮
                rescueSub() {
                    let isPass = true
                    if (!this.rescueData.type) {
                        isPass = false
                        this.errText = "请选择救援系统"
                        return false
                    }
                    if (!this.rescueData.password) {
                        isPass = false
                        this.errText = "请输入临时密码"
                        return false
                    }

                    if (isPass) {
                        this.errText = ''
                        this.loading7 = true
                        // 调用救援系统接口
                        const params = {
                            id: this.id,
                            type: this.rescueData.type,
                            password: this.rescueData.password
                        }
                        rescue(params).then(res => {
                            if (res.data.status === 200) {
                                this.$message.success("救援模式发起成功!")
                                this.getRemoteInfo()
                                this.getCloudStatus()
                            }
                            this.isShowRescue = false
                            this.loading7 = false
                        }).catch(err => {
                            this.errText = err.data.msg
                            this.loading7 = false
                        })
                    }
                },
                // 网络开始
                // 获取ip列表
                getIpList() {
                    const params = {
                        id: this.id,
                        ...this.netParams
                    }
                    this.loading8 = true
                    ipList(params).then(res => {
                        if (res.data.status === 200) {
                            this.netParams.total = res.data.data.count
                            this.netDataList = res.data.data.list
                        }
                        this.loading8 = false
                    })
                },
                netSizeChange(e) {
                    this.netParams.limit = e
                    this.netParams.page = 1
                    // 获取列表
                    this.getIpList()
                },
                netCurrentChange(e) {
                    this.netParams.page = e
                    this.getIpList()
                },
                // 获取网络流量
                doGetFlow() {
                    const params = {
                        id: this.id
                    }
                    getFlow(params).then(res => {
                        if(res.data.status ===200){
                            this.flowData = res.data.data
                        }
                    })
                },
                // 日志开始
                logSizeChange(e) {
                    this.logParams.limit = e
                    this.logParams.page = 1
                    // 获取列表
                    this.getLogList()
                },
                logCurrentChange(e) {
                    this.logParams.page = e
                    this.getLogList()
                },
                getLogList() {
                    this.loading9 = true
                    const params = {
                        ...this.logParams,
                        id: this.id
                    }
                    getLog(params).then(res => {
                        if (res.data.status === 200) {
                            this.logParams.total = res.data.data.count
                            this.logDataList = res.data.data.list
                        }
                        this.loading9 = false
                    }).catch(error => {
                        this.loading9 = false
                    })
                }
            },

        }).$mount(template)
        typeof old_onload == 'function' && old_onload()
    };
})(window);
