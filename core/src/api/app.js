import request from '@/utils/request'

export function getStore(params) {
  return request({
    url: '/core/app/store',
    method: 'get',
    params
  })
}

export function getStoreCaptcha(params) {
  return request({
    url: '/core/app/storeCaptcha',
    method: 'get',
    params
  })
}

export function loginStore(data)
{
  return request({
    url: '/core/app/loginStore',
    method: 'post',
    data
  })
}

export function registerStore(data)
{
  return request({
    url: '/core/app/registerStore',
    method: 'post',
    data
  })
}

export function getPage(params) {
  return request({
    url: '/core/app/page',
    method: 'get',
    params
  })
}

export function getList(params) {
  return request({
    url: '/core/app/list',
    method: 'get',
    params
  })
}

export function getInfo(params) {
  return request({
    url: '/core/app/info',
    method: 'get',
    params
  })
}

export function install(data) {
  return request({
    url: '/core/app/install',
    method: 'post',
    data
  })
}

export function uninstall(data) {
  return request({
    url: '/core/app/uninstall',
    method: 'post',
    data
  })
}

export function update(data) {
  return request({
    url: '/core/app/update',
    method: 'post',
    data
  })
}

export function upgrade(data) {
  return request({
    url: '/core/app/upgrade',
    method: 'post',
    data
  })
}

export function remove(data) {
  return request({
    url: '/core/app/remove',
    method: 'post',
    data
  })
}

export function updateStatus(data) {
  return request({
    url: '/core/app/updateStatus',
    method: 'post',
    data
  })
}

export function upgradeConfig(data) {
  return request({
    url: '/core/app/upgradeConfig',
    method: 'post',
    data
  })
}

