export const state = () => ({
  id: -1,
  token: '',
  name: '',
  email: '',
  createTime: 0,
  wallet_id: '',
  walletInfo: 0,
  totalProfit: 0,
  totalBetInfo: {
    totalBet: 0,
    totalCount: 0,
    maxProfit: 0,
    minProfit: 0
  }
})

export const getters = {
  getId: (state) => {
    return state.id
  },
  getToken: (state) => {
    return state.token
  },
  getName: (state) => {
    return state.name
  },
  getEmail: (state) => {
    return state.email
  },
  getCreateTime: (state) => {
    return state.createTime
  },
  getWalletId: (state) => {
    return state.wallet_id
  },
  getWalletInfo: (state) => {
    return state.walletInfo
  },
  getTotalProfit: (state) => {
    return state.totalProfit
  },
  getTotalBetInfo: (state) => {
    return state.totalBetInfo
  }
}

export const mutations = {
  SET_ID: (state, id) => {
    state.id = id
  },
  SET_TOKEN: (state, token) => {
    state.token = token
  },
  SET_NAME: (state, name) => {
    state.name = name
  },
  SET_EMAIL: (state, email) => {
    state.email = email
  },
  SET_CREATE_TIME: (state, createTime) => {
    state.createTime = createTime
  },
  SET_WALLET_ID: (state, walletId) => {
    state.wallet_id = walletId
  },
  SET_WALLET_INFO: (state, walletInfo) => {
    state.walletInfo = walletInfo
  },
  SET_TOTAL_PROFIT: (state, totalProfit) => {
    state.totalProfit = totalProfit
  },
  SET_TOTAL_BET_INFO: (state, totalBetInfo) => {
    state.totalBetInfo = totalBetInfo
  }
}

export const actions = {
  login({ commit }, info) {
    const _username = info.username
    const _password = info.password

    return new Promise((resolve, reject) => {
      this.$axios
        .post('/User/login', {
          username: _username.trim(),
          password: _password
        })
        .then((response) => {
          const { status, data } = response
          if (status === 'success') {
            commit('SET_ID', data.ID)
            commit('SET_TOKEN', data.jwt_token)
            commit('SET_NAME', data.USERNAME)
            commit('SET_EMAIL', data.EMAIL)
            commit('SET_CREATE_TIME', data.CREATE_TIME)
            commit('SET_WALLET_ID', data.WALLET_ID)
          }

          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },

  getInfo({ commit, state }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .post('/User/info')
        .then((response) => {
          const { status, data } = response
          if (status === 'success') {
            commit('SET_ID', data.ID)
            commit('SET_TOKEN', data.jwt_token)
            commit('SET_NAME', data.USER_NAME)
            commit('SET_EMAIL', data.USER_EMAIL)
            commit('SET_CREATE_TIME', data.CREATE_TIME)
            commit('SET_WALLET_ID', data.WALLET_ID)
          }

          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },

  logout({ commit, state }) {
    commit('SET_ID', -1)
    commit('SET_TOKEN', '')
    commit('SET_NAME', '')
    commit('SET_EMAIL', '')
    commit('SET_CREATE_TIME', 0)
    commit('SET_WALLET_ID', '')
    commit('SET_WALLET_INFO', 0)
    commit('SET_TOTAL_PROFIT', 0)
    commit('SET_TOTAL_BET_INFO', {
      totalBet: 0,
      totalCount: 0,
      maxProfit: 0,
      minProfit: 0
    })
  },

  resetToken({ commit }) {
    commit('SET_TOKEN', '')
  },

  getWalletInfo({ commit }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .post('/User/wallet')
        .then((response) => {
          const { status, data } = response
          if (status === 'success') {
            commit('SET_WALLET_INFO', parseInt(data))
          }

          resolve(data)
        })
        .catch((error) => {
          console.log(error)
          error = 0
          reject(error)
        })
    })
  },

  getTotalProfit({ commit }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .post('/User/getTotalProfit')
        .then((response) => {
          const { status, data } = response
          if (status === 'success') {
            commit('SET_TOTAL_PROFIT', parseInt(data))
          }

          resolve(data)
        })
        .catch((error) => {
          error = 0
          reject(error)
        })
    })
  },

  getBettingData({ commit }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .post('/User/getBettingData')
        .then((response) => {
          const { status, data } = response
          if (status === 'success') {
            commit('SET_TOTAL_BET_INFO', data)
          }

          resolve(data)
        })
        .catch((error) => {
          error = 0
          reject(error)
        })
    })
  },

  setEmail({ commit }, email) {
    commit('SET_EMAIL', email)
  }
}
