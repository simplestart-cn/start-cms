<template>
    <div class="layout" :class="classObj">
        <div v-if="device==='mobile' && sidebar.opened" class="drawer-bg" @click="closeSideBar" />
        <sidebar class="layout-sidebar" v-if="layout === 'left'" />
        <div class="layout-header">
            <navbar class="layout-navbar" v-if="layout === 'top'" />
            <tags-view v-if="needTagsView" :collapse="!sidebar.opened" />
        </div>
        <div class="layout-container">
            <!-- 单体模式: 未定义应用远程服务入口时-->
            <transition name="fade-transform" mode="out-in" v-if="app.name == '' || app.entry == '' || app.entry == '/'">
                <keep-alive :include="cachedViews">
                    <router-view :key="path" />
                </keep-alive>
            </transition>
            <!-- 微应用模式：基座基于web目录部署，所以微应用基础路由统一设置为/web -->
            <micro-app :name='app.name' :url='app.entry' :line="app.debug" :keep-alive="!app.debug" :ssr="app.ssr" :disableScopecss="!Boolean(app.scopecss)" :disableSandbox="Boolean(app.sandbox)"  @datachange='onDataChange' baseRoute="/web" @created='created' @beforemount='beforemount' @mounted='mounted' @unmount='unmount' @error='error' v-else></micro-app>
        </div>
        <right-panel v-if="showSetting">
            <setting />
        </right-panel>
    </div>
</template>

<script>
import RightPanel from '@/components/RightPanel'
import Breadcrumb from '@/components/Breadcrumb'
import ResizeMixin from './mixin/ResizeHandler'
import microApp from '@micro-zoe/micro-app'
import { mapState, mapGetters } from 'vuex'
import { EventCenterForMicroApp } from '@micro-zoe/micro-app'
import { Navbar, Setting, Sidebar, TagsView } from './components'
export default {
    name: 'Layout',
    components: {
        Navbar,
        RightPanel,
        Setting,
        Sidebar,
        TagsView,
        Breadcrumb
    },
    mixins: [ResizeMixin],
    computed: {
        ...mapGetters(['apps', 'user', 'token', 'authorize']),
        ...mapState({
            layout: state => state.core.config.layout,
            sidebar: state => state.app.sidebar,
            device: state => state.app.device,
            showSetting: state => state.setting.showSetting,
            needTagsView: state => state.setting.tagsView,
        }),
        classObj() {
            return {
                top: this.layout === 'top',
                left: this.layout === 'left',
                hideSidebar: !this.sidebar.opened,
                openSidebar: this.sidebar.opened,
                hasTagsView: this.needTagsView,
                withoutAnimation: this.sidebar.withoutAnimation,
                mobile: this.device === 'mobile'
            }
        },
        cachedViews() {
            return this.$store.state.tagsView.cachedViews
        },
        path() {
            return this.$route.path
        },
        app() {
            let name = this.$route.meta.app ? this.$route.meta.app : ''
            let app = this.apps.find(item => item.name == name)
            if (typeof app == 'undefined') {
                return {
                    name: '',
                    entry: '/'
                };
            }
            if (app.entry.indexOf('http') == -1) {
                app.entry = window.location.origin + app.entry
            }
            // 关闭沙箱模式后绑定通讯对象
            if (!app.sandbox){
                window[app.name + 'EventCenter'] = new EventCenterForMicroApp(app.name)
            }
            return app;
        },
    },
    watch: {
        $route() {
            this.setAppData();
        },
    },
    methods: {
        closeSideBar() {
            this.$store.dispatch('app/closeSideBar', { withoutAnimation: false })
        },
        created(e) {
            // console.log('元素被创建', e)
        },
        beforemount(e) {
            this.setAppData()
            // console.log(e.detail.name, '即将被渲染',e)
        },
        mounted(e) {
            // console.log(e.detail.name, '已经渲染完成',e)
        },
        unmount(e) {
            // console.log(e.detail.name, '已经卸载',e)
        },
        error(e) {
            console.log(e.detail.name, '渲染出错', e)
        },
        // 下发子应用数据
        setAppData() {
            microApp.setData(this.app.name, {
                app: this.app,
                user: this.user,
                token: this.token,
                route: this.$route,
                authorize: this.authorize,
            })
        },
        // 监听子应用数据
        onDataChange(e) {
            const { data } = e.detail
            // 控制基座路由
            if (data.route) {
                this.$router.push({
                    path: data.route.path || '/',
                    hash: data.route.hash || '',
                    query: data.route.query || {},
                    params: data.route.params || ''
                })
            }
            // 监听应用消息
            if (data.msg) {
                this.$store.dispatch('app/sendMsg', data.msg)
            }
        }
    }
}
</script>

<style lang="scss" scoped>
@import "~@/styles/mixin.scss";
@import "~@/styles/variables.scss";

.layout {
    @include clearfix;
    position: relative;
    display: flex;
    min-height: 100%;
    height: 100%;
    width: 100%;
    .layout-header {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 9;
        width: 100%;
        transition: 0.28s;
    }
    .layout-container {
        position: relative;
        width: 100%;
        min-height: calc(100vh - 50px);
        transition: margin-left 0.28s;
        margin-left: $sideBarWidth;
        overflow-y: scroll;
    }

    &.mobile.openSidebar {
        position: fixed;
        top: 0;
    }
    &.top {
        > .layout-header {
            left: 0px;
        }
        > .layout-container {
            margin-left: 0 !important;
            padding-top: 90px;
        }
    }
    &.left {
        > .layout-header {
            left: 150px;
            width: calc(100% - 150px);
        }
        > .layout-container {
            padding-top: 40px;
        }
    }
    &.left.hideSidebar {
        .layout-header {
            left: 54px;
            width: calc(100% - 54px);
        }
        &.mobile {
            .layout-header {
                left: 0;
                width: 100%;
            }
        }
    }
}

.drawer-bg {
    background: #000;
    opacity: 0.3;
    width: 100%;
    top: 0;
    height: 100%;
    position: absolute;
    z-index: 999;
}

.mobile .layout-header {
    width: 100%;
}
</style>
