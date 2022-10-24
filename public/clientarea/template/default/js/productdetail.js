(function (window, undefined) {
  var old_onload = window.onload
  window.onload = function () {
    const template = document.getElementsByClassName('product_detail')[0]
    Vue.prototype.lang = window.lang
    new Vue({
      components: {
        asideMenu,
        topMenu,
        pagination,
      },
      created () {
        this.id = location.href.split('?')[1].split('=')[1]
        this.getCommonData()
        this.getList()
      },
      mounted () {
      },
      updated () {
        // // 关闭loading
        document.getElementById('mainLoading').style.display = 'none';
        document.getElementsByClassName('product_detail')[0].style.display = 'block'
      },
      destroyed () {

      },
      data () {
        return {
          id: '',
          params: {
            page: 1,
            limit: 20,
            pageSizes: [20, 50, 100],
            total: 200,
            orderby: 'id',
            sort: 'desc',
            keywords: '',
          },
          commonData: {},
          content: ''
        }
      },
      filters: {
        formateTime (time) {
          if (time && time !== 0) {
            return formateDate(time * 1000)
          } else {
            return "--"
          }
        }
      },
      methods: {
        async getList () {
          try {
            const res = await getProductDetail(this.id)
            this.$nextTick(() => {
              $('.config-box .content').html(res.data.data.content)
            })
            this.content = res.data.data.content
          } catch (error) {
            
          }
        },
        // 每页展示数改变
        sizeChange (e) {
          this.params.limit = e
          this.params.page = 1
          // 获取列表
        },
        // 当前页改变
        currentChange (e) {
          this.params.page = e

        },

        // 获取通用配置
        getCommonData () {
          getCommon().then(res => {
            if (res.data.status === 200) {
              this.commonData = res.data.data
              localStorage.setItem('common_set_before', JSON.stringify(res.data.data))
              // document.title = this.commonData.website_name + '-工单系统'
            }
          })
        }
      },

    }).$mount(template)
    typeof old_onload == 'function' && old_onload()
  };
})(window);