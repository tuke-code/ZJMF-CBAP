const template = document.getElementsByClassName("common_config")[0];
Vue.prototype.lang = window.lang;
new Vue({
  data() {
    return {
      host: location.origin,
      id: "",
      tabs: "duration", // duration,data_center,model,hardware,flexible,limit,system,other
      hover: true,
      tableLayout: false,
      delVisible: false,
      loading: false,
      currency_prefix:
        JSON.parse(localStorage.getItem("common_set")).currency_prefix || "¥",
      currency_suffix:
        JSON.parse(localStorage.getItem("common_set")).currency_suffix || "",
      optType: "add", // 新增/编辑
      comTitle: "",
      delTit: "",
      delType: "",
      delId: "",
      submitLoading: false,
      /* 周期 */
      cycleData: [],
      dataModel: false,
      cycleModel: false,
      cycleForm: {
        product_id: "",
        name: "",
        num: "",
        unit: "month",
        price_factor: null,
        price: null,
      },
      ratioModel: false,
      ratioData: [],
      ratioColumns: [
        {
          colKey: "name",
          title: lang.cycle_name,
          ellipsis: true,
        },
        {
          colKey: "unit",
          title: lang.cycle_time,
          ellipsis: true,
        },
        {
          colKey: "ratio",
          title: lang.mf_ratio,
          ellipsis: true,
        },
      ],
      cycleTime: [
        {
          value: "hour",
          label: lang.hour,
        },
        {
          value: "day",
          label: lang.day,
        },
        {
          value: "month",
          label: lang.natural_month,
        },
      ],
      cycleColumns: [
        {
          colKey: "name",
          title: lang.cycle_name,
          ellipsis: true,
        },
        {
          colKey: "unit",
          title: lang.cycle_time,
          ellipsis: true,
          ellipsis: true,
        },
        {
          colKey: "price_factor",
          title: lang.price_factor,
          ellipsis: true,
        },
        {
          colKey: "price",
          title: lang.cycle_price,
          ellipsis: true,
        },
        {
          colKey: "ratio",
          title: lang.cycle_ratio,
          ellipsis: true,
        },
        {
          colKey: "op",
          title: lang.operation,
          width: 100,
        },
      ],
      cycleRules: {
        name: [
          {
            required: true,
            message: lang.input + lang.cycle_name,
            type: "error",
          },
          {
            validator: (val) => val?.length <= 10,
            message: lang.verify8 + "1-10",
            type: "warning",
          },
        ],
        num: [
          {
            required: true,
            message: lang.input + lang.cycle_time,
            type: "error",
          },
          {
            pattern: /^[0-9]*$/,
            message: lang.input + lang.verify16,
            type: "warning",
          },
          {
            validator: (val) => val > 0 && val <= 999,
            message: lang.cycle_time + "1-999",
            type: "warning",
          },
        ],
        // 系统相关
        image_group_id: [
          {
            required: true,
            message: lang.select + lang.system_classify,
            type: "error",
          },
        ],
        rel_image_id: [
          {
            required: true,
            message: lang.input + lang.opt_system + "ID",
            type: "error",
          },
          {
            pattern: /^[0-9]*$/,
            message: lang.input + lang.verify16,
            type: "warning",
          },
        ],
        price: [
          { required: true, message: lang.input + lang.price, type: "error" },
          {
            pattern: /^\d+(\.\d{0,2})?$/,
            message: lang.verify12,
            type: "warning",
          },
          {
            validator: (val) => val >= 0,
            message: lang.verify12,
            type: "warning",
          },
        ],
        icon: [
          {
            required: true,
            message: lang.select + lang.mf_icon,
            type: "error",
            trigger: "change",
          },
        ],
      },
      /* 操作系统 */
      systemGroup: [],
      systemList: [],
      systemParams: {
        product_id: "",
        page: 1,
        limit: 1000,
        image_group_id: "",
        keywords: "",
      },
      systemModel: false,
      createSystem: {
        // 添加操作系统表单
        image_group_id: "",
        name: "",
        charge: 0,
        price: "",
        enable: 0,
        rel_image_id: "",
      },
      systemColumns: [
        // 套餐表格
        {
          colKey: "id",
          title: lang.order_index,
          width: 100,
        },
        {
          colKey: "image_group_name",
          title: lang.system_classify,
          width: 200,
          ellipsis: true,
        },
        {
          colKey: "name",
          title: lang.system_name,
          ellipsis: true,
        },
        {
          colKey: "charge",
          title: lang.mf_charge,
          width: 200,
        },
        {
          colKey: "price",
          title: lang.price,
        },
        {
          colKey: "enable",
          title: lang.mf_enable,
          width: 200,
        },
        {
          colKey: "op",
          title: lang.operation,
          width: 100,
        },
      ],
      groupColumns: [
        // 套餐表格
        {
          // 列拖拽排序必要参数
          colKey: "drag",
          width: 20,
          className: "drag-icon",
        },
        {
          colKey: "image_group_name",
          title: lang.system_classify,
          ellipsis: true,
        },
        {
          colKey: "op",
          title: lang.operation,
          width: 100,
        },
      ],
      // 操作系统图标
      iconList: [
        "Windows",
        "CentOS",
        "Ubuntu",
        "Debian",
        "ESXi",
        "XenServer",
        "FreeBSD",
        "Fedora",
        "其他",
        "ArchLinux",
        "Rocky",
        "OpenEuler",
        "AlmaLinux",
      ],
      iconSelecet: [],
      classModel: false,
      classParams: {
        id: "",
        name: "",
        icon: "",
      },
      popupProps: {
        overlayClassName: `custom-select`,
        overlayStyle: (trigger) => ({ width: `${trigger.offsetWidth}px` }),
      },
      /* 其他设置 */
      otherForm: {
        product_id: "",
        manual_resource: 0,
        rand_ssh_port: 0,
        reset_password_sms_verify: 0,
        reinstall_sms_verify: 0,
        level_discount_memory_order: 0,
        level_discount_memory_upgrade: 0,
        level_discount_disk_order: 0,
        level_discount_disk_upgrade: 0,
        level_discount_bw_upgrade: 0,
        level_discount_ip_num_upgrade: 0,
      },
      rulesList: [
        // 平衡规则
        { value: 1, label: lang.mf_rule1 },
        { value: 2, label: lang.mf_rule2 },
        { value: 3, label: lang.mf_rule3 },
      ],
      dataRules: {
        data_center_id: [
          {
            required: true,
            message: `${lang.select}${lang.area}`,
            type: "error",
          },
        ],
        line_id: [
          {
            required: true,
            message: `${lang.select}${lang.line_name}`,
            type: "error",
          },
        ],
        flow: [
          { required: true, message: `${lang.input}CPU`, type: "error" },
          {
            pattern: /^[0-9]*$/,
            message: lang.input + "0-999999" + lang.verify2,
            type: "warning",
          },
          {
            validator: (val) => val >= 0 && val <= 999999,
            message: lang.input + "0-999999" + lang.verify2,
            type: "warning",
          },
        ],
        host_prefix: [
          {
            required: true,
            message: `${lang.input}${lang.host_prefix}`,
            type: "error",
          },
          {
            pattern: /^[A-Z][a-zA-Z0-9_.]{0,9}$/,
            message: lang.verify8 + "1-10",
            type: "warning",
          },
        ],
        host_length: [
          {
            required: true,
            message: `${lang.input}${lang.mf_tip2}`,
            type: "error",
          },
          {
            pattern: /^[0-9]*$/,
            message: lang.mf_tip2,
            type: "warning",
          },
        ],
        country_id: [
          {
            required: true,
            message: lang.select + lang.country,
            type: "error",
          },
        ],
        city: [
          { required: true, message: lang.input + lang.city, type: "error" },
        ],
        cloud_config: [
          { required: true, message: lang.select + lang.city, type: "error" },
        ],
        cloud_config_id: [
          { required: true, message: lang.input + "ID", type: "error" },
        ],
        area: [
          {
            required: true,
            message: `${lang.input}${lang.area}${lang.nickname}`,
            type: "error",
          },
        ],
        name: [
          {
            required: true,
            message: `${lang.input}${lang.box_label23}`,
            type: "error",
          },
        ],
        description: [
          {
            required: true,
            message: `${lang.input}${lang.description}`,
            type: "error",
          },
        ],
        order: [
          {
            required: true,
            message: `${lang.input}${lang.sort}ID`,
            type: "error",
          },
          {
            pattern: /^[0-9]*$/,
            message: lang.verify7,
            type: "warning",
          },
          {
            validator: (val) => val >= 0,
            message: lang.verify7,
            type: "warning",
          },
        ],
        network_type: [
          {
            required: true,
            message: lang.select + lang.net_type,
            type: "error",
          },
        ],
        bw: [
          { required: true, message: `${lang.input}${lang.bw}`, type: "error" },
          {
            pattern: /^[0-9]*$/,
            message: lang.input + "1-30000" + lang.verify2,
            type: "warning",
          },
          {
            validator: (val) => val >= 1 && val <= 30000,
            message: lang.input + "1-30000" + lang.verify2,
            type: "warning",
          },
        ],
        peak_defence: [
          {
            pattern: /^[0-9]*$/,
            message: lang.input + "1-999999" + lang.verify2,
            type: "warning",
          },
          {
            validator: (val) => val >= 1 && val <= 999999,
            message: lang.input + "1-999999" + lang.verify2,
            type: "warning",
          },
        ],
        min_memory: [
          {
            required: true,
            message: `${lang.input}${lang.memory}`,
            type: "error",
          },
          {
            pattern: /^[0-9]*$/,
            message: lang.input + "1-512" + lang.verify2,
            type: "warning",
          },
          {
            validator: (val) => val >= 1 && val <= 512,
            message: lang.input + "1-512" + lang.verify2,
            type: "warning",
          },
        ],
        max_memory: [
          {
            required: true,
            message: `${lang.input}${lang.memory}`,
            type: "error",
          },
          {
            pattern: /^[0-9]*$/,
            message: lang.input + "1-512" + lang.verify2,
            type: "warning",
          },
          {
            validator: (val) => val >= 1 && val <= 512,
            message: lang.input + "1-512" + lang.verify2,
            type: "warning",
          },
        ],
        line_id: [
          {
            required: true,
            message: `${lang.select}${lang.bw_line}`,
            type: "error",
          },
        ],
        min_bw: [
          {
            pattern: /^[0-9]*$/,
            message: lang.input + "1-30000" + lang.verify2,
            type: "warning",
          },
          {
            validator: (val) => val >= 1 && val <= 30000,
            message: lang.input + "1-30000" + lang.verify2,
            type: "warning",
          },
        ],
        max_bw: [
          {
            pattern: /^[0-9]*$/,
            message: lang.input + "1-30000" + lang.verify2,
            type: "warning",
          },
          {
            validator: (val) => val >= 1 && val <= 30000,
            message: lang.input + "1-30000" + lang.verify2,
            type: "warning",
          },
        ],
        model_config_id: [
          {
            required: true,
            message: `${lang.select}${lang.box_title46}`,
            type: "error",
          },
        ],
      },
      backupColumns: [
        // 备份表格
        {
          colKey: "id",
          title: lang.order_index,
          width: 160,
        },
        {
          colKey: "num",
          title: lang.allow_back_num,
          ellipsis: true,
          width: 180,
        },
        {
          colKey: "price",
          title: lang.min_cycle_price,
          className: "back-price",
        },
      ],
      snappColumns: [
        // 快照表格
        {
          colKey: "id",
          title: lang.order_index,
          width: 160,
        },
        {
          colKey: "num",
          title: lang.allow_back_num,
          width: 180,
          ellipsis: true,
        },
        {
          colKey: "price",
          title: lang.min_cycle_price,
        },
      ],
      backList: [],
      snapList: [],
      backLoading: false,
      snapLoading: false,
      backAllStatus: false,
      /* 计算配置 */
      modelList: [],
      modelLoading: false,
      memoryList: [],
      memoryLoading: false,
      memoryType: "", // 内存方式
      modelColumns: [
        // model表格 order_text68
        {
          colKey: "id",
          title: lang.order_text68,
          width: 100,
        },
        {
          colKey: "name",
          title: lang.config_name,
          width: 120,
          ellipsis: true,
        },
        {
          colKey: "group_id",
          title: `${lang.sale_group}ID`,
          width: 200,
        },
        {
          colKey: "cpu",
          title: lang.mf_cpu,
          width: 200,
          ellipsis: true,
        },
        {
          colKey: "cpu_param",
          title: lang.mf_cpu_param,
          width: 200,
          ellipsis: true,
        },
        {
          colKey: "memory",
          title: lang.memory,
          width: 200,
          ellipsis: true,
        },
        {
          colKey: "disk",
          title: lang.disk,
          width: 200,
          ellipsis: true,
        },
        // {
        //   colKey: 'price',
        //   title: lang.price,
        // },
        {
          colKey: "op",
          title: lang.operation,
          width: 100,
        },
      ],
      memoryColumns: [
        // memory表格
        {
          colKey: "value",
          title: `${lang.memory}（GB）`,
          width: 300,
        },
        {
          colKey: "price",
          title: lang.price,
        },
        {
          colKey: "op",
          title: lang.operation,
          width: 100,
        },
      ],
      calcType: "", // cpu, memory
      calcForm: {
        // model
        name: "",
        group_id: "",
        cpu: "",
        cpu_param: "",
        memory: "",
        disk: "",
        price: [],
      },
      calcModel: false,
      configType: [
        { value: "radio", label: lang.mf_radio },
        { value: "step", label: lang.mf_step },
        { value: "total", label: lang.mf_total },
      ],
      calcRules: {
        // 模型配置验证
        name: [
          {
            required: true,
            message: `${lang.input}${lang.config_name}`,
            type: "error",
          },
        ],
        group_id: [
          {
            required: true,
            message: `${lang.input}${lang.sale_group}ID`,
            type: "error",
          },
          {
            pattern: /^[0-9]*$/,
            message: lang.input + lang.verify7,
            type: "warning",
          },
        ],
        cpu: [
          {
            required: true,
            message: `${lang.input}${lang.mf_cpu}`,
            type: "error",
          },
        ],
        cpu_param: [
          {
            required: true,
            message: `${lang.input}${lang.mf_cpu_param}`,
            type: "error",
          },
        ],
        memory: [
          {
            required: true,
            message: `${lang.input}${lang.memory}`,
            type: "error",
          },
        ],
        disk: [
          {
            required: true,
            message: `${lang.input}${lang.disk}`,
            type: "error",
          },
        ],
        value: [
          { required: true, message: `${lang.input}${lang.bw}`, type: "error" },
          {
            pattern: /^[0-9]*$/,
            message: lang.input + "0-30000" + lang.verify2,
            type: "warning",
          },
          {
            validator: (val) => val >= 0 && val <= 30000,
            message: lang.input + "0-30000" + lang.verify2,
            type: "warning",
          },
        ],
        type: [
          {
            required: true,
            message: `${lang.select}${lang.config}${lang.mf_way}`,
            type: "error",
          },
        ],
        price: [
          {
            pattern: /^\d+(\.\d{0,2})?$/,
            message: lang.input + lang.money,
            type: "warning",
          },
          {
            validator: (val) => val >= 0 && val <= 999999,
            message: lang.verify12,
            type: "warning",
          },
        ],
        min_value: [
          {
            required: true,
            message: `${lang.input}${lang.min_value}`,
            type: "error",
          },
          {
            pattern: /^([1-9][0-9]*)$/,
            message: lang.input + "1~1048576" + lang.verify2,
            type: "warning",
          },
          {
            validator: (val) => val >= 1 && val <= 1048576,
            message: lang.input + "1~1048576" + lang.verify2,
            type: "warning",
          },
        ],
        max_value: [
          {
            required: true,
            message: `${lang.input}${lang.max_value}`,
            type: "error",
          },
          {
            pattern: /^([1-9][0-9]*)$/,
            message: lang.input + "2~1048576" + lang.verify2,
            type: "warning",
          },
          {
            validator: (val) => val >= 2 && val <= 1048576,
            message: lang.input + "2~1048576" + lang.verify2,
            type: "warning",
          },
        ],
        step: [
          {
            required: true,
            message: `${lang.input}${lang.min_step}`,
            type: "error",
          },
          {
            pattern: /^([1-9][0-9]*)$/,
            message: lang.input + lang.verify16,
            type: "warning",
          },
        ],
        traffic_type: [
          {
            required: true,
            message: `${lang.select}${lang.traffic_type}`,
            type: "error",
          },
        ],
        bill_cycle: [
          {
            required: true,
            message: `${lang.select}${lang.billing_cycle}`,
            type: "error",
          },
        ],
      },
      isAdvance: false, // 是否展开高级配置
      /* 数据中心 */
      dataList: [],
      dataLoading: false,
      dataColumns: [
        {
          colKey: "order",
          title: lang.index_text8,
          width: 100,
        },
        {
          colKey: "country_name",
          title: lang.country,
          width: 150,
          ellipsis: true,
          className: "country-td",
        },
        {
          colKey: "city",
          title: lang.city,
          width: 150,
          ellipsis: true,
          className: "city-td",
        },
        {
          colKey: "area",
          title: `${lang.area}${lang.nickname}`,
          width: 150,
          ellipsis: true,
          className: "area-td",
        },
        {
          colKey: "line",
          title: lang.line_name,
          className: "line-td",
          width: 250,
          ellipsis: true,
        },
        {
          colKey: "price",
          title: lang.price,
          className: "line-td",
          ellipsis: true,
        },
        {
          colKey: "op",
          title: lang.operation,
          width: 100,
          className: "line-td",
        },
      ],
      dataForm: {
        // 新建数据中心
        country_id: "",
        city: "",
        area: "",
        order: 0,
      },
      countryList: [],
      // 配置选项
      dataConfig: [
        { value: "node", lable: lang.node + "ID" },
        { value: "area", lable: lang.area + "ID" },
        { value: "node_group", lable: lang.node_group + "ID" },
      ],
      /* 线路相关 */
      lineType: "", // 新增,编辑线路，新增的时候本地操作，保存一次性提交
      subType: "", // 线路子项类型， line_bw, line_flow, line_defence, line_ip
      lineForm: {
        country_id: "", // 线路国家
        city: "", // 线路城市
        data_center_id: "",
        name: "",
        bill_type: "", // bw, flow
        bw_ip_group: "",
        defence_ip_group: "",
        defence_enable: 0, // 防护开关
        bw_data: [], // 带宽
        flow_data: [], //流量
        defence_data: [], // 防护
        ip_data: [], // ip
        order: 0,
        /* 推荐配置 */
        line_id: "",
        flow: "",
        description: "",
        cpu: "",
        memory: "",
        system_disk_size: "",
        system_disk_type: "",
        data_disk_size: "",
        data_disk_type: "",
        network_type: "",
        bw: "",
        peak_defence: "",
        /* 配置限制 */
        line_id: "",
        model_config_id: [],
        min_bw: "",
        max_bw: "",
        min_memory: "",
        max_memory: "",
        min_flow: "",
        max_flow: "",
      },
      bw_ip_show: false, // bw 高级配置
      defence_ip_show: false, // 防护高级配置
      subForm: {
        // 线路子项表单
        type: "",
        value: "",
        price: [],
        min_value: "",
        max_value: "",
        step: 1,
        other_config: {
          in_bw: "",
          out_bw: "",
          bill_cycle: "",
        },
      },
      lineModel: false,
      lineRight: false,
      delSubIndex: 0,
      subId: "",
      countrySelect: [], // 国家三级联动
      billType: [
        { value: "bw", label: lang.mf_bw },
        { value: "flow", label: lang.mf_flow },
      ],
      bwColumns: [
        {
          colKey: "fir",
          title: lang.bw,
        },
        {
          colKey: "price",
          title: lang.price,
        },
        {
          colKey: "op",
          title: lang.operation,
          width: 100,
        },
      ],
      trafficTypes: [
        { value: "1", label: lang.in },
        { value: "2", label: lang.out },
        { value: "3", label: lang.in_out },
      ],
      billingCycle: [
        { value: "month", label: lang.natural_month },
        { value: "last_30days", label: lang.last_30days },
      ],
      /* 配置限制 */
      limit_list: [],
      limit_loading: false,
      line_oading: false,
      limit_columns: [
        {
          colKey: "area",
          title: lang.data_center,
        },
        {
          colKey: "model",
          title: lang.server_model,
          ellipsis: true,
          width: 300,
        },
        {
          colKey: "bw",
          title: `${lang.bw}/${lang.cloud_flow}`,
        },
        {
          colKey: "op",
          title: lang.operation,
          width: 100,
        },
      ],
      line_columns: [],
      limitArr: [
        { name: "cpu", tit: lang.mf_tip16 },
        { name: "data_center", tit: lang.mf_tip17 },
        { name: "line", tit: lang.mf_tip18 },
      ],
      limitType: "",
      limitModel: false,
      limitMemoryType: "", // 配置限制里面内存的方式
      bwValidator: "",
      /* 硬件配置 */
      hardwareArr: [
        { name: "cpu", label: `${lang.mf_cpu}` },
        { name: "memory", label: `${lang.memory}` },
        { name: "disk", label: `${lang.disk}` },
      ],
      cpuList: [],
      memoryList: [],
      diskList: [],
      cpuLoading: false,
      memoryLoading: false,
      diskLoading: false,
      cpu_columns: [
        {
          colKey: "value",
          title: lang.mf_cpu,
          ellipsis: true,
        },
        {
          colKey: "price",
          title: lang.price,
          width: 200,
          ellipsis: true,
        },
        {
          colKey: "op",
          title: lang.operation,
          width: 100,
        },
      ],
      memory_columns: [],
      disk_columns: [],
      hardwareForm: {
        value: "",
        order: null,
        other_config: {
          memory_slot: null,
          memory: null,
        },
        price: [],
      },
      hardDialog: false,
      hardMode: "", // cpu memory disk
      hardRules: {
        name: [
          {
            required: true,
            message: `${lang.input}${lang.model_name}`,
            type: "error",
          },
        ],
        group_id: [
          {
            required: true,
            message: `${lang.input}${lang.sale_group}ID`,
            type: "error",
          },
          {
            pattern: /^[0-9]*$/,
            message: lang.input + lang.verify7,
            type: "warning",
          },
        ],
        cpu_option_id: [
          {
            required: true,
            message: `${lang.select}${lang.mf_cpu}`,
            type: "error",
          },
        ],
        cpu_num: [
          {
            required: true,
            message: `${lang.input}${lang.auth_num}`,
            type: "error",
          },
        ],
        disk_option_id: [
          {
            required: true,
            message: `${lang.select}${lang.disk}`,
            type: "error",
          },
        ],
        disk_num: [
          {
            required: true,
            message: `${lang.input}${lang.auth_num}`,
            type: "error",
          },
        ],
        mem_option_id: [
          {
            required: true,
            message: `${lang.select}${lang.memory}`,
            type: "error",
          },
        ],
        mem_num: [
          {
            required: true,
            message: `${lang.input}${lang.auth_num}`,
            type: "error",
          },
        ],
        ip_num: [
          {
            required: true,
            message: `${lang.input}IP${lang.auth_num}`,
            type: "error",
          },
        ],
        bw: [
          {
            required: true,
            message: `${lang.input}${lang.bw}`,
            type: "error",
          },
        ],
        "other_config.memory_slot": [
          {
            required: true,
            message: `${lang.input}${lang.memory_slot_num}`,
            type: "error",
          },
        ],
        "other_config.memory": [
          {
            required: true,
            message: `${lang.input}${lang.memory}${lang.capacity}`,
            type: "error",
          },
        ],
        order: [
          {
            required: true,
            message: `${lang.input}${lang.sort}`,
            type: "error",
          },
        ],
        price: [
          {
            pattern: /^\d+(\.\d{0,2})?$/,
            message: lang.input + lang.money,
            type: "warning",
          },
          {
            validator: (val) => val >= 0 && val <= 999999,
            message: lang.verify12,
            type: "warning",
          },
        ],
      },
      /* 灵活机型 */
      packageList: [],
      packageLoading: [],
      flexList: [],
      flexLoading: false,
      flexColumns: [
        // model表格 order_text68
        {
          colKey: "order",
          title: lang.order_text68,
          width: 100,
        },
        {
          colKey: "name",
          title: lang.model_name,
          width: 200,
          ellipsis: true,
          className: "model-name",
        },
        {
          colKey: "cpu",
          title: lang.mf_cpu,
          width: 300,
          ellipsis: true,
        },
        {
          colKey: "memory",
          title: lang.memory,
          width: 200,
          ellipsis: true,
        },
        {
          colKey: "disk",
          title: lang.disk,
          width: 200,
          ellipsis: true,
        },
        {
          colKey: "bw",
          title: lang.bw,
          width: 100,
          ellipsis: true,
        },
        {
          colKey: "ip_num",
          title: `IP${lang.auth_num}`,
          width: 100,
          ellipsis: true,
        },
        {
          colKey: "hidden",
          title: lang.mf_tip40,
          ellipsis: true,
          width: 150,
        },
        {
          colKey: "op",
          title: lang.operation,
          width: 100,
        },
      ],
      flexModel: false,
      flexForm: {
        name: "",
        order: 0,
        group_id: "",
        cpu_option_id: "",
        cpu_num: undefined,
        mem_option_id: "",
        mem_num: undefined,
        disk_option_id: "",
        disk_num: undefined,
        bw: undefined,
        ip_num: undefined,
        description: "",
        optional_memory_id: [],
        mem_max: undefined,
        mem_max_num: undefined,
        optional_disk_id: [],
        disk_max_num: undefined,
        price: [],
      },
    };
  },
  watch: {
    store_limit: {
      immediate: true,
      handler(val) {
        if (val * 1) {
          this.getStoreLimitList("system_disk_limit");
          this.getStoreLimitList("data_disk_limit");
        }
      },
    },
  },
  computed: {
    isShowFill() {
      return (price) => {
        const index = price.findIndex((item) => item.price);
        return index === -1;
      };
    },
    calcName() {
      return (type) => {
        switch (type) {
          case "memory":
            return `${lang.memory_config}`;
          case "system_disk":
            return `${lang.system_disk_size}${lang.capacity}`;
          case "data_disk":
            return `${lang.data_disk}${lang.capacity}`;
          case "line_bw":
            return `${lang.bw}（Mbps）`;
        }
      };
    },
    calcIcon() {
      return (
        this.host +
        "/upload/common/country/" +
        this.countryList.filter(
          (item) => item.id === this.dataForm.country_id
        )[0]?.iso +
        ".png"
      );
    },
    calcIcon1() {
      if (!this.countrySelect) {
        return;
      }
      return (
        this.host +
        "/upload/common/country/" +
        this.countrySelect.filter(
          (item) => item.id === this.lineForm.country_id
        )[0]?.iso +
        ".png"
      );
    },
    calcCity() {
      if (!this.countrySelect) {
        return;
      }
      const city =
        this.countrySelect.filter(
          (item) => item.id === this.lineForm.country_id
        )[0]?.city || [];
      if (city.length === 1) {
        this.lineForm.city = city[0].name;
      }
      return city || [];
    },
    calcArea() {
      if (!this.countrySelect) {
        return;
      }
      const area =
        this.countrySelect
          .filter((item) => item.id === this.lineForm.country_id)[0]
          ?.city.filter((item) => item.name === this.lineForm.city)[0]?.area ||
        [];
      if (area.length === 1) {
        this.lineForm.data_center_id = area[0].id;
      }
      return area;
    },
    calcSelectLine() {
      if (!this.countrySelect) {
        return;
      }
      const line =
        this.countrySelect
          .filter((item) => item.id === this.lineForm.country_id)[0]
          ?.city.filter((item) => item.name === this.lineForm.city)[0]
          ?.area.filter((item) => item.id === this.lineForm.data_center_id)[0]
          ?.line || [];
      // if (line.length === 1) {
      //   this.lineForm.line_id = line[0].id
      //   this.calcLineType = line[0].bill_type
      // }
      return line;
    },
    calcColums() {
      return (val) => {
        const temp = JSON.parse(JSON.stringify(this.bwColumns));
        switch (val) {
          case "flow":
            temp[0].title = lang.cloud_flow + "（GB）";
            return temp;
          case "defence":
            temp[0].title = lang.defence + "（GB）";
            return temp;
          case "ip":
            temp[0].title = "IP" + lang.auth_num + `（${lang.one}）`;
            return temp;
        }
      };
    },
    calcSubTitle() {
      // 副标题
      return (data) => {
        if (data.length > 0) {
          return lang[`mf_${data[0].type}`] + lang.mf_way;
        } else {
          return "";
        }
      };
    },
    calcPrice() {
      // 处理本地价格展示
      return (price) => {
        // 找到价格最低的
        const arr = Object.values(price)
          .sort((a, b) => {
            return a - b;
          })
          .filter(Number);
        if (arr.length > 0) {
          let temp = "";
          Object.keys(price).forEach((item) => {
            if (price[item] * 1 === arr[0] * 1) {
              const name = this.cycleData.filter((el) => el.id === item * 1)[0]
                ?.name;
              temp = (arr[0] * 1).toFixed(2) + "/" + name;
            }
          });
          return temp;
        } else {
          return "0.00";
        }
      };
    },
    // 子项的计费方式是否可选
    calcShow() {
      switch (this.subType) {
        case "line_bw":
          return this.lineForm.bw_data.length > 0 ? true : false;
      }
    },
    calcCpu() {
      return (val) => {
        return val.value + lang.cores;
      };
    },
    calcMemory() {
      return (val) => {
        return val.value + "GB";
      };
    },
    calcLine() {
      // 当前线路
      return this.dataList.filter(
        (item) =>
          item.country_id === this.lineForm.country_id &&
          item.city === this.lineForm.city
      )[0]?.line;
    },
    calcMemery() {
      return (data) => {
        return data.split(",");
      };
    },
    calcRange() {
      // 计算验证范围
      return (val) => {
        if (this.calcType === "memory") {
          // 内存
          return val >= 1 && val <= 512;
        } else {
          return val >= 1 && val <= 1048576;
        }
      };
    },
    calcReg() {
      // 动态生成规则
      return (name, min, max, tag) => {
        // tag 标识 NC
        let pattern = "";
        if (tag === "nc") {
          pattern = /^[0-9NC]*$/;
        } else if (tag === "ip") {
          pattern = /(^NC$)|(^\d+$)|(^\d+_\d*)(,(\d+_\d+)|(\d+))+([0-9]|$)/g;
        } else {
          pattern = /^[0-9]*$/;
        }
        const pass = (val) => {
          if (val === "NC") {
            return true;
          } else if (tag === "ip") {
            return true;
          } else {
            return val >= min && val <= max;
          }
        };
        return [
          { required: true, message: `${lang.input}${name}`, type: "error" },
          {
            pattern: pattern,
            message:
              tag === "ip" ? "" : lang.input + `${min}-${max}` + lang.verify18,
            type: "warning",
          },
          {
            validator: (val) => pass(val),
            message:
              tag === "ip" ? "" : lang.input + `${min}-${max}` + lang.verify18,
            type: "warning",
          },
        ];
      };
    },
    calcIpNum() {
      return (value) => {
        if (value.includes("_")) {
          value = value.split(",").reduce((all, cur) => {
            all += cur.split("_")[0] * 1;
            return all;
          }, 0);
          return value;
        } else {
          return value;
        }
      };
    },
    calcHardwareData() {
      return (type) => {
        return this[`${type}List`];
      };
    },
    calcHardwareColumns() {
      return (type) => {
        return this[`${type}_columns`];
      };
    },
    calcHardwareLoading() {
      return (type) => {
        return this[`${type}Loading`];
      };
    },
    calcHardValue() {
      switch (this.hardMode) {
        case "cpu":
          return lang.mf_cpu;
        case "memory":
          return lang.memory;
        case "disk":
          return lang.disk;
      }
    },
  },
  methods: {
    /* 硬件配置 */
    async getHardwareList(mod) {
      try {
        this[`${mod}Loading`] = true;
        const res = await getHardware(mod, { product_id: this.id });
        this[`${mod}List`] = res.data.data.list;
        this[`${mod}Loading`] = false;
      } catch (error) {
        this[`${mod}Loading`] = false;
      }
    },
    addHardware(mod) {
      this.optType = "add";
      if (mod === "cpu") {
        this.comTitle = `${lang.order_new}${lang.mf_cpu}`;
      }
      if (mod === "memory") {
        this.comTitle = `${lang.order_new}${lang.memory}`;
      }
      if (mod === "disk") {
        this.comTitle = `${lang.order_new}${lang.disk}`;
      }
      const price = this.cycleData
        .reduce((all, cur) => {
          all.push({
            id: cur.id,
            name: cur.name,
            price: "",
          });
          return all;
        }, [])
        .sort((a, b) => {
          return a.id - b.id;
        });
      (this.hardwareForm = {
        value: "",
        order: 0,
        other_config: {
          memory_slot: undefined,
          memory: undefined,
        },
        price,
      }),
        (this.hardMode = mod);
      this.hardDialog = true;
    },
    async editHard(mod, row) {
      if (mod === "cpu") {
        this.comTitle = `${lang.edit}${lang.mf_cpu}`;
      }
      if (mod === "memory") {
        this.comTitle = `${lang.edit}${lang.memory}`;
      }
      if (mod === "disk") {
        this.comTitle = `${lang.edit}${lang.disk}`;
      }
      this.hardMode = mod;
      this.optType = "update";
      const res = await getHardwareDetails(mod, { id: row.id });
      temp = res.data.data;
      this.delId = row.id;
      const price = temp.duration
        .reduce((all, cur) => {
          all.push({
            id: cur.id,
            name: cur.name,
            price: cur.price,
          });
          return all;
        }, [])
        .sort((a, b) => {
          return a.id - b.id;
        });
      Object.assign(this.hardwareForm, temp);
      this.hardwareForm.price = price;
      this.hardDialog = true;
    },
    async submitHard({ validateResult, firstError }) {
      if (validateResult === true) {
        try {
          const params = JSON.parse(JSON.stringify(this.hardwareForm));
          params.price = params.price.reduce((all, cur) => {
            cur.price && (all[cur.id] = cur.price);
            return all;
          }, {});
          params.product_id = this.id;
          if (this.optType === "add") {
            delete params.id;
          }
          this.submitLoading = true;
          const res = await createAndUpdateHardware(
            this.hardMode,
            this.optType,
            params
          );
          this.$message.success(res.data.msg);
          this.getHardwareList(this.hardMode);
          this.hardDialog = false;
          this.submitLoading = false;
        } catch (error) {
          this.$message.error(error.data.msg);
          this.submitLoading = false;
        }
      } else {
        console.log("Errors: ", validateResult);
        this.$message.warning(firstError);
      }
    },
    async delHard() {
      try {
        const res = await delHardware(this.hardMode, { id: this.delId });
        this.$message.success(res.data.msg);
        this.delVisible = false;
        this.getHardwareList(this.hardMode);
      } catch (error) {
        this.$message.error(error.data.msg);
      }
    },
    /* 硬件配置 end */

    /* 灵活机型 */
    async onChange(row) {
      try {
        const res = await changePackageShow({
          id: row.id,
          hidden: row.hidden,
        });
        this.$message.success(res.data.msg);
        this.getHardwareList("package");
      } catch (error) {
        this.$message.error(error.data.msg);
        this.getHardwareList("package");
      }
    },
    changeRange(e) {
      if (e[e.length - 1] === 0) {
        this.flexForm.optional_memory_id = [0];
        this.flexForm.mem_max = undefined;
        this.flexForm.mem_max_num = undefined;
      } else {
        this.flexForm.optional_memory_id = e.filter((item) => item !== 0);
      }
    },
    changeMemRange(e) {
      if (e[e.length - 1] === 0) {
        this.flexForm.optional_disk_id = [0];
        this.flexForm.disk_max_num = undefined;
      } else {
        this.flexForm.optional_disk_id = e.filter((item) => item !== 0);
      }
    },
    addFlex() {
      this.optType = "add";
      this.comTitle = `${lang.order_text53}${lang.model_specs}`;
      this.flexModel = true;
      const price = this.cycleData
        .reduce((all, cur) => {
          all.push({
            id: cur.id,
            name: cur.name,
            price: "",
          });
          return all;
        }, [])
        .sort((a, b) => {
          return a.id - b.id;
        });
      this.flexForm = {
        name: "",
        order: 0,
        group_id: "",
        cpu_option_id: "",
        cpu_num: undefined,
        mem_option_id: "",
        mem_num: undefined,
        disk_option_id: "",
        disk_num: undefined,
        bw: undefined,
        ip_num: undefined,
        description: "",
        optional_memory_id: [0],
        mem_max: undefined,
        mem_max_num: undefined,
        optional_disk_id: [0],
        disk_max_num: undefined,
        price,
      };
    },
    async editFlex(row) {
      this.comTitle = `${lang.edit}${lang.model_specs}`;
      this.optType = "update";
      const res = await getHardwareDetails("package", { id: row.id });
      temp = res.data.data;
      this.delId = row.id;
      const price = temp.duration
        .reduce((all, cur) => {
          all.push({
            id: cur.id,
            name: cur.name,
            price: cur.price,
          });
          return all;
        }, [])
        .sort((a, b) => {
          return a.id - b.id;
        });
      if (temp.optional_memory_id.length === 0) {
        temp.optional_memory_id.push(0);
        temp.mem_max = undefined;
        temp.mem_max_num = undefined;
      }
      if (temp.optional_disk_id.length === 0) {
        temp.optional_disk_id.push(0);
        temp.disk_max_num = undefined;
      }
      Object.assign(this.flexForm, temp);
      this.flexForm.price = price;
      this.flexModel = true;
    },
    async submitFlex({ validateResult, firstError }) {
      if (validateResult === true) {
        try {
          const params = JSON.parse(JSON.stringify(this.flexForm));
          params.price = params.price.reduce((all, cur) => {
            cur.price && (all[cur.id] = cur.price);
            return all;
          }, {});
          params.product_id = this.id;
          if (this.optType === "add") {
            delete params.id;
          }
          params.optional_memory_id = params.optional_memory_id.filter(
            (item) => item !== 0
          );
          params.optional_disk_id = params.optional_disk_id.filter(
            (item) => item !== 0
          );

          this.submitLoading = true;
          const res = await createAndUpdateHardware(
            "package",
            this.optType,
            params
          );
          this.$message.success(res.data.msg);
          this.getHardwareList("package");
          this.flexModel = false;
          this.submitLoading = false;
        } catch (error) {
          this.submitLoading = false;
          this.$message.error(error.data.msg);
        }
      } else {
        console.log("Errors: ", validateResult);
        this.$message.warning(firstError);
      }
    },
    async delFlex() {
      try {
        const res = await delHardware("package", { id: this.delId });
        this.$message.success(res.data.msg);
        this.delVisible = false;
        this.getHardwareList("package");
      } catch (error) {
        this.$message.error(error.data.msg);
      }
    },
    /* 灵活机型 end */

    async autoFill(name, data) {
      try {
        const price = JSON.parse(JSON.stringify(data)).reduce((all, cur) => {
          if (cur.price) {
            all[cur.id] = cur.price;
          }
          return all;
        }, {});
        const params = {
          product_id: this.id,
          price,
        };
        const res = await fillDurationRatio(params);
        const fillPrice = res.data.data.list;
        this[name].price = this[name].price.map((item) => {
          item.price = fillPrice[item.id];
          return item;
        });
      } catch (error) {
        this.$message.error(error.data.msg);
      }
    },
    changeBw(e, val) {
      setTimeout(() => {
        this.bwValidator = this.$refs[val].errorClasses === "t-is-success";
      }, 0);
    },
    async changeSort(e) {
      try {
        this.systemGroup = e.newData;
        const image_group_order = e.newData.reduce((all, cur) => {
          all.push(cur.id);
          return all;
        }, []);
        const res = await changeImageGroup({ image_group_order });
        this.$message.success(res.data.msg);
        this.getGroup();
      } catch (error) {
        this.$message.error(error.data.msg);
      }
    },
    // 切换选项卡
    changeTab(e) {
      this.allStatus = false;
      this.backAllStatus = false;
      switch (e) {
        case "duration":
          this.getDurationList();
          break;
        case "data_center":
          this.getDataList();
          this.getCountryList();
          this.chooseData();
          this.getDurationList();
          break;
        case "model":
          this.getModelList();
          this.getDurationList();
          break;
        case "hardware":
          const temp = JSON.parse(JSON.stringify(this.cpu_columns));
          temp[0].title = lang.memory;
          this.memory_columns = temp;
          temp[0].title = lang.disk;
          this.disk_columns = temp;
          this.getHardwareList("cpu");
          this.getHardwareList("memory");
          this.getHardwareList("disk");
          break;
        case "flexible":
          this.getHardwareList("package");
          this.getHardwareList("cpu");
          this.getHardwareList("memory");
          this.getHardwareList("disk");
          break;
        case "limit":
          this.getConfigLimitList();
          this.getModelList();
          this.chooseData();
          break;
        case "system":
          this.getSystemList();
          this.getGroup();
          break;
        case "other":
          this.getOtherConfig();
          break;
        default:
          break;
      }
    },
    checkLimit(val) {
      const reg = /^[0-9]*$/;
      if (reg.test(val) && val >= 0 && val <= 99999999) {
        return { result: true };
      } else {
        return {
          result: false,
          message: lang.input + "0~99999999" + lang.verify2,
          type: "warning",
        };
      }
    },
    // 处理价格
    blurPrice(val, ind) {
      let temp = String(val).match(/^\d*(\.?\d{0,2})/g)[0] || "";
      if (temp && !isNaN(Number(temp))) {
        temp = Number(temp).toFixed(2);
      }
      if (temp >= 999999) {
        this.calcForm.price[ind].price = Number(999999).toFixed(2);
      } else {
        this.calcForm.price[ind].price = temp;
      }
    },
    blurSubPrice(val, ind) {
      let temp = String(val).match(/^\d*(\.?\d{0,2})/g)[0] || "";
      if (temp && !isNaN(Number(temp))) {
        temp = Number(temp).toFixed(2);
      }
      if (temp >= 999999) {
        val = 999999.0;
        this.subForm.price[ind].price = Number(999999).toFixed(2);
      } else {
        this.subForm.price[ind].price = temp;
      }
    },
    blurHardPrice(val, ind) {
      let temp = String(val).match(/^\d*(\.?\d{0,2})/g)[0] || "";
      if (temp && !isNaN(Number(temp))) {
        temp = Number(temp).toFixed(2);
      }
      if (temp >= 999999) {
        this.hardwareForm.price[ind].price = Number(999999).toFixed(2);
      } else {
        this.hardwareForm.price[ind].price = temp;
      }
    },
    blurFlexPrice(val, ind) {
      let temp = String(val).match(/^\d*(\.?\d{0,2})/g)[0] || "";
      if (temp && !isNaN(Number(temp))) {
        temp = Number(temp).toFixed(2);
      }
      if (temp >= 999999) {
        this.flexForm.price[ind].price = Number(999999).toFixed(2);
      } else {
        this.flexForm.price[ind].price = temp;
      }
    },

    changeAdvance() {
      this.isAdvance = !this.isAdvance;
    },
    /* 配置限制 */
    /* name: cpu , data_center ,line */
    async getConfigLimitList(name) {
      try {
        this.limit_loading = true;
        const res = await getConfigLimit({
          product_id: this.id,
          type: name,
          orderby: "id",
          sort: "desc",
          page: 1,
          limit: 1000,
        });
        this.limit_list = res.data.data.list;
        this.limit_loading = false;
      } catch (error) {
        this.limit_loading = false;
        this.$message.error(err.data.msg);
      }
    },
    addLimit(name) {
      this.optType = "add";
      this.limitType = name;
      this.limitModel = true;
      this.dataForm.country_id = "";
      this.lineForm = {
        country_id: "",
        city: "",
        data_center_id: "",
        line_id: "",
        model_config_id: [],
        min_bw: "",
        max_bw: "",
        memory: [],
        min_memory: "",
        max_memory: "",
      };
      this.comTitle = `${lang.order_text53}${lang.limit}`;
    },
    editLimit(name, row) {
      this.comTitle = `${lang.edit}${lang.limit}`;
      this.limitType = name;
      this.limitModel = true;
      this.optType = "update";
      const temp = JSON.parse(JSON.stringify(row));
      temp.line_id = temp.line_id || "";
      this.lineForm = temp;
      this.limitMemoryType = row.bill_type;
    },
    changeLine(e) {
      this.limitMemoryType = this.calcSelectLine.filter(
        (item) => item.id === e
      )[0]?.bill_type;
    },
    async submitLimit({ validateResult, firstError }) {
      if (validateResult === true) {
        try {
          const params = JSON.parse(JSON.stringify(this.lineForm));
          params.product_id = this.id;
          params.type = this.limitType;
          if (this.optType === "add") {
            delete params.id;
          }
          this.submitLoading = true;
          const res = await createAndUpdateConfigLimit(this.optType, params);
          this.$message.success(res.data.msg);
          this.getConfigLimitList(this.limitType);
          this.limitModel = false;
          this.submitLoading = false;
        } catch (error) {
          this.submitLoading = false;
          this.$message.error(error.data.msg);
        }
      } else {
        this.$message.warning(firstError);
      }
    },
    async delLimit() {
      try {
        const res = await delConfigLimit({ id: this.delId });
        this.$message.success(res.data.msg);
        this.delVisible = false;
        this.getConfigLimitList();
      } catch (error) {
        this.$message.error(error.data.msg);
      }
    },
    /* 线路 */
    addLine() {
      this.lineModel = true;
      this.lineType = "add";
      this.dataForm.country_id = "";
      this.lineForm = {
        country_id: "", // 线路国家
        city: "", // 线路城市
        data_center_id: "",
        name: "",
        bill_type: "bw", // bw, flow
        bw_ip_group: "",
        defence_ip_group: "",
        ip_enable: 0, // ip开关
        defence_enable: 0, // 防护开关
        bw_data: [], // 带宽
        flow_data: [], //流量
        defence_data: [], // 防护
        ip_data: [], // ip
        order: 0,
      };
      this.lineRight = false;
    },
    async editLine(row) {
      try {
        const res = await getLineDetails({ id: row.id });
        this.lineForm = JSON.parse(JSON.stringify(res.data.data));
        this.lineType = "update";
        this.optType = "update";
        this.lineRight = false;
        this.lineModel = true;
        this.bw_ip_show = this.lineForm.bw_ip_group ? true : false;
        this.defence_ip_show = this.lineForm.defence_ip_group ? true : false;
        this.subId = row.id;
      } catch (error) {}
    },
    changeCountry() {
      this.lineForm.city = "";
      this.lineForm.data_center_id = "";
      this.lineForm.line_id = "";
    },
    changeCity() {
      this.lineForm.data_center_id = "";
      this.lineForm.line_id = "";
    },
    // 编辑线路子项
    async editSubItem(row, index, type) {
      this.subType = type;
      this.optType = "update";
      this.delSubIndex = index;
      this.lineRight = true;
      let temp = "";
      this.bwValidator = true;
      if (this.lineType === "add") {
        temp = row;
      } else {
        const res = await getLineChildDetails(type, { id: row.id });
        temp = res.data.data;
        this.delId = row.id;
      }
      setTimeout(() => {
        const price = temp.duration
          .reduce((all, cur) => {
            all.push({
              id: cur.id,
              name: cur.name,
              price: cur.price,
            });
            return all;
          }, [])
          .sort((a, b) => {
            return a.id - b.id;
          });
        Object.assign(this.subForm, temp);
        this.subForm.price = price;
        if (
          this.subForm.other_config.in_bw ||
          this.subForm.other_config.advanced_bw
        ) {
          this.isAdvance = true;
        } else {
          this.isAdvance = false;
        }
      }, 0);
    },
    // 删除线路子项
    async delSubItem() {
      try {
        this.lineRight = false;
        if (this.lineType === "add") {
          // 本地删除
          switch (this.delType) {
            case "line_bw":
              return this.lineForm.bw_data.splice(this.delSubIndex, 1);
            case "line_flow":
              return this.lineForm.flow_data.splice(this.delSubIndex, 1);
            case "line_defence":
              return this.lineForm.defence_data.splice(this.delSubIndex, 1);
            case "line_ip":
              return this.lineForm.ip_data.splice(this.delSubIndex, 1);
          }
        } else {
          // 编辑的时候删除
          const res = await delLineChild(this.delType, { id: this.delId });
          this.$message.success(res.data.msg);
          this.delVisible = false;
          // this.editLine({ id: this.subId })
          this.submitLine({ validateResult: true, firstError: "" }, false);
        }
      } catch (error) {}
    },
    // 新增线路子项
    addLineSub(type) {
      this.subType = type;
      if (this.$refs["bw-item"]) {
        this.bwValidator =
          this.$refs["bw-item"].errorClasses === "t-is-success";
      } else if (this.$refs["ip-item"]) {
        this.bwValidator =
          this.$refs["ip-item"].errorClasses === "t-is-success";
      } else {
        this.bwValidator = true;
      }
      this.optType = "add";
      this.isAdvance = false;
      if (type === "line_bw") {
        this.subForm.type = this.lineForm.bw_data[0]?.type || "radio";
      }

      this.subForm.value = "";
      this.subForm.min_value = "";
      this.subForm.max_value = "";
      this.subForm.other_config = {
        in_bw: "",
        bill_cycle: "last_30days",
      };
      this.lineRight = true;
      const price = this.cycleData
        .reduce((all, cur) => {
          all.push({
            id: cur.id,
            name: cur.name,
            price: "",
          });
          return all;
        }, [])
        .sort((a, b) => {
          return a.id - b.id;
        });
      this.subForm.price = price;
      this.bw_ip_show = false;
      this.defence_ip_show = false;
    },
    // 保存线路子项
    async submitSub({ validateResult, firstError }) {
      if (validateResult === true) {
        try {
          const params = JSON.parse(JSON.stringify(this.subForm));
          params.step = 1;
          params.product_id = this.id;
          this.submitLoading = true;
          const duration = JSON.parse(JSON.stringify(params.price));
          params.price = params.price.reduce((all, cur) => {
            cur.price && (all[cur.id] = cur.price);
            return all;
          }, {});

          // 新增的时候本地处理
          if (this.lineType === "add") {
            params.duration = duration;
            switch (this.subType) {
              case "line_bw":
                this.optType === "add"
                  ? this.lineForm.bw_data.unshift(params)
                  : this.lineForm.bw_data.splice(this.delSubIndex, 1, params);
                break;
              case "line_flow":
                this.optType === "add"
                  ? this.lineForm.flow_data.unshift(params)
                  : this.lineForm.flow_data.splice(this.delSubIndex, 1, params);
                break;
              case "line_defence":
                this.optType === "add"
                  ? this.lineForm.defence_data.unshift(params)
                  : this.lineForm.defence_data.splice(
                      this.delSubIndex,
                      1,
                      params
                    );
                break;
              case "line_ip":
                this.optType === "add"
                  ? this.lineForm.ip_data.unshift(params)
                  : this.lineForm.ip_data.splice(this.delSubIndex, 1, params);
                break;
            }
            this.submitLoading = false;
            this.lineRight = false;
            return;
          }
          // 新增：传线路id，编辑传配置id
          params.id = this.optType === "add" ? this.subId : this.delId;
          const res = await createAndUpdateLineChild(
            this.subType,
            this.optType,
            params
          );
          this.$message.success(res.data.msg);
          // this.editLine({ id: this.subId })
          this.submitLine({ validateResult: true, firstError: "" }, false);
          this.submitLoading = false;
        } catch (error) {
          this.submitLoading = false;
          this.$message.error(error.data.msg);
        }
      } else {
        if (this.$refs["bw-item"]) {
          this.bwValidator =
            this.$refs["bw-item"].errorClasses === "t-is-success";
        }
        this.$message.warning(firstError);
      }
    },

    async submitLine({ validateResult, firstError }, bol = true) {
      if (validateResult === true) {
        try {
          const params = JSON.parse(JSON.stringify(this.lineForm));
          params.product_id = this.id;
          const isAdd = params.id ? "update" : "add";
          this.submitLoading = true;
          const res = await createAndUpdateLine(isAdd, params);
          if (bol) {
            this.$message.success(res.data.msg);
            this.getDataList();
            this.lineModel = false;
          } else {
            this.editLine({ id: this.subId });
          }
          this.submitLoading = false;
        } catch (error) {
          this.submitLoading = false;
          this.$message.error(error.data.msg);
        }
      } else {
        console.log("Errors: ", validateResult);
        this.$message.warning(firstError);
      }
    },

    /* 数据中心 */
    async getDataList() {
      try {
        this.dataLoading = true;
        const res = await getDataCenter({
          product_id: this.id,
          page: 1,
          limit: 1000,
        });
        this.dataList = res.data.data.list;
        this.dataLoading = false;
      } catch (error) {
        this.dataLoading = false;
      }
    },
    // 国家列表
    async getCountryList() {
      try {
        const res = await getCountry();
        this.countryList = res.data.data.list;
      } catch (error) {}
    },
    async chooseData() {
      try {
        const res = await chooseDataCenter({
          product_id: this.id,
        });
        this.countrySelect = res.data.data.list;
        if (this.countrySelect.length === 1) {
          this.lineForm.country_id = this.countrySelect[0].id;
        }
      } catch (error) {}
    },
    changeType() {
      this.$refs.dataForm.clearValidate(["cloud_config_id"]);
    },
    addData() {
      this.optType = "add";
      this.dataModel = true;
      this.dataForm.country_id = "";
      this.dataForm.city = "";
      this.dataForm.area = "";
      this.comTitle = lang.new_create + lang.data_center;
    },
    async deleteData() {
      try {
        const res = await deleteDataCenter({ id: this.delId });
        this.$message.success(res.data.msg);
        this.delVisible = false;
        this.getDataList();
        this.chooseData();
      } catch (error) {
        this.$message.error(error.data.msg);
      }
    },
    async deleteLine() {
      try {
        const res = await delLine({ id: this.delId });
        this.$message.success(res.data.msg);
        this.delVisible = false;
        this.getDataList();
      } catch (error) {
        this.$message.error(error.data.msg);
      }
    },
    editData(row) {
      this.comTitle = lang.edit + lang.data_center;
      this.optType = "update";
      this.dataModel = true;
      const { id, country_id, city, area, order } = row;
      this.dataForm = {
        id,
        country_id,
        city,
        area,
        order,
      };
    },
    // 保存数据中心
    async submitData({ validateResult, firstError }) {
      if (validateResult === true) {
        try {
          const params = JSON.parse(JSON.stringify(this.dataForm));
          params.product_id = this.id;
          if (this.optType === "add") {
            delete params.id;
          }
          this.submitLoading = true;
          const res = await createOrUpdateDataCenter(this.optType, params);
          this.$message.success(res.data.msg);
          this.getDataList();
          this.chooseData();
          this.dataModel = false;
          this.submitLoading = false;
        } catch (error) {
          this.submitLoading = false;
          this.$message.error(error.data.msg);
        }
      } else {
        console.log("Errors: ", validateResult);
        this.$message.warning(firstError);
      }
    },
    /* 型号配置 */
    async getModelList() {
      try {
        this.modelLoading = true;
        const res = await getModel({
          product_id: this.id,
          page: 1,
          limit: 1000,
        });
        this.modelList = res.data.data.list;
        this.modelLoading = false;
      } catch (error) {
        this.modelLoading = false;
      }
    },
    addCalc(type) {
      // 固定机型
      // 添加model
      this.calcType = type;
      this.optType = "add";
      let temp_type = "";
      switch (type) {
        case "model":
          this.comTitle = `${lang.order_text53}${lang.model_specs}`;
          break;
      }
      this.calcModel = true;
      const price = this.cycleData
        .reduce((all, cur) => {
          all.push({
            id: cur.id,
            name: cur.name,
            price: "",
          });
          return all;
        }, [])
        .sort((a, b) => {
          return a.id - b.id;
        });
      this.isAdvance = false;
      this.calcForm = {
        product_id: "",
        cpuValue: "", // cpu里面的value， 提交的时候转换
        price,
        other_config: {
          advanced_cpu: "",
          cpu_limit: "",
          ipv6_num: "",
          disk_type: "",
        },
        // memory
        type: temp_type,
        value: "",
        min_value: "",
        max_value: "",
        step: "",
      };
    },
    // 编辑 model
    async editCalc(row, type) {
      this.calcType = type;
      this.optType = "update";
      this.disabledWay = true;
      this.comTitle = `${lang.edit}${lang.model_specs}`;
      this.editModel(row);
      this.isAdvance = false;
    },
    async editModel(row) {
      try {
        const res = await getModelDetails({
          id: row.id,
        });
        this.calcModel = true;
        const temp = res.data.data;
        let price = temp.duration
          .reduce((all, cur) => {
            all.push({
              id: cur.id,
              name: cur.name,
              price: cur.price,
            });
            return all;
          }, [])
          .sort((a, b) => {
            return a.id - b.id;
          });
        temp.price = price;
        delete temp.duration;
        Object.assign(this.calcForm, temp);
        this.optType = "update";
        this.calcModel = true;
      } catch (error) {}
    },
    submitCalc({ validateResult, firstError }) {
      if (validateResult === true) {
        switch (this.calcType) {
          case "model":
            return this.handlerModel();
        }
      } else {
        console.log("Errors: ", validateResult);
        this.$message.warning(firstError);
      }
    },
    async deleteModel() {
      try {
        const res = await delModel({
          id: this.delId,
        });
        this.$message.success(res.data.msg);
        this.delVisible = false;
        this.getModelList();
      } catch (error) {
        this.$message.error(error.data.msg);
      }
    },
    // 提交model
    async handlerModel() {
      try {
        const params = JSON.parse(JSON.stringify(this.calcForm));
        params.price = params.price.reduce((all, cur) => {
          cur.price && (all[cur.id] = cur.price);
          return all;
        }, {});
        params.product_id = this.id;
        if (this.optType === "add") {
          delete params.id;
        }
        this.submitLoading = true;
        const res = await createAndUpdateModel(this.optType, params);
        this.$message.success(res.data.msg);
        this.getModelList();
        this.calcModel = false;
        this.submitLoading = false;
      } catch (error) {
        this.submitLoading = false;
        this.$message.error(error.data.msg);
      }
    },
    /* 改变最大最小值：内存，系统盘和数据盘
    根据calcType来区分：memory=512， 其他 1048576
     */
    changeMin(e) {
      const num = this.calcType === "memory" ? 512 : 1048576;
      if (e * 1 >= num) {
        this.calcForm.min_value = 1;
      } else if (e * 1 >= this.calcForm.max_value * 1) {
        if (this.calcForm.max_value * 1) {
          this.calcForm.max_value = e * 1 + 1;
        }
      }
    },
    changeMax(e) {
      const num = this.calcType === "memory" ? 512 : 1048576;
      if (e * 1 === 1) {
        return (this.calcForm.max_value = 2);
      }
      if (e * 1 > num) {
        this.calcForm.max_value = num;
      } else if (e * 1 <= this.calcForm.min_value * 1 && e * 1 > 1) {
        if (this.calcForm.min_value * 1) {
          this.calcForm.min_value = e * 1 - 1;
        }
      }
    },
    changeStep(e) {
      if (e * 1 > this.calcForm.max_value * 1 - this.calcForm.min_value * 1) {
        this.calcForm.step = 1;
      }
    },
    /* 型号配置 end*/
    /* 周期相关 */
    async changeRadio() {
      try {
        const res = await getDurationRatio({
          product_id: this.id,
        });
        this.ratioData = res.data.data.list.map((item) => {
          item.ratio = item.ratio ? item.ratio * 1 : null;
          return item;
        });
        this.ratioModel = true;
      } catch (error) {
        this.$message.error(error.data.msg);
      }
    },
    async saveRatio() {
      try {
        const isAll = this.ratioData.every((item) => item.ratio);
        if (!isAll) {
          return this.$message.error(`${lang.input}${lang.mf_ratio}`);
        }
        const temp = JSON.parse(JSON.stringify(this.ratioData)).reduce(
          (all, cur) => {
            all[cur.id] = cur.ratio;
            return all;
          },
          {}
        );
        const params = {
          product_id: this.id,
          ratio: temp,
        };
        this.submitLoading = true;
        const res = await saveDurationRatio(params);
        this.submitLoading = false;
        this.ratioModel = false;
        this.$message.success(res.data.msg);
        this.getDurationList();
      } catch (error) {
        this.submitLoading = false;
        this.$message.error(error.data.msg);
      }
    },
    closeData() {
      this.dataModel = false;
    },
    async getDurationList() {
      try {
        this.loading = true;
        const res = await getDuration({
          product_id: this.id,
          page: 1,
          limit: 100,
        });
        this.cycleData = res.data.data.list;
        this.loading = false;
      } catch (error) {
        this.loading = false;
      }
    },
    addCycle() {
      this.optType = "add";
      this.comTitle = lang.add_cycle;
      this.cycleForm.name = "";
      this.cycleForm.unit = "month";
      this.cycleForm.num = "";
      this.cycleForm.price_factor = 1;
      this.cycleForm.price = null;
      this.cycleModel = true;
    },
    editCycle(row) {
      this.optType = "update";
      this.comTitle = lang.update + lang.cycle;
      this.cycleForm = JSON.parse(JSON.stringify(row));
      this.cycleModel = true;
      if (this.cycleForm.price) {
        this.cycleForm.price = this.cycleForm.price * 1;
      }
    },
    async submitCycle({ validateResult, firstError }) {
      if (validateResult === true) {
        try {
          const params = JSON.parse(JSON.stringify(this.cycleForm));
          params.product_id = this.id;
          if (this.optType === "add") {
            delete params.id;
          }
          if (!params.price_factor && params.price_factor !== 0) {
            params.price_factor = "1.00";
          }
          this.submitLoading = true;
          const res = await createAndUpdateDuration(this.optType, params);
          this.$message.success(res.data.msg);
          this.getDurationList();
          this.cycleModel = false;
          this.submitLoading = false;
        } catch (error) {
          this.submitLoading = false;
          this.$message.error(error.data.msg);
        }
      } else {
        console.log("Errors: ", validateResult);
        this.$message.warning(firstError);
      }
    },
    // 删除周期
    async deleteCycle() {
      try {
        const res = await delDuration({
          product_id: this.id,
          id: this.delId,
        });
        this.$message.success(res.data.msg);
        this.delVisible = false;
        this.getDurationList();
      } catch (error) {
        this.$message.error(error.data.msg);
      }
    },
    /* 操作系统 */
    // 系统列表
    async getSystemList() {
      try {
        this.loading = true;
        const params = JSON.parse(JSON.stringify(this.systemParams));
        params.product_id = this.id;
        const res = await getImage(params);
        this.systemList = res.data.data.list;
        this.loading = false;
      } catch (error) {
        this.loading = false;
      }
    },
    // 系统分类
    async getGroup() {
      try {
        const res = await getImageGroup({
          product_id: this.id,
          orderby: "id",
          sort: "desc",
        });
        this.systemGroup = res.data.data.list;
      } catch (error) {}
    },
    createNewSys() {
      // 新增
      this.systemModel = true;
      this.optType = "add";
      this.comTitle = `${lang.add}${lang.system}`;
      this.createSystem.image_group_id = "";
      this.createSystem.name = "";
      this.createSystem.charge = 0;
      this.createSystem.price = "";
      this.createSystem.enable = 0;
      this.createSystem.rel_image_id = "";
    },
    editSystem(row) {
      this.optType = "update";
      this.comTitle = lang.update + lang.system;
      this.createSystem = { ...row };
      this.systemModel = true;
    },
    async submitSystem({ validateResult, firstError }) {
      if (validateResult === true) {
        try {
          const params = JSON.parse(JSON.stringify(this.createSystem));
          params.product_id = this.id;
          if (this.optType === "add") {
            delete params.id;
          }
          this.submitLoading = true;
          const res = await createAndUpdateImage(this.optType, params);
          this.$message.success(res.data.msg);
          this.getSystemList();
          this.systemModel = false;
          this.submitLoading = false;
        } catch (error) {
          this.submitLoading = false;
          this.$message.error(error.data.msg);
        }
      } else {
        console.log("Errors: ", validateResult);
        this.$message.warning(firstError);
      }
    },
    // 列表修改状态
    async changeSystemStatus(row) {
      try {
        const params = JSON.parse(JSON.stringify(row));
        params.product_id = this.id;
        const res = await createAndUpdateImage("update", params);
        this.$message.success(res.data.msg);
        this.getSystemList();
      } catch (error) {}
    },
    // 拉取系统
    async refeshImageHandler() {
      try {
        this.$message.success(lang.mf_tip);
        await refreshImage({
          product_id: this.id,
        });
        this.getSystemList();
        this.getGroup();
      } catch (error) {}
    },
    // 分类管理
    classManage() {
      this.classModel = true;
      this.classParams.name = "";
      this.classParams.icon = "";
      this.optType = "add";
    },
    async submitSystemGroup({ validateResult, firstError }) {
      if (validateResult === true) {
        try {
          const params = JSON.parse(JSON.stringify(this.classParams));
          if (this.optType === "add") {
            delete params.id;
            params.product_id = this.id;
          }
          this.submitLoading = true;
          const res = await createAndUpdateImageGroup(this.optType, params);
          this.$message.success(res.data.msg);
          this.getGroup();
          this.submitLoading = false;
          this.classParams.name = "";
          this.classParams.icon = "";
          this.$refs.classForm.reset();
          this.optType = "add";
        } catch (error) {
          this.submitLoading = false;
          this.$message.error(error.data.msg);
        }
      } else {
        console.log("Errors: ", validateResult);
        this.$message.warning(firstError);
      }
    },
    editGroup(row) {
      this.optType = "update";
      this.classParams = JSON.parse(JSON.stringify(row));
    },
    async deleteGroup() {
      try {
        const res = await delImageGroup({
          id: this.delId,
        });
        this.$message.success(res.data.msg);
        this.delVisible = false;
        this.getGroup();
        this.classParams.name = "";
        this.classParams.icon = "";
        this.$refs.classForm.reset();
        this.optType = "add";
      } catch (error) {
        this.$message.error(error.data.msg);
      }
    },
    async deleteSystem() {
      try {
        const res = await delImage({
          id: this.delId,
        });
        this.$message.success(res.data.msg);
        this.delVisible = false;
        this.getSystemList();
      } catch (error) {
        this.$message.error(error.data.msg);
      }
    },
    /* 其他设置 */
    async getOtherConfig() {
      try {
        const res = await getCloudConfig({
          product_id: this.id,
        });
        this.otherForm = res.data.data;
      } catch (error) {
        this.$message.error(error.data.msg);
      }
    },
    async submitConfig({ validateResult, firstError }) {
      if (validateResult === true) {
        try {
          const params = JSON.parse(JSON.stringify(this.otherForm));
          params.product_id = this.id;
          this.submitLoading = true;
          const res = await saveCloudConfig(params);
          this.$message.success(res.data.msg);
          this.submitLoading = false;
          this.dataModel = false;
          this.getOtherConfig();
        } catch (error) {
          this.submitLoading = false;
          this.$message.error(error.data.msg);
        }
      } else {
        console.log("Errors: ", validateResult);
        this.$message.warning(firstError);
      }
    },
    /* 通用删除按钮 */
    comDel(type, row, index, mod) {
      this.hardMode = mod;
      this.delId = row.id;
      if (type === "cycle") {
        this.delTit = lang.sure_del_cycle;
      }
      this.delTit = lang.sureDelete;
      this.delType = type;
      // 新增的时候，本地删除线路子项
      if (
        this.lineType === "add" &&
        (this.subType === "line_bw" ||
          this.subType === "line_flow" ||
          this.subType === "line_defence" ||
          this.subType === "line_ip")
      ) {
        this.delSubIndex = index;
        this.delSubItem();
        return;
      }
      this.delVisible = true;
    },
    // 通用删除
    sureDelete() {
      switch (this.delType) {
        case "cycle":
          return this.deleteCycle();
        case "model":
          return this.deleteModel();
        case "memory":
          return this.deleteMemory();
        case "system": // 删除镜像
          return this.deleteSystem();
        case "group": // 删除镜像分类
          return this.deleteGroup();
        case "system_disk":
          return this.deleteStore("system_disk");
        case "data_disk":
          return this.deleteStore("data_disk");
        case "system_disk_limit":
          return this.deleteStoreLimit("system_disk_limit");
        case "data_disk_limit":
          return this.deleteStoreLimit("data_disk_limit");
        case "data":
          return this.deleteData();
        case "c_line":
          return this.deleteLine();
        case "line_bw":
        case "line_flow":
        case "line_defence":
        case "line_ip":
          return this.delSubItem();
        case "recommend":
          return this.delRecommend();
        case "limit":
          return this.delLimit();
        case "hard":
          return this.delHard();
        case "flex":
          return this.delFlex();
        default:
          return null;
      }
    },
    formatPrice(val) {
      return (val * 1).toFixed(2);
    },
  },
  created() {
    this.id = location.href.split("?")[1].split("=")[1];
    this.iconSelecet = this.iconList.reduce((all, cur) => {
      all.push({
        value: cur,
        label: `${this.host}/plugins/server/mf_dcim/template/admin/img/${cur}.svg`,
      });
      return all;
    }, []);
    // 默认拉取数据
    this.getDurationList();
  },
}).$mount(template);
