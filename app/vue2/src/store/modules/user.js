import { getState } from "@/api/user";
import { getToken, setToken, removeToken } from "@/utils/cookie";

const state = {
    info: {},
    token: getToken(),
    authorize: false,
};

const mutations = {
    SET_INFO: (state, data) => {
        state.info = data;
    },
    SET_TOKEN: (state, token) => {
        state.token = token;
        setToken(token);
    },
    SET_AUTHORIZE: (state, authorize) => {
        state.authorize = authorize;
    },
};

const actions = {
    // 获取账户状态
    getState({ commit, state, rootState }) {
        return new Promise((resolve, reject) => {
            getState()
                .then((response) => {
                    const { info, token, authorize } = response.data;
                    commit("SET_INFO", info);
                    commit("SET_TOKEN", token);
                    if (authorize) {
                        commit("SET_AUTHORIZE", authorize);
                    } else {
                        reject(new Error("拉取用户权限失败"));
                    }
                    resolve(response.data);
                })
                .catch((error) => {
                    reject(error);
                });
        });
    },

    // 缓存账户状态
    setState({ commit }, data) {
        return new Promise((resolve) => {
            if (data.info) {
                commit("SET_INFO", data.info);
            }
            if (data.token) {
                commit("SET_TOKEN", data.token);
            }
            if (data.authorize) {
                commit("SET_AUTHORIZE", data.authorize);
            }
            resolve();
        });
    },
    // 设置信息
    setInfo({ commit }, data) {
        return new Promise((resolve) => {
            commit("SET_INFO", data);
            resolve();
        });
    },
    // 设置Token
    setToken({ commit }, data) {
        return new Promise((resolve) => {
            commit("SET_TOKEN", data);
            resolve();
        });
    },
    // 删除token
    resetToken({ commit }) {
        return new Promise((resolve) => {
            commit("SET_TOKEN", "");
            commit("SET_AUTHORIZE", "");
            removeToken();
            resolve();
        });
    },
    // 设置权限
    setAuthorize({ commit }, data) {
        return new Promise((resolve) => {
            commit("SET_AUTHORIZE", data);
            resolve();
        });
    },
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
};
