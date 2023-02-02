import Cookies from "js-cookie";
// token存储key
const tokenKey = "User-Token";
// token在Cookie中存储的天数，默认3天
const expireDay = 3;

/**
 * 设置值
 */
export function set(key, value, options = {}) {
    return Cookies.set(key, value, options);
}

/**
 * 获取值
 */
export function get(key) {
    return Cookies.get(key);
}

/**
 * 获取token
 */
export function getToken() {
    return Cookies.get(tokenKey);
}

/**
 * 设置token
 * @param {*} token
 */
export function setToken(token) {
    return Cookies.set(tokenKey, token, { expires: expireDay });
}

/**
 * 删除token
 */
export function removeToken() {
    return Cookies.remove(tokenKey);
}
