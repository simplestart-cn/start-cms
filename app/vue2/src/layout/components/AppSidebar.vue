<template>
    <div :class="classObj" class="app-sidebar">
        <el-scrollbar class="scrollbar-container">
            <el-menu :default-active="activeMenu" :collapse="isCollapse" :unique-opened="false" :collapse-transition="false" mode="vertical" menu-trigger="click">
                <menu-item v-for="route in sidebar.menus" :key="route.path" :item="route" :collapse="isCollapse" :base-path="route.path" />
            </el-menu>
        </el-scrollbar>
        <div class="hamburger" @click="toggleSideBar">
            <i :class="sidebar.opened ? 'el-icon-s-fold': 'el-icon-s-unfold'" ></i>
        </div>
    </div>
</template>

<script>
import { mapState } from 'vuex'
import MenuItem from './Sidebar/MenuItem'

export default {
    name: 'AppSidebar',
    components: { MenuItem },
    computed: {
        ...mapState({
            sidebar: state => state.app.sidebar
        }),
        classObj() {
            return {
                hide: !this.sidebar.opened,
            }
        },
        activeMenu() {
            const route = this.$route
            const { meta, path } = route
            // if set path, the sidebar will highlight the path you set
            if (meta.activeMenu) {
                return meta.activeMenu
            }
            return path
        },
        isCollapse() {
            return !this.sidebar.opened
        }
    },
    methods: {
        toggleSideBar() {
            this.$store.dispatch('app/toggleSideBar')
        },
    }
}
</script>
<style lang="scss" scoped>
.app-sidebar {
    position: relative;
    font-family: Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-align: center;
    color: #2c3e50;
    width: 200px;
    height: calc(100vh - 40px);
    transition: width 0.28s;
    display: inline-block;
    border-right: 1px solid rgb(230, 230, 230);
    flex-shrink: 0;
    .scrollbar-container {
        height: 100%;
        :deep().el-scrollbar__wrap {
            overflow-x: hidden;
        }
    }

    .el-menu-item {
        font-size: 16px;
    }

    .el-menu {
        width: 100%;
        border-right: none !important;
    }

    .el-submenu__title span {
        font-size: 16px;
        user-select: none;
    }

    .el-menu-item span {
        font-size: 14px;
        user-select: none;
    }
    &.hide {
        width: 54px !important;
        :deep().el-submenu__title {
            padding: 0 !important;
        }
        :deep().el-submenu__icon-arrow {
            display: none !important;
        }
    }
    .hamburger {
        position: absolute;
        right: 5px;
        bottom: 10px;
        display: block;
        width: 45px;
        line-height: 30px;
        font-size: 22px;
        cursor: pointer;
        background: #ffffff;
        -webkit-tap-highlight-color: transparent;
        z-index: 1;
    }
}
.layout.top {
  .app-sidebar {
    /* 90 = navbar + tags-view = 50 + 40 */
    height: calc(100vh - 90px);
  }
  .fixed-header+.app-sidebar {
    padding-top: 90px;
  }
}
</style>
