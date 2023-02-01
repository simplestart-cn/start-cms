<template>
  <div class="logo-container" :class="{'collapse':collapse}">
    <transition name="logoFade">
      <router-link key="collapse" class="logo-link" to="/">
        <img v-if="logo" :src="logo" class="logo">
        <h1  class="logo-title">{{ title }} </h1>
      </router-link>
    </transition>
  </div>
</template>

<script>
export default {
  name: 'Logo',
  props: {
    collapse: {
      type: Boolean,
      default: false,
    }
  },
  data() {
    return {}
  },
  computed: {
    logo() {
      return this.$store.getters.config ? this.$store.getters.config.logo : ''
    },
    title() {
      return this.$store.getters.config ? this.$store.getters.config.title : ''
    }
  }
}
</script>

<style lang="scss" scoped>
.logoFade-enter-active {
  transition: opacity 1.5s;
}

.logoFade-enter,
.logoFade-leave-to {
  opacity: 0;
}

.logo-container {
  position: relative;
  display: inline-flex;
  justify-content: center;
  align-item: center;
  text-align: center;
  overflow: hidden;

  & .logo-link {
    height: 100%;
    width: 100%;
    padding-left: 15px;
    padding-right: 15px;

    & .logo {
      width: 42px;
      height: 42px;
      vertical-align: middle;
    }

    & .logo-title {
      display: inline-block;
      margin: 0;
      font-weight: 600;
      line-height: 58px;
      font-size: 20px;
      font-family: Avenir, Helvetica Neue, Arial, Helvetica, sans-serif;
      vertical-align: middle;
    }
  }
}
</style>
