import axios from "axios";
import { Message, MessageBox } from "element-ui";
import store from "@/store";
import { getToken, getSignature } from "@/utils/auth";
import setting from "@/setting.js";
import qs from "qs";

// create an axios instance
const service = axios.create({
    baseURL: process.env.VUE_APP_BASE_API, // url = base url + request url
    // withCredentials: true, // send cookies when cross-domain requests
    timeout: 30000, // request timeout
});

// 请求拦截器
service.interceptors.request.use(
    (config) => {
        // token
        config.headers["app-id"] = setting.appid;
        config.headers["app-secret"] = setting.appSecret;
        if (store.getters.token && store.getters.token !== undefined) {
            config.headers["user-token"] = getToken();
            config.headers["client-type"] = "web";
        }
        // signature
        if (config.method === "post") {
            config.data = getSignature(config.data);
            // 当type为form-data时，即表示是文件上传
            if (config.type !== "form-data") {
                config.data = qs.stringify(config.data);
            }
        } else if (config.method === "get") {
            config.params = getSignature(config.params);
        }
        return config;
    },
    (error) => {
        // do something with request error
        console.log("requet Error", error); // for debug
        return Promise.reject(error);
    }
);

// 响应拦截器
service.interceptors.response.use(
    /**
     * If you want to get http information such as headers or status
     * Please return  response => response
     */
    (response) => {
        const res = response.data;
        if (process.env.NODE_ENV == "development") {
            console.log(response.config.url, res);
        }
        // 业务逻辑错误
        if (res.code !== 0) {
            // token 过期了
            if (res.code === 401) {
                MessageBox.confirm(
                    "登录已过期，可以取消继续留在该页面，或者重新登录",
                    "确定登出",
                    {
                        confirmButtonText: "重新登录",
                        cancelButtonText: "取消",
                        type: "warning",
                    }
                ).then(() => {
                    store.dispatch("user/resetToken").then(() => {
                        location.reload();
                    });
                });
            }
            return res;
        } else {
            return res;
        }
    },
    (error) => {
        error = error.response;
        Message({
            type: "error",
            message: error.statusText + "：" + error.status,
            showClose: true,
            duration: 3 * 1000,
        });
        return Promise.reject(error.data);
    }
);

export default service;
