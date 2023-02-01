import { getList, getTree } from "@/api/region";
const state = {
  region: [],
};
const mutations = {
  SET_REGION(state, region) {
    state.region = region;
  },
};
const actions = {
  // 获取地区
  getList({ commit }, data) {
    return new Promise((resolve, reject) => {
      getList(data).then((response) => {
        const { data } = response;
        data.forEach((item) => {
          item.value = item.id;
          item.label = item.title;
          if (item.has_children) {
            item.children = [];
          }
        });
        commit("SET_REGION", data);
        resolve(data);
      }).catch((error) => {
        reject(error);
      });
    });
  },
  // 获取地区树
  getTree({ commit }, data = {}) {
    return new Promise((resolve, reject) => {
      getTree(data).then(response => {
        resolve(response.data)
      }).catch(error => {
        reject(error)
      })
    })
  }
};

export default {
  namespaced: true,
  state,
  mutations,
  actions
}