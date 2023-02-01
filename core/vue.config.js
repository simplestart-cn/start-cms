'use strict'
const path = require('path')

function resolve(dir) {
  return path.join(__dirname, dir)
}
const port = process.env.port || process.env.npm_config_port || 9528 // dev port
module.exports = {
  publicPath: '/core/view/',
  outputDir: './view',
  assetsDir: './static',
  indexPath: './index/index.html',
  lintOnSave: false,
  productionSourceMap: false,
  devServer: {
    port: port,
    open: false,
    overlay: {
      warnings: false,
      errors: true
    },
    proxy: {
      // 解決跨域问题(所有请求将会代理转发到target站点)
      // detail: https://cli.vuejs.org/config/#devserver-proxy
      [process.env.VUE_APP_BASE_API]: {
        target: 'http://localhost:8080',
        // target: 'http://demo.startcms.cn',
        changeOrigin: true,
        pathRewrite: {
          ['^' + process.env.VUE_APP_BASE_API]: '/'
        }
      }
    },
    headers: {
      'Access-Control-Allow-Origin': '*',
    },
  },
  configureWebpack: {
    name: 'Storage',
    resolve: {
      alias: {
        '@': resolve('src')
      }
    }
  },
  pluginOptions: {
    electronBuilder: {
      customFileProtocol: "./"
    }
  }
}
