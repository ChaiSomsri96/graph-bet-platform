export default {
  mode: 'universal',
  server: {
    /* host: process.env.APP_HOST || '127.0.0.1',
    port: process.env.APP_PORT || 3000 */
    host: '127.0.0.1',
    port: 3000
  },
  /*
   ** Headers of the page
   */
  head: {
    title: process.env.npm_package_name || '',
    meta: [
      {
        charset: 'utf-8'
      },
      {
        name: 'viewport',
        content: 'width=device-width, initial-scale=1'
      },
      {
        hid: 'description',
        name: 'description',
        content: process.env.npm_package_description || ''
      }
    ],
    link: [
      {
        rel: 'icon',
        type: 'image/x-icon',
        href: '/favicon.ico'
      }
    ]
  },
  /*
   ** Customize the progress-bar color
   */
  loading: {
    color: '#FFFFFF00'
  },
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
    '@/plugins/icons',
    '@/plugins/axios',
    '@/plugins/bus',
    '@/plugins/filter',
    {
      src: '@/plugins/socket.io.js',
      ssr: false
    },
    {
      src: '@/plugins/localStorage.js',
      ssr: false
    },
    '@/plugins/vue-toastr.js',
    '@/plugins/vue-clipboard.js'
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
      target: 'http://localhost:3001/User', // process.env.BACKEND_URL + ':' + process.env.BACKEND_PORT + '/User',
      pathRewrite: {
        '^/User/': ''
      }
    },
    '/Chat/': {
      target: 'http://localhost:3001/Chat', // process.env.BACKEND_URL + ':' + process.env.BACKEND_PORT + '/Chat',
      pathRewrite: {
        '^/Chat/': ''
      }
    },
    '/Crash/': {
      target: 'http://localhost:4202/Crash', // process.env.BACKEND_URL + ':' + process.env.CRASH_PORT + '/Crash',
      pathRewrite: {
        '^/Crash/': ''
      }
    },
    '/Setting/': {
      target: 'http://localhost:3001/Setting', // process.env.BACKEND_URL + ':' + process.env.CRASH_PORT + '/Setting',
      pathRewrite: {
        '^/Setting/': ''
      }
    },
    '/Bitcoin/': {
      target: 'http://localhost:3001/Bitcoin', // process.env.BACKEND_URL + ':' + process.env.CRASH_PORT + '/Setting',
      pathRewrite: {
        '^/Bitcoin/': ''
      }
    },
    '/Faq/': {
      target: 'http://localhost:3001/Faq', // process.env.BACKEND_URL + ':' + process.env.BACKEND_PORT + '/Faq',
      pathRewrite: {
        '^/Faq/': ''
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
