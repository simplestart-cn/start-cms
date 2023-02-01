import Cookies from 'js-cookie'
import tree from '@/utils/tree'
import { getConfig, getList } from '@/api/app'
const state = {
  config: {},
  menus: [],
  sidebar: {
    opened: true,
    withoutAnimation: false
  }
}

const mutations = {
  SET_SIDEBAR: (state, menus) => {
    if (Array.isArray(menus)) {
      state.menus = menus
    } else {
      state.menus = []
    }
  },
  TOGGLE_SIDEBAR: state => {
    state.sidebar.opened = !state.sidebar.opened
    state.sidebar.withoutAnimation = false
    if (state.sidebar.opened) {
      Cookies.set('sidebarStatus', 1)
    } else {
      Cookies.set('sidebarStatus', 0)
    }
  }
}

const actions = {
  toggleSideBar({ commit }) {
    commit('TOGGLE_SIDEBAR')
  },
  generateRoutes({ dispatch, commit }, routes) {
    // 格式化
    routes.forEach(item => {
      item.hidden = !!item.hidden
      item.menu = !!item.menu
      item.meta = {
        icon: item.icon,
        title: item.title,
        cache: !!item.cache,
      }
      if(item.params){
        item.path += + item.params
      }
    });
    // 排序
    routes.sort((prev, next) => {
      return parseInt(prev.sort) - parseInt(next.sort);
    });
    // 构建菜单
    let sidebar = tree.listToTree(JSON.parse(JSON.stringify(routes)));
    // 构建路由
    return new Promise((resolve) => {
      routes.forEach(function(item) {
        item.component = () => import("@/layout");
        if (item.view) {
          let child = JSON.parse(JSON.stringify(item));
          let component = child.view;
          child.component = (resolve) => require([`@/views${component}`], resolve);
          item.children = [];
          item.children.push(child);
          item.name = item.name + "_layout";
        }
      });
      routes.push({ path: "*", redirect: "/404", hidden: true });
      commit("SET_SIDEBAR", sidebar);
      resolve(routes);
    });
  },
  // 获取app列表
  getList({ commit }, data) {
    return new Promise((resolve, reject) => {
      getList(data)
        .then((response) => {
          const { data } = response;
          const list = data.map((item) => {
            return {
              label: item.title,
              value: item.name,
            };
          });
          resolve(list);
        })
        .catch((error) => {
          reject(error);
        });
    });
  },
  getConfig({ commit, state }, app = 'core') {
    return new Promise((resolve, reject) => {
      getConfig({ app: app }).then(response => {
        const { data } = response
        resolve(data)
      }).catch(error => {
        reject(error)
      })
    })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
