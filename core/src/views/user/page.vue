<template>
  <div class="app-container">
    <!-- 操作 -->
    <el-row class="handle-container" align="bottom">
      <div class="filter-container">
        <page-filter @submit="handleFilter" @reset="handleFilterClear" />
      </div>
      <div class="tools-container">
        <el-tooltip content="添加" placement="top">
          <el-button v-waves type="success" icon="el-icon-plus" circle @click="handleCreate" />
        </el-tooltip>
        <el-tooltip content="删除" placement="top">
          <el-button v-waves :loading="btnLoading" :disabled="btnDisabled" type="danger" icon="el-icon-delete" circle @click="handleRemoveAll()" />
        </el-tooltip>
        <el-tooltip content="更多" placement="top">
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
    <!-- 类型 -->
    <el-tabs v-model="tableType">
      <el-tab-pane label="管理账户" name="admin" />
      <el-tab-pane label="游客账户" name="guest" />
    </el-tabs>
    <!-- 表格 -->
    <el-table :key="tableKey" v-loading="tableLoading" border fit size="mini" highlight-current-row :data="tableData.data" @selection-change="handleSelectionChange">
      <el-table-column type="selection" width="40" />
      <el-table-column label="ID" align="center" width="60px" fixed>
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column label="头像" width="65px" align="center" fixed>
        <template slot-scope="scope">
          <span class="link-type" @click="handleImg(scope.row.avatar)" v-if="scope.row.avatar">
            <img :src="scope.row.avatar" width="40" height="40">
          </span>
          <i class="el-icon-user" style="font-size: 40px" v-else></i>
        </template>
      </el-table-column>
      <el-table-column label="登录账户" width="100px" align="center" fixed>
        <template slot-scope="scope">
          <router-link :to="{path:'/core/user/info?id='+scope.row.id }"><span class="link-type">{{ scope.row.account || '未设置' }}</span>
          </router-link>
        </template>
      </el-table-column>
      <el-table-column label="姓名" width="110px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.name }}</span>
        </template>
      </el-table-column>
      <el-table-column label="部门" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.group_title }}</span>
        </template>
      </el-table-column>
      <el-table-column label="职位" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.role_title }}</span>
        </template>
      </el-table-column>
      <el-table-column label="手机" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.mobile }}</span>
        </template>
      </el-table-column>
      <el-table-column label="邮箱" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.email }}</span>
        </template>
      </el-table-column>
      <el-table-column label="备注" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.describe }}</span>
        </template>
      </el-table-column>
      <el-table-column label="状态" width="70px" align="center">
        <template slot-scope="scope">
          <el-switch v-model="scope.row.status" :active-value="1" :inactive-value="0" @change="handleUpdateStatus(scope.$index)"></el-switch>
        </template>
      </el-table-column>
      <el-table-column label="管理身份" width="70px" align="center">
        <template slot-scope="scope">
          <el-switch v-model="scope.row.is_admin" :active-value="1" :inactive-value="0" @change="handleUpdateIdentity(scope.$index)"></el-switch>
        </template>
      </el-table-column>
      <el-table-column label="登录IP" width="120px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.login_ip }}</span>
        </template>
      </el-table-column>
      <el-table-column label="登录时间" width="160px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.login_time }}</span>
        </template>
      </el-table-column>
      <el-table-column label="注册时间" width="160px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.create_time }}</span>
        </template>
      </el-table-column>
      <el-table-column label="操作" width="150px" align="center" class-name="small-padding" fixed="right">
        <template slot-scope="scope">
          <el-tooltip content="编辑" placement="top">
            <el-button v-waves type="primary" icon="el-icon-edit" size="mini" circle @click.stop="handleUpdate(scope.$index,scope.row.id)" />
          </el-tooltip>
          <el-tooltip content="删除" placement="top">
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
import { getPage, update, updateList, updateStatus, updateIdentity, remove } from '@/api/user'
import { getArrByKey } from '@/utils'
import editPage from './edit'
import pageFilter from './pageFilter'
import openWindow from '@/utils/open-window'

export default {
  name: 'Page',
  components: { pageFilter, editPage },
  filters: {
    statusFilter(value) {
      const map = {
        0: '禁用',
        1: '正常'
      }
      return map[value]
    },
    adminFilter(value) {
      const map = {
        0: '游客账户',
        1: '管理账户'
      }
      return map[value]
    }
  },
  data() {
    return {
      tableKey: 0,
      tableType: 'admin',
      tableData: {
        data: [],
        total: 0
      },
      tableFilter: {
        page: 1,
        per_page: 30,
        is_admin: 1
      },
      tableLoading: false,
      btnDisabled: true,
      btnLoading: false,
      currentIndex: -1,
      selectedRows: []
    }
  },
  watch: {
    tableType(type) {
      this.fetchList()
    }
  },
  created() {
    this.fetchList()
  },
  methods: {
    fetchList() {
      this.tableLoading = true
      this.tableFilter.is_admin = this.tableType == 'admin' ? 1 : 0;
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
        per_page: 30
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
      this.fetchList()
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
    async handleUpdateStatus(index) {
      const item = this.tableData.data[index]
      const res = await updateStatus({ id: item.id, 'status': item.status })
      !res.code ? this.tableData.data[index]['status'] = item.status : this.tableData.data[index]['status'] = 1 - item.status
    },
    async handleUpdateIdentity(index) {
      const item = this.tableData.data[index];
      const res = await updateIdentity({ id: item.id, 'is_admin': item.is_admin })
      !res.code ? this.tableData.data[index]['is_admin'] = item.is_admin : this.tableData.data[index]['is_admin'] = 1 - item.is_admin
    },
    handleRemove(index, id) {
      const _this = this
      this.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        _this.$set(_this.tableData.data[index], 'delete', true)
        remove(id).then(response => {
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
    },
    handleImg(avatar) {
      openWindow(avatar, '图片预览', '500', '400')
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