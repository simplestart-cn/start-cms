// 微前端环境下__MICRO_APP_PUBLIC_PATH__为应用入口地址
// 如果应用和框架是同域名部署则不需要引入publi-path,否则动态路由会无法生效
// import "./public-path";
import Vue from "vue";
import App from "./App.vue";
import store from "./store";
import router from "./router";

// SVG图标
import './icons'

// 波纹指令
import waves from "@/directive/waves";
// 权限指令
import auth from "@/directive/auth";
// ElementUI
import element from "element-ui";
import "element-ui/lib/theme-chalk/index.css";
// 表单插件
import formCreate from "@form-create/element-ui";
import fileLibrary from "@/components/FormCreate/FileLibrary";
import wangEditor from "@/components/FormCreate/WangEditor";
formCreate.component("file-library", fileLibrary);
formCreate.component("wang-editor", wangEditor);

Vue.use(auth);
Vue.use(waves);
Vue.use(element, {
    size: "small",
});
Vue.use(formCreate);
Vue.config.productionTip = false;

// 启动微应用数据
async function start() {
    // 路由守卫
    router.beforeEach(async (to, from, next) => {
        if (window.__MICRO_APP_ENVIRONMENT__) {
            window.microApp.dispatch({
                route: to,
            });
        }
        next();
    });
    // 是否是微前端环境
    if (window.__MICRO_APP_ENVIRONMENT__) {
        // 监听基座下发的数据
        window.microApp.addDataListener((data) => {
            // console.log("监听基座下发的数据:", data);
            if (data.route && data.route.path !== router.currentRoute.path) {
                router.push(data.route.path);
            }
        });
        // 获取基座下发的数据
        const { app, user, token, authorize, route } =
            window.microApp.getData();
        // console.log("获取基座下发的数据:", window.microApp.getData());
        // 接收登录账户
        if (user && token && authorize) {
            await store.dispatch("user/setState", {
                info: user,
                token: token,
                authorize: authorize,
            });
        }
        // 构建权限路由
        if (app && app.routes) {
            const accessRoutes = await store.dispatch(
                "app/generateRoutes",
                app.routes
            );
            router.addRoutes(accessRoutes);
        }
        // 进入指定页面
        if (route) {
            router.push(route, () => {});
        }
    } else {
        // 调试：主动获取后端数据
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
        router.addRoutes(accessRoutes);
    }
}

// --------默认模式--------
// 应用挂载
// const app = new Vue({
//   router,
//   store,
//   render: (h) => h(App),
// }).$mount("#app");
// // 应用卸载
// window.addEventListener("unmount", function () {
//   app.$destroy();
// });
// // 应用启动
// start();

// --------umd模式--------
let app = null;
// 将渲染操作放入 mount 函数
function mount() {
    app = new Vue({
        router,
        store,
        render: (h) => h(App),
    }).$mount("#app");
    // 应用启动
    start();
}

// 将卸载操作放入 unmount 函数
function unmount() {
    app.$destroy();
    app.$el.innerHTML = "";
    app = null;
}

if (window.__MICRO_APP_ENVIRONMENT__) {
    // 微前端环境下，注册mount和unmount方法
    window[`micro-app-${window.__MICRO_APP_NAME__}`] = { mount, unmount };
} else {
    // 非微前端环境直接渲染
    mount();
}
