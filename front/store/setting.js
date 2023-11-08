export const state = () => ({
  setting: {
    fee: 1
  }
})

export const getters = {
  getFee: (state) => {
    return state.setting.fee
  }
}

export const mutations = {
  SET_SETTING: (state, setting) => {
    state.setting = setting
  }
}

export const actions = {
  getInfo({ commit }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .post('/Setting/list', {})
        .then((response) => {
          const { status, data } = response
          if (status === 'success') {
            const setting = {}
            for (let i = 0; i < data.length; i++) {
              setting[data[i].VARIABLE] = data[i].VALUE
            }
            commit('SET_SETTING', setting)
          }

          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  }
}
