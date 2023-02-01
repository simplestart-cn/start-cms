<template>
    <div class="app-container solid">
        <!-- 操作 -->
        <div class="flex align-center justify-between margin-top">
            <el-radio-group class="flex align-center" v-model="appType" size="medium" @input="handleType">
                <el-radio-button label="全部"></el-radio-button>
                <el-radio-button label="已下载"></el-radio-button>
                <el-radio-button label="已启用"></el-radio-button>
                <el-radio-button label="未启用"></el-radio-button>
            </el-radio-group>
            <div class="flex align-center">
                <account></account>
            </div>
        </div>
        <div class="flex align-center margin-top">
            <el-button type="primary" icon="el-icon-upload" size="small" @click="handleUpload">本地上传</el-button>
            <el-input v-model="tableFilter.keyword" class="search-input" placeholder="应用名称或简介" @change="handleFilter" clearable>
                <i slot="prefix" class="el-input__icon el-icon-search"></i>
            </el-input>
        </div>
        <!-- <div class="flex align-center">
            <div class="margin-top margin-right-sm">分类:</div>
            <el-radio-group class="flex align-center" v-model="appCategory" size="mini" @input="handleCategory">
                <el-radio-button class="margin-top" :label="item" v-for="(item, index) in categroyList" :key="index"></el-radio-button>
                <el-radio-button class="margin-top" label="未分类"></el-radio-button>
            </el-radio-group>
        </div>
        <div class="flex align-center margin-top">
            <div class="margin-right-sm">价格:</div>
            <el-radio-group class="flex align-center" v-model="appPrice" size="mini" @input="handlePrice">
                <el-radio-button label="全部"></el-radio-button>
                <el-radio-button label="免费"></el-radio-button>
                <el-radio-button label="付费"></el-radio-button>
            </el-radio-group>
        </div> -->
        <el-divider></el-divider>
        <!-- 表格 -->
        <el-table v-loading="tableLoading" :data="tableData.data" fit highlight-current-row style="width: 100%; margin-top: 15px">
            <el-table-column label="名称" width="150" align="center">
                <template slot-scope="scope">
                    <span>{{ scope.row.title }}</span>
                </template>
            </el-table-column>
            <el-table-column label="作者" width="150px" align="center">
                <template slot-scope="scope">
                    <span>{{ scope.row.author }}</span>
                </template>
            </el-table-column>
            <el-table-column label="简介">
                <template slot-scope="scope">
                    <span>{{ scope.row.summary }}</span>
                </template>
            </el-table-column>
            <el-table-column label="版本" width="80px" align="center">
                <template slot-scope="scope">
                    <div class="version">
                        <el-tooltip content="点击升级" placement="top" v-if="scope.row.upgradeable">
                            <el-badge is-dot class="pointer">{{ scope.row.version }}</el-badge>
                        </el-tooltip>
                        <span v-else>{{scope.row.version}}</span>
                    </div>
                </template>
            </el-table-column>
            <el-table-column label="价格" width="80px" align="center">
                <template slot-scope="scope">
                    <span class="text-orange" v-if="scope.row.price">{{ scope.row.price }}</span>
                    <span v-else>免费</span>
                </template>
            </el-table-column>
            <el-table-column label="状态" width="70px" align="center">
                <template slot-scope="scope">
                    <span class="text-blue" v-if="scope.row.status">▶</span>
                    <span class="text-lg text-orange" v-else-if="scope.row.installed">∎</span>
                    <span class="text-sm text-gray" v-else>未启用</span>
                </template>
            </el-table-column>
            <el-table-column label="操作" width="200px" align="center">
                <template slot-scope="scope">
                    <el-button v-waves type="primary" size="mini" :loading="btnLoading" round @click="handleDownload(scope.row.name)" v-if="!scope.row.downloaded">安装</el-button>
                    <el-button v-waves type="default" size="mini" :loading="btnLoading" round @click="handleUpdate(scope.row.name)" v-if="scope.row.downloaded && scope.row.installed">设置</el-button>
                    <el-button v-waves type="success" size="mini" :loading="btnLoading" round @click="handleInstall(scope.row.name)" v-if="scope.row.downloaded && !scope.row.installed">启用</el-button>
                    <el-button v-waves type="danger" size="mini" :loading="btnLoading" round @click="handleUninstall(scope.row.name)" v-if="scope.row.installed">停用</el-button>
                    <el-button v-waves type="danger" size="mini" :loading="btnLoading" round @click="handleRemove(scope.$index, scope.row.name)" v-if="!scope.row.installed">移除</el-button>
                </template>
            </el-table-column>
        </el-table>

        <!-- 分页 -->
        <div class="pagination-container">
            <el-pagination v-show="tableData.total > 0" :current-page="tableData.cur_page" :page-size="tableData.per_page" :total="tableData.total" background layout="total, prev, pager, next, jumper" @current-change="handleCurrentChange" />
        </div>

        <!-- 表单 -->
        <uploader ref="uploader" @success="handleUploadSuccess"></uploader>
        <edit-page ref="editPage" @submit="fetchList" />
    </div>
</template>

<script>
import { getPage, uninstall, install, updateStatus, remove } from '@/api/app'
import editPage from './edit'
import uploader from './uploader'
import account from './account'
export default {
    name: 'Auth',
    components: {
        account,
        uploader,
        editPage
    },
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
            appType: '全部',
            appPrice: '全部',
            appCategory: '全部',
            categroyList: ['全部', '小程序', '多媒体', '工作流', '内容管理', '市场营销', '数据分析', '活动/游戏', '业务/办公', '安全/验证', '开发/测试', '财务/支付', '配送/交付'],
            tableData: {
                data: [],
                total: 0,
            },
            tableFilter: {
                page: 1,
                keyword: ''
            },
            tableLoading: false,
            btnLoading: false,
        }
    },
    watch: {
    },
    created() {
        this.fetchList()
    },
    methods: {
        async fetchList() {
            this.tableLoading = true
            const { data } = await getPage(this.tableFilter);
            this.tableData = data;
            this.tableLoading = false;
        },
        handleType(value) {
            this.appType = value
            switch (value) {
                case '已下载':
                    this.tableFilter.type = "downloaded"
                    break;
                case '已启用':
                    this.tableFilter.type = "installed"
                    break;
                case '未启用':
                    this.tableFilter.type = "uninstall"
                    break;
                default:
                    this.tableFilter.type = ""
                    break;
            }
            this.fetchList()
        },
        handlePrice(value) {
            this.appPrice = value
            if(value == '全部'){
                this.tableFilter.price = ''
            }else{
                this.tableFilter.price = value
            }
            this.fetchList();
        },
        handleCategory(value) {
            this.appPrice = '全部';
            this.tableFilter.price = '';
            this.appCategory = value;
            if(value == '全部'){
                this.tableFilter.category = ''
            }else{
                this.tableFilter.category = value;
            }
            this.fetchList()
        },
        handleFilter(keyword) {
            if (keyword) {
                this.tableFilter.keyword = keyword
            } else {
                this.tableFilter.keyword = ''
            }
            this.tableFilter.page = 1
            this.fetchList()
        },
        handleFilterClear() {
            this.tableFilter.page = 1;
            this.tableFilter.keyword = '';
            this.fetchList()
        },
        handleCurrentChange(val) {
            this.tableFilter.page = val
            this.fetchList()
        },
        handleUpload() {
            this.$refs.uploader.show()
        },
        handleUploadSuccess(installed){
            if(installed){
                this.fetchList()
            }else{
                this.appType = '已下载'
                this.fetchList()
            }
        },
        handleRemove(index, name) {
            const _this = this
            this.$confirm('此操作将永久删除应用目录及相关数据, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                _this.$set(_this.tableData.data[index], 'delete', true)
                remove({ name }).then(response => {
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
        handleInstall(name) {
            const _this = this
            _this.$confirm('此操作将安装此应用, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                _this.btnLoading = true
                install({ name }).then(response => {
                    if (response.code === 0) {
                        _this.fetchList()
                        _this.$message.success(response.msg)
                    } else {
                        _this.$message.error(response.msg)
                    }
                    _this.btnLoading = false
                }).catch(() => {
                    _this.btnLoading = false
                })
            }).catch(error => {
                _this.btnLoading = false
                error.message && this.$message.error(error.message)
            })
        },
        handleUninstall(name) {
            const _this = this
            _this.$confirm('此操作将卸载此应用, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                _this.btnLoading = true
                uninstall({ name }).then(response => {
                    if (response.code === 0) {
                        _this.fetchList()
                        _this.$message.success(response.msg)
                    } else {
                        _this.$message.error(response.msg)
                    }
                    _this.btnLoading = false
                }).catch(() => {
                    _this.btnLoading = false
                })
            }).catch(error => {
                _this.btnLoading = false
                error.message && this.$message.error(error.message)
            })
        },
        // 更新升级
        handleUpgrade() {
            const _this = this
            _this.$confirm('此操作将更新此应用, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                _this.btnLoading = true
                update({ name }).then(response => {
                    if (response.code === 0) {
                        _this.fetchList()
                        _this.$message.success(response.msg)
                    } else {
                        _this.$message.error(response.msg)
                    }
                    _this.btnLoading = false
                }).catch(() => {
                    _this.btnLoading = false
                })
            }).catch(error => {
                _this.btnLoading = false
                error.message && this.$message.error(error.message)
            })
        },
        handleUpdate(name) {
            this.$refs.editPage.handleUpdate(name)
        },
        async handleUpdateStatus(index) {
            const item = this.tableData.data[index]
            const res = await updateStatus({ name: item.name, 'status': item.status })
            !res.code ? this.tableData.data[index]['status'] = item.status : this.tableData.data[index]['status'] = 1 - item.status
        }
    }
}
</script>
<style rel="stylesheet/scss" lang="scss" scope>
.search-input {
    width: 220px;
    margin-left: 10px;
}
.version {
    .el-badge__content.is-fixed.is-dot {
        top: 5px;
        right: 0;
    }
}
</style>
