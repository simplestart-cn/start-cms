import request from '@/utils/request'

export function getPage(params = {}) {
  return request({
    url: '/vue2/page1/page',
    method: 'get',
    params
  })
}

export function getList(params = {}) {
  return request({
    url: '/vue2/page1/list',
    method: 'get',
    params
  })
}

export function getSku(params) {
  return request({
    url: '/vue2/page1/sku',
    method: 'get',
    params
  })
}

export function getInfo(id) {
  return request({
    url: '/vue2/page1/info',
    method: 'get',
    params: { id }
  })
}

export function create(data) {
  return request({
    url: '/vue2/page1/create',
    method: 'post',
    data
  })
}

export function update(data) {
  return request({
    url: '/vue2/page1/update',
    method: 'post',
    data
  })
}

export function updateList(data) {
  return request({
    url: '/vue2/page1/updateList',
    method: 'post',
    data
  })
}

export function remove(data) {
  return request({
    url: '/vue2/page1/remove',
    method: 'post',
    data
  })
}

