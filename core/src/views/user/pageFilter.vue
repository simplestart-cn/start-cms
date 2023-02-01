<template>
  <div class="filter-wrapper">
    <form-create v-model="model" :rule="rule" :option="option" @mounted="formMounted"/>
    <el-button v-waves type="primary" icon="el-icon-search" size="small" @click="handleSubmit">搜索</el-button>
    <el-tooltip content="刷新" placement="top">
      <el-button v-waves type="warning" icon="el-icon-refresh" circle size="small" @click="handleReset" />
    </el-tooltip>
  </div>
</template>
<script>
import { mapActions } from 'vuex'
export default {
  name: 'FilterForm',
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
      rule: [
      {
        type: 'select',
        field: 'group_id',
        value: '',
        options: [],
        props: { placeholder: '部门' }
      },
      {
        type: 'select',
        field: 'role_id',
        value: '',
        options: [],
        props: { placeholder: '职位' }
      },{
        type: 'input',
        field: 'keyword',
        props: { placeholder: '姓名/手机号' }
      }]
    }
  },
  methods: {
    handleSubmit() {
      this.model.submit(formData => {
        this.$emit('submit', formData)
      })
    },
    handleReset() {
      this.model.resetFields()
      this.$emit('reset')
    },
    ...mapActions({
      getRole: 'user/getRole',
      getGroup: 'user/getGroup'
    }),
    formMounted() {
      this.getRole().then(response => {
        this.model.mergeRule('role_id', {
          options: response
        })
      })
      this.getGroup().then(response => {
        this.model.mergeRule('group_id', {
          options: response
        })
      })
    }
  }
}
</script>
