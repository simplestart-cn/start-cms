<template>
  <el-dialog title="日志内容" :visible.sync="visible" width="80%">
    <div class="dialog" style="white-space: pre-line;">{{ info }}</div>
  </el-dialog>
</template>

<script>
import { runtime } from '@/api/record'
export default {
  name: 'RuntimeDialog',
  data() {
    return {
      visible: false,
      info: ''
    }
  },
  methods: {
    fetchInfo(file) {
      runtime({ file }).then(response => {
        this.info = response.data
      }).catch(error => {
        console.log(error)
      })
    },
    handleOpen(file) {
      this.visible = true
      this.fetchInfo(file)
    }
  }
}
</script>

<style scoped>
.dialog {
  height: 500px;
  overflow: auto;
}
</style>
