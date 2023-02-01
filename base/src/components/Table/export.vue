<template>
    <el-dialog title="选择导出的数据数" :visible.sync="drawerVisible" :before-close="handleClose" width="30%">
        <div v-for="(item, index) in length" :key="index">
            <el-link type="primary" @click="handleExport(item)">
                {{ item | pageFilter(perPage, exportData) }}
            </el-link>
        </div>
    </el-dialog>
</template>

<script>
import excel from '@/utils/excel'
export default {
    name: 'Export',
    data() {
        return {
            drawerVisible: false,
            exportFilter: {},
            exportData: {
                data: [],
                total: 0
            },
            length: 0
        }
    },
    props: {
        api: {
            type: Function,
            default: () => { }
        },
        rule: {
            type: Array,
            default: () => []
        },
        perPage: {
            type: Number,
            default: 2000
        }
    },
    filters: {
        pageFilter(page, perPage, exportData) {
            const max = Math.floor(exportData.total / perPage)
            if (page > max) {
                return '第（' + (page - 1) * perPage + '-' + exportData.total + '）条'
            } else {
                return '第（' + (page - 1) * perPage + '-' + page * perPage + '）条'
            }
        }
    },
    methods: {
        handleClose() {
            this.drawerVisible = false
        },
        async handleCreate(filter) {
            this.exportFilter = filter
            this.exportFilter.page = 1;
            this.exportFilter.per_page = this.perPage;
            const { data } = await this.api(this.exportFilter)
            this.exportData = data
            this.length = Math.ceil(data.total / this.perPage)
            this.drawerVisible = true
        },
        // 导出数据
        async handleExport(page) {
            const perPage = this.perPage
            this.exportFilter.page = page;
            this.exportFilter.per_page = perPage;
            const { data } = await this.api(this.exportFilter)
            const list = excel.ruleToArray(this.rule, data.data)
            const max = Math.floor(this.exportData.total / perPage)
            if (page > max) {
                list.name = '第（' + (page - 1) * perPage + '-' + this.exportData.total + '）条'
            } else {
                list.name = '第（' + (page - 1) * perPage + '-' + page * perPage + '）条'
            }
            excel.exportFromArray(list)
        }
    }
}
</script>

<style scoped>
</style>
