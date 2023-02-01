<template>
  <div class="filter-wrapper">
    <form-create v-model="model" :rule="rule" :option="option" />
    <el-button v-waves type="primary" icon="el-icon-search" size="small" @click="handleSubmit">搜索</el-button>
    <el-tooltip content="刷新" placement="top">
      <el-button v-waves type="warning" icon="el-icon-refresh" circle size="small" @click="handleReset" />
    </el-tooltip>
  </div>
</template>
<script>
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
      rule: [{
        type: 'input',
        field: 'title',
        props: { placeholder: '名称' }
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
    }
  }
}
</script>
