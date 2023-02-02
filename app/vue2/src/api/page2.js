import request from '@/utils/request'

export function getPage(params = {}) {
  return request({
    url: '/vue2/page2/page',
    method: 'get',
    params
  })
}

export function getList(params = {}) {
  return request({
    url: '/vue2/page2/list',
    method: 'get',
    params
  })
}

export function getSku(params) {
  return request({
    url: '/vue2/page2/sku',
    method: 'get',
    params
  })
}

export function getInfo(id) {
  return request({
    url: '/vue2/page2/info',
    method: 'get',
    params: { id }
  })
}

export function create(data) {
  return request({
    url: '/vue2/page2/create',
    method: 'post',
    data
  })
}

export function update(data) {
  return request({
    url: '/vue2/page2/update',
    method: 'post',
    data
  })
}

export function updateList(data) {
  return request({
    url: '/vue2/page2/updateList',
    method: 'post',
    data
  })
}

export function remove(data) {
  return request({
    url: '/vue2/page2/remove',
    method: 'post',
    data
  })
}

