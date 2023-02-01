import request from '@/utils/request'

export function getPage(params = {}) {
  return request({
    url: '/core/group/page',
    method: 'get',
    params
  })
}

export function getList(params = {}) {
  return request({
    url: '/core/group/list',
    method: 'get',
    params
  })
}

export function getTree() {
  return request({
    url: '/core/group/tree',
    method: 'get'
  })
}

export function getInfo(id) {
  return request({
    url: '/core/group/info',
    method: 'get',
    params: { id }
  })
}

export function create(data) {
  return request({
    url: '/core/group/create',
    method: 'post',
    data
  })
}

export function update(data) {
  return request({
    url: '/core/group/update',
    method: 'post',
    data
  })
}

export function updateStatus(data) {
  return request({
    url: '/core/group/updateStatus',
    method: 'post',
    data
  })
}

export function updateList(data) {
  return request({
    url: '/core/group/updateList',
    method: 'post',
    data
  })
}

export function remove(data) {
  return request({
    url: '/core/group/remove',
    method: 'post',
    data
  })
}

