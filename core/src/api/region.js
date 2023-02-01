import request from '@/utils/request'

export function getPage(params = {}) {
  return request({
    url: '/core/region/page',
    method: 'get',
    data: params
  })
}

export function getList(params = {}) {
  return request({
    url: '/core/region/list',
    method: 'get',
    params
  })
}

export function getTree(params = {}) {
  return request({
    url: '/core/region/tree',
    method: 'get',
    params
  })
}

export function getInfo(id) {
  return request({
    url: '/core/region/info',
    method: 'get',
    params: { id }
  })
}

export function create(data) {
  return request({
    url: '/core/region/create',
    method: 'post',
    data
  })
}

export function update(data) {
  return request({
    url: '/core/region/update',
    method: 'post',
    data
  })
}

export function updateList(data) {
  return request({
    url: '/core/region/updateList',
    method: 'post',
    data
  })
}

export function remove(data) {
  return request({
    url: '/core/region/remove',
    method: 'post',
    data
  })
}
