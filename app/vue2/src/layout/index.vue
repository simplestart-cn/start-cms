<template>
    <div class="app-wrapper">
        <app-sidebar  v-if="sidebar" />
        <app-main />
    </div>
</template>

<script>
import { sidebar } from '@/setting'
import { AppMain, AppSidebar } from './components'
import ResizeMixin from './mixin/ResizeHandler'

export default {
    name: 'Layout',
    data(){
        return {
            sidebar: sidebar
        }
    },
    components: {
        AppMain,
        AppSidebar
    },
    mixins: [ResizeMixin],
}
</script>

<style lang="scss" scoped>
.app-wrapper {
    position: relative;
    height: 100%;
    width: 100%;
    display: flex;
    &:after {
        content: "";
        display: table;
        clear: both;
    }
}
</style>
