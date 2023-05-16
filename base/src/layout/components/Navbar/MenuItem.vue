<template>
    <div v-if="item.menu && !item.hidden" class="menu-wrapper">
        <template v-if="hasOneShowingChild(item.children, item) && (!onlyOneChild.children||onlyOneChild.noShowingChildren)">
            <app-link v-if="onlyOneChild.meta" :to="onlyOneChild.path">
                <el-menu-item :index="resolvePath(onlyOneChild.path)" :class="{'submenu-title-noDropdown':!isNest}" :style="'background-color:' + backgroundColor">
                    <item :icon="onlyOneChild.meta.icon||(item.meta&&item.meta.icon)" :title="onlyOneChild.meta.title" :collapse="collapse" />
                </el-menu-item>
            </app-link>
        </template>

        <el-submenu v-else ref="subMenu" :index="resolvePath(item.path)" popper-append-to-body>
            <template slot="title">
                <item v-if="item.meta" :icon="item.meta && item.meta.icon" :title="item.meta.title" :collapse="collapse" />
            </template>
            <menu-item v-for="child in item.children" :key="child.path" :is-nest="true" :item="child" :base-path="resolvePath(child.path)" class="nest-menu" />
        </el-submenu>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'
import path from 'path'
import { isExternal } from '@/utils/validate'
import Item from './Item'
import AppLink from './Link'
import FixiOSBug from './FixiOSBug'

export default {
    name: 'MenuItem',
    components: { Item, AppLink },
    mixins: [FixiOSBug],
    props: {
        // route object
        item: {
            type: Object,
            required: true
        },
        isNest: {
            type: Boolean,
            default: false
        },
        collapse: {
            type: Boolean,
            default: false
        },
        basePath: {
            type: String,
            default: ''
        }
    },
    computed: {
        ...mapGetters(['config']),
        textColor() {
            return this.config.text_color;
        },
        backgroundColor() {
            return this.config.background_color;
        }
    },
    data() {
        // To fix https://github.com/PanJiaChen/vue-admin-template/issues/237
        // TODO: refactor with render function
        this.onlyOneChild = null
        return {}
    },
    methods: {
        hasOneShowingChild(children = [], parent) {
            const showingChildren = children.filter(item => {
                if (!item.menu || item.hidden) {
                    return false
                } else {
                    // Temp set(will be used if only has one showing child)
                    this.onlyOneChild = item
                    return true
                }
            })
            // When there is only one child router, the child router is displayed by default
            if (showingChildren.length === 1) {
                return true
            }

            // Show parent if there are no child router to display
            if (showingChildren.length === 0) {
                const obj = JSON.parse(JSON.stringify(parent))
                delete obj.children;
                this.onlyOneChild = obj
                // Colber 调整菜单(:to="onlyOneChild.path" 少了path.resolve()调用)
                // this.onlyOneChild = { ... parent, path: '', noShowingChildren: true }
                return true
            }

            return false
        },
        resolvePath(routePath) {
            if (isExternal(routePath)) {
                return routePath
            }
            if (isExternal(this.basePath)) {
                return this.basePath
            }
            return path.resolve(this.basePath, routePath)
        }
    }
}
</script>
<style lang="scss" scoped>
.el-menu--horizontal > .menu-wrapper {
    ::v-deep .el-submenu__title,.submenu-title-noDropdown {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        span {
            font-size: 12px;
            line-height: initial;
        }
        i {
            color: inherit;
        }
    }
    ::v-deep .el-submenu__icon-arrow {
        display: none;
    }
}
.el-menu--horizontal {
    .el-menu {
        .menu-wrapper {
            ::v-deep .el-menu-item,
            ::v-deep.el-submenu__title {
                height: 42px;
                line-height: 42px;
                padding: 0 20px;
                i {
                    color: inherit;
                }
            }
        }
    }
}
.menu-wrapper {
    height: 100%;
    .svg-icon {
        display: block;
        margin-bottom: 5px;
        font-size: 22px;
        transition: 0.5s;
        opacity: 0.5;
    }
    span {
        display: block;
        margin-bottom: -5px;
        font-weight: 500;
        overflow: hidden;
        transition: 0.5s;
    }
}
.menu-wrapper .is-active .svg-icon,.menu-wrapper:hover .svg-icon {
    opacity: 1;
    transform: scale(1.15);
}
.el-menu--horizontal .el-menu .menu-wrapper,
.el-menu--horizontal .el-menu .el-submenu__title {
    float: none;
}
</style>
