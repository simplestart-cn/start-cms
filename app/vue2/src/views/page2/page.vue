<template>
  <div class="app-container">
    <!-- 操作 -->
    <el-row class="handle-container" align="bottom">
      <div class="filter-container">
        <page-filter @submit="handleFilter" @reset="handleFilterClear" />
      </div>
      <div class="tools-container">
        <el-tooltip content="添加" placement="top" v-auth:official_index_create>
          <el-button v-waves type="success" icon="el-icon-plus" circle @click="handleCreate" />
        </el-tooltip>
        <el-tooltip content="删除" placement="top" v-auth:official_index_remove>
          <el-button v-waves :loading="btnLoading" :disabled="btnDisabled" type="danger" icon="el-icon-delete" circle @click="handleRemoveAll()" />
        </el-tooltip>
        <el-tooltip content="更多" placement="top" v-auth:official_index_updatelist>
          <el-dropdown trigger="click" placement="bottom" style="margin-left: 10px;" @command="handleCommand">
            <el-button :disabled="btnDisabled" type="Info" circle><i class="el-icon-more" /></el-button>
            <el-dropdown-menu slot="dropdown">
              <el-dropdown-item command="1">设为正常</el-dropdown-item>
              <el-dropdown-item command="0">设为禁用</el-dropdown-item>
            </el-dropdown-menu>
          </el-dropdown>
        </el-tooltip>
      </div>
    </el-row>
    <!-- 表格 -->
    <el-table :key="tableKey" v-loading="tableLoading" border fit size="mini" highlight-current-row :data="tableData.data" @selection-change="handleSelectionChange" @row-click="handleShow">
      <el-table-column type="selection" width="40" />
      <el-table-column label="ID" align="center" width="80px" fixed>
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column label="名称" width="200px" align="center" fixed>
        <template slot-scope="scope">
          <span>{{ scope.row.title }}</span>
        </template>
      </el-table-column>
      <el-table-column label="应用配置">
        <template slot-scope="scope">
          <p>AppId: {{ scope.row.app_id }}</p>
          <p>AppSecret: {{ scope.row.app_secret }}</p>
        </template>
      </el-table-column>
      <el-table-column label="支付配置">
        <template slot-scope="scope">
          <p>商户号: {{ scope.row.mch_id }}</p>
          <p>商户秘钥: {{ scope.row.mch_key }}</p>
        </template>
      </el-table-column>
      <el-table-column label="接口地址">
        <template slot-scope="scope">
          <p>Api: {{ scope.row.api_domain }}</p>
          <p>Auth: {{ scope.row.auth_domain }}</p>
          <p>Socket: {{ scope.row.socket_domain }}</p>
        </template>
      </el-table-column>
      <el-table-column label="备注" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.remark }}</span>
        </template>
      </el-table-column>
      <el-table-column label="状态" width="80px" align="center">
        <template slot-scope="scope">
          <div @click.stop="handleUpdateStatus(scope.$index)">
            <span class="el-icon-success text-green" v-if="scope.row.status">{{ scope.row.status | statusFilter }}</span>
            <span class="el-icon-error text-red" v-else>{{ scope.row.status | statusFilter }}</span>
          </div>
        </template>
      </el-table-column>
      <el-table-column label="更新时间" width="120px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.update_time }}</span>
        </template>
      </el-table-column>
      <el-table-column label="操作" width="150px" align="center" class-name="small-padding" fixed="right">
        <template slot-scope="scope">
          <el-tooltip content="编辑" placement="top" v-auth:official_index_update>
            <el-button v-waves type="primary" icon="el-icon-edit" size="mini" circle @click.stop="handleUpdate(scope.$index,scope.row.id)" />
          </el-tooltip>
          <el-tooltip content="删除" placement="top" v-auth:official_index_remove>
            <el-button v-waves :loading="scope.row.delete" type="danger" icon="el-icon-delete" size="mini" circle @click.stop="handleRemove(scope.$index,scope.row.id)" />
          </el-tooltip>
        </template>
      </el-table-column>
    </el-table>
    <!-- 分页 -->
    <div class="pagination-container">
      <el-pagination v-show="tableData.total>0" :current-page="tableFilter.page" :page-sizes="[15,20,30, 50]" :page-size="tableFilter.per_page" :total="tableData.total" background layout="total, sizes, prev, pager, next, jumper" @size-change="handleSizeChange" @current-change="handleCurrentChange" />
    </div>
    <!-- 表单 -->
    <edit-page ref="editPage" @updateRow="updateRow" />
  </div>
</template>
<script>
import { getPage, update, updateList, remove } from '@/api/page1'
import { getArrByKey } from '@/utils'
import editPage from './edit'
import pageFilter from './pageFilter'

export default {
  name: 'Page',
  components: { pageFilter, editPage },
  filters: {
    statusFilter(status) {
      const statusMap = {
        0: '禁用',
        1: '正常'
      }
      return statusMap[status]
    }
  },
  data() {
    return {
      tableKey: 0,
      tableData: {
        data: [],
        total: 0
      },
      tableFilter: {
        page: 1,
        per_page: 15
      },
      tableLoading: false,
      btnDisabled: true,
      btnLoading: false,
      currentIndex: -1,
      selectedRows: []
    }
  },
  watch: {

  },
  created() {
    this.fetchList()
  },
  methods: {
    fetchList() {
      this.tableLoading = true
      getPage(this.tableFilter).then(response => {
        this.tableData = response.data
        this.tableLoading = false
      })
    },
    handleFilter(filter) {
      Object.assign(this.tableFilter, filter)
      this.tableFilter.page = 1
      this.fetchList()
    },
    handleFilterClear() {
      this.tableFilter = {
        page: 1,
        per_page: 15
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
        this.btnDisabled = false
      } else {
        this.btnDisabled = true
      }
      this.selectedRows = val
    },
    updateRow(temp) {
      if (this.currentIndex >= 0 && temp.id > 0) {
        this.tableData.data.splice(this.currentIndex, 1, temp)
      } else {
        if (this.tableData.data.length >= 15) {
          this.tableData.data.pop()
        }
        this.tableData.data.unshift(temp)
        this.tableData.total++
      }
      this.currentIndex = -1
    },
    handleShow(row) {
      this.$refs.editPage.handleShow(row.id);
    },
    handleCreate() {
      this.$refs.editPage.handleCreate()
    },
    handleUpdate(index, id) {
      this.currentIndex = index
      this.$refs.editPage.handleUpdate(id)
    },
    handleUpdateStatus(index) {
      const item = this.tableData.data[index];
      this.tableData.data[index]['status'] = 1 - item.status
      update({ id: item.id, 'status': item.status }).then(response => {})
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
            _this.$notify.success(response.msg)
          } else {
            _this.$notify.error(response.msg)
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
          remove({ id: idstr }).then(response => {
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
<style rel="stylesheet/scss" lang="scss" scoped>
.el-table__row {
  cursor: pointer;
  p {
    margin: 0;
    line-height: 1.5;
  }
}
</style>