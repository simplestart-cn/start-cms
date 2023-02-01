import request from '@/utils/request'

export function getPage(params = {}) {
  return request({
    url: '/core/dashboard/page',
    method: 'get',
    params
  })
}

export function getList(params = {}) {
  return request({
    url: '/core/dashboard/list',
    method: 'get',
    params
  })
}

export function getInfo(id) {
  return request({
    url: '/core/dashboard/info',
    method: 'get',
    params: { id }
  })
}

export function create(data) {
  return request({
    url: '/core/dashboard/create',
    method: 'post',
    data
  })
}

export function update(data) {
  return request({
    url: '/core/dashboard/update',
    method: 'post',
    data
  })
}

export function updateStatus(data) {
  return request({
    url: '/core/dashboard/updateStatus',
    method: 'post',
    data
  })
}

export function remove(data) {
  return request({
    url: '/core/dashboard/remove',
    method: 'post',
    data
  })
}

