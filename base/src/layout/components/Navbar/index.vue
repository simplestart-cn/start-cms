<template>
    <div :style="'color:'+textColor+';background-color:' + backgroundColor + ';'">
        <logo :collapse="isCollapse" class="logo" />
        <app-menu class="app-menu" />
        <profile class="profile" :textColor="textColor"/>
    </div>
</template>

<script>
import { mapGetters } from 'vuex';
import Logo from './Logo';
import AppMenu from './Menu';
import Profile from '../Profile'


export default {
    components: {
        Logo,
        AppMenu,
        Profile
    },
    data() {
        return {
            list: []
        }
    },
    computed: {
        ...mapGetters([
            'config',
            'sidebar',
            'appNavbar',
        ]),
        layout() {
            return this.config.layout
        },
        textColor() {
            return this.config.text_color
        },
        backgroundColor() {
            return this.config.background_color
        },
        isCollapse() {
            return !this.sidebar.opened
        }
    },
    methods: {

    }
}
</script>

<style lang="scss" scoped>
.layout-navbar {
    display: flex;
    align-items: center;
    height: 58px;
    overflow: hidden;
    position: relative;
    background-color: #001528;
    color: #fff;
    box-shadow: 0 1px 4px rgba(0, 21, 41, 0.08);

    .logo {
        flex-shrink: 0;
    }
    .app-menu {
        display: block;
        flex-grow: 1;
    }
    .profile {
        flex-shrink: 0;
    }
}
</style>
