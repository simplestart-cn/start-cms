import request from '@/utils/request'

export function operation(params) {
  return request({
    url: '/core/record/operation',
    method: 'get',
    params
  })
}

export function runtime(params) {
  return request({
    url: '/core/record/runtime',
    method: 'get',
    params
  })
}

export function remove(params) {
  return request({
    url: '/core/record/remove',
    method: 'post',
    params
  })
}
