<template>
  <div>
    <el-scrollbar>
      <el-menu
        :default-active="activeMenu"
        :collapse="!isCollapse"
        :unique-opened="false"
        :collapse-transition="false"
        mode="vertical"
        menu-trigger="click">
        <menu-item v-for="route in menus" :key="route.path" :item="route" :collapse="isCollapse" :base-path="route.path" />
      </el-menu>
    </el-scrollbar>
    <!-- <div v-if="sidebar.opened" class="drawer-bg" @click="handleClickOutside" /> -->
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
      menus: state => state.app.menus,
      sidebar: state => state.app.sidebar
    }),
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
    handleClickOutside() {
      this.$store.dispatch('app/closeSideBar', { withoutAnimation: false })
    }
  }
}
</script>
<style lang="scss" scoped>
  .drawer-bg {
    background: #000;
    opacity: 0.3;
    width: 100%;
    top: 0;
    height: 100%;
    position: absolute;
    z-index: 999;
  }
</style>
