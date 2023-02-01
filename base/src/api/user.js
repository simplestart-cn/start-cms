import request from '@/utils/request'

/**
 * 账户登录
 * @param  {[type]} data [description]
 * @return {[type]}      [description]
 */
export function login(data) {
  return request({
    url: '/core/user/login',
    method: 'post',
    data
  })
}

/**
 * 授权登录
 * @param {}
 * @returns 
 */
 export function authLogin(data) {
	return request({
		url: "/core/user/authLogin",
		method: "post",
    data: data,
	});
};

/**
 * 手机登录
 * @param {}
 * @returns 
 */
 export function mobileLogin(data) {
	return request({
		url: "/core/user/mobileLogin",
		method: "post",
    data: data
	});
};

/**
 * 用户注册
 * @param { }
 * @returns 
 */
 export function register(data) {
	return request({
		url: "/core/user/register",
		method: "post",
    data: data
	});
};

/**
 * 用户登出
 * @param  {[type]} data [description]
 * @return {[type]}      [description]
 */
 export function logout(data) {
  return request({
    url: '/core/user/logout',
    method: 'post',
    data
  })
}

/**
 * 获取登录态
 * @return {[type]} [description]
 */
 export function getState() {
  return request({
    url: '/core/user/state',
    method: 'get'
  })
}
