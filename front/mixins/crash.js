export default {
  name: 'Crash',
  data() {
    return {}
  },
  computed: {
    wallet() {
      const wallet = this.$store.getters['user/getWalletInfo']
      return wallet !== undefined && wallet !== '' ? wallet : 0
    },
    getBetStatus() {
      const isbet = this.$store.getters['betting/getBetStatus']
      return isbet !== undefined ? isbet : false
    },
    getCashOutStatus() {
      const iscashout = this.$store.getters['betting/getCashOutStatus']
      return iscashout !== undefined ? iscashout : false
    }
  },
  watch: {
    getBetStatus: {
      handler(val, oldVal) {
        this.isBet = val !== undefined ? val : false
        this.updateBtn()
      },
      immediate: true
    },
    getCashOutStatus: {
      handler(val, oldVal) {
        this.isCashOut = val !== undefined ? val : false
        this.updateBtn()
      },
      immediate: true
    }
  },
  beforeDestroy() {
    this.$crashSocketConn.$off()
  },
  mounted() {
    const self = this
    this.$axios
      .post('/User/getUserCount', {})
      .then((response) => {
        if (response.status === 'success') {
          self.onlineUserCount = response.data
          self.$bus.$emit('user-count-changed', self.onlineUserCount)
        }
      })
      .catch((error) => {
        error = 0
      })
    this.$axios
      .post('/Crash/getHistory')
      .then((response) => {
        const { status, data } = response
        if (status === 'success') {
          self.histories = data
          self.updateCashHistory()
        }
      })
      .catch((error) => {
        error = 0
      })

    // if (this.userId !== null || this.userId !== undefined)
    this.$axios
      .post('/Crash/getStatus')
      .then((response) => {
        if (response.status === 'success') {
          self.$store.dispatch('app/setCurGameId', response.data.game_id)
          if (self.storeBetGameId === response.data.game_id) {
            self.payOut = self.storePayout
            self.isBet = self.getBetStatus
            self.isCashOut = self.getCashOutStatus
          } else {
            self.$store.dispatch('betting/clearBettingFlagInfo')
          }
          self.updateBtn()
          self.reloadPlayers(response.data)
        }
      })
      .catch((error) => {
        error = 0
      })

    this.$crashSocketConn.$on('onMessage', function(data) {
      switch (data.code) {
        case 'WaitGame':
          self.users.splice(0, self.users.length)
          self.onWait(data)
          break

        case 'GameStart':
          self.startGame(data)
          break

        case 'CrashUpdate':
          self.crashUpdate(data)
          break

        case 'GameCrash':
          self.crashGame(data)
          break

        case 'Bet':
          self.onBet(data)
          break

        case 'CancelBet':
          self.onCancelBet(data)
          break

        case 'CashOut':
          self.cashOut(data)
          break

        case 'Tick':
          if (Date.now() - self.timeStamp > 500) {
            self.timeStamp = Date.now()
          }
          self.doTick(data.tick)
          break

        case 'ReloadPlayers':
          self.reloadPlayers(data)
          break

        case 'GameRule':
          break
      }
    })

    this.$crashSocketConn.$on('disconnect', function() {
      self.stopGame()
    })

    this.$bus.$on('current-payout', function(payout) {
      self.tick = parseInt(payout.value * 100)
      if (self.state === 'STARTED') {
        if (!self.isAutoBetting) {
          self.updateBtn()
          if (
            payout.value >= self.payOut &&
            self.isBet &&
            !self.isCashOut &&
            self.payOut > 1 &&
            !self.isPayoutCash
          ) {
            self.isPayoutCash = true
            self.doCashOut()
          }
        } else if (!self.isPayoutCash) self.doAutoCashOut()
      }
    })
  },
  methods: {
    doBet() {
      this.isWaitResponse = true
      this.$axios
        .post('/Crash/bet', {
          // user_id: this.userId,
          bet: this.betAmount,
          user_name: this.name
        })
        .then((response) => {
          if (response.status === 'success') {
            this.$store.dispatch('user/getWalletInfo')
            this.$store.dispatch('user/getBettingData')
            this.$store.dispatch('betting/setBetStatus', true)
            this.$store.dispatch('betting/setBetGameId', response.gameid)
            this.$store.dispatch('betting/setBet', response.value)
            if (this.isAutoBetting) {
              this.$store.dispatch(
                'betting/setSessionProfit',
                parseInt(this.sessionProfit) - parseInt(response.value)
              )
            }
            this.updateBtn()
          } else {
            this.$toast.error({
              title: 'Error',
              message: response.res_msg,
              position: 'top right',
              type: 'error',
              progressBar: true,
              showMethod: 'shake',
              color: '#BE2739',
              showDuration: 1000,
              hideDuration: 1000,
              timeOut: 5000
            })
          }
          this.isWaitResponse = false
          this.updateBtn()
        })
        .catch((error) => {
          error = 0
        })
    },
    cancelBet() {
      this.isWaitResponse = true
      this.$axios.post('/Crash/cancelBet', {
        bet: this.betAmount,
        user_name: this.name
      })

      this.updateBtn()
    },
    doAutoBet() {
      this.autoData = this.autoDataTemp
      if (
        parseInt(this.autoData.bet) > 0 &&
        parseFloat(this.autoData.cash) > 1
      ) {
        this.betAmount = parseInt(this.autoData.bet)
        this.doBet()
      } else {
        this.isAutoBetting = false
        this.$store.dispatch('betting/setIsAuto', this.isAutoBetting)
        this.stopAutoBetting()
      }
    },
    doCashOut() {
      this.isWaitResponse = true
      this.$axios
        .post('/Crash/cashOut', {
          stopped_at: this.tick,
          bet: this.betAmount
        })
        .then((response) => {
          if (response.status === 'success') {
            this.$toast.success({
              title: 'Success',
              message: 'あなたの利益は' + response.profit + 'です。',
              position: 'top right',
              type: 'success',
              progressBar: true,
              showMethod: 'fadeIn',
              color: '#51A351',
              showDuration: 1000,
              hideDuration: 1000,
              timeOut: 5000
            })
            this.$store.dispatch('betting/setCashOutStatus', true)
            if (this.isAutoBetting) {
              this.$store.dispatch(
                'betting/setSessionProfit',
                parseInt(this.sessionProfit) +
                  parseInt(response.profit) +
                  parseInt(this.betAmount)
              )
            }
            this.$store.dispatch('user/getWalletInfo')
            this.$store.dispatch('user/getBettingData')
            this.$store.dispatch('user/getTotalProfit')
          } else {
            this.updateBtn()
            this.$toast.error({
              title: 'Error',
              message: response.res_msg,
              position: 'top right',
              type: 'error',
              progressBar: true,
              showMethod: 'shake',
              color: '#BE2739',
              showDuration: 1000,
              hideDuration: 1000,
              timeOut: 5000
            })
          }
          this.isWaitResponse = false
          if (this.isAutoBettingTemp) {
            this.btnStatus = 'disabled'
            this.titleBetBtn = 'Bet'
          }
        })
        .catch((error) => {
          error = 0
        })
    },
    doAutoCashOut() {
      if (!this.isAutoBetting) return

      if (this.tick < parseFloat(this.autoData.cash) * 100) return

      if (
        this.autoData.cash === undefined ||
        this.autoData.cash === '' ||
        parseFloat(this.autoData.cash) < 1
      )
        this.autoData = this.autoDataTemp

      if (this.isBet && !this.isCashOut && !this.isPayoutCash) {
        this.tick = parseInt(parseFloat(this.autoData.cash) * 100)
        this.isPayoutCash = true
        this.doCashOut()
      }
    },
    updateCashHistory() {
      this.cashHistory = []
      let i
      for (i = 0; i < 6 && i < this.histories.length; i++)
        this.cashHistory.push(this.histories[i])
    },
    onWait(data) {
      this.maxBet = 0
      this.maxProfit = 0
      this.$store.dispatch('app/setCurGameId', data.game_id)
      this.emitBus('game-created', { duration: 0 })
      this.timeLeft = data.time_left
      this.state = 'WAITING'
      this.gameId = data.game_id
      this.isCashOut = false
      this.isBet = false
      this.$store.dispatch('betting/setBetStatus', false)
      this.$store.dispatch('betting/setCashOutStatus', false)
      this.isPayoutCash = false
      this.initTimer()
      this.isAutoBetting = this.isAutoBettingTemp
      this.$store.dispatch('betting/setIsAuto', this.isAutoBetting)
      if (this.isAutoBetting) {
        this.doAutoBet()
        this.btnStatus = 'disabled'
      }
      if (!this.isAutoBetting && this.isPreBet && this.betTemp > 0) {
        this.isPreBet = false
        this.betAmount = this.betTemp
        this.doBet()
      }
      this.updateBtn()

      const self = this
      this.$axios
        .post('/User/getUserCount', {})
        .then((response) => {
          if (response.status === 'success') {
            self.onlineUserCount = response.data
          }
        })
        .catch((error) => {
          error = 0
        })
    },
    startGame(data) {
      this.$store.dispatch('app/setCurGameId', data.game_id)
      this.closeTimer()

      this.tick = data.tick
      this.timeStamp = Date.now()

      this.state = 'STARTED'
      this.emitBus('game-started', { crash: this.tick })

      this.updateBtn()
    },
    crashUpdate(data) {
      const historyData = {
        GAME_ID: data.game_id,
        BUST: data.bust,
        BET: this.isBet ? this.betAmount : null,
        CASHOUT: null,
        PROFIT: null
      }
      if (this.isBet) {
        let userInf
        for (let i = 0; i < this.users.length; i++) {
          userInf = this.users[i]
          if (userInf.userid === this.userId && !userInf.isBot) {
            historyData.CASHOUT = userInf.cashout
              ? parseFloat(userInf.cashout / 100).toFixed(2) + 'x'
              : null
            historyData.PROFIT = userInf.profit ? userInf.profit : null
          }
          if (this.maxProfit < userInf.profit) {
            this.maxProfit = parseInt(userInf.profit)
          }
        }
      }
      this.histories.reverse()
      if (this.histories.length >= 50) {
        this.histories.splice(0, 1)
      }
      this.histories.push(historyData)
      this.histories.reverse()
      this.updateCashHistory()
      /*
      const self = this
      this.$axios
        .post('/Crash/getHistory')
        .then((response) => {
          const { status, data } = response
          if (status === 'success') {
            self.histories = data
            self.updateCashHistory()
          }
        })
        .catch((error) => {
          error = 0
        })
      */
    },
    crashGame(data) {
      this.state = 'CRASHED'
      this.tick = data.crash
      this.emitBus('game-finished', { crash: this.tick })

      for (let i = 0; i < this.users.length; i++) {
        const nCashOut = this.users[i].cashout
        if (nCashOut === 0)
          this.users[i].profit = parseInt(-this.users[i].value).toFixed(0)
        if (this.maxProfit < this.users[i].profit) {
          this.maxProfit = this.users[i].profit
        }
      }

      if (this.isBet && !this.isCashOut) {
        this.updateBtn()

        this.$store.dispatch('user/getBettingData')
        this.$store.dispatch('user/getTotalProfit')
      }

      if (this.isAutoBetting) {
        this.emitBus('auto-finish-game', { value: this.isCashOut })
      }
    },
    onBet(data) {
      const userInf = {
        userid: data.userid,
        username: data.username,
        cashout: 0,
        value: data.value,
        profit: '-'
      }
      if (this.maxBet < data.value) {
        this.maxBet = parseFloat(data.value)
      }
      this.users.push(userInf)

      this.users.sort(function(itemA, itemB) {
        if (itemA.cashout === 0 && itemB.cashout === 0) {
          if (itemA.value < itemB.value) return 1
          else if (itemA.value > itemB.value) return -1
          else return 0
        } else if (itemA.cashout === 0) return -1
        else if (itemB.cashout === 0) return 1
        else if (itemA.cashout < itemB.cashout) return 1
        else if (itemA.cashout > itemB.cashout) return -1
        else return 0
      })
    },
    onCancelBet(data) {
      this.isWaitResponse = false
      let i = 0
      for (i = 0; i < this.users.length; i++) {
        if (
          this.users[i].username === data.username &&
          this.users[i].value === data.value &&
          this.users[i].userid === data.userid
        ) {
          this.users.splice(i, 1)
          break
        }
      }

      if (data.userid === this.userId) {
        this.isBet = false
        this.betAmount = 0
        this.updateBtn()
        this.$store.dispatch('user/getWalletInfo')
        this.$store.dispatch('user/getBettingData')
      }
    },
    cashOut(data) {
      let i = 0
      for (i = 0; i < this.users.length; i++) {
        if (
          this.users[i].username === data.username &&
          this.users[i].value === data.value &&
          data.cashout > 100
        ) {
          this.users[i].cashout = parseInt(data.cashout)
          this.users[i].profit = parseInt(
            (data.cashout / 100 - 1) * data.value + 0.5
          ).toFixed(0)
          if (this.maxProfit < parseInt(this.users[i].profit)) {
            this.maxProfit = parseInt(this.users[i].profit)
          }
          break
        }
      }
      this.users.sort(function(itemA, itemB) {
        if (itemA.cashout === 0 && itemB.cashout === 0) {
          if (itemA.value < itemB.value) return 1
          else if (itemA.value > itemB.value) return -1
          else return 0
        } else if (itemA.cashout === 0) return -1
        else if (itemB.cashout === 0) return 1
        else if (itemA.cashout < itemB.cashout) return 1
        else if (itemA.cashout > itemB.cashout) return -1
        else return 0
      })
    },
    stopGame() {
      this.emitBus('game-error', {})
    },
    reloadPlayers(data) {
      let i = 0
      let aryData = data.curUser
      let item

      this.users.splice(0, this.users.length)
      for (i = 0; i < aryData.length; i++) {
        item = aryData[i]
        if (item.cashout > this.tick) {
          item.cashout = 0
          item.profit = '-'
        }
        this.users.push(item)
      }

      aryData = data.cashoutUser
      for (i = 0; i < aryData.length; i++) {
        item = aryData[i]
        item.profit = parseInt(
          (item.cashout / 100 - 1) * item.value + 0.5
        ).toFixed(0)
        this.users.push(item)
      }
      for (i = 0; i < this.users.length; i++) {
        if (this.maxBet < this.users[i].value) {
          this.maxBet = parseFloat(this.users[i].value)
        }
        if (this.maxProfit < this.users[i].profit) {
          this.maxProfit = parseInt(this.users[i].profit)
        }
      }
      this.users.sort(function(itemA, itemB) {
        if (itemA.cashout === 0 && itemB.cashout === 0) {
          if (itemA.value < itemB.value) return 1
          else if (itemA.value > itemB.value) return -1
          else return 0
        } else if (itemA.cashout === 0) return -1
        else if (itemB.cashout === 0) return 1
        else if (itemA.cashout < itemB.cashout) return 1
        else if (itemA.cashout > itemB.cashout) return -1
        else return 0
      })
    },
    initTimer() {
      if (this.timerHandler) return

      this.timerHandler = setInterval(this.intervalFunc, this.interval)
    },
    closeTimer() {
      if (this.timerHandler === 0) return

      clearInterval(this.timerHandler)
      this.timerHandler = 0
    },
    intervalFunc() {
      this.emitBus('game-created', { duration: this.timeLeft })
      this.timeLeft -= this.interval
      if (this.timeLeft <= 0) {
        clearInterval(this.timerHandler)
        this.timerHandler = 0
      }
    }
  }
}
