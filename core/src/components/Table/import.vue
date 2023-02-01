<template>
<!-- 请转用：Table/import -->
  <div class="upload-import">
    <el-dialog title="导入模板" :visible.sync="visible" :before-close="handleClose" center width="75%" :append-to-body="true" class="app-container" top="5vh">
      <div class="margin-tb fr margin-lr">
        <el-button v-waves :loading="btnLoading" type="warning" size="mini" icon="el-icon-download" @click="handleMould" class="margin-right">下载模板</el-button>
        <el-upload action="" :on-change="handleUpload" :show-file-list="false" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" :auto-upload="false" style="display: inline-block;">
          <el-button v-waves :loading="btnLoading" type="success" icon="el-icon-upload" size="mini">上传文件</el-button>
        </el-upload>
      </div>
      <div v-if="!tableShow" style="height: 450px;"></div>
      <el-table ref="table" height="450" v-loading="tableLoading" border fit highlight-current-row size="mini" :data="tableData" v-else>
        <el-table-column type="index" label="数目" fixed align="center"></el-table-column>
        <el-table-column
          v-for="item in rule"
          :key="item.value"
          :label="item.label"
          :prop="item.value"
          :width="item.width"
          align="center"
          :fixed="item.fixed">
          <template slot-scope="scope">
            <span :class="rule | classFilter(scope.row)">{{ scope.row[item.value] }}</span>
          </template>
        </el-table-column>
      </el-table>
      <!-- 分页 -->
      <div class="pagination-container">
        <el-pagination v-show="importData.length > 0" :current-page="tableFilter.page" :page-sizes="[30, 50, 100]" :page-size="tableFilter.per_page" :total="importData.length" background layout="total, sizes, prev, pager, next, jumper" @size-change="handleSizeChange" @current-change="handleCurrentChange" />
      </div>
      <el-row type="flex" justify="center" class="margin-top">
        <el-button size="small" type="danger" @click="handleClose">取消操作</el-button>
        <el-button size="small" type="primary" @click="handleImport">确认导入</el-button>
      </el-row>
    </el-dialog>
    <el-progress class="import-progress" :text-inside="true" :stroke-width="16" :percentage="percentage" status="success" v-if="progressStatus"></el-progress>
  </div>
</template>

<script>
import excel from '@/utils/excel'
export default {
  name: 'Import',
  props: {
    // 导入数据接口。注：接口参数只有唯一一个list，且为JSON序列化
    api: {
      type: Function,
      required: true
    },
    rule: {
      type: Array,
      required: true
    },
    // 导入数据是否过滤，初始化为传入什么返回什么。
    filterData: {
      type: Function,
      default: _ => _
    }
  },
  data() {
    return {
      visible: false,
      tableShow: false,
      tableLoading: false,
      tableData: [],
      importData: [],
      btnLoading: false,
      tableFilter: {
        page: 1,
        per_page: 30
      },
      current: {
        index: -1,
        class: ''
      },
      // 进度条显示开关
      percentage: 0,
      progressStatus: false,
    }
  },
  filters: {
    classFilter(rule, row) {
      let className = ''
      rule.some(item => {
        if (item['required'] && !row[item.value]) {
          className = 'text-red'
          return true
        } else {
          className = ''
          return false
        }
      })
      return className
    }
  },
  methods: {
    iniData() {
      this.tableShow = false
      this.tableLoading = false
      this.tableData = []
      this.importData = []
      this.btnLoading = false
      this.tableFilter = {
        page: 1,
        per_page: 30
      }
    },
    fetchList() {
      const page = this.tableFilter.page - 1
      const per_page = this.tableFilter.per_page
      this.tableData = this.importData.slice(page * per_page, page * per_page + per_page)
      this.tableLoading = false
    },
    // 创建新的表格
    handleCreate() {
      this.tableFilter = {
        page: 1,
        per_page: 30
      }
      this.iniData()
      this.visible = true
    },
    // 关闭
    handleClose() {
      if (this.tableShow) {
        this.$confirm('引入的数据将不会被保存，确定要取消吗？')
          .then(() => {
            this.visible = false
          })
          .catch(() => {})
      } else {
        this.visible = false
      }
    },
    handleSizeChange(val) {
      this.tableLoading = true
      this.tableFilter.per_page = val
      this.fetchList()
    },
    handleCurrentChange(val) {
      this.tableLoading = true
      this.tableFilter.page = val
      this.fetchList()
    },
    handleFilterClear() {
      this.tableFilter = {
        page: 1,
        per_page: 30
      }
      this.fetchList()
    },
    // 导入数据
    handleUpload(file) {
      const name = file.name.split('.')[1]
      const suffix = ['xlsx', 'xls', 'csv']
      if (suffix.indexOf(name) !== -1) {
        this.tableLoading = true
        this.tableShow = true
        excel.importFromLocal(file.raw).then(response => {
          // data 就是需要上传服务器的数据
          if (JSON.stringify(response) === '{}') {
						this.$message.warning('请导入正确模板')
						return false
					}
          const data = excel.arrayByRule(response, this.rule, 'label', 'value')
          this.importData = data
          this.handleFilterClear()
        }).catch(error => {
          this.$message.error(error.message)
        })
      } else {
        this.$message.error('导入失败，请选择正确的文件格式(xlsx, xlx, csv)')
      }
    },
    // 导出模板
    handleMould() {
      const mould = excel.ruleToArray(this.rule)
      excel.exportFromArray(mould)
    },
    handleImport() {
      if (this.tableShow) {
        let count = 0
        const importData = this.importData.filter(item => {
          const status = this.rule.some(ruleKey => {
            return ruleKey['required'] && !item[ruleKey['value']]
          })
          if (!status) {
            return item
          } else {
            count += 1
          }
        })
        this.handleSubmit(importData, count)
      }
      this.visible = false
    },
    async handleSubmit(data, count) {
      const filterData = this.filterData(data) || [];
      if(!Array.isArray(filterData)) {
        throw new Error('filterData函数返回值必须为数组')
      }
      let success = 0
      let error = 0
      const length = filterData.length
      const loading = this.$loading({
          lock: true,
          text: 'Loading',
          spinner: 'el-icon-loading',
          background: 'rgba(0, 0, 0, 0.7)'
        });
      try {
        this.progressStatus = true
        for(let i = 0; i < Math.ceil(length / 10); i++) {
          const response = await this.api({ list: JSON.stringify(filterData.slice(i*10, i*10 + 10)) })
          if(response.code === 0) {
            success += response.data.success
            error += response.data.error
            this.percentage = ((success + error) / filterData.length).toFixed(4) * 100
          }
        }
        this.$message.success(`总导入数${ success + error }，成功导入${ success }条，不合格${ error + count }条。`)
        // 导入完成后
        this.$emit('success')
        loading.close()
        setTimeout(() => {
          this.progressStatus = false
        }, 2000)
      } catch(error) {
        console.log(error)
        this.$message.error(error)
        loading.close()
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.upload-import {
  position: relative;
  .import-progress {
    width: 600px;
    position: fixed;
    bottom: 15px;
    right: 30px;
    z-index: 2020;
  }
}
.import-table {
  width: 100%;
  height: 500px;
  overflow: auto;
}
</style>
