import { login, register, logout, getState } from "@/api/user";
import { getToken, setToken, removeToken } from "@/utils/auth";
import { resetRouter } from "@/router";

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
  },
  SET_AUTHORIZE: (state, authorize) => {
    state.authorize = authorize;
  },
};

const actions = {
  // 登录
  login({ commit }, userInfo) {
    const { account, password, code, uniqid } = userInfo;
    return new Promise((resolve, reject) => {
      login({
        account: account.trim(),
        password: password.trim(),
        code: code.trim(),
        uniqid: uniqid,
      })
        .then((response) => {
          const { data } = response;
          commit("SET_TOKEN", data.token);
          setToken(data.token);
          resolve(response);
        })
        .catch((error) => {
          reject(error);
        });
    });
  },
  // 登出
  logout({ commit, state, dispatch }) {
    return new Promise((resolve, reject) => {
      logout().then((response) => {
        commit("SET_TOKEN", "");
        commit("SET_AUTHORIZE", "");
        removeToken();
        resetRouter();
        // reset visited views and cached views
        // to fixed https://github.com/PanJiaChen/vue-element-admin/issues/2485
        dispatch("tagsView/delAllViews", null, { root: true });
        resolve();
      });
    });
  },
  // 注册
  register({ commit }, userInfo) {
    const { mobile, code } = userInfo;
    return new Promise((resolve, reject) => {
      register({
        mobile: mobile.trim(),
        code: code.trim(),
      })
        .then((response) => {
          const { data } = response;
          commit("SET_TOKEN", data.token);
          setToken(data.token);
          resolve(response);
        })
        .catch((error) => {
          reject(error);
        });
    });
  },
  // 获取登录态
  getState({ commit, state, rootState }) {
    return new Promise((resolve, reject) => {
      getState()
        .then((response) => {
          const { info, config, authorize } = response.data;
          if (authorize) {
            commit("SET_AUTHORIZE", authorize);
          } else {
            reject(new Error("拉取用户权限失败"));
          }
          commit("SET_INFO", info);
          if (config) {
            commit("core/UPDATE_CONFIG", config, { root: true });
          }
          resolve(response.data);
        })
        .catch((error) => {
          reject(error);
        });
    });
  },
  // 设置Token
  setToken({ commit }, data) {
    return new Promise((resolve) => {
      commit("SET_TOKEN", data);
      setToken(data);
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
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
};
