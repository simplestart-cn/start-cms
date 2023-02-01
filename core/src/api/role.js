import request from '@/utils/request'

export function getList(params = {}) {
  return request({
    url: '/core/role/list',
    method: 'get',
    params
  })
}

export function getTree() {
  return request({
    url: '/core/role/tree',
    method: 'get'
  })
}

export function getInfo(id) {
  return request({
    url: '/core/role/info',
    method: 'get',
    params: { id }
  })
}

export function create(data) {
  return request({
    url: '/core/role/create',
    method: 'post',
    data
  })
}

export function update(data) {
  return request({
    url: '/core/role/update',
    method: 'post',
    data
  })
}

export function updateStatus(data) {
  return request({
    url: '/core/role/updateStatus',
    method: 'post',
    data
  })
}

export function updateList(data) {
  return request({
    url: '/core/role/updateList',
    method: 'post',
    data
  })
}

export function remove(data) {
  return request({
    url: '/core/role/remove',
    method: 'post',
    data
  })
}

