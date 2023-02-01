import request from '@/utils/request'

export function getPage(params = {}) {
  return request({
    url: '/storage/recycle/page',
    method: 'get',
    params: params
  })
}

export function restore(data) {
  return request({
    url: '/storage/recycle/restore',
    method: 'post',
    data
  })
}

export function remove(data) {
  return request({
    url: '/storage/recycle/remove',
    method: 'post',
    data
  })
}