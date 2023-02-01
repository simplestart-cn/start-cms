<template>
    <div class="dashboard-container">
        <el-row :gutter="10">
            <el-col class="dashboard" :span="item.col" v-for="(item, index) in list" :key="index" v-auth="item.auth">
                <div class="control">
                    <el-dropdown trigger="click" placement="bottom" @command="handleUpdate">
                        <i class="pointer el-icon-more" />
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item :command="{index: index, action:'update'}">编辑</el-dropdown-item>
                            <el-dropdown-item :command="{index: index, action:'hidden'}">隐藏</el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                </div>
                <micro-app :name='item.name' :url='getEntry(item)' :line="false" :keep-alive="true" baseRoute="/web" @beforemount='beforeMount(item)' @datachange='dataChange'></micro-app>
            </el-col>
        </el-row>
        <el-dialog :title="temp.title" :visible.sync="formVisible" width="500px">
            <el-form :model="temp">
                <el-form-item label="标题">
                    <el-input v-model="temp.title" autocomplete="off"></el-input>
                </el-form-item>
                <el-form-item label="排序">
                    <el-input-number v-model="temp.sort" autocomplete="off"></el-input-number>
                </el-form-item>
                <el-form-item label="状态">
                    <el-switch v-model="temp.status" :active-value="1" :inactive-value="0"></el-switch>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="formVisible = false">取 消</el-button>
                <el-button type="primary" @click="handleSubmit">确 定</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
import { getList, update, updateStatus } from '@/api/dashboard'
import { mapGetters } from 'vuex'
import microApp from '@micro-zoe/micro-app'
export default {
    name: 'Dashboard',
    data() {
        return {
            temp: {},
            list: [],
            formVisible: false
        }
    },
    computed: {
        ...mapGetters(['apps', 'user', 'token', 'authorize']),
    },
    created() {
        // 获取看板
        getList().then(res => {
            this.list = res.data
        })
    },
    methods: {
        getEntry(item) {
            const app = this.apps.find(app => app.name == item.app) || {}
            if (typeof app == 'undefined') {
                return '/'
            }
            if (app.entry.indexOf('http') == -1) {
                return window.location.origin + app.entry
            }
            return app.entry;
        },
        beforeMount(item) {
            const app = this.apps.find(app => app.name == item.app) || {}
            const route = item.route ? { path: item.route } : { path: '/dashboard' }
            microApp.setData(item.name, {
                app: app,
                route: route,
                user: this.user,
                token: this.token,
                authorize: this.authorize,
            })

        },
        // 监听子应用数据
        dataChange(e) {
            const { data } = e.detail
            if (data.route) {
                // 此处不允许控制基座路由,否则仪表盘无法显示一个应用的多个模块
                setTimeout(() => {
                    this.$router.push({
                        path: '/dashboard'
                    })
                }, 1000)
            }
            // 监听应用消息
            if (data.msg) {
                this.$store.dispatch('app/sendMsg', data.msg)
            }
        },
        handleUpdate(command) {
            let item = this.list[command.index];
            switch (command.action) {
                case 'update':
                    this.temp = item;
                    this.formVisible = true;
                    break;
                case 'hidden':
                    updateStatus({ id: item.id, status: 0 })
                    this.list.splice(command.index, 1)
                    break;
                default:
                    break;
            }
        },
        async handleSubmit(){
          const { code, msg } = await update({
            id: this.temp.id,
            sort: this.temp.sort,
            title: this.temp.title,
            status: this.temp.status
          })
          if(!code){
            this.$message.success(msg)
            this.formVisible = false
          }else{
            this.$message.error(msg)
          }
        }
    }
}
</script>
<style lang="scss" scoped>
.dashboard-container {
    min-height: 100%;
    padding: 15px;
    background-color: #f0f2f5;
    .dashboard {
        position: relative;
        margin-bottom: 10px;
        border-radius: 5px;
        overflow: hidden;
        .control {
            position: absolute;
            right: 15px;
            top: 15px;
            z-index: 1;
        }
        .el-icon-more {
            transform: rotate(90deg);
        }
    }
}
</style>
