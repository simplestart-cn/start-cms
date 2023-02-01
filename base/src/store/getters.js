const getters = {
  user: (state) => state.user.info,
  token: (state) => state.user.token,
  authorize: (state) => state.user.authorize,
  config: (state) => state.core.config,
  msg: (state) => state.app.msg,
  apps: (state) => state.app.apps,
  appNavbar: (state) => state.app.appNavbar,
  appRoutes: (state) => state.app.appRoutes,
  size: (state) => state.app.size,
  device: (state) => state.app.device,
  sidebar: (state) => state.app.sidebar,
  visitedViews: (state) => state.tagsView.visitedViews,
  cachedViews: (state) => state.tagsView.cachedViews,
  errorLogs: (state) => state.errorLog.logs,
};
export default getters;
