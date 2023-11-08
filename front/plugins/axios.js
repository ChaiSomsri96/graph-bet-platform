export default ({ $axios, app }) => {
  $axios.defaults.timeout = 15000
  $axios.defaults.baseURL =
    'http://' + process.env.APP_HOST + ':' + process.env.APP_PORT + '/'
  $axios.defaults.prefix =
    'http://' + process.env.APP_HOST + ':' + process.env.APP_PORT + '/'
  $axios.defaults.headers.common.Authorization =
    'bearer ' + app.store.getters['user/getToken'] || ''

  $axios.onRequest((config) => {
    if (config.withoutToken) {
      config.headers.common.Authorization = ''
    } else {
      config.headers.common.Authorization =
        'bearer ' + app.store.getters['user/getToken'] || ''
    }
    app.$bus.$emit('loading', true)
  })

  $axios.onResponse((response) => {
    app.$bus.$emit('loading', false)

    return response.data
  })

  $axios.onError((err) => {
    app.$bus.$emit('loading', false)

    return Promise.reject(err)
  })
}
