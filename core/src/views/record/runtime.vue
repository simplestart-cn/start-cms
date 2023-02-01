<template>
    <div class="app-container">
        <!-- 搜索 -->
        <el-row class="handle-container padding-bottom" align="bottom">
            <el-button v-waves :loading="btnLoading" type="primary" @click="handleReturn" v-if="tableFilter.folder">返回</el-button>
            <el-button v-waves :loading="btnLoading" type="warning" @click="handleFilterClear">首页</el-button>
            <el-button v-waves :loading="btnLoading" type="danger" @click="handleRefresh">清空缓存</el-button>
        </el-row>
        <!-- 表格 -->
        <el-table key="id" v-loading="tableLoading" :data="tableList" border fit highlight-current-row style="width: 100%;" @row-click="handleRowClick">
            <el-table-column label="名称" min-width="200px">
                <template slot-scope="scope">
                    <div class="flex align-center pointer">
                        <i class="el-icon-folder text-blue folder-icon text-xxl padding-right-xs" v-if="scope.row.type === 'folder'"></i>
                        <span class="text-lg"> {{ scope.row.name }}</span>
                    </div>
                </template>
            </el-table-column>
            <el-table-column label="修改日期" min-width="120px">
                <template slot-scope="scope">
                    <span>{{ scope.row.update_time }}</span>
                </template>
            </el-table-column>
            <el-table-column label="类型">
                <template slot-scope="scope">
                    <span>{{ scope.row.type | typeFilter }}</span>
                </template>
            </el-table-column>
            <el-table-column label="大小">
                <template slot-scope="scope">
                    <span>{{ scope.row.size }}</span>
                </template>
            </el-table-column>
            <el-table-column label="操作" width="80px" align="center" class-name="small-padding" fixed="right">
                <template slot-scope="scope">
                    <el-button v-waves :loading="btnLoading" type="danger" icon="el-icon-delete" size="mini" circle @click.stop="handleRemove(scope.$index,scope.row)" />
                </template>
            </el-table-column>
        </el-table>
        <runtime-dialog ref="runtimeDialog"></runtime-dialog>
    </div>
</template>

<script>
import { runtime, remove } from '@/api/record'
import runtimeDialog from './runtimeDialog'

export default {
    name: 'Log',
    components: {
        runtimeDialog
    },
    filters: {
        typeFilter(type) {
            const typeList = {
                folder: '文件夹',
                file: '文件'
            }
            return typeList[type]
        }
    },
    data() {
        return {
            tableKey: 0,
            tableList: [],
            tableLoading: true,
            showSearch: false,
            tableFilter: {
                folder: ''
            },
            btnLoading: false,
            btnDisabled: false
        }
    },
    created() {
        this.fetchList()
    },
    methods: {
        fetchList() {
            this.tableLoading = true
            this.btnLoading = true
            runtime(this.tableFilter).then(response => {
                this.tableList = response.data
                this.tableLoading = false
                this.btnLoading = false
            }).catch(error => {
                console.log(error)
                this.tableLoading = false
                this.btnLoading = false
            })
        },
        handleFilterClear() {
            this.tableFilter = {
                folder: ''
            }
            this.fetchList()
        },
        handleRowClick(row) {
            if (row.type === 'folder') {
                this.tableFilter.folder = row.folder
                this.fetchList()
            } else if (row.type === 'file') {
                this.$refs.runtimeDialog.handleOpen(row.folder)
            }
        },
        handleReturn() {
            const folder = this.tableFilter.folder
            if (folder) {
                const beforeFolder = folder.split('/')
                beforeFolder.pop()
                this.tableFilter.folder = beforeFolder.join('/')
                this.fetchList()
            }
        },
        // 清空缓存
        handleRefresh() {
            const _this = this
            _this.btnLoading = true
            this.$confirm('此操作将清除缓存, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                remove({ folder: 'cache' }).then(response => {
                    if (response.code) {
                        _this.tableFilter.folder = ''
                        _this.fetchList()
                        _this.$message.success('清除缓存成功')
                    } else {
                        _this.$message.error(response.msg)
                    }
                    _this.btnLoading = false
                })
            }).catch(() => {
                _this.$message({
                    type: 'info',
                    message: '已取消删除'
                })
                _this.btnLoading = false
            })
        },
        handleRemove(index, row) {
            const _this = this
            _this.btnLoading = true
            this.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                _this.$set(_this.tableList[index], 'delete', true)
                const obj = row.type === 'file' ? { file: row.folder } : { folder: row.folder }
                remove(obj).then(response => {
                    if (response.code === 0) {
                        _this.tableList.splice(index, 1)
                        _this.$message.success(response.msg)
                    } else {
                        _this.$message.error(response.msg)
                    }
                    _this.$set(_this.tableList[index], 'delete', false)
                    _this.btnLoading = false
                    // eslint-disable-next-line handle-callback-err
                }).catch(() => {
                    _this.$set(_this.tableList[index], 'delete', false)
                    _this.btnLoading = false
                })
            }).catch(() => {
                this.$message({
                    type: 'info',
                    message: '已取消删除'
                })
                _this.btnLoading = false
            })
        },
    }
}
</script>
<style lang="scss" scoped>
.folder-icon {
    font-size: 22px;
    cursor: default;
}
</style>
