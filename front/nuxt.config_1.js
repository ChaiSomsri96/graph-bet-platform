export default {
  mode: 'universal',
  server: {
    // host: process.env.APP_HOST || '127.0.0.1',
    // port: process.env.App_PORT || 3000
    host: '127.0.0.1',
    port: 3000
  },
  /*
   ** Headers of the page
   */
  head: {
    title: process.env.npm_package_name || '',
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      {
        hid: 'description',
        name: 'description',
        content: process.env.npm_package_description || ''
      }
    ],
    link: [{ rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }]
  },
  /*
   ** Customize the progress-bar color
   */
  loading: { color: '#fff' },
  /*
   ** Global CSS
   */
  css: [
    '@/assets/styles/index.scss',
    'pretty-checkbox/src/pretty-checkbox.scss'
  ],
  /*
   ** Plugins to load before mounting the App
   */
  plugins: [
    // '@/plugins/icons',
    '@/plugins/axios',
    '@/plugins/bus',
    '@/plugins/url'
  ],
  /*
   ** Nuxt.js dev-modules
   */
  buildModules: [
    // Doc: https://github.com/nuxt-community/eslint-module
    '@nuxtjs/eslint-module'
  ],
  /*
   ** Nuxt.js modules
   */
  modules: [
    // Doc: https://bootstrap-vue.js.org
    'bootstrap-vue/nuxt',
    // Doc: https://axios.nuxtjs.org/usage
    '@nuxtjs/axios',
    '@nuxtjs/pwa',
    // Doc: https://github.com/nuxt-community/dotenv-module
    '@nuxtjs/dotenv'
  ],
  /*
   ** Axios module configuration
   ** See https://axios.nuxtjs.org/options
   */
  axios: {
    proxy: true
  },
  proxy: {
    '/User/': {
      // target: 'http://3.12.160.61:3001/User', // process.env.BACKEND_URL + ':' + process.env.BACKEND_PORT + '/User',
      // target: process.env.BACKEND_URL + ':' + process.env.BACKEND_PORT + '/User',
      target: 'http://localhost:3001/User',
      pathRewrite: {
        '^/User/': ''
      }
    },
    '/Chat/': {
      // target: process.env.BACKEND_URL + ':' + process.env.BACKEND_PORT + '/Chat',
      // target: 'http://3.12.160.61:3001/Chat',
      target: 'http://localhost:3001/Chat',
      pathRewrite: {
        '^/Chat/': ''
      }
    },
    '/Crash/': {
      // target: 'http://3.12.160.61:4202/Crash', // process.env.BACKEND_URL + ':' + process.env.CRASH_PORT + '/Crash',
      // target: process.env.BACKEND_URL + ':' + process.env.CRASH_PORT + '/Crash',
      target: 'http://localhost:4202/Crash',
      pathRewrite: {
        '^/Crash/': ''
      }
    }
  },
  /*
   ** Build configuration
   */
  build: {
    transpile: [],
    /*
     ** You can extend webpack config here
     */
    extend(config, ctx) {
      // set svg-sprite-loader
      // remove old pattern from the older loader
      config.module.rules.forEach((value) => {
        if (String(value.test) === String(/\.(png|jpe?g|gif|svg|webp)$/i)) {
          // reduce to svg and webp, as other images are handled above
          value.test = /\.(png|jpe?g|gif|webp)$/
          // keep the configuration from svg-sprite-loader here unchanged
        }
      })
      config.module.rules.push({
        test: /\.svg$/,
        use: {
          loader: 'svg-sprite-loader',
          options: {
            symbolId: 'icon-[name]'
          }
        }
      })
    }
  }
}
