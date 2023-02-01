import request from '@/utils/request'

export function getImage(data) {
  return request({
    url: '/core/captcha/image',
    method: 'post',
    data
  })
}

export function getCode(data) {
    return request({
      url: '/core/captcha/mobile',
      method: 'post',
      data
    })
  }