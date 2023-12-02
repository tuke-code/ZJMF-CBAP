{include file="header"}
<!-- =======内容区域======= -->
<link rel="stylesheet" href="/{$template_catalog}/template/{$themes}/css/upstream_order.css">
<div id="content" class="supplier_product" v-cloak>
  <t-card class="list-card-container">
    <ul class="common-tab">
      <li>
        <a :href="`supplier_order.htm?id=${supplier_id}`">{{lang.box_title1}}</a>
      </li>
      <li>
        <a :href="`supplier_goods.htm?id=${supplier_id}`">{{lang.product_list}}</a>
      </li>
      <li class="active">
        <a href="javascript:;">{{lang.goods_list}}</a>
      </li>
    </ul>
    <div class="common-header">
      <div></div>
      <div class="com-search">
        <t-input v-model="params.keywords" class="search-input" :placeholder="`ID、${lang.product_name}、${lang.products_token}、${lang.username}、${lang.email}、${lang.phone}`" @keyup.native.enter="seacrh" :on-clear="clearKey" clearable>
        </t-input>
        <t-icon size="20px" name="search" @click="seacrh" class="com-search-btn" />
      </div>
    </div>
    <t-table row-key="id" :data="data" size="medium" :columns="columns" :hover="hover" :loading="loading" :table-layout="tableLayout ? 'auto' : 'fixed'" :hide-sort-tips="true" @sort-change="sortChange">
      <template slot="sortIcon">
        <t-icon name="caret-down-small"></t-icon>
      </template>
      <template #client_id="{row}">
        <a :href="`client_detail.htm?client_id=${row?.client_id}`" class="aHover">
          <template>
            <span v-if="row.username">{{row.username}}</span>
            <span v-else-if="row.phone">+{{row.phone_code}}-{{row.phone}}</span>
            <span v-else="row.email">{{row.email}}</span>
          </template>
          <span v-if="row.company">({{row.company}})</span>
        </a>
      </template>
      <template #renew_amount="{row}">
        <template v-if="row.billing_cycle">
          {{currency_prefix}}&nbsp;{{row.renew_amount}}<span>/</span>{{row.billing_cycle}}
        </template>
        <template v-else>
          {{currency_prefix}}&nbsp;{{row.first_payment_amount}}/{{lang.onetime}}
        </template>
      </template>
      <template #product_name="{row}">
        <a :href="`host_detail.htm?client_id=${row?.client_id}&id=${row.id}`" class="aHover">{{row.product_name}}</a>
        <t-tag theme="default" variant="light" v-if="row.status==='Cancelled'" class="canceled">{{lang.canceled}}</t-tag>
        <t-tag theme="warning" variant="light" v-if="row.status==='Unpaid'">{{lang.Unpaid}}</t-tag>
        <t-tag theme="primary" variant="light" v-if="row.status==='Pending'">{{lang.Pending}}</t-tag>
        <t-tag theme="success" variant="light" v-if="row.status==='Active'">{{lang.Active}}</t-tag>
        <t-tag theme="danger" variant="light" v-if="row.status==='Failed'">{{lang.Failed}}</t-tag>
        <t-tag theme="default" variant="light" v-if="row.status==='Suspended'">{{lang.Suspended}}</t-tag>
        <t-tag theme="default" variant="light" v-if="row.status==='Deleted'" class="delted">{{lang.Deleted}}
        </t-tag>
      </template>
      <template #id="{row}">
        <a :href="`host_detail.htm?client_id=${row?.client_id}&id=${row.id}`" class="aHover">{{row.id}}</a>
      </template>
      <template #active_time="{row}">
        <span>{{row.active_time ===0 ? '-' : moment(row.active_time * 1000).format('YYYY/MM/DD HH:mm')}}</span>
      </template>
      <template #due_time="{row}">
        <span>{{row?.due_time ===0 ? '-' : moment(row?.due_time * 1000).format('YYYY/MM/DD HH:mm')}}</span>
      </template>
    </t-table>
    <t-pagination show-jumper :total="total" v-if="total" :current="params.page" :page-size="params.limit" :page-size-options="pageSizeOptions" :on-change="changePage" />
  </t-card>
</div>
<!-- =======页面独有======= -->
<script src="/{$template_catalog}/template/{$themes}/api/common.js"></script>
<script src="/{$template_catalog}/template/{$themes}/api/upstream.js"></script>
<script src="/{$template_catalog}/template/{$themes}/js/supplier_product.js"></script>
{include file="footer"}
