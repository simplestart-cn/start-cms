<template>
  <div class="filter-wrapper">
    <form-create v-model="model" :rule="rule" :option="option" @mounted="formMounted" />
    <el-button v-waves type="primary" icon="el-icon-search" size="small" @click="handleSubmit">搜索</el-button>
    <el-tooltip content="刷新" placement="top">
      <el-button v-waves type="warning" icon="el-icon-refresh" circle size="small" @click="handleReset" />
    </el-tooltip>
  </div>
</template>
<script>
import { mapActions } from 'vuex'
export default {
  name: 'OperationFilter',
  data() {
    return {
      model: {},
      option: {
        form: {
          inline: true,
          showMessage: true,
          inlineMessage: false,
          statusIcon: false,
          validateOnRuleChange: true,
          size: 'small'
        },
        submitBtn: false,
        resetBtn: false
      },
      rule: [{
        type: 'input',
        field: 'keyword',
        props: { placeholder: '动作/行为/用户名' }
      }, {
        type: 'select',
        field: 'user_id',
        options: [],
        props: { filterable: true, placeholder: '用户id' }
      }]
    }
  },
  methods: {
    ...mapActions({
      getAdmin: 'user/getAdminList'
    }),
    formMounted() {
      this.getAdmin().then(response => {
        this.model.mergeRule('user_id', {
          options: response
        })
      })
    },
    handleSubmit() {
      this.model.submit(formData => {
        this.$emit('submit', formData)
      })
    },
    handleReset() {
      this.model.resetFields()
      this.$emit('reset')
    }
  }
}
</script>
