<link rel="stylesheet" href="/plugins/addon/promo_code/template/admin/css/promo_code.css" />
<!-- =======内容区域======= -->

<div id="content" class="promo-detail" v-cloak>
  <t-card class="list-card-container">
    <p class="com-h-tit">{{optTit}}</p>
    <div class="box">
      <t-form :data="formData" :rules="rules" :label-width="80" label-align="top" ref="formValidatorStatus" @submit="onSubmit">
        <t-form-item name="code" :label="lang.promo_code">
          <t-input v-model="formData.code" :disabled="optType ==='update'" :placeholder="lang.promo_tip9"></t-input>
          <span class="random" @click="randomCode" v-if="optType ==='add'">{{lang.order_random}}</span>
        </t-form-item>
        <t-form-item :label="lang.coupon_code_type" class="type-box" name="type">
          <t-form-item label-align="left" class="type">
            <t-radio-group v-model="formData.type" :options="typeOptions" :disabled="optType ==='update'" @change="changeType">
            </t-radio-group>
          </t-form-item>
          <!-- 数值 -->
          <t-form-item name="value" :label="calcLabel" label-align="left" class="type-lable" v-if="formData.type !== 'free'" :rules="formData.type === 'percent' ? [
                    { required: true, message: calcPlaceholder, type: 'error' },
                    {
                      pattern: /^\d+(\.\d{0,2})?$/, message: lang.verify17, type: 'error'
                    },
                    {
                      validator: val => val >= 0, message: lang.verify17, type: 'error'
                    },
                    {
                      validator: val =>  val <= 100, message: lang.promo_tip6, type: 'error'
                    }]: [
                    { required: true, message: calcPlaceholder, type: 'error' },
                    {
                      pattern: /^\d+(\.\d{0,2})?$/, message: lang.verify17, type: 'error'
                    },
                    {
                      validator: val => val >= 0, message: lang.verify17, type: 'error'
                    }]
                    ">
            <t-input v-model="formData.value" :placeholder="calcPlaceholder" :disabled="optType ==='update'">
            </t-input>
            <span v-if="formData.type === 'percent'" style="margin-left: 10px;">%</span>
          </t-form-item>
        </t-form-item>
        <!-- 时间选择 -->
        <div class="time-item">
          <div class="s-item">
            <t-form-item name="start_time" :label="lang.assert_time" :rules="[{ validator: checkTime}]">
              <t-date-picker enable-time-picker allow-input v-model="formData.start_time" :disable-date="{ before: moment().subtract(1, 'day').format() }" @change="changeStart" />
            </t-form-item>
            <t-select v-model="curTime" :placeholder="lang.quick_select" @change="fastClick" clearable>
              <t-option v-for="item in timeOpt" :value="item.value" :label="item.label" :key="item.value">
              </t-option>
            </t-select>
          </div>
          <div class="s-item">
            <t-form-item name="end_time" :label="lang.deadline" :rules="[{ validator: checkTime1}]">
              <t-date-picker enable-time-picker allow-input clearable v-model="formData.end_time" @focus="chooseEnd" @change="changeEnd" :disable-date="{ before: moment().subtract(1, 'day').format() }" />
            </t-form-item>
            <span v-if="formData.end_time && time_diff" class="time-diff">{{lang.all_time}}：{{time_diff}}</span>
          </div>
        </div>
        <!-- 产品选择 -->
        <t-form-item name="products" :label="lang.apply_products">
          <t-tree-select v-model="formData.products" :data="productList" multiple clearable :tree-props="treeProps" :min-collapsed-num="1">
          </t-tree-select>
          <span class="p-tip"><span class="tip">*</span>{{lang.promo_tip7}}</span>
        </t-form-item>
        <t-form-item name="need_products" :label="lang.need_products">
          <t-tree-select v-model="formData.need_products" :data="productList" multiple clearable :tree-props="treeProps" :min-collapsed-num="1">
          </t-tree-select>
          <span class="p-tip"><span class="tip">*</span>{{lang.promo_tip8}}</span>
        </t-form-item>
        <!-- 次数限制 -->
        <t-form-item name="max_times" :label="lang.max_times">
          <t-input v-model="formData.max_times" :placeholder="lang.max_times_tip"></t-input>
        </t-form-item>
        <t-form-item name="client_type" :label="lang.user_type_limit">
          <t-radio-group v-model="formData.client_type" :options="useType"></t-radio-group>
        </t-form-item>
        <!-- 功能开关 -->
        <div class="switch">
          <t-form-item name="single_user_once" :label="lang.single_user_once" label-align="left">
            <t-switch size="medium" :custom-value="[1,0]" v-model="formData.single_user_once">
            </t-switch>
            <t-tooltip :content="lang.promo_tip" :show-arrow="false" theme="light" placement="top-left" class="data-tip">
              <t-icon name="help-circle" class="pack-tip"></t-icon>
            </t-tooltip>
          </t-form-item>
          <t-form-item name="upgrade" :label="lang.upgrade_discount" label-align="left">
            <t-switch size="medium" :custom-value="[1,0]" v-model="formData.upgrade">
            </t-switch>
            <t-tooltip :content="lang.promo_tip1" :show-arrow="false" theme="light" placement="top-left" class="data-tip">
              <t-icon name="help-circle" class="pack-tip"></t-icon>
            </t-tooltip>
          </t-form-item>
          <t-form-item name="host_upgrade" :label="lang.host_upgrade" label-align="left">
            <t-switch size="medium" :custom-value="[1,0]" v-model="formData.host_upgrade">
            </t-switch>
            <t-tooltip :content="lang.promo_tip2" :show-arrow="false" theme="light" placement="top-left" class="data-tip">
              <t-icon name="help-circle" class="pack-tip"></t-icon>
            </t-tooltip>
          </t-form-item>
          <t-form-item name="renew" :label="lang.renew_discount" label-align="left">
            <t-switch size="medium" :custom-value="[1,0]" v-model="formData.renew">
            </t-switch>
            <t-tooltip :content="lang.promo_tip3" :show-arrow="false" theme="light" placement="top-left" class="data-tip">
              <t-icon name="help-circle" class="pack-tip"></t-icon>
            </t-tooltip>
          </t-form-item>
          <t-form-item name="loop" :label="lang.loop_discount" label-align="left">
            <t-switch size="medium" :custom-value="[1,0]" v-model="formData.loop">
            </t-switch>
            <t-tooltip :content="lang.promo_tip4" :show-arrow="false" theme="light" placement="top-left" class="data-tip">
              <t-icon name="help-circle" class="pack-tip"></t-icon>
            </t-tooltip>
          </t-form-item>
          <t-form-item name="cycle_limit" :label="lang.cycle_limit" label-align="left">
            <t-switch size="medium" :custom-value="[1,0]" v-model="formData.cycle_limit">
            </t-switch>
            <t-tooltip :content="lang.promo_tip5" :show-arrow="false" theme="light" placement="top-left" class="data-tip">
              <t-icon name="help-circle" class="pack-tip"></t-icon>
            </t-tooltip>
          </t-form-item>
        </div>
        <t-form-item name="cycle" label-align="left" style="margin-top: 24px;" v-if="formData.cycle_limit" :rules=" formData.cycle_limit ? rules.cycle : [{required: false}]">
          <t-checkbox-group v-model="formData.cycle" :options="cycleOpt" @change="chooseCycle">
          </t-checkbox-group>
        </t-form-item>
        <t-form-item name="notes" :label="lang.notes" style="margin-top: 24px;">
          <t-textarea :placeholder="lang.input + lang.notes" v-model="formData.notes" />
        </t-form-item>
        <div class="f-btn">
          <t-button theme="primary" type="submit" :loading="loading" style="margin-right: 15px;">{{lang.hold}}
          </t-button>
          <t-button theme="default" variant="base" @click="back">{{lang.close}}</t-button>
        </div>
      </t-form>
    </div>
  </t-card>
</div>
<script src="/plugins/addon/promo_code/template/admin/js/common/moment.min.js"></script>
<script src="/plugins/addon/promo_code/template/admin/api/promo_code.js"></script>
<script src="/plugins/addon/promo_code/template/admin/js/create_promo_code.js"></script>