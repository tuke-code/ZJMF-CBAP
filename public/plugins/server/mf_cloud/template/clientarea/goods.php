<!-- 页面独有样式 -->
<link rel="stylesheet" href="/plugins/server/mf_cloud/template/clientarea/css/mf_cloud.css">
<div class="template">
  <!-- 自己的东西 -->
  <h1 class="tit">{{tit}}</h1>
  <div class="main-card mf-cloud" v-loading="loadingPrice && isInit">
    <el-tabs v-model="activeName" @tab-click="handleClick" class="top-tab">
      <!-- 快速配置 -->
      <el-tab-pane :label="lang.fast_config" name="fast" v-if="isUpdate ? activeName === 'fast' : showFast">
        <div class="con">
          <p class="com-tit">{{lang.basic_config}}</p>
          <el-form :model="params" :rules="rules" ref="orderForm" label-position="left" label-width="100px" hide-required-asterisk class="fast-form">
            <el-form-item :label="lang.common_cloud_label1">
              <el-tabs v-model="country" @tab-click="changeCountry" :class="{hide: dataList.length === 1}">
                <el-tab-pane :label="item.name" :name="String(item.id)" v-for="item in dataList" :key="item.id">
                  <el-radio-group v-model="city" @input="changeCity($event,item.city)">
                    <el-radio-button :label="c.name" v-for="(c,cInd) in item.city" :key="cInd">
                    </el-radio-button>
                  </el-radio-group>
                </el-tab-pane>
              </el-tabs>
              <p class="s-tip">{{lang.mf_tip1}}&nbsp;<span>{{lang.mf_tip2}}</span>{{lang.mf_tip3}}</p>
            </el-form-item>
            <!-- 实例 -->
            <el-form-item :label="lang.cloud_menu_1">
              <div class="cloud-box" v-if="recommendList.length > 0">
                <div class="cloud-item" :class="{active: index=== cloudIndex}" v-for="(item,index) in recommendList" :key="index" @click="changeRecommend(item,index)">
                  <div class="top">
                    {{item.name}}<span class="des">（{{item.description}}）</span>
                  </div>
                  <div class="info">
                    <p><span class="name">{{lang.mf_specs}}：</span>{{item.cpu}}{{lang.mf_cores}}{{item.memory}}G
                    </p>
                    <p>
                      <span class="name">{{lang.mf_system}}：</span>{{item.system_disk_size}}GB<span v-if="item.system_disk_type">，{{item.system_disk_type}}</span>
                    </p>
                    <p v-if="item.data_disk_size * 1">
                      <span class="name">{{lang.common_cloud_text1}}：</span>{{item.data_disk_size}}GB<span v-if="item.data_disk_type">，{{item.data_disk_type}}</span>
                    </p>
                    <p v-if="item.bw"><span class="name">{{lang.mf_bw}}：</span>{{item.bw}}M</p>
                    <template v-else>
                      <p v-if="item.flow"><span class="name">{{lang.mf_flow}}：</span>{{item.flow}}GB</p>
                      <p v-if="item.flow===0"><span class="name">{{lang.mf_flow}}：</span>{{lang.mf_tip28}}</p>
                    </template>
                    <p>
                      <span class="name">{{lang.network_type}}：</span> {{item.network_type === 'normal' ? lang.mf_normal : lang.mf_vpc}}
                    </p>
                    <p v-if="item.peak_defence">
                      <span class="name">{{lang.peak_defence}}：</span>{{ item.peak_defence + 'G'}}
                    </p>
                  </div>
                </div>
              </div>
              <div class="empty" v-else>
                {{lang.mf_tip9}}
              </div>

            </el-form-item>
            <!-- 镜像 -->
            <el-form-item :label="lang.cloud_menu_5" class="image" id="image">
              <div class="image-box">
                <div class="image-ul">
                  <div class="image-item" v-for="(item,index) in calcImageList" :key="item.id" :class="{active: curImage===index}" @click="changeImage(item,index)" @mouseenter="mouseenter(index)" @mouseleave="hover = false">
                    <img :src="`/plugins/server/mf_cloud/template/clientarea/img/mf_cloud/${item.icon}.svg`" alt="" class="icon" />
                    <div class="r-info">
                      <p class="name">{{item.name}}</p>
                      <p class="version">{{curImageId === item.id ? version:
                              lang.choose_version}}
                      </p>
                    </div>
                    <div class="version-select" v-show="(curImage === index) && hover">
                      <div class="v-item" :class="{active: ver.id === params.image_id}" v-for="(ver,v) in item.image" :key="ver.id" @click="chooseVersion(ver,item.id)">
                        <el-popover placement="right" trigger="hover" popper-class="image-pup" :disabled="ver.name.length < 20" :content="ver.name">
                          <span slot="reference">{{ver.name}}</span>
                        </el-popover>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="empty-image" v-if="calcImageList.length === 4 && isHide" @click="isHide = false">
                  <i class="el-icon-arrow-down"></i>
                </div>
              </div>
              <p class="s-tip" v-if="imageName">{{imageName && (imageName.indexOf('Win') !== -1 ? lang.mf_tip26 :
                        lang.mf_tip27)}}
              </p>
              <span class="error-tip" v-show="showImage">{{lang.mf_tip6}}</span>
            </el-form-item>
            <!-- 登录方式 -->
            <el-form-item :label="lang.login_way">
              <el-radio-group v-model="login_way">
                <el-radio-button :label="lang.auto_create"></el-radio-button>
              </el-radio-group>
              <p class="s-tip">{{lang.mf_tip5}}</p>
            </el-form-item>
          </el-form>
        </div>
      </el-tab-pane>
      <!-- 自定义配置 -->
      <el-tab-pane :label="lang.custom_config" name="custom" :lazy="true" v-if="isUpdate ? activeName === 'custom' :  true">
        <div class="con">
          <p class="com-tit">{{lang.basic_config}}</p>
          <el-form :model="params" :rules="rules" ref="orderForm" label-position="left" label-width="100px" hide-required-asterisk>
            <el-form-item :label="lang.common_cloud_label1">
              <el-tabs v-model="country" @tab-click="changeCountry" :class="{hide: dataList.length === 1}">
                <el-tab-pane :label="item.name" :name="String(item.id)" v-for="item in dataList" :key="item.id">
                  <el-radio-group v-model="city" @input="changeCity($event,item.city)">
                    <el-radio-button :label="c.name" v-for="(c,cInd) in item.city" :key="cInd">
                    </el-radio-button>
                  </el-radio-group>
                </el-tab-pane>
              </el-tabs>
              <p class="s-tip">{{lang.mf_tip1}}&nbsp;<span>{{lang.mf_tip2}}</span>{{lang.mf_tip3}}</p>
            </el-form-item>
            <!-- 可用区 -->
            <el-form-item :label="lang.usable_area">
              <el-radio-group v-model="area_name" @input="changeArea">
                <el-radio-button :label="c.name" v-for="(c,cInd) in calcAreaList" :key="cInd">
                </el-radio-button>
              </el-radio-group>
              <p class="s-tip">{{lang.mf_tip10}}</p>
            </el-form-item>
            <p class="com-tit">{{lang.cloud_config}}</p>
            <!-- cpu -->
            <el-form-item label="CPU">
              <el-radio-group v-model="cpuName" @input="changeCpu">
                <el-radio-button :label="c.value + lang.mf_cores" v-for="(c,cInd) in cpuList" :key="cInd">
                </el-radio-button>
              </el-radio-group>
            </el-form-item>
            <!-- 内存 -->
            <el-form-item :label="lang.cloud_memery" v-if="memoryList.length > 0  && activeName ==='custom'" class="move">
              <!-- 单选 -->
              <el-radio-group v-model="memoryName" v-if="memoryType" @input="changeMemory">
                <el-radio-button :label="c.value + 'G'" v-for="(c,cInd) in calaMemoryList" :key="cInd" :class="{'com-dis': c.disabled}">
                </el-radio-button>
              </el-radio-group>
              <!-- 拖动框 -->
              <template v-else>
                <el-tooltip effect="light" :content="lang.mf_range+ memoryTip" placement="top-end">
                  <el-slider v-model="params.memory" show-input :show-tooltip="false" :min="calaMemoryList[0] * 1" :max="calaMemoryList[calaMemoryList.length -1] * 1" :show-stops="false" @change="changeMem">
                  </el-slider>
                </el-tooltip>
              </template>
              <span class="unit" v-if="!memoryType">GB</span>
            </el-form-item>
            <el-form-item label=" " v-if="memoryList.length > 0 && memoryList[0].type !== 'radio'">
              <div class="marks">
                <span class="item" v-for="(item,index) in Object.keys(memMarks)">{{memMarks[item]}}GB</span>
              </div>
            </el-form-item>

            <!-- 镜像 -->
            <el-form-item :label="lang.cloud_menu_5" class="image" id="image1">
              <div class="image-box">
                <div class="image-ul">
                  <div class="image-item" v-for="(item,index) in calcImageList" :key="item.id" :class="{active: curImage===index}" @click="changeImage(item,index)" @mouseenter="mouseenter(index)" @mouseleave="hover = false">
                    <img :src="`/plugins/server/mf_cloud/template/clientarea/img/mf_cloud/${item.icon}.svg`" alt="" class="icon" />
                    <div class="r-info">
                      <p class="name">{{item.name}}</p>
                      <p class="version">{{curImageId === item.id ? version:
                              lang.choose_version}}
                      </p>
                    </div>
                    <div class="version-select" v-show="(curImage === index) && hover">
                      <div class="v-item" :class="{active: ver.id === params.image_id}" v-for="(ver,v) in item.image" :key="ver.id" @click="chooseVersion(ver,item.id)">
                        <el-popover placement="right" trigger="hover" popper-class="image-pup" :disabled="ver.name.length < 20" :content="ver.name">
                          <span slot="reference">{{ver.name}}</span>
                        </el-popover>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="empty-image" v-if="calcImageList.length === 4 && isHide" @click="isHide = false">
                  <i class="el-icon-arrow-down"></i>
                </div>
              </div>
              <p class="s-tip" v-if="imageName">{{imageName && (imageName.indexOf('Win') !== -1 ? lang.mf_tip26 :
                        lang.mf_tip27)}}
              </p>
              <span class="error-tip" v-show="showImage">{{lang.mf_tip6}}</span>
            </el-form-item>
            <!-- 存储 -->
            <el-form-item :label="lang.cloud_menu_3" v-if="activeName === 'custom'" class="store-item">
              <el-table :data="storeList" style="width: 100%" :row-class-name="tableRowClassName">
                <el-table-column prop="name" :label="lang.mf_purpose" width="190">
                </el-table-column>
                <!-- 磁盘类型 -->
                <el-table-column prop="disk_type" :label="lang.disk_type">
                  <template slot-scope="{row}">
                    <template v-if="row.index === 0">
                      <el-select v-model="params.system_disk.disk_type" :placeholder="lang.placeholder_pre2" v-if="params.system_disk" :disabled="systemType.length === 1 && systemType[0].value === ''">
                        <el-option v-for="item in systemType" :key="item.value" :label="item.label" :value="item.value">
                        </el-option>
                      </el-select>
                    </template>
                    <template v-else>
                      <el-select v-model="params.data_disk[row.index - 1].disk_type" :disabled="dataType.length === 1 && dataType[0].value === ''" :placeholder="lang.placeholder_pre2" @change="changeDataDisk($event, row.index)" v-if="params.data_disk && params.data_disk.length > 0">
                        <el-option v-for="item in dataType" :key="item.value" :label="item.label" :value="item.value">
                        </el-option>
                      </el-select>
                    </template>
                  </template>
                </el-table-column>
                <!-- 磁盘容量 -->
                <el-table-column prop="size" :label="lang.disk_size">
                  <template slot-scope="{row}">
                    <template v-if="row.type==='radio'">
                      <template v-if="row.index === 0">
                        <!-- 系统盘 -->
                        <el-select v-model="params.system_disk.size" :placeholder="lang.placeholder_pre2" v-if="params.system_disk" @change="getCycleList">
                          <el-option v-for="item in systemNum" :key="item.value" :label="item.label" :value="item.value">
                          </el-option>
                        </el-select>
                      </template>
                      <!-- 数据盘 -->
                      <template v-else>
                        <el-select v-model="params.data_disk[row.index - 1].size" :placeholder="lang.placeholder_pre2" v-if="params.data_disk && params.data_disk.length > 0" @change="getCycleList">
                          <el-option v-for="item in dataNumObj[params.data_disk[row.index - 1].disk_type]" :key="item.value" :label="item.label" :value="item.value">
                          </el-option>
                        </el-select>
                      </template>
                    </template>
                    <template v-else>
                      <!-- 存储是范围时 -->
                      <template v-if="row.index === 0">
                        <el-tooltip effect="light" :content="lang.mf_range + systemRangTip[params.system_disk.disk_type]" placement="top">
                          <el-input-number v-model="params.system_disk.size" :min="row.min" :max="row.max" @change="changeSysNum">
                          </el-input-number>
                        </el-tooltip>
                      </template>
                      <template v-if="row.index !== 0 && params.data_disk.length > 0 ">
                        <el-tooltip effect="light" :content="lang.mf_range + dataRangTip[params.data_disk[row.index - 1].disk_type]" placement="top">
                          <el-input-number v-model="params.data_disk[row.index - 1].size" :min="row.min" :max="row.max" @change="changeDataNum($event,row.index)">
                          </el-input-number>
                        </el-tooltip>
                      </template>
                    </template>
                    GB
                  </template>
                </el-table-column>
                <el-table-column :label="lang.file_opt" width="100">
                  <template slot-scope="{row}">
                    <span v-if="row.index + 1 > 1" class="del-data" @click="delDataDisk(row.index)">{{lang.referral_btn4}}</span>
                  </template>
                </el-table-column>
              </el-table>
              <div class="store" v-if="this.storeList.length < 17">
                <span class="add-disk" @click="addDataDisk">
                  <i class="el-icon-circle-plus-outline"></i>
                  <span class="txt">{{lang.mf_add_disk}}</span>&nbsp;
                </span>
                {{lang.mf_tip11}}<span class="txt num">{{17 - this.storeList.length}}</span>{{lang.mf_tip12}}
              </div>
            </el-form-item>
            <!-- 网络配置 -->
            <p class="com-tit">{{lang.net_config}}</p>
            <el-form-item :label="lang.network_type">
              <el-radio-group v-model="netName" @change="changeNet">
                <el-radio-button :label="lang.mf_normal" v-if="baseConfig.support_normal_network"></el-radio-button>
                <el-radio-button :label="lang.mf_vpc" v-if="baseConfig.support_vpc_network"></el-radio-button>
              </el-radio-group>
            </el-form-item>
            <template v-if="params.network_type === 'vpc'">
              <el-form-item :label="lang.cloud_menu_2">
                <div class="choose-net">
                  <el-select v-model="params.vpc.id" filterable class="w-select" :placeholder="`${lang.placeholder_pre2}${lang.cloud_menu_2}`">
                    <el-option value="" :label="lang.create_network"></el-option>
                    <el-option v-for="item in vpcList" :key="item.id" :label="item.name" :value="item.id">
                    </el-option>
                  </el-select>
                  <i class="el-icon-loading" v-show="vpcLoading"></i>
                  <i class="el-icon-refresh" class="refresh" @click="getVpcList" v-show="!vpcLoading"></i>
                </div>
                <div class="vpc" v-if="params.vpc.id === ''">
                  <el-select v-model="plan_way" class="w-select">
                    <el-option :value="0" :label="lang.auto_plan"></el-option>
                    <el-option :value="1" :label="lang.custom"></el-option>
                  </el-select>
                  <!-- 自定义vpc -->
                  <div class="custom" v-if="plan_way === 1">
                    <el-select v-model="vpc_ips.vpc1.value" @change="changeVpcIp">
                      <el-option v-for="item in vpc_ips.vpc1.select" :key="item" :label="item" :value="item">
                      </el-option>
                    </el-select>
                    <span>·</span>
                    <el-tooltip :content="vpc_ips.vpc1.tips" placement="top" effect="light">
                      <el-input-number :disabled="vpc_ips.vpc1.value === 192" v-model="vpc_ips.vpc2" :step="1" :controls="false" :min="vpc_ips.min" :max="vpc_ips.max"></el-input-number>
                    </el-tooltip>
                    <span class="mr-5 ml-5">·</span>
                    <el-tooltip :content="vpc_ips.vpc3Tips" placement="top" effect="light" :disabled="!vpc_ips.vpc3Tips">
                      <el-input-number :disabled="vpc_ips.vpc6.value === 16" @blur="changeVpc3" v-model="vpc_ips.vpc3" :step="1" :controls="false" :min="0" :max="255">
                      </el-input-number>
                    </el-tooltip>
                    <span class="mr-5 ml-5">·</span>
                    <el-tooltip :content="vpc_ips.vpc4Tips" placement="top" effect="light" :disabled="!vpc_ips.vpc4Tips">
                      <el-input-number :disabled="vpc_ips.vpc6.value < 25" @blur="changeVpc4" v-model="vpc_ips.vpc4" :step="1" :controls="false" :min="0" :max="255">
                      </el-input-number>
                    </el-tooltip>
                    <span class="mr-5 ml-5">/</span>
                    <el-select v-model="vpc_ips.vpc6.value" style="width: 70px" @change="changeVpcMask">
                      <el-option v-for="item in vpc_ips.vpc6.select" :key="item" :label="item" :value="item">
                      </el-option>
                    </el-select>
                  </div>
                </div>

              </el-form-item>
              <el-form-item :label="lang.common_cloud_title3">
                {{lang.mf_tip23}}
              </el-form-item>
            </template>
            <!-- 线路 -->
            <el-form-item :label="lang.mf_line">
              <el-radio-group v-model="lineName" @input="changeLine">
                <el-radio-button :label="c.name" v-for="(c,cInd) in lineList" :key="cInd">
                </el-radio-button>
              </el-radio-group>
            </el-form-item>
            <!-- 带宽 -->
            <el-form-item :label="lang.mf_bw" v-if="lineDetail.bill_type === 'bw' && lineDetail.bw.length > 0">
              <!-- 单选 -->
              <el-radio-group v-model="bwName" v-if="lineDetail.bw[0].type === 'radio'" @input="changeBw">
                <el-radio-button :label="c.value + 'M'" v-for="(c,cInd) in lineDetail.bw" :key="cInd">
                </el-radio-button>
              </el-radio-group>
              <!-- 拖动框 -->
              <el-tooltip effect="light" v-else :content="lang.mf_range + bwTip" placement="top-end">
                <el-slider v-model="params.bw" show-input :step="1" :min="lineDetail.bw[0].min_value" :max="lineDetail.bw[lineDetail.bw.length - 1].max_value" @change="changeBwNum">
                </el-slider>
              </el-tooltip>
            </el-form-item>
            <el-form-item label=" " v-if="lineDetail.bw && lineDetail.bw[0].type !== 'radio'">
              <div class="marks">
                <span class="item" v-for="(item,index) in Object.keys(bwMarks)">{{bwMarks[item]}}Mbps</span>
              </div>
            </el-form-item>
            <!-- 流量 -->
            <el-form-item :label="lang.mf_flow" v-if="lineDetail.bill_type === 'flow' && lineDetail.flow.length > 0">
              <el-radio-group v-model="flowName" @input="changeFlow">
                <el-radio-button :label="c.value > 0 ? (c.value + 'G') : lang.mf_tip28" v-for="(c,cInd) in lineDetail.flow" :key="cInd">
                </el-radio-button>
              </el-radio-group>
            </el-form-item>
            <!-- 防御 -->
            <el-form-item :label="lang.mf_defense" v-if="lineDetail.defence && lineDetail.defence.length >0">
              <el-radio-group v-model="defenseName">
                <el-radio-button :label="c.value + 'G'" v-for="(c,cInd) in lineDetail.defence" :key="cInd" @click.native="chooseDefence($event,c)">
                </el-radio-button>
              </el-radio-group>
            </el-form-item>

            <template v-if="isLogin">
              <!-- 安全组 -->
              <p class="com-tit">{{lang.security_group}}</p>
              <el-form-item :label="lang.security_group">
                <el-radio-group v-model="groupName" @change="changeGroup">
                  <el-radio-button :label="lang.create_group"></el-radio-button>
                  <el-radio-button :label="lang.exist_group"></el-radio-button>
                </el-radio-group>
                <div class="group-box">
                  <!-- 新增安全组 -->
                  <div class="create-group" v-if="groupName === lang.create_group">
                    <p class="top">
                      <span class="tit">{{lang.mf_tip21}}</span>
                      <span>{{lang.mf_tip22}}</span>
                    </p>
                    <div class="select">
                      <el-checkbox v-model="item.check" v-for="(item,index) in groupSelect" :key="index">
                        {{item.name}}
                      </el-checkbox>
                    </div>
                  </div>
                  <!-- 选择已有安全组 -->
                  <el-form-item :label="lang.security_group" v-if="groupName === lang.exist_group">
                    <el-select v-model="params.security_group_id" filterable :placeholder="`${lang.placeholder_pre2}${lang.security_group}`">
                      <el-option v-for="item in groupList" :key="item.id" :label="item.name" :value="item.id">
                      </el-option>
                    </el-select>
                    <i class="el-icon-loading" v-show="groupLoading"></i>
                    <i class="el-icon-refresh" class="refresh" @click="getGroup" v-show="!groupLoading"></i>
                  </el-form-item>
                </div>
                <p class="s-tip" v-if="groupName === lang.create_group">{{lang.mf_tip24}}</p>
              </el-form-item>
              <!-- 其他配置 -->
              <p class="com-tit" id="ssh">{{lang.other_config}}</p>
              <el-form-item :label="lang.login_way">
                <el-radio-group v-model="login_way" @change="changeLogin">
                  <el-radio-button :label="lang.security_tab1" v-if="baseConfig.support_ssh_key"></el-radio-button>
                  <el-radio-button :label="lang.set_pas"></el-radio-button>
                  <el-radio-button :label="lang.auto_create"></el-radio-button>
                </el-radio-group>
                <p class="s-tip" v-if="login_way === lang.auto_create">{{lang.mf_tip5}}</p>
                <div class="login-box" v-else>
                  <el-form-item :label="lang.login_name">
                    <el-input v-model="root_name" disabled></el-input>
                  </el-form-item>
                  <!-- ssh -->
                  <template v-if="login_way === lang.security_tab1">
                    <el-form-item :label="lang.ssh_key">
                      <el-select v-model="params.ssh_key_id" :placeholder="`${lang.placeholder_pre2}${lang.ssh_key}`">
                        <el-option v-for="item in sshList" :key="item.id" :label="item.name" :value="item.id">
                        </el-option>
                      </el-select>
                      <i class="el-icon-loading" v-show="sshLoading"></i>
                      <i class="el-icon-refresh" class="refresh" @click="getSsh" v-show="!sshLoading"></i>
                    </el-form-item>
                    <el-form-item v-show="showSsh" label=' ' class="empty-item">
                      <span class="error-tip">{{lang.placeholder_pre2}}{{lang.security_tab1}}</span>
                    </el-form-item>
                    <el-form-item label=" ">
                      <p class="s-tip jump-box">{{lang.mf_tip17}}&nbsp;&nbsp;
                        <a href="security_ssh.html" target="_blank" class="add-ssh">
                          {{lang.mf_tip18}}
                          <img src="/plugins/server/mf_cloud/template/clientarea/img/common/jump.svg" alt="" class="icon">
                        </a>
                      </p>
                    </el-form-item>
                  </template>
                  <!-- 密码 -->
                  <template v-if="login_way === lang.set_pas">
                    <el-form-item :label="lang.login_password" prop="password">
                      <el-popover placement="right" trigger="click" popper-class="test-pup">
                        <div class="test-password">
                          <div class="t-item">
                            <span class="dot" v-show="!hasLen"></span>
                            <i class="el-icon-check" v-show="hasLen"></i>
                            {{lang.mf_val1}}
                          </div>
                          <div class="t-item">
                            <!-- 指定范围 -->
                            <span class="dot" v-show="hasAppoint"></span>
                            <i class="el-icon-check" v-show="!hasAppoint"></i>
                            {{lang.mf_val2}}
                          </div>
                          <div class="t-item">
                            <span class="dot" v-show="hasLine"></span>
                            <i class="el-icon-check" v-show="!hasLine"></i>
                            {{lang.mf_val3}}
                          </div>
                          <div class="t-item">
                            <span class="dot" v-show="!hasMust"></span>
                            <i class="el-icon-check" v-show="hasMust"></i>
                            {{lang.mf_val4}}
                          </div>
                        </div>
                        <el-input v-model="params.password" type="password" show-password @input="changeInput" :placeholder="`${lang.placeholder_pre1}${lang.login_password}`" slot="reference">
                        </el-input>
                      </el-popover>
                      <span class="error-tip" v-show="showPas">{{lang.mf_tip20}}</span>
                    </el-form-item>
                    <el-form-item :label="lang.sure_password">
                      <el-input v-model="params.re_password" type="password" show-password :placeholder="`${lang.placeholder_pre1}${lang.sure_password}`">
                      </el-input>
                      <span class="error-tip" v-show="showRepass">{{lang.mf_tip19}}</span>
                    </el-form-item>

                  </template>
                </div>
              </el-form-item>
              <el-form-item class="optional">
                <template slot="label">
                  {{lang.cloud_name}}
                  <el-tooltip class="item" effect="light" :content="lang.mf_tip14" placement="top">
                    <i class="el-icon-warning-outline"></i>
                  </el-tooltip>
                </template>
                <el-input v-model="params.notes" :placeholder="lang.mf_tip15"></el-input>
              </el-form-item>
              <el-form-item :label="lang.auto_renew" class="renew">
                <el-checkbox v-model="params.auto_renew">{{lang.open_auto_renew}}</el-checkbox>
              </el-form-item>
            </template>
          </el-form>
        </div>
      </el-tab-pane>
    </el-tabs>
  </div>
  <!-- 底部 -->
  <div class="f-order">
    <div class="l-empty"></div>
    <div class="el-main">
      <div class="main-card">
        <div class="left">
          <div class="time">
            <span class="l-txt">{{lang.mf_time}}</span>
            <el-select v-model="params.duration_id" class="duration-select" popper-class="duration-pup" :visible-arrow="false" :placeholder="`${lang.placeholder_pre2}${lang.mf_duration}`" @change="changeDuration">
              <el-option v-for="item in cycleList" :key="item.id" :label="item.name" :value="item.id">
                <span class="txt">{{item.name}}</span>
                <span class="tip" v-if="item.discount">{{item.discount}}{{lang.mf_tip25}}</span>
              </el-option>
            </el-select>
          </div>
          <div class="num">
            <span class="l-txt">{{lang.shoppingCar_goodsNums}}</span>
            <el-input-number v-model="qty" :min="1" :max="999" @change="changQty"></el-input-number>
          </div>
        </div>
        <div class="mid">
          <el-popover placement="top" trigger="hover" popper-class="cur-content">
            <div class="content">
              <div class="tit">{{lang.mf_tip7}}</div>
              <div class="con">
                <p class="c-item">
                  <span class="l-txt">{{lang.cloud_table_head_1}}：</span>
                  {{calcArea}}
                </p>
                <p class="c-item">
                  <span class="l-txt">{{lang.network_type}}：</span>
                  {{this.params.network_type === 'normal' ? lang.mf_normal : lang.mf_vpc}}
                </p>
                <p class="c-item">
                  <span class="l-txt">{{lang.usable_area}}：</span>
                  {{calcUsable}}
                </p>
                <p class="c-item">
                  <span class="l-txt">{{lang.ip_line}}：</span>
                  {{calcLine}}
                </p>
                <p class="c-item">
                  <span class="l-txt">{{lang.cloud_menu_1}}：</span>
                  {{params.cpu}}{{lang.mf_cores}}{{params.memory}}G
                </p>
                <p class="c-item" v-if="lineType === 'bw'">
                  <span class="l-txt">{{lang.mf_bw}}：</span>
                  {{params.bw ? params.bw + 'Mbps': '--'}}
                </p>
                <p class="c-item">
                  <span class="l-txt">{{lang.cloud_menu_5}}：</span>
                  {{version || '--'}}
                </p>
                <p class="c-item" v-if="lineType === 'flow'">
                  <span class="l-txt">{{lang.mf_flow}}：</span>
                  {{params.flow ? params.flow + 'GB': (params.flow === 0 ? lang.mf_tip28 : '--')}}
                </p>
                <p class="c-item">
                  <span class="l-txt">{{lang.mf_system}}：</span>
                  {{params.system_disk?.size}}GB
                </p>
                <p class="c-item" v-if="(activeName === 'fast' && params.peak_defence ) || (lineDetail.defence && params.peak_defence)">
                  <span class="l-txt">{{lang.peak_defence}}：</span>
                  {{ params.peak_defence + 'G'}}
                </p>
                <p class="c-item" v-if="params.data_disk[0]?.size">
                  <span class="l-txt">{{lang.common_cloud_text1}}：</span>
                  {{calcDataNum}}GB
                </p>
              </div>
            </div>
            <a class="link" slot="reference">{{lang.cur_config}}</a>
          </el-popover>
          <div class="line-empty"></div>
          <el-popover placement="top" trigger="hover" popper-class="free-content">
            <div class="content">
              <div class="tit">{{lang.config_free_details}}</div>
              <div class="con">
                <p class="c-item" v-for="(item,index) in preview" :key="index">
                  <span class="l-txt">{{item.name}}：{{item.value}}</span>
                  <span class="price">{{commonData.currency_prefix}}{{item.price}}</span>
                </p>
              </div>
              <div class="bot">
                <p class="c-item" v-if="discount">
                  <span class="l-txt">{{lang.mf_discount}}：</span>
                  <span class="price">-{{commonData.currency_prefix}}{{discount}}</span>
                </p>
                <p class="c-item">
                  <span class="l-txt">{{lang.mf_total}}：</span>
                  <span class="price">{{commonData.currency_prefix}}{{(totalPrice * 1 * qty - discount *
                            1).toFixed(2)}}</span>
                </p>
              </div>
            </div>
            <a class="link" slot="reference">{{lang.config_free}}</a>
          </el-popover>
          <div class="bot-price" v-loading="loadingPrice">
            <div class="new">{{commonData.currency_prefix}}<span>{{(totalPrice * 1 * qty - discount *
                        1).toFixed(2)}}</span></div>
            <div class="old">
              <div class="show" v-if="discount">
                {{commonData.currency_prefix}}{{(totalPrice * 1 * qty).toFixed(2)}}
              </div>
              <!-- 优惠码 -->
              <!-- 未使用 -->
              <el-popover placement="top" trigger="click" popper-class="discount-pup" v-model="dis_visible" v-if="!discount">
                <div class="discount">
                  <img src="/plugins/server/mf_cloud/template/clientarea/img/common/close_icon.png" alt="" class="close" @click="dis_visible = !dis_visible">
                  <div class="code">
                    <el-input v-model="promo.promo_code" :placeholder="`${lang.placeholder_pre1}${lang.cloud_code}`"></el-input>
                    <button class="sure" @click="useDiscount">{{lang.referral_btn6}}</button>
                  </div>
                  <span class="error-tip" v-show="showErr">{{lang.mf_tip8}}</span>
                </div>
                <p class="use" slot="reference">{{lang.use_discount}}</p>
              </el-popover>
              <!-- 已使用 -->
              <div class="used" v-else>
                <span>{{promo.promo_code}}</span>
                <i class="el-icon-circle-close" @click="canclePromo"></i>
              </div>
            </div>
          </div>
        </div>
        <div class="right">
          <el-popover placement="top" trigger="hover" popper-class="cart-pup" :content="calcCartName">
            <div class="add-cart" slot="reference" @click="handlerCart">
              <img src="/plugins/server/mf_cloud/template/clientarea/img/common/cart.svg" alt="">
            </div>
          </el-popover>
          <div class="buy" @click="submitOrder">{{lang.product_buy_now}}</div>
        </div>
      </div>
    </div>

  </div>
  <el-dialog title="" :visible.sync="cartDialog" custom-class="cartDialog" :show-close="false">
    <span class="tit">{{lang.product_tip}}</span>
    <span slot="footer" class="dialog-footer">
      <el-button type="primary" @click="cartDialog = false">{{lang.product_continue}}</el-button>
      <el-button @click="goToCart">{{lang.product_settlement}}</el-button>
    </span>
  </el-dialog>
</div>
<!-- =======页面独有======= -->
<script src="/plugins/server/mf_cloud/template/clientarea/api/common.js"></script>
<script src="/plugins/server/mf_cloud/template/clientarea/api/mf_cloud.js"></script>
<script src="/plugins/server/mf_cloud/template/clientarea/utils/util.js"></script>
<script src="/plugins/server/mf_cloud/template/clientarea/js/mf_cloud.js"></script>