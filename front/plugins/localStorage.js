import createPersistedState from 'vuex-persistedstate'
import * as Cookies from 'js-cookie'
// import Cookies from '~/node_modules/js-cookie'

export default ({ store }) => {
  window.onNuxtReady(() => {
    createPersistedState({
      storage: {
        getItem: (key) => {
          return Cookies.get(key)
        },
        setItem: (key, value) => {
          Cookies.set(key, value)
        },
        removeItem: (key) => Cookies.remove(key)
      }
    })(store)
  })
}
