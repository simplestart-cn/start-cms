module.exports = {
    // 配置显示在浏览器标签的title
    title: '后台管理系统',
    // 应用appid
    appid: 'ty9fd2848a039ab554',
    // token在Cookie中存储的天数，默认1天
    cookieExpires: 1,
    /**
     * @type {boolean} true | false
     * @description Whether show the setting right-panel
     */
    showSetting: false,
    /**
     * @type {boolean} true | false
     * @description Whether need tagsView
     */
    tagsView: true,
    /**
     * @type {boolean} true | false
     * @description Whether show the logo in sidebar
     */
    sidebarLogo: true,
    /**
     * @type {string | array} 'production' | ['production', 'development']
     * @description Need show err logs component.
     * The default is only used in the production env
     * If you want to also use it in dev, you can pass ['production', 'development']
     */
    errorLog: 'development'
  }
  