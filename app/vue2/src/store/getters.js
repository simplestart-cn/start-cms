const getters = {
    sidebar: state => state.app.sidebar,
    user: state => state.user.info,
    token: state => state.user.token,
    authorize: state => state.user.authorize,
}
export default getters