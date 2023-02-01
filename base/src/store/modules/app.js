import Cookies from "js-cookie";
import tree from "@/utils/tree";
import { constantRoutes } from "@/router";
const state = {
  msg: [],
  apps: [],
  appNavbar: [],
  appRoutes: [],
  sidebar: {
    opened: Cookies.get("sidebarStatus") ? !!+Cookies.get("sidebarStatus") : true,
    withoutAnimation: false,
  },
  device: "desktop",
  size: Cookies.get("size") || "medium",
};

const mutations = {
  TOGGLE_SIDEBAR: (state) => {
    state.sidebar.opened = !state.sidebar.opened;
    state.sidebar.withoutAnimation = false;
    if (state.sidebar.opened) {
      Cookies.set("sidebarStatus", 1);
    } else {
      Cookies.set("sidebarStatus", 0);
    }
  },
  CLOSE_SIDEBAR: (state, withoutAnimation) => {
    Cookies.set("sidebarStatus", 0);
    state.sidebar.opened = false;
    state.sidebar.withoutAnimation = withoutAnimation;
  },
  TOGGLE_DEVICE: (state, device) => {
    state.device = device;
  },
  SET_SIZE: (state, size) => {
    state.size = size;
    Cookies.set("size", size);
  },
  SET_APPS: (state, apps) => {
    state.apps = apps;
  },
  SEND_MSG: (state, msg) => {
    state.msg.push(msg);
  },
  SET_NAVBAR: (state, menus) => {
    state.appNavbar = menus;
  },
  SET_ROUTES: (state, routes) => {
    state.appRoutes = constantRoutes.concat(routes);
  }
};

const actions = {
  toggleSideBar({ commit }) {
    commit("TOGGLE_SIDEBAR");
  },
  closeSideBar({ commit }, { withoutAnimation }) {
    commit("CLOSE_SIDEBAR", withoutAnimation);
  },
  toggleDevice({ commit }, device) {
    commit("TOGGLE_DEVICE", device);
  },
  setSize({ commit }, size) {
    commit("SET_SIZE", size);
  },
  sendMsg({ commit }, data) {
    commit("SEND_MSG", data);
  },
  generateRoutes({ dispatch, commit }, apps) {
    // 格式
    const routes = apps.reduce((pre, app) => {
      if (app.routes instanceof Array) {
        app.routes.forEach((item) => {
          item.entry = app.entry;
          item.hidden = !!item.hidden;
          item.menu = !!item.menu;
          item.meta = {
            app: app.name,
            icon: item.icon,
            title: item.title,
            cache: !!item.cache,
          };
          if (item.params) {
            item.path += +item.params;
          }
        });
        return pre.concat(app.routes);
      }
      return pre;
    }, []);
    // 排序
    routes.sort((prev, next) => {
      return parseInt(prev.sort) - parseInt(next.sort);
    });
    // 构建菜单
    let navbar = tree.listToTree(JSON.parse(JSON.stringify(routes)));
    // 构建路由
    return new Promise((resolve) => {
      routes.forEach(function(item) {
        item.component = () => import("@/layout");
        if (item.view && (item.entry == '' || item.entry == '/')) {
          // 非layout布局：
          // let component = item.view
          // item.component = (resolve) => require([`@/views/${component}`], resolve)
          // 用layout布局：
          let child = JSON.parse(JSON.stringify(item));
          let component = child.view;
          child.component = (resolve) => require([`@/views${component}`], resolve);
          item.children = [];
          item.children.push(child);
          item.name = item.name + "_layout";
        }
      });
      routes.push({ path: "*", redirect: "/404", hidden: true });
      commit("SET_APPS", apps);
      commit("SET_ROUTES", routes);
      commit("SET_NAVBAR", navbar);
      resolve(routes);
    });
  }
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
};
