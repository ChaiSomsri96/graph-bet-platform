export const state = () => ({
  bettingBox: {
    clientHeight: 0
  },
  windowBox: {
    clientWidth: 0,
    clientHeight: 0
  },
  gameHistory: {
    id: 0,
    bust: 0,
    gameLogId: 0
  },
  confirmPwd: '',
  curGameId: 0
})

export const getters = {
  getBettingBoxHeight: (state) => state.bettingBox.clientHeight,
  getWindowBoxWidth: (state) => state.windowBox.clientWidth,
  getWindowBoxHeight: (state) => state.windowBox.clientHeight,
  getGameHistoryId: (state) => state.gameHistory.id,
  getGameHistoryBust: (state) => state.gameHistory.bust,
  getGameHistoryLogId: (state) => state.gameHistory.gameLogId,
  getConfirmPwd: (state) => state.confirmPwd,
  getCurGameId: (state) => state.curGameId
}

export const mutations = {
  SET_BETTING_BOX_HEIGHT: (state, height) => {
    state.bettingBox.clientHeight = height
  },
  SET_WINDOW_BOX_WIDTH: (state, width) => {
    state.windowBox.clientWidth = width
  },
  SET_WINDOW_BOX_HEIGHT: (state, height) => {
    state.windowBox.clientHeight = height
  },
  SET_GAME_HISTORY: (state, history) => {
    state.gameHistory.id = history.GAME_ID
    state.gameHistory.bust = history.BUST
  },
  SET_GAME_HISTORY_LOG_ID: (state, id) => {
    state.gameHistory.gameLogId = id
  },
  SET_CONFIRM_PWD: (state, pwd) => {
    state.confirmPwd = pwd
  },
  SET_CUR_GAME_ID: (state, id) => {
    state.curGameId = id
  }
}

export const actions = {
  setBettingBoxHeight({ commit }, height) {
    commit('SET_BETTING_BOX_HEIGHT', height)
  },
  setWindowBoxWidth({ commit }, width) {
    commit('SET_WINDOW_BOX_WIDTH', width)
  },
  setWindowBoxHeight({ commit }, height) {
    commit('SET_WINDOW_BOX_HEIGHT', height)
  },
  setGameHistory({ commit }, history) {
    commit('SET_GAME_HISTORY', history)
  },
  setGameHistoryLogId({ commit }, id) {
    commit('SET_GAME_HISTORY_LOG_ID', id)
  },
  setConfirmPwd({ commit }, pwd) {
    commit('SET_CONFIRM_PWD', pwd)
  },
  setCurGameId({ commit }, id) {
    commit('SET_CUR_GAME_ID', id)
  }
}

// export default {
//   namespaced: true,
//   state,
//   mutations,
//   actions
// }
