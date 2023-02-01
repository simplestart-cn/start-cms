<template>
  <div class="category-container" v-if="value.length > 0">
    <el-table
      ref="table"
      :data="tableData"
      row-key="id"
      :current-row-key="0"
      size="medium"
      :show-header="false"
      row-class-name="pointer category"
      highlight-current-row
      fit
      @row-click="handleSelect">
      <el-table-column label="名称" min-width="140px" align="right">
        <template slot-scope="scope">
          <div class="flex align-center tools" v-if="editEnabled">
            <el-tooltip content="删除" placement="top">
              <i class="el-icon-close text-gray margin-right-xs" @click.stop="handleRemove(scope.row)" />
            </el-tooltip>
            <el-tooltip content="编辑" placement="top">
              <i class="el-icon-edit text-gray margin-right-xs" @click.stop="handleUpdate(scope.row.id)" />
            </el-tooltip>
            <el-tooltip content="子分类" placement="top" v-if="treeEnabled">
              <i class="el-icon-plus text-gray" @click.stop="handleCreate(scope.row.id)" />
            </el-tooltip>
          </div>

          <el-input
            v-model="scope.row.title"
            v-show="scope.row.editable"
            size="mini"
            placeholder="请输入"
            :autofocus="scope.row.editable"
            @keyup.enter.native="handleSubmit(scope.$index, scope.row)"
            @blur="handleSubmit(scope.$index, scope.row)"></el-input>
          <span class="text-cut" @dblclick="handleUpdate(scope.row.id)" v-show="!scope.row.editable">{{ scope.row.title }}</span>
        </template>
      </el-table-column>
    </el-table>
  </div>
</template>
<script>
import tree from '@/utils/tree'
export default {
  name: 'Group',
  components: {},
  model: {
    name: 'value',
    event: 'change'
  },
  props: {
    value: {
      type: Array,
      default: () => []
    },
    treeEnabled: {
      type: Boolean,
      default: false
    },
    addEnabled: {
      type: Boolean,
      default: true
    },
    editEnabled: {
      type: Boolean,
      default: true
    },
  },
  data() {
    return {
      list: [],
      tableData: [],
      locking: false,
      // 只能打开一个输入框
      inputLocking: false
    }
  },
  computed: {
  },
  watch: {
    value: {
      handler(val) {
        this.list = [].concat(val)
      },
      immediate: true,
      deep: true
    },
    list: {
      handler(val) {
        this.tableData = []
        if (this.treeEnabled) {
          this.tableData = tree.listToTree(val, 0, 'id', 'pid', 'children', { 'delete': false })
        } else {
          this.tableData = val
        }
      },
      immediate: true,
      deep: true
    }
  },
  mounted(){
    if(this.tableData.length){
      this.$refs.table.setCurrentRow(this.tableData[0]);
    }
  },
  methods: {
    arraySpanMethod({ row, column, rowIndex, columnIndex }) {
      if (row.editable) {
        if (columnIndex === 0) {
          return [0, 0]
        } else if (columnIndex === 1) {
          return [1, 2]
        }
      }
    },
    handleSelect(row) {
      this.$emit('selected', row.title)
    },
    handleCreate(pid = 0) {
      if (!this.inputLocking) {
        this.inputLocking = true
        let index = 0
        let item = {
          id: false,
          pid: pid,
          title: '',
          editable: true
        }
        let list = this.list.map((item, idx) => {
          item.editable = false
          if (item.id == pid) {
            index = idx
          }
          return item
        })
        if (pid == 0) {
          list.unshift(item)
        } else {
          list.splice(index + 1, 0, item)
        }
        this.list = list
      }
    },
    handleUpdate(id) {
      let list = this.list.map(item => {
        if (item.id == id) {
          item.editable = true
        } else {
          item.editable = false
        }
        return item
      })
      this.list = list
    },
    handleRemove(row) {
      this.$emit('remove', row)
    },
    async handleSubmit(index, row) {
      // 关闭输入框
      this.inputLocking = false
      let list = this.list.map(item => {
        item.editable = false
        return item
      })
      if (!row.id && row.title == '') {
        // 放弃添加
        list.splice(index, 1)
        this.list = list
        return false
      }
      this.list = list
      delete row.children
      if (!this.locking) {
        this.locking = true
        const value = this.value.map(item => {
          if (item.id === row.id) {
            item.title = row.title
          }
          return item
        })
        this.$emit('change', value)
      }
      this.locking = false
    }
  }
}
</script>
<style type="text/scss" lang="scss" scoped>
  .category-container {
    margin-right: 15px;
    border-right: 2px solid #e4e7ed;

    .el-table::before {
      display:none
    }
    :deep().category td {
      border-bottom: 0
    }
    :deep().category td:last-child{
      padding-right: 15px;
    }
    :deep().category .tools{
      position: absolute;
      left: 0;
      top: 0;
      height: 100%;
      padding-left: 5px;
      display:none;
      visibility:hidden;
      background-color: #F5F7FA;
    }
    :deep().category:hover .tools{
      display:flex;
      visibility:visible
    }
    :deep().category .el-table_1_column_1 .cell {
      padding-left: 23px;
    }
    :deep().category.el-table__row--level-0 .el-table_1_column_1 .cell {
      padding-left: 0;
    }
    :deep().category .el-table__indent {
      padding-left: 0 !important;
    }
  }
  @media only screen and (max-width: 992px){
    .category-container {
      padding-right: 0;
      margin-bottom: 30px;
    }
  }
</style>
