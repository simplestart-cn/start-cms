import request from '@/utils/request'

// 获取账户登录态
export function getState(params = {}) {
  return request({
    url: '/core/user/state',
    method: 'get',
    params
  })
}
