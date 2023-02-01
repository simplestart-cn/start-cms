<template>
  <div class="app-container">
    <!-- 操作 -->
    <el-row class="handle-container" align="bottom">
      <div class="margin-tb-sm">
        <el-tooltip content="刷新" placement="top">
          <el-button v-waves type="warning" icon="el-icon-refresh" circle @click="handleFilterClear" />
        </el-tooltip>
        <el-tooltip content="添加" placement="top">
          <el-button v-waves type="success" icon="el-icon-plus" circle @click="handleCreate" />
        </el-tooltip>
        <el-tooltip content="删除" placement="top">
          <el-button v-waves :loading="deleting" :disabled="buttonDisabled" type="danger" icon="el-icon-delete" circle @click="handleRemoveAll()" />
        </el-tooltip>
        <el-tooltip content="更多" placement="top">
          <el-dropdown trigger="click" placement="bottom" style="margin-left: 10px;" @command="handleCommand">
            <el-button :disabled="buttonDisabled" type="Info" circle>
              <i class="el-icon-more" />
            </el-button>
            <el-dropdown-menu slot="dropdown">
              <el-dropdown-item command="1">设为正常</el-dropdown-item>
              <el-dropdown-item command="0">设为禁用</el-dropdown-item>
            </el-dropdown-menu>
          </el-dropdown>
        </el-tooltip>
      </div>
    </el-row>
    <!-- 表格 -->
    <el-table
      row-key="id"
      v-loading="listLoading"
      :data="tableData.data"
      border
      fit
      highlight-current-row
      style="width: 100%;"
      @selection-change="handleSelectionChange">
      <el-table-column type="selection" width="50px" align="center" />
      <el-table-column label="名称" width="180px">
        <template slot-scope="scope">
          <span>{{ scope.row.title }}</span>
        </template>
      </el-table-column>
      <el-table-column label="备注" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.remark }}</span>
        </template>
      </el-table-column>
      <el-table-column label="状态" width="70px" align="center">
        <template slot-scope="scope">
          <span :class="{'el-icon-success text-green':scope.row.status == 1,'el-icon-error text-red':scope.row.status == 0}" @click="handleUpdateStatus(scope.$index)">{{ scope.row.status | statusFilter }}</span>
        </template>
      </el-table-column>
      <el-table-column label="操作" align="center" width="120px" class-name="small-padding">
        <template slot-scope="scope">
          <el-button v-waves type="primary" icon="el-icon-edit-outline" circle @click="handleUpdate(scope.$index,scope.row.id)" />
          <el-button v-waves :loading="scope.row.delete" type="danger" icon="el-icon-delete" circle @click="handleRemove(scope.$index,scope.row.id)" />
        </template>
      </el-table-column>
    </el-table>
    <!-- 分页 -->
    <div class="pagination-container">
      <el-pagination v-show="tableData.total > 0" :current-page="tableFilter.page" :page-sizes="[10,20,30, 50]" :page-size="tableFilter.per_page" :total="tableData.total" background layout="total, sizes, prev, pager, next, jumper" @size-change="handleSizeChange" @current-change="handleCurrentChange" />
    </div>
    <!-- 表单 -->
    <editForm ref="editForm" @updateRow="updateRow" />

  </div>
</template>

<script>
import { getPage, update, updateList, remove } from '@/api/group'
import { getArrByKey } from '@/utils'
import editForm from './edit'

export default {
  name: 'Page',
  components: { editForm },
  filters: {
    statusFilter(status) {
      const map = {
        0: '禁用',
        1: '正常'
      }
      return map[status]
    }
  },
  data() {
    return {
      tableKey: 0,
      tableData: {
        data: [],
        total: 0
      },
      selectedRows: null,
      listLoading: true,
      tableFilter: {
        page: 1,
        per_page: 10
      },
      buttonDisabled: true,
      deleting: false
    }
  },
  watch: {
  },
  created() {
    this.fetchList()
  },
  methods: {
    fetchList() {
      this.listLoading = true
      getPage(this.tableFilter).then(response => {
        this.tableData = response.data
        this.listLoading = false
      })
    },
    handleFilterClear() {
      this.tableFilter = {
        page: 1,
        per_page: 10
      }
      this.fetchList()
    },
    handleSizeChange(val) {
      this.tableFilter.per_page = val
      this.fetchList()
    },
    handleCurrentChange(val) {
      this.tableFilter.page = val
      this.fetchList()
    },
    handleSelectionChange(val) {
      if (val.length > 0) {
        this.buttonDisabled = false
      } else {
        this.buttonDisabled = true
      }
      this.selectedRows = val
    },
    handleCreate() {
      this.$refs.editForm.handleCreate()
    },
    handleUpdate(index, id) {
      this.$refs.editForm.handleUpdate(id)
    },
    handleUpdateStatus(index) {
      const item = this.tableData.data[index]
      this.tableData.data[index]['status'] = 1 - item.status
      update({ id: item.id, 'status': item.status }).then(response => {})
    },
    updateRow(temp) {
      this.fetchList()
    },
    handleRemove(index, id) {
      const _this = this
      this.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        _this.$set(_this.tableData.data[index], 'delete', true)
        remove({ id }).then(response => {
          if (response.code === 0) {
            _this.tableData.data.splice(index, 1)
            _this.tableData.total--
            _this.$message.success(response.msg)
          } else {
            _this.$message.error(response.msg)
          }
          _this.$set(_this.tableData.data[index], 'delete', false)
          // eslint-disable-next-line handle-callback-err
        }).catch(() => {
          _this.$set(_this.tableData.data[index], 'delete', false)
        })
      }).catch(() => {
        this.$message({
          type: 'info',
          message: '已取消删除'
        })
      })
    },
    handleRemoveAll() {
      const _this = this
      if (this.selectedRows.length > 0) {
        this.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          _this.btnLoading = true
          const ids = getArrByKey(_this.selectedRows, 'id')
          const idstr = ids.join(',')
          remove(idstr).then(response => {
            if (response.code === 0) {
              const delindex = []
              _this.tableData.data.forEach(function(item, index, input) {
                if (ids.indexOf(item.id) > -1) {
                  delindex.push(index)
                }
              })
              for (let i = delindex.length - 1; i >= 0; i--) {
                _this.tableData.data.splice(delindex[i], 1)
              }
              _this.tableData.total = _this.tableData.total - delindex.length
              _this.$message.success(response.msg)
            } else {
              _this.$message.error(response.msg)
            }
            _this.btnLoading = false
            // eslint-disable-next-line handle-callback-err
          }).catch(() => {
            _this.btnLoading = false
          })
        }).catch(error => {
          _this.btnLoading = false
          error.message && this.$message.error(error.message)
        })
      } else {
        _this.$message.error('请选择要删除的数据')
      }
    },
    handleCommand(command) {
      const _this = this
      if (this.selectedRows.length > 0) {
        const ids = getArrByKey(this.selectedRows, 'id')
        const list = ids.map(id => {
          return { id: id, status: command }
        })
        updateList({ list }).then(response => {
          if (response.code === 0) {
            _this.tableData.data.forEach(function(item, index, input) {
              if (ids.indexOf(item.id) > -1) {
                _this.tableData.data[index]['status'] = command
              }
            })
            this.$message.success(response.msg)
          } else {
            this.$message.error(response.msg)
          }
          // eslint-disable-next-line handle-callback-err
        }).catch(() => {
        })
      } else {
        this.$message.error('请选择要操作的数据')
      }
    }
  }
}
</script>
<style rel="stylesheet/scss" lang="scss">
  .text-red{
    color: red;
    cursor: pointer;
  }
  .text-green{
    color: green;
    cursor: pointer;
  }
</style>
