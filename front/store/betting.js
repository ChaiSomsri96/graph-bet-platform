export const state = () => ({
  betgameid: 0,
  bet: '1000',
  bettemp: '1000',
  isbet: false,
  iscashout: false,
  payout: '',
  isAuto: false,
  isAutoTemp: false,
  baseBet: '1000',
  autobet: '1000',
  cashout: '',
  stopIf: '',
  onWinCond: 0,
  onWinValue: '',
  onLossCond: 0,
  onLossValue: '',
  sessionProfit: 0
})

export const getters = {
  getBetGameId: (state) => state.betgameid,
  getBet: (state) => state.bet,
  getBetTemp: (state) => state.bettemp,
  getBetStatus: (state) => state.isbet,
  getCashOutStatus: (state) => state.iscashout,
  getPayout: (state) => state.payout,
  getIsAuto: (state) => state.isAuto,
  getIsAutoTemp: (state) => state.isAutoTemp,
  getBaseBet: (state) => state.baseBet,
  getAutoBet: (state) => state.autobet,
  getCashOut: (state) => state.cashout,
  getStopIf: (state) => state.stopIf,
  getOnWinCond: (state) => state.onWinCond,
  getOnWinValue: (state) => state.onWinValue,
  getOnLossCond: (state) => state.onLossCond,
  getOnLossValue: (state) => state.onLossValue,
  getSessionProfit: (state) => state.sessionProfit
}

export const mutations = {
  SET_BET_GAME_ID: (state, val) => {
    state.betgameid = val
  },
  SET_BET: (state, val) => {
    state.bet = val
  },
  SET_BET_TEMP: (state, val) => {
    state.bettemp = val
  },
  SET_BET_STATUS: (state, val) => {
    state.isbet = val
  },
  SET_CASHOUT_STATUS: (state, val) => {
    state.iscashout = val
  },
  SET_PAYOUT: (state, val) => {
    state.payout = val
  },
  SET_IS_AUTO: (state, val) => {
    state.isAuto = val
  },
  SET_IS_AUTO_TEMP: (state, val) => {
    state.isAutoTemp = val
  },
  SET_BASE_BET: (state, val) => {
    state.baseBet = val
  },
  SET_AUTO_BET: (state, val) => {
    state.autobet = val
  },
  SET_CASHOUT: (state, val) => {
    state.cashout = val
  },
  SET_STOP_IF: (state, val) => {
    state.stopIf = val
  },
  SET_ON_WIN_COND: (state, val) => {
    state.onWinCond = val
  },
  SET_ON_WIN_VALUE: (state, val) => {
    state.onWinValue = val
  },
  SET_ON_LOSS_COND: (state, val) => {
    state.onLossCond = val
  },
  SET_ON_LOSS_VALUE: (state, val) => {
    state.onLossValue = val
  },
  SET_SESSION_PROFIT: (state, val) => {
    state.sessionProfit = val
  }
}

export const actions = {
  setBetGameId({ commit }, val) {
    commit('SET_BET_GAME_ID', val)
  },
  setBet({ commit }, val) {
    commit('SET_BET', val)
  },
  setBetTemp({ commit }, val) {
    commit('SET_BET_TEMP', val)
  },
  setBetStatus({ commit }, val) {
    commit('SET_BET_STATUS', val)
  },
  setCashOutStatus({ commit }, val) {
    commit('SET_CASHOUT_STATUS', val)
  },
  setPayout({ commit }, val) {
    commit('SET_PAYOUT', val)
  },
  setIsAuto({ commit }, val) {
    commit('SET_IS_AUTO', val)
  },
  setIsAutoTemp({ commit }, val) {
    commit('SET_IS_AUTO_TEMP', val)
  },
  setBaseBet({ commit }, val) {
    commit('SET_BASE_BET', val)
  },
  setAutoBet({ commit }, val) {
    commit('SET_AUTO_BET', val)
  },
  setCashOut({ commit }, val) {
    commit('SET_CASHOUT', val)
  },
  setStopIf({ commit }, val) {
    commit('SET_STOP_IF', val)
  },
  setOnWinCond({ commit }, val) {
    commit('SET_ON_WIN_COND', val)
  },
  setOnWinValue({ commit }, val) {
    commit('SET_ON_WIN_VALUE', val)
  },
  setOnLossCond({ commit }, val) {
    commit('SET_ON_LOSS_COND', val)
  },
  setOnLossValue({ commit }, val) {
    commit('SET_ON_LOSS_VALUE', val)
  },
  setSessionProfit({ commit }, val) {
    commit('SET_SESSION_PROFIT', val)
  },
  clearBettingInfo({ commit }) {
    commit('SET_BET_GAME_ID', 0)
    commit('SET_BET', '1000')
    commit('SET_BET_TEMP', '1000')
    commit('SET_BET_STATUS', false)
    commit('SET_CASHOUT_STATUS', false)
    commit('SET_PAYOUT', '')
    commit('SET_BASE_BET', '1000')
    commit('SET_AUTO_BET', '1000')
    commit('SET_CASHOUT', '')
    commit('SET_STOP_IF', '')
    commit('SET_ON_WIN_COND', 0)
    commit('SET_ON_WIN_VALUE', '')
    commit('SET_ON_LOSS_COND', 0)
    commit('SET_ON_LOSS_VALUE', '')
  },
  clearBettingFlagInfo({ commit }) {
    commit('SET_BET_GAME_ID', 0)
    commit('SET_BET_STATUS', false)
    commit('SET_CASHOUT_STATUS', false)
  },
  clearSessionProfit({ commit }) {
    commit('SET_IS_AUTO_TEMP', false)
    commit('SET_SESSION_PROFIT', 0)
  }
}
