{include file="header"}
<!-- =======内容区域======= -->
<link rel="stylesheet" href="/{$template_catalog}/template/{$themes}/css/addon.css">
<!-- =======内容区域======= -->
<div id="content" class="addon" v-cloak>
  <t-card class="list-card-container">
    <div class="common-header">
      <!-- <a href="https://market.idcsmart.com/shop/" target="_blank">
        <t-button class="add">
          {{lang.more_plugins}}
        </t-button>
      </a> -->
      <t-button class="add" @click="toMarket">{{lang.more_plugins}}</t-button>
      <t-badge dot :count="0" v-if="isNeedUpgrade">
        <t-button @click="getSystem" :loading="btnLoading">{{lang.sync_plugin}}</t-button>
      </t-badge>
      <t-button v-else @click="getSystem" :loading="btnLoading">{{lang.sync_plugin}}</t-button>
      <!-- <div class="com-search">
        <t-input v-model="params.keywords" class="search-input" :placeholder="`${lang.plug_name}`" @keyup.enter.native="seacrh" :on-clear="clearKey" clearable>
        </t-input>
        <t-icon size="20px" name="search" @click="seacrh" class="com-search-btn" />
      </div> -->
    </div>
    <t-table row-key="id" :data="data" size="medium" :columns="columns" :hover="hover" :loading="loading" :table-layout="tableLayout ? 'auto' : 'fixed'" @sort-change="sortChange" display-type="fixed-width" :hide-sort-tips="true">
      <template slot="sortIcon">
        <t-icon name="caret-down-small"></t-icon>
      </template>
      <template #version="{row}">
        {{row.version}}
        <t-tooltip :content="lang.upgrade_plugin" :show-arrow="false" theme="light" v-if="row.isUpdate">
          <span class="upgrade" @click="updatePlugin(row)">
            <img v-if="row.status !== 3" :src="`${urlPath}/img/upgrade.svg`" alt="">
          </span>
        </t-tooltip>
      </template>

      <template #type_name="{row}">
        {{typeObj[row.type] || lang.plugin}}
      </template>
      <template #status="{row}">
        <t-tag theme="success" class="com-status" v-if="row.status === 1" variant="light">{{lang.enable}}</t-tag>
        <t-tag theme="danger" class="com-status" v-else-if="row.status === 0" variant="light">{{lang.deactivate}}</t-tag>
        <t-tag class="com-status" v-else variant="light">{{lang.not_install}}</t-tag>
      </template>
      <template #op="{row}">
        <t-tooltip :content="enableTitle(row.status)" :show-arrow="false" theme="light">
          <a class="common-look" @click="changeStatus(row)" v-if="row.status !== 3 && authList.includes('PluginController::status')">
            <img v-if="row.status === 0" :src='`${urlPath}/img/icon/enable.png`' alt="">
            <img v-else-if="row.status === 1" :src='`${urlPath}/img/icon/disable.png`' alt="">
          </a>
        </t-tooltip>
        <t-tooltip :content="installTitle(row.status)" :show-arrow="false" theme="light">
          <a class="common-look" @click="installHandler(row)" v-if="authList.includes('PluginController::setting')">
            <img v-if="row.status === 3" :src='`${urlPath}/img/icon/install.png`' alt="">
            <img v-else-if="row.status !== 3" :src='`${urlPath}/img/icon/uninstall.png`' alt="">
          </a>
        </t-tooltip>
      </template>
    </t-table>
    <!-- <t-pagination show-jumper :total="total" :page-size="params.limit" :current="params.page"
      :page-size-options="pageSizeOptions" @change="changePage" /> -->
  </t-card>

  <!-- 卸载/安装 -->
  <t-dialog theme="warning" :header="installTip" :visible.sync="delVisible">
    <template slot="footer">
      <t-button theme="primary" @click="sureDel" :loading="submitLoading">{{lang.sure}}</t-button>
      <t-button theme="default" @click="cancelDel">{{lang.cancel}}</t-button>
    </template>
  </t-dialog>

  <!-- 启用/停用 -->
  <t-dialog theme="warning" :header="statusTip" :visible.sync="statusVisble">
    <template slot="footer">
      <t-button theme="primary" @click="sureChange" :loading="submitLoading">{{lang.sure}}</t-button>
      <t-button theme="default" @click="closeDialog">{{lang.cancel}}</t-button>
    </template>
  </t-dialog>
  <!-- 升级弹窗 -->
  <t-dialog theme="warning" :header="`${lang.sure}${lang.upgrade_plugin}？`" :visible.sync="upVisible">
    <template slot="footer">
      <t-button theme="primary" @click="sureUpgrade" :loading="upLoading">{{lang.sure}}</t-button>
      <t-button theme="default" @click="upVisible=false">{{lang.cancel}}</t-button>
    </template>
  </t-dialog>
  <!-- 同步插件 -->
  <t-dialog :header="lang.sync_plugin" :visible.sync="syncVisible" width="600" :footer="false">
    <t-table row-key="id" :data="syncPluginList" size="medium" :columns="pluginColumns" :hover="hover" :loading="btnLoading" :table-layout="tableLayout ? 'auto' : 'fixed'" @sort-change="sortChange" display-type="fixed-width" :hide-sort-tips="true" :max-height="500">
      <template slot="sortIcon">
        <t-icon name="caret-down-small"></t-icon>
      </template>
      <template #type_name="{row}">
        {{typeObj[row.type] || lang.plugin}}
      </template>
      <template #op="{row, rowIndex}">
        <t-icon name="loading" style="color: var(--td-brand-color);" v-if="curDownIndex === rowIndex"></t-icon>
        <template v-else>
          <t-tooltip :content="lang.download" :show-arrow="false" theme="light" v-if="row.downloaded * 1 === 0 || row.upgrade*1 === 1">
            <a class="common-look" @click="handlerDownload(row, rowIndex)">
              <t-icon name="arrow-down"></t-icon>
            </a>
          </t-tooltip>
          <span v-else>--</span>
        </template>
      </template>
    </t-table>
  </t-dialog>
  <!-- 同步插件 end -->
</div>
<!-- =======页面独有======= -->
<script src="/{$template_catalog}/template/{$themes}/api/addon.js"></script>
<script src="/{$template_catalog}/template/{$themes}/js/addon.js"></script>
{include file="footer"}
