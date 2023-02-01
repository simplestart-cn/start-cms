<template>
  <div class="app-container">
    <!-- 操作 -->
    <el-row class="handle-container" align="bottom">
      <div class="margin-tb-sm">
        <el-tooltip content="构建菜单" placement="top">
          <el-button v-waves type="danger" icon="el-icon-refresh-left" circle @click="handleBuild" />
        </el-tooltip>
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
      :key="tableKey"
      row-key="id"
      v-loading="listLoading"
      :data="getMenuTree"
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
      <el-table-column label="图标" width="100px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.icon }}</span>
        </template>
      </el-table-column>
      <el-table-column label="权限标识" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.name }}</span>
        </template>
      </el-table-column>
      <el-table-column label="路径" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.path }}</span>
        </template>
      </el-table-column>
      <el-table-column label="参数" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.params }}</span>
        </template>
      </el-table-column>
      <el-table-column label="模板" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.template }}</span>
        </template>
      </el-table-column>
      <el-table-column label="排序" width="100px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.sort }}</span>
        </template>
      </el-table-column>
      <el-table-column label="菜单" width="70px" align="center">
        <template slot-scope="scope">
          <span :class="{'el-icon-success text-green':scope.row.menu == 1,'el-icon-error text-red':scope.row.menu == 0}" @click="handleUpdateStatus(scope.row.id, 'menu', scope.row.menu)">{{ scope.row.menu | menuFilter }}</span>
        </template>
      </el-table-column>
      <el-table-column label="状态" width="70px" align="center">
        <template slot-scope="scope">
          <span :class="{'el-icon-success text-green':scope.row.status == 1,'el-icon-error text-red':scope.row.status == 0}" @click="handleUpdateStatus(scope.row.id, 'status', scope.row.status)">{{ scope.row.status | statusFilter }}</span>
        </template>
      </el-table-column>
      <el-table-column label="操作" align="center" width="150px" class-name="small-padding">
        <template slot-scope="scope">
          <el-button v-waves type="primary" icon="el-icon-edit-outline" size="mini" circle @click="handleUpdate(scope.$index,scope.row.id)" />
          <el-button v-waves :loading="scope.row.delete" type="danger" icon="el-icon-delete" size="mini" circle @click="handleRemove(scope.$index,scope.row.id)" />
        </template>
      </el-table-column>
    </el-table>

    <!-- 表单 -->
    <editForm ref="editForm" :node-list="list" @updateRow="updateRow" />

  </div>
</template>

<script>
import { getList, build, update, updateList, remove } from '@/api/auth'
import { getArrByKey } from '@/utils'
import tree from '@/utils/tree'
import editForm from './edit'

export default {
  name: 'Tree',
  components: { editForm },
  filters: {
    statusFilter(status) {
      const map = {
        0: '禁用',
        1: '正常'
      }
      return map[status]
    },
    menuFilter(status) {
      const map = {
        0: '否',
        1: '是'
      }
      return map[status]
    },
    hiddenFilter(status) {
      const map = {
        0: '可见',
        1: '隐藏'
      }
      return map[status]
    }
  },
  data() {
    return {
      tableKey: 0,
      list: null,
      selectedRows: null,
      listLoading: true,
      listQuery: {
        status: '-1',
        title: ''
      },
      buttonDisabled: true,
      deleting: false
    }
  },
  computed: {
    getMenuTree() {
      return tree.listToTree(this.list, 0, 'id', 'pid', 'children', { 'delete': false })
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
      getList(this.listQuery).then(response => {
        this.list = response.data
        this.listLoading = false
      })
    },
    handleBuild() {
      this.$confirm('此操作将覆盖现有数据并根据节点信息重建菜单, 是否继续?', '非开发人员切勿操作', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'danger'
      }).then(() => {
        this.listLoading = true
        build().then(response => {
          this.listLoading = false
          this.fetchList()
          this.$message.success(response.msg)
        })
      }).catch(err => {
        console.log(err)
      })
    },
    handleFilterClear() {
      this.listQuery = {
        status: '-1',
        title: ''
      }
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
    handleCreate(pid = 0) {
      this.$refs.editForm.handleCreate(pid)
    },
    handleUpdate(index, id) {
      this.$refs.editForm.handleUpdate(id)
    },
    handleUpdateStatus(id, field, value) {
      const data = {}
      data.id = id
      data[field] = 1 - value
      if (field === 'status') {
        const treeData = tree.listToTree(this.list, 0, 'id', 'pid', 'children', { 'delete': false })
        tree.updateTree(treeData, 'id', id, data)
        data.id = id
      } else {
        tree.updateList(this.list, 'id', id, data)
      }
      update(data).then(response => {})
    },
    updateRow(temp) {
      this.fetchList()
    },
    handleRemove(index, id) {
      this.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        remove({ id }).then(response => {
          if (response.code === 0) {
            this.fetchList()
            this.$message.success(response.msg)
          } else {
            this.$message.error(response.msg)
          }
        // eslint-disable-next-line handle-callback-err
        }).catch(error => {
          this.$message.error(error)
        })
      }).catch(error => {
        this.$message.error(error)
      })
    },
    handleRemoveAll() {
      if (this.selectedRows.length > 0) {
        this.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          this.deleting = true
          const ids = getArrByKey(this.selectedRows, 'id')
          const idstr = ids.join(',')
          remove({ id: idstr }).then(response => {
            if (response.code === 0) {
              this.fetchList()
              this.$message.success(response.msg)
            } else {
              this.$message.error(response.msg)
            }
            this.deleting = false
          // eslint-disable-next-line handle-callback-err
          }).catch(error => {
            this.deleting = false
          })
        }).catch(() => {
          this.$message.info('已取消')
        })
      } else {
        this.$message.error('请选择要删除的数据')
      }
    },
    handleCommand(command) {
      const _this = this
      if (this.selectedRows.length > 0) {
        const ids = getArrByKey(this.selectedRows, 'id')
        const list = ids.map(id => {
          return { id: id, status: command }
        })
        updateList({ tree }).then((response) => {
          if (response.code === 0) {
            _this.fetchList()
            _this.$message.success(response.msg)
          } else {
            _this.$message.error(response.msg)
          }
        // eslint-disable-next-line handle-callback-err
        }).catch(error => {
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
