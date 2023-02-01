import Vue from 'vue'

import Cookies from 'js-cookie'

import 'normalize.css/normalize.css' // a modern alternative to CSS resets

import element from 'element-ui'
import microApp from '@micro-zoe/micro-app'
import './styles/element-variables.scss'

import '@/styles/index.scss' // global css

import App from './App'
import store from './store'
import router from './router'
// 水波纹指令
import waves from '@/directive/waves'
// 权限指令
import auth from "@/directive/auth";

import './icons' // icon
import './auth' // auth control
import './utils/error-log' // error log

import * as filters from './filters' // global filters


Vue.use(auth);
Vue.use(waves);
Vue.use(element, {
  size: Cookies.get('size') || 'small' // set element-ui default size
})

// register global utility filters
Object.keys(filters).forEach(key => {
  Vue.filter(key, filters[key])
})

Vue.config.productionTip = false

microApp.start()

new Vue({
  el: '#start',
  router,
  store,
  render: h => h(App)
})
