import request from '@/utils/request'

export function getInfo(params = {}) {
  return request({
    url: '/core/config/info',
    method: 'get',
    params
  })
}

export function getList(params = {}) {
  return request({
    url: '/core/config/list',
    method: 'get',
    params
  })
}

export function updateList(data) {
  return request({
    url: '/core/config/updateList',
    method: 'post',
    data
  })
}
