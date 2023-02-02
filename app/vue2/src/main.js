// 微前端环境下__MICRO_APP_PUBLIC_PATH__为应用入口地址
// 如果应用和框架是同域名部署则不需要引入publi-path,否则动态路由会无法生效
// import "./public-path";
import Vue from "vue";
import App from "./App.vue";
import store from "./store";
import router from "./router";

// 波纹指令
import waves from "@/directive/waves";
// 权限指令
import auth from "@/directive/auth";
// ElementUI
import element from "element-ui";
import "element-ui/lib/theme-chalk/index.css";
// 表单插件
import formCreate from "@form-create/element-ui";

Vue.use(auth);
Vue.use(waves);
Vue.use(element, {
  size: "small",
});
Vue.use(formCreate);
Vue.config.productionTip = false;

async function appStart() {
  // 是否是微前端环境
  if (window.__MICRO_APP_ENVIRONMENT__) {
    // 启动时主动获取基座下发的数据
    const { app, user, token, route, authorize } = window.microApp.getData();
    console.log("获取基座下发的数据:", { app, user, token, route, authorize });
    // 记录账户状态
    if(user && token && authorize){
      await store.dispatch("user/setState", {
        info: user,
        token: token,
        authorize: authorize,
      });
    }
    // 记录应用配置
    if (app && app.config){
      await store.dispatch('app/setConfig', app.config)
    }
    // 构建权限路由
    if(app && app.routes){
      const accessRoutes = await store.dispatch("app/generateRoutes", app.routes);
      accessRoutes.forEach(item => {
        router.addRoute(item)
      })
    }
    // 进入指定页面
    if (route) {
      router.push(route, ()=>{});
    }
    // 监听基座下发的数据
    window.microApp.addDataListener((data) => {
      console.log("监听基座下发的数据:", data);
      if (data.route && data.route.path !== router.currentRoute.path) {
        router.push(data.route.path);
      }
    });
  } else {
    // 单应用模式：主动获取后端数据
    const { apps } = await store.dispatch("user/getState");
    const routes = apps.reduce((pre, app) => {
      if (app.routes instanceof Array) {
        app.routes.forEach((item) => {
          item.entry = app.entry;
        });
        return pre.concat(app.routes);
      }
      return pre;
    }, []);
    // 构建权限路由
    const accessRoutes = await store.dispatch("app/generateRoutes", routes);
    accessRoutes.forEach(item => {
      router.addRoute(item)
    })
  }
  // 添加路由守卫
  router.beforeEach(async (to, from, next) => {
    if (window.__MICRO_APP_ENVIRONMENT__) {
      console.log('to',to)
      // 通知基座路由变更
      window.microApp.dispatch({
        route: to,
      });
    }
    next();
  });
}

// 应用挂载
const app = new Vue({
  router,
  store,
  render: (h) => h(App),
}).$mount("#app");
// 应用启动
appStart();
// 应用卸载
window.addEventListener("unmount", function () {
  app.$destroy();
});
