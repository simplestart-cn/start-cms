<template>
  <div class="app-container">
    <!-- 搜索 -->
    <el-row class="handle-container inline" align="bottom">
      <div class="filter-container">
        <filter-form @submit="handleFilter" @reset="handleFilterClear" />
      </div>
    </el-row>

    <!-- 表格 -->
    <el-table
      key="id"
      v-loading="tableLoading"
      :data="tableData.data"
      border
      fit
      highlight-current-row
      style="width: 100%;"
    >
      <el-table-column label="ID" align="center" width="100">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column label="用户ID" width="100px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.user_id }}</span>
        </template>
      </el-table-column>
      <el-table-column label="用户名" width="100px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.user_name }}</span>
        </template>
      </el-table-column>
      <el-table-column label="行为" width="150px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.action }}</span>
        </template>
      </el-table-column>
      <el-table-column label="说明 ">
        <template slot-scope="scope">
          <span>{{ scope.row.content }}</span>
        </template>
      </el-table-column>
      <el-table-column label="访问IP" width="150px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.geoip }}</span>
        </template>
      </el-table-column>
      <el-table-column label="操作时间" width="160px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.create_time }}</span>
        </template>
      </el-table-column>
    </el-table>

    <!-- 分页 -->
    <div class="pagination-container">
      <el-pagination v-show="tableData.total>0" :current-page="tableFilter.page" :page-sizes="[15, 20,30, 50]" :page-size="tableFilter.per_page" :total="tableData.total" background layout="total, sizes, prev, pager, next, jumper" @size-change="handleSizeChange" @current-change="handleCurrentChange" />
    </div>
  </div>
</template>

<script>
import { operation } from '@/api/record'
import waves from '@/directive/waves'
import filterForm from './operationFilter'

export default {
  name: 'Log',
  components: { filterForm },
  directives: {
    waves
  },
  filters: {
  },
  data() {
    return {
      tableKey: 0,
      tableData: {
        data: [],
        total: 0
      },
      tableLoading: true,
      tableFilter: {
        page: 1,
        per_page: 15
      }
    }
  },
  created() {
    this.fetchList()
  },
  methods: {
    fetchList() {
      this.tableLoading = true
      operation(this.tableFilter).then(response => {
        this.tableData = response.data
        this.tableLoading = false
      })
    },
    handleFilter() {
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
    }
  }
}
</script>
