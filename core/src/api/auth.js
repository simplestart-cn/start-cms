import request from '@/utils/request'

export function getList(params = {}) {
  return request({
    url: '/core/auth/list',
    method: 'get',
    params
  })
}

export function getTree() {
  return request({
    url: '/core/auth/tree',
    method: 'get'
  })
}

export function getInfo(id) {
  return request({
    url: '/core/auth/info',
    method: 'get',
    params: { id }
  })
}

export function restore() {
  return request({
    url: '/core/auth/restore',
    method: 'post'
  })
}

export function create(data) {
  return request({
    url: '/core/auth/create',
    method: 'post',
    data
  })
}

export function update(data) {
  return request({
    url: '/core/auth/update',
    method: 'post',
    data
  })
}

export function updateList(data) {
  return request({
    url: '/core/auth/updateList',
    method: 'post',
    data
  })
}

export function remove(data) {
  return request({
    url: '/core/auth/remove',
    method: 'post',
    data
  })
}

