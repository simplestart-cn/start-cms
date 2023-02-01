import { getState, getAdmin, getInfo } from "@/api/user";
import { getList as getAuth } from '@/api/auth'
import { getList as getRole } from "@/api/role";
import { getList as getGroup } from "@/api/group";
import { getToken, setToken, removeToken } from "@/utils/auth";

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
  // 获取用户信息
  getState({ commit, state, rootState }) {
    return new Promise((resolve, reject) => {
      getState()
        .then((response) => {
          const { info, token, authorize } = response.data;
          commit("SET_INFO", info);
          commit('SET_TOKEN', token)
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
  // 设置Token
  setInfo({ commit }, data) {
    return new Promise((resolve) => {
      commit("SET_INFO", data);
      resolve();
    });
  },
  setState({ commit }, data) {
    return new Promise((resolve) => {
      if(data.info){
        commit("SET_INFO", data.info);
      }
      if(data.token){
        commit("SET_TOKEN", data.token);
      }
      if(data.authorize){
        commit("SET_AUTHORIZE", data.authorize);
      }
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
  // 获取管理员
  getAdminList({ commit }) {
    return new Promise((resolve, reject) => {
      getAdmin({ is_admin: 1 })
        .then((response) => {
          const { data } = response;
          data.forEach((item) => {
            item.value = item.id;
            item.label = item.name + (item.mobile && `(${item.mobile})`);
          });
          resolve(data);
        })
        .catch((error) => {
          reject(error);
        });
    });
  },
  // 获取管理员信息
  getAdminInfo({ commit }, id) {
    return new Promise((resolve, reject) => {
        return getInfo(id).then((response) => {
          resolve(response.data);
        });
    });
  },
  // 获取部门
  getGroup({ commit }, data) {
    return new Promise((resolve, reject) => {
      getGroup(data)
        .then((response) => {
          const { data } = response;
          data.forEach((item) => {
            item.value = item.id;
            item.label = item.title;
          });
          resolve(data);
        })
        .catch((error) => {
          reject(error);
        });
    });
  },
  // 获取职位
  getRole({ commit }, data) {
    return new Promise((resolve, reject) => {
      getRole(data)
        .then((response) => {
          const { data } = response;
          data.forEach((item) => {
            item.value = item.id;
            item.label = item.title;
          });
          resolve(data);
        })
        .catch((error) => {
          reject(error);
        });
    });
  },
  getAuth({ commit }) {
    return new Promise((resolve, reject) => {
      getAuth().then(response => {
        const { data } = response
        data.forEach(item => {
          item.value = item.name,
          item.label = item.title
        })
        resolve(data)
      }).catch(error => {
        reject(error)
      })
    })
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
