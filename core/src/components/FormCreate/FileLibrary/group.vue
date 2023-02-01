<template>
  <el-dialog title="添加分组" :visible.sync="visible" center width="20%" append-to-body>
    <el-form class="margin-bottom-sm" ref="form" :model="form" label-width="100px" @submit.native.prevent>
      <el-form-item label="分组名称：">
        <el-input placeholder="分组名称" v-model="form.title"></el-input>
      </el-form-item>
      <el-form-item>
        <el-button size="small" type="primary" @click="handleSubmit">确认</el-button>
        <el-button size="small" type="danger" @click="handleClose">取消</el-button>
      </el-form-item>
    </el-form>
  </el-dialog>
</template>

<script>
import { create, update } from '@/api/storage/folder'
export default {
  name: 'GroupDialog',
  data() {
    return {
      temp: {},
      form: {
        title: ''
      },
      visible: false
    }
  },
  methods: {
    handleCreate() {
      this.temp = {}
      this.form = {
        title: ''
      }
      this.visible = true
    },
    handleEdit(temp) {
      this.form = {
        title: temp.title
      }
      this.temp = temp
      this.visible = true
    },
    handleClose() {
      this.visible = false
    },
    handleSubmit() {
      const success = (response) => {
        if (response.code == 0) {
          if (!this.temp.id) {
            this.temp.id = response.data.id
          }
          this.temp.name = this.temp.id.toString()
          this.$emit('updateTabs', this.temp)
          this.$message.success(response.msg)
        } else {
          this.$message.error(response.msg)
        }
      }
      const error = (error) => {
        this.$message.error(error.message)
      }
      Object.assign(this.temp, this.form)
      if (this.temp.id) {
        update(this.temp).then(success).catch(error)
      } else {
        create(this.temp).then(success).catch(error)
      }
      this.visible = false
    }
  }
}
</script>

<style scoped>

</style>
