import tree from "@/utils/tree";
import cookie from "@/utils/cookie";
const state = {
    config: {},
    sidebar: {
        menus: [],
        opened: true,
        withoutAnimation: false,
    },
};

const mutations = {
    SET_CONFIG: (state, data) => {
        state.config = data;
    },
    SET_SIDEBAR: (state, menus) => {
        if (Array.isArray(menus)) {
            state.sidebar.menus = menus;
        } else {
            state.sidebar.menus = [];
        }
    },
    TOGGLE_SIDEBAR: (state) => {
        state.sidebar.opened = !state.sidebar.opened;
        state.sidebar.withoutAnimation = false;
        if (state.sidebar.opened) {
            cookie.set("sidebarStatus", 1);
        } else {
            cookie.set("sidebarStatus", 0);
        }
    },
};

const actions = {
    // 更新设置
    setConfig({ commit }, data) {
        return new Promise((resolve) => {
            commit("SET_CONFIG", data);
            resolve();
        });
    },
    // 控制侧栏
    toggleSideBar({ commit }) {
        commit("TOGGLE_SIDEBAR");
    },
    // 构建路由
    generateRoutes({ dispatch, commit }, routes) {
        // 格式化
        routes.forEach((item) => {
            item.hidden = !!item.hidden;
            item.menu = !!item.menu;
            item.meta = {
                icon: item.icon,
                title: item.title,
                cache: !!item.cache,
            };
            if (item.params) {
                item.path += +item.params;
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
            routes.forEach(function (item) {
                item.component = () => import("@/layout");
                if (item.view) {
                    let child = JSON.parse(JSON.stringify(item));
                    let component = child.view;
                    child.component = (resolve) =>
                        require([`@/views${component}`], resolve);
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
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
};
