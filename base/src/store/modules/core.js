const state = {
  config: {
    logo: "http://www.simplestart.cn/logo.png",
    text_color: "#fffff",
    background_color: "#06478c",
    title: "简艺科技",
    slogan: "为需求提供最简单有效的解决方案",
    domain: "http://www.simplestart.cn",
    copyright: "COPYRIGHT © 2017-2022 SIMPLESTART ALL RIGHTS RESERVED.",
    analysis: "",
    contact: "",
    phone: "",
    email: "",
    icp: "",
    cookie_expires: 1,
  }
};
const mutations = {
  UPDATE_CONFIG: (state, data) => {
    state.config = JSON.parse(JSON.stringify(data));
  },
  MERGE_CONFIG: (state, data) => {
    let deepMerge = function(origin, target = {}) {
      for (let key in target) {
        if (Object.prototype.hasOwnProperty.call(target, key)) {
          let value = target[key];
          let isObj = Object.prototype.toString.call(value) === "[object Object]";
          if (origin.hasOwnProperty(key) && isObj) {
            deepMerge(origin[key], value);
          } else {
            origin[key] = value;
          }
        }
      }
    };
    deepMerge(state.config, data);
  },
};
const actions = {
  // 覆盖式更新
  updateConfig({ commit }, data) {
    commit("UPDATE_CONFIG", data);
  },
  // 合并更新
  mergeConfig({ commit }, data) {
    commit("MERGE_CONFIG", data);
  }
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
};
