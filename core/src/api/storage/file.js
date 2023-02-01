import request from '@/utils/request'

export function getPage(params = {}) {
  return request({
    url: '/storage/file/page',
    method: 'get',
    params: params
  })
}

export function getInfo(id) {
  return request({
    url: '/storage/file/info',
    method: 'get',
    params: {
      id
    }
  })
}

export function transfer(data) {
  return request({
    url: '/storage/file/transfer',
    method: 'post',
    data
  })
}

export function update(data) {
  return request({
    url: '/storage/file/update',
    method: 'post',
    data
  })
}

export function remove(data) {
  return request({
    url: '/storage/file/remove',
    method: 'post',
    data
  })
}

export function upload(data) {
  return request({
    url: '/storage/upload/file',
    method: 'post',
    headers: {
      'Content-Type': 'multipart/form-data'
    },
    type: 'form-data',
    data
  })
}
