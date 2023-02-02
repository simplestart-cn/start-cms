<template>
  <el-drawer ref="drawer" :with-header="false" size="50%" :before-close="handleClose" :visible.sync="formVisible" direction="rtl" custom-class="drawer-page">
    <div class="drawer-content">
      <form-create v-model="model" :rule="formRule" :option="formOption" />
    </div>
    <div class="drawer-footer" v-if="!formDisable">
      <el-row type="flex" justify="center">
        <el-button v-waves type="success" icon="el-icon-upload" @click="handleSubmit" v-auth="['official_index_create', 'official_index_update']">提交保存</el-button>
        <el-button v-waves type="default" icon="el-icon-refresh" @click="handleReset" v-auth:official_index_create>重置</el-button>
      </el-row>
    </div>
  </el-drawer>
</template>
<script>
import { getInfo, create, update } from '@/api/page1'
import { formRule, formOption } from './editForm'

export default {
  name: 'Edit',
  components: {},
  data() {
    return {
      model: {},
      modelChange: false,
      formRule: formRule,
      formOption: formOption,
      formVisible: false,
      formLoading: false,
      formDisable: false,
      temp: {}
    }
  },
  watch: {
    'model.form': {
      handler(val) {
        if (typeof val !== 'undefined') {
          this.modelChange = true
        }
      },
      immediate: true,
      deep: true
    },
    formVisible: function() {
      this.handleReset()
    },
    formDisable: function(val) {
      this.formOption.form.disabled = val
      this.model.updateOptions && this.model.updateOptions({
        form: {
          disabled: val
        }
      })
    },
    formLoading: function(val) {
      this.model.btn && this.model.btn.loading(val)
    }
  },
  methods: {
    handleClose(done) {
      if (this.formLoading) {
        return false
      }
      if (this.modelChange) {
        this.$confirm('更改将不会被保存，确定要取消吗？')
          .then(() => {
            this.formVisible = false
          })
          .catch(() => {})
      } else {
        this.formVisible = false
      }
    },
    handleShow(id) {
      this.formVisible = true
      this.formDisable = true
      getInfo(id).then(response => {
        if (response.code === 0) {
          this.temp = response.data
          this.model.setValue(this.temp)
          setTimeout(() => { this.modelChange = false }, 100)
        }
      })
    },
    handleCreate() {
      this.formVisible = true
      this.formDisable = false
    },
    handleUpdate(id) {
      this.formVisible = true
      this.formDisable = false
      getInfo(id).then(response => {
        if (response.code === 0) {
          this.temp = response.data
          this.model.setValue(this.temp)
          setTimeout(() => { this.modelChange = false }, 100)
        }
      })
    },
    handleReset() {
      this.temp = {}
      this.model.form && this.model.resetFields()
      this.modelChange = false
    },
    handleSubmit() {
      const success = (response) => {
        if (response.code === 0) {
          if (!this.temp.id) {
            this.temp.id = response.data.id
          }
          this.$emit('updateRow', this.temp)
          this.formVisible = false
          this.$message.success(response.msg)
        } else {
          this.$message.error(response.msg)
        }
        this.formLoading = false
      }
      const error = (error) => {
        this.$message.error(error.message)
        this.formLoading = false
      }
      this.model.submit(formData => {
        Object.assign(this.temp, formData)
        this.formLoading = true;
        if (this.temp.id) {
          update(this.temp).then(success).catch(error)
        } else {
          create(this.temp).then(success).catch(error)
        }
      })
    }
  }
}
</script>
<style rel="stylesheet/scss" lang="scss" scoped>
</style>