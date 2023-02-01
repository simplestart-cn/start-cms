import request from '@/utils/request'

export function getInfo(params = {}) {
  return request({
    url: '/core/config/info',
    method: 'get',
    params
  })
}
