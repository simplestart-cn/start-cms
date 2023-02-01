<template>
  <el-drawer
    ref="drawer"
    :with-header="false"
    size="50%"
    :before-close="handleClose"
    :visible.sync="formVisible"
    direction="rtl"
    custom-class="drawer-page">
    <div class="drawer-header">
      配置项
    </div>
    <div class="drawer-content">
      <form-create v-model="model" :rule="formRule" :option="formOption" @mounted="formMounted"></form-create>
    </div>
    <div class="drawer-footer" v-if="!formDisable">
      <el-row type="flex" justify="center">
        <el-button v-waves @click="handleClose">取 消</el-button>
        <el-button v-waves :loading="btnLoading" type="primary" @click="handleSubmit">保存</el-button>
      </el-row>
    </div>
  </el-drawer>
</template>

<script>
import { formOption, formRule} from './editForm'

export default {
  name: 'Edit',
  components: {},
  props: {
    group: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      temp: {},
      model: {},
      formRule: formRule,
      formOption: formOption,
      formDisable: false,
      btnLoading: false,
      modelChange: false,
      formVisible: false,
      nodeTop: [{ 'id': 0, 'title': '顶级' }]
    }
  },
  computed: {
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
    group(val) {
      this.model.form && this.model.mergeRule('group', {
        options: this.group
      })
    }
  },
  created() {

  },
  methods: {
    formMounted() {
      this.model.mergeRule('group',{
        options: this.group
      })
    },
    handleClose(done) {
      if (this.btnLoading) {
        return
      }
      if (this.modelChange) {
        this.$confirm('更改将不会被保存，确定要取消吗？')
          .then(_ => {
            this.formVisible = false
          })
          .catch(_ => {})
      } else {
        this.formVisible = false
      }
    },
    handleReset() {
      this.temp = {}
      this.model.form && this.model.resetFields()
      this.modelChange = false
    },
    handleCreate() {
      this.btnLoading = false
      this.formVisible = true
    },
    handleUpdate(id) {
      this.formVisible = true
    },
    handleEdit(temp) {
      this.formVisible = true
      this.$nextTick(() => {
        this.temp = temp
        this.model.form && this.model.setValue(temp)
      })
    },
    handleSubmit() {
      this.model.submit(form => {
        Object.assign(this.temp, form)
        const temp = this.temp
        let field = { props: {}, col: { span: 14 } }
        let props = {}
        for (let k in temp) {
          if (k.indexOf('props_') > -1){
            let arr = k.split('_')
            field.props[arr[1]] = temp[k]
            delete form[k]
          } else {
            field[k] = temp[k]
          }
        }
        this.$emit('updateField', field)
        this.formVisible = false
      })
    },
  }
}
</script>
<style type="text/scss" lang="scss" scoped>
  .edit-drawer {

  }
</style>
