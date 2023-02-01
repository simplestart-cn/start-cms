import store from '@/store'
const arrayAuth = (value, access) => {
  if (value instanceof Array && value.length > 0) {
    return access.some(role => value.includes(role))
  }
  throw new Error(`need access! Like v-auth="['admin','editor']"`)
}

const stringAuth = (arg, access) => {
  return access.some(role => role === arg)
}

const wholeAuth = (value, access) => {
  if (value instanceof Array && value.length > 0) {
    return access.some(role => value.every(item => item === role))
  }
  throw new Error(`need access! Like v-auth.whole="['admin','editor']"`)
}


export default {
  inserted(el, binding, vnode) {
    const { arg, value, modifiers } = binding
    let hasauth = true
    const access = store.getters && store.getters.authorize
    if (access.includes('all')) {
      return true
    }
    if (arg) {
      hasauth = stringAuth(arg, access)
    } else if (value) {
      if (modifiers.whole) {
        hasauth = wholeAuth(value, access)
      } else if(value instanceof Array) {
        hasauth = arrayAuth(value, access)
      }else{
        hasauth = stringAuth(value, access)
      }
    } else {
      throw new Error(`need access! Like v-auth:admin or v-auth="['admin','editor']"!
                      if you want to judge whole auth, please use whole modifier.
                      such as v-auth.whole="['admin', 'editor']"`)
    }
    if (!hasauth) {
      el.parentNode && el.parentNode.removeChild(el)
    }
  }
}
