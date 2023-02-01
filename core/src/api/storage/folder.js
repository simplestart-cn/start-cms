import request from '@/utils/request'

export function getPage(params = {}) {
  return request({
    url: '/storage/folder/page',
    method: 'get',
    params
  })
}

export function getList(params = {}) {
  return request({
    url: '/storage/folder/list',
    method: 'get',
    params
  })
}

export function getInfo(id) {
  return request({
    url: '/storage/folder/info',
    method: 'get',
    params: { id }
  })
}

export function create(data) {
  return request({
    url: '/storage/folder/create',
    method: 'post',
    data
  })
}

export function update(data) {
  return request({
    url: '/storage/folder/update',
    method: 'post',
    data
  })
}

export function remove(data) {
  return request({
    url: '/storage/folder/remove',
    method: 'post',
    data
  })
}

export function restore(data) {
  return request({
    url: '/storage/folder/restore',
    method: 'post',
    data
  })
}

