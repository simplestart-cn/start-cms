<template>
    <div class="tags-view-container">
        <scroll-pane ref="scrollPane" class="tags-view-wrapper">
            <hamburger id="hamburger-container" :is-active="sidebar.opened" class="hamburger-container" @toggleClick="toggleSideBar" v-if="layout === 'left'" />
            <router-link v-for="tag in visitedViews" ref="tag" :key="tag.path" :class="isActive(tag)?'active':''" :to="{ path: tag.path, query: tag.query, fullPath: tag.fullPath }" tag="span" class="tags-view-item" @click.middle.native="!isAffix(tag)?closeSelectedTag(tag):''" @contextmenu.prevent.native="openMenu(tag,$event)">
                {{ tag.title }}
                <span v-if="!isAffix(tag)" class="el-icon-close" @click.prevent.stop="closeSelectedTag(tag)" />
            </router-link>
        </scroll-pane>

        <div class="tags-tools-container">
            <el-tooltip content="全屏" effect="dark" placement="bottom" v-if="device !== 'mobile'">
                <screenfull id="screenfull" class="hover-effect" />
            </el-tooltip>
            <el-tooltip content="消息" effect="dark" placement="bottom" v-if="msg.length">
                <div class="message-tags">
                    <el-badge is-dot class="item">
                        <span @click="openMsg">
                            <svg-icon icon-class="message" class="text-xl" />
                        </span>
                    </el-badge>
                </div>
            </el-tooltip>
            <el-tooltip class="pointer" effect="dark" content="内置图标" placement="bottom" v-if="debug">
                <router-link :to="{path: '/icons'}">
                    <svg-icon icon-class="icon" class="text-xl" />
                </router-link>
            </el-tooltip>
            <el-tooltip class="pointer" effect="dark" content="应用中心" placement="bottom" v-auth:core_app>
                <router-link :to="{path: '/core/app'}">
                    <svg-icon icon-class="appstore" style="font-size:26px" />
                </router-link>
            </el-tooltip>
            <profile v-if="layout === 'left'"></profile>
        </div>

        <ul v-show="visible" :style="{left:left+'px',top:top+'px'}" class="contextmenu">
            <li @click="refreshSelectedTag(selectedTag)">刷新</li>
            <li v-if="!isAffix(selectedTag)" @click="closeSelectedTag(selectedTag)">关闭</li>
            <li @click="closeOthersTags">关闭其它</li>
            <li @click="closeAllTags(selectedTag)">全部关闭</li>
        </ul>
        <msg ref="msg"></msg>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Msg from './Msg'
import Profile from '../Profile'
import ScrollPane from './ScrollPane'
import Screenfull from '@/components/Screenfull'
import Hamburger from '@/components/Hamburger'
import path from 'path'

export default {
    components: { Msg, Profile, ScrollPane, Screenfull, Hamburger },
    props: {
        collapse: {
            type: Boolean,
            default: false,
        }
    },
    data() {
        return {
            visible: false,
            top: 0,
            left: 0,
            selectedTag: {},
            affixTags: []
        }
    },
    computed: {
        ...mapGetters([
            'msg',
            'device',
            'sidebar',
            'config'
        ]),
        debug() {
            return this.config.debug
        },
        layout() {
            return this.config.layout
        },
        visitedViews() {
            if(this.device === 'mobile'){
                return []
            }
            return this.$store.state.tagsView.visitedViews
        },
        routes() {
            return this.$store.state.app.appRoutes
        }
    },
    watch: {
        $route() {
            this.addTags()
            this.moveToCurrentTag()
        },
        visible(value) {
            if (value) {
                document.body.addEventListener('click', this.closeMenu)
            } else {
                document.body.removeEventListener('click', this.closeMenu)
            }
        }
    },
    mounted() {
        this.initTags()
        this.addTags()
    },
    methods: {
        toggleSideBar() {
            this.$store.dispatch('app/toggleSideBar')
        },
        isActive(route) {
            return route.path === this.$route.path
        },
        isAffix(tag) {
            return tag.meta && tag.meta.affix
        },
        filterAffixTags(routes, basePath = '/') {
            let tags = []
            routes.forEach(route => {
                if (route.meta && route.meta.affix) {
                    const tagPath = path.resolve(basePath, route.path)
                    tags.push({
                        fullPath: tagPath,
                        path: tagPath,
                        name: route.name,
                        title: route.title,
                        meta: { ...route.meta }
                    })
                }
                if (route.children) {
                    const tempTags = this.filterAffixTags(route.children, route.path)
                    if (tempTags.length >= 1) {
                        tags = [...tags, ...tempTags]
                    }
                }
            })
            return tags
        },
        initTags() {
            const affixTags = this.affixTags = this.filterAffixTags(this.routes)
            for (const tag of affixTags) {
                // Must have tag name
                if (tag.name) {
                    this.$store.dispatch('tagsView/addVisitedView', tag)
                }
            }
        },
        addTags() {
            const { name } = this.$route
            if (name) {
                this.$store.dispatch('tagsView/addView', this.$route)
            }
            return false
        },
        moveToCurrentTag() {
            const tags = this.$refs.tag
            this.$nextTick(() => {
                for (const tag of tags) {
                    if (tag.to.path === this.$route.path) {
                        this.$refs.scrollPane.moveToTarget(tag)
                        // when query is different then update
                        if (tag.to.fullPath !== this.$route.fullPath) {
                            this.$store.dispatch('tagsView/updateVisitedView', this.$route)
                        }
                        break
                    }
                }
            })
        },
        refreshSelectedTag(view) {
            this.$store.dispatch('tagsView/delCachedView', view).then(() => {
                const { fullPath } = view
                this.$nextTick(() => {
                    this.$router.replace({
                        path: '/redirect' + fullPath
                    })
                })
            })
        },
        closeSelectedTag(view) {
            this.$store.dispatch('tagsView/delView', view).then(({ visitedViews }) => {
                if (this.isActive(view)) {
                    this.toLastView(visitedViews, view)
                }
            })
        },
        closeOthersTags() {
            this.$router.push(this.selectedTag)
            this.$store.dispatch('tagsView/delOthersViews', this.selectedTag).then(() => {
                this.moveToCurrentTag()
            })
        },
        closeAllTags(view) {
            this.$store.dispatch('tagsView/delAllViews').then(({ visitedViews }) => {
                if (this.affixTags.some(tag => tag.path === view.path)) {
                    return
                }
                this.toLastView(visitedViews, view)
            })
        },
        toLastView(visitedViews, view) {
            const latestView = visitedViews.slice(-1)[0]
            if (latestView) {
                this.$router.push(latestView.fullPath)
            } else {
                // now the default is to redirect to the home page if there is no tags-view,
                // you can adjust it according to your needs.
                if (view.name === 'Dashboard') {
                    // to reload home page
                    this.$router.replace({ path: '/redirect' + view.fullPath })
                } else {
                    this.$router.push('/')
                }
            }
        },
        openMenu(tag, e) {
            const menuMinWidth = 105
            const offsetLeft = this.$el.getBoundingClientRect().left // container margin left
            const offsetWidth = this.$el.offsetWidth // container width
            const maxLeft = offsetWidth - menuMinWidth // left boundary
            const left = e.clientX - offsetLeft + 15 // 15: margin right

            if (left > maxLeft) {
                this.left = maxLeft
            } else {
                this.left = left
            }

            this.top = e.clientY
            this.visible = true
            this.selectedTag = tag
        },
        closeMenu() {
            this.visible = false
        },
        openMsg() {
            this.$refs.msg.toggle()
        }
    }
}
</script>

<style lang="scss" scoped>
.tags-view-container {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 40px;
    width: 100%;
    padding: 0 10px;
    background: #fff;
    border-bottom: 1px solid #d8dce5;
    transition: padding-left 0.28s;
    box-shadow: 0 0 8px 0 rgba(0, 0, 0, 0.1);
    .tags-view-wrapper {
        .tags-view-item {
            display: inline-block;
            position: relative;
            cursor: pointer;
            height: 26px;
            line-height: 26px;
            border: 1px solid #d8dce5;
            color: #495060;
            background: #fff;
            padding: 0 8px;
            font-size: 12px;
            margin-left: 5px;
            &.active {
                background-color: #42b983;
                color: #fff;
                border-color: #42b983;
                &::before {
                    content: "";
                    background: #fff;
                    display: inline-block;
                    width: 8px;
                    height: 8px;
                    border-radius: 50%;
                    position: relative;
                    margin-right: 2px;
                }
            }
        }

        // for hamburger
        .hamburger-container {
            display: inline-block;
            width: 30px;
            line-height: 30px;
            text-align: center;
            cursor: pointer;
            background: #ffffff;
            -webkit-tap-highlight-color: transparent;
            z-index: 1;
        }
    }
    .contextmenu {
        margin: 0;
        background: #fff;
        z-index: 3000;
        position: absolute;
        list-style-type: none;
        padding: 5px 0;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 400;
        color: #333;
        box-shadow: 2px 2px 3px 0 rgba(0, 0, 0, 0.3);
        li {
            margin: 0;
            padding: 7px 16px;
            cursor: pointer;
            &:hover {
                background: #eee;
            }
        }
    }
}

.tags-tools-container {
    display: flex;
    align-items: center;
    flex-shrink: 0;
    font-size: 18px;
    color: #323232;
    cursor: pointer;
    .el-tooltip {
        display: flex;
        align-items: center;
        padding: 0 10px;
    }
}
</style>

<style lang="scss">
.appstore {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
}
//reset element css of el-icon-close
.tags-view-wrapper {
    .tags-view-item {
        .el-icon-close {
            width: 16px;
            height: 16px;
            vertical-align: 2px;
            border-radius: 50%;
            text-align: center;
            transition: all 0.3s cubic-bezier(0.645, 0.045, 0.355, 1);
            transform-origin: 100% 50%;
            &:before {
                transform: scale(0.6);
                display: inline-block;
                vertical-align: -3px;
            }
            &:hover {
                background-color: #b4bccc;
                color: #fff;
            }
        }
    }
}
</style>
