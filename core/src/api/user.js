import request from '@/utils/request'

/**
 * 用户登录
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

/**
 * 获取登录用户
 * @return {[type]} [description]
 */
export function getCurrent() {
  return request({
    url: '/core/user/current',
    method: 'get'
  })
}

/**
 * 获取用户列表
 * @param  {[type]} params [description]
 * @return {[type]}       [description]
 */
export function getPage(params) {
  return request({
    url: '/core/user/page',
    method: 'get',
    params
  })
}

/**
 * 获取管理员列表
 * @param  {[type]} params [description]
 * @return {[type]}       [description]
 */
export function getAdmin(params={}) {
  return request({
    url: '/core/user/admin',
    method: 'get',
    params
  })
}

/**
 * 获取用户信息
 * @param  {[type]} id [description]
 * @return {[type]}    [description]
 */
export function getInfo(id) {
  return request({
    url: '/core/user/info',
    method: 'get',
    params: { id }
  })
}

/**
 * 创建账户
 * @param  {[type]} data [description]
 * @return {[type]}      [description]
 */
export function create(data) {
  return request({
    url: '/core/user/create',
    method: 'post',
    data
  })
}

/**
 * 更新账户信息
 * @param  {[type]} data [description]
 * @return {[type]}      [description]
 */
export function update(data) {
  return request({
    url: '/core/user/update',
    method: 'post',
    data
  })
}

/**
 * 更新账户状态
 * @param  {[type]} data [description]
 * @return {[type]}      [description]
 */
export function updateStatus(data) {
  return request({
    url: '/core/user/updateStatus',
    method: 'post',
    data
  })
}

/**
 * 更新账户身份
 * @param  {[type]} data [description]
 * @return {[type]}      [description]
 */
export function updateIdentity(data) {
  return request({
    url: '/core/user/updateIdentity',
    method: 'post',
    data
  })
}

/**
 * 更新账户密码
 * @param  {[type]} data [description]
 * @return {[type]}      [description]
 */
export function updatePass(data) {
  return request({
    url: '/core/user/updatePass',
    method: 'post',
    data
  })
}

/**
 * 更新当前账户
 * @param  {[type]} data [description]
 * @return {[type]}      [description]
 */
export function updateCurrent(data) {
  return request({
    url: '/core/user/updateCurrent',
    method: 'post',
    data
  })
}

/**
 * 删除用户
 * @param  {[type]} id [description]
 * @return {[type]}    [description]
 */
export function remove(id) {
  return request({
    url: '/core/user/remove',
    method: 'post',
    data: { id }
  })
}

