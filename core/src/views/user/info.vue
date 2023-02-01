<template>
  <div class="app-container">
    <div>
      <div class="padding-tb-sm"><span style="display: inline-block; width: 90px; ">账号</span><span>{{ temp.account }}</span></div>
      <div class="padding-tb-sm">
        <span style="display: inline-block; width: 90px; ">头像</span>
        <el-image
          style="width: 100px; height: 100px"
          :src="temp.avatar"
          fit="cover"></el-image>
      </div>
      <div class="padding-tb-sm"><span style="display: inline-block; width: 90px; ">姓名</span><span>{{ temp.name }}</span></div>
      <div class="padding-tb-sm"><span style="display: inline-block; width: 90px; ">手机</span><span>{{ temp.mobile }}</span></div>
      <div class="padding-tb-sm"><span style="display: inline-block; width: 90px; ">邮箱</span><span>{{ temp.email }}</span></div>
      <div class="padding-tb-sm"><span style="display: inline-block; width: 90px; ">微信</span><span>{{ temp.wechat }}</span></div>
      <div class="padding-tb-sm"><span style="display: inline-block; width: 90px; ">备注</span><span>{{ temp.describe }}</span></div>
      <div class="padding-tb-sm"><span style="display: inline-block; width: 90px; ">排序</span><span>{{ temp.sort }}</span></div>
      <div class="padding-tb-sm"><span style="display: inline-block; width: 90px; ">角色权限</span><span>{{ temp.group }}</span></div>
    </div>
  </div>
</template>

<script>
import { getInfo } from '@/api/user'
import { mapGetters } from 'vuex'

export default {
  name: 'UserInfo',
  data() {
    return {
      btnLoading: false,
      temp: {},
    }
  },
  computed: {
    ...mapGetters([
      'name',
      'group'
    ])
  },
  watch: {
    temp: {
      handler(newVal, oldVal) {},
      immediate: true,
      deep: true
    }
  },
  created() {
    getInfo(this.$route.query.id).then(response => {
      response.data.password = ''
      this.temp = response.data
    })
  },
  destroyed() {

  },
  methods: {

  }
}
</script>
