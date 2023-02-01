import store from '@/store'

/**
 * @param {Array} value
 * @returns {Boolean}
 * @example see @/views/auth/directive.vue
 */
export default function checkAuth(value) {
  if (value && value instanceof Array && value.length > 0) {
    const access = store.getters && store.getters.access
    const authAccess = value

    const hasAuth = access.some(role => {
      return authAccess.includes(role)
    })

    if (!hasAuth) {
      return false
    }
    return true
  } else {
    console.error(`need access! Like v-auth="['admin','editor']"`)
    return false
  }
}
