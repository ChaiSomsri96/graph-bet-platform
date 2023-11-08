<template>
  <div class="main-container">
    <div class="main-large">
      <div class="flex-row-hc-vt flex-wrap v-full">
        <div class="graph-col flex-col-hc-vc v-full">
          <betting-box
            ref="bettingBox"
            :title-bet-btn="titleBetBtn"
            :btn-status="btnStatus"
            :cash-history="cashHistory"
            :overlay-visible="isWaitResponse && !isAutoBetting"
            :is-disable="isAutoBettingTemp"
            :auto-bet-btn-title="isAutoBettingTemp ? 'Stop Auto' : 'Bet'"
            :max-bet="maxBet"
            :max-profit="maxProfit"
            @changeBet="changeBet"
            @changePayout="changePayout"
            @clickBet="clickBet"
            @changeAuto="changeAuto"
            @clickAutoBetting="clickAutoBetting"
          />
          <history-chat-box
            class="history-chat-box-root"
            :history-infos="histories"
            :chat-infos="chats"
            :user-infos="users"
            :is-user-list-show="!isUserListShow"
            :auto-bet-btn-title="isAutoBettingTemp ? 'Stop Auto' : 'Bet'"
            @changeAuto="changeAuto"
            @clickAutoBetting="clickAutoBetting"
          />
        </div>
        <div class="list-col v-full">
          <user-list
            :user-infos="users"
            :online-user-count="onlineUserCount"
            :is-crashed="state === 'CRASHED'"
          />
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import UserList from '@/components/pc/main/UserList.vue'
import BettingBox from '@/components/pc/main/BettingBox.vue'
import HistoryChatBox from '@/components/pc/main/HistoryChatBox.vue'

import CrashMixin from '~/mixins/crash'
import GlobalMixin from '~/mixins/global'
import { toThousandFilter } from '@/utils/index.js'

export default {
  name: 'MainPage',
  components: {
    UserList,
    BettingBox,
    HistoryChatBox
  },
  mixins: [CrashMixin, GlobalMixin],
  props: {},
  data() {
    return {
      gameId: 0,

      betAmount: 0,
      betTemp: 1000,
      isBet: false,
      isPreBet: false,
      isCashOut: false,
      btnStatus: 'bet',
      isWaitResponse: false,
      tick: 0,
      timeStamp: 0,
      state: 'WAITING',
      payOut: '',
      isPayoutCash: false,

      timerHandler: 0,
      interval: 10,
      timeLeft: 5000,

      titleBetBtn: 'Bet',

      isUserListShow: true,

      isAutoBetting: false,
      isAutoBettingTemp: false,
      autoData: {},
      autoDataTemp: {},
      sessionProfit: 0,

      users: [],
      chats: [],
      histories: [],
      cashHistory: [],
      onlineUserCount: 0,

      maxBet: 0,
      maxProfit: 0
    }
  },
  computed: {
    userId() {
      const id = this.$store.getters['user/getId']
      return id !== undefined && id !== '' ? id : 0
    },
    storeSessionProfit() {
      return this.$store.getters['betting/getSessionProfit']
    },
    storeIsAutoTemp() {
      const session = this.$store.getters['betting/getIsAutoTemp']
      return session !== undefined && session !== '' ? session : false
    },
    storeIsAuto() {
      const session = this.$store.getters['betting/getIsAuto']
      return session !== undefined && session !== '' ? session : false
    },
    storeBet() {
      return this.$store.getters['betting/getBet']
    },
    storeBetTemp() {
      const bet = this.$store.getters['betting/getBetTemp']
      return bet !== undefined && bet !== '' ? bet : '1000'
    },
    storePayout() {
      return this.$store.getters['betting/getPayout']
    },
    storeBetGameId() {
      const betid = this.$store.getters['betting/getBetGameId']
      return betid !== undefined && betid !== '' ? betid : 0
    },
    getCurGameId() {
      const curid = this.$store.getters['betting/getCurGameId']
      return curid !== undefined && curid !== '' ? curid : 0
    }
  },
  watch: {
    storeSessionProfit: {
      handler(val, oldVal) {
        this.sessionProfit = val !== undefined && val !== '' ? val : 0
      },
      immediate: true
    },
    storeIsAutoTemp: {
      handler(val, oldVal) {
        this.isAutoBettingTemp = val !== undefined && val !== '' ? val : false
      },
      immediate: true
    },
    storeIsAuto: {
      handler(val, oldVal) {
        this.isAutoBetting = val !== undefined && val !== '' ? val : false
      },
      immediate: true
    },
    storeBet: {
      handler(val, oldVal) {
        this.betAmount = val !== undefined ? val : '1000'
      },
      immediate: true
    },
    storeBetTemp: {
      handler(val, oldVal) {
        this.betTemp = val !== undefined ? val : '1000'
      },
      immediate: true
    },
    storePayout: {
      handler(val, oldVal) {
        this.payOut = val !== undefined ? val : ''
      },
      immediate: true
    },
    getCurGameId: {
      handler(val, oldVal) {
        this.gameId = val !== undefined ? val : 0
      },
      immediate: true
    }
  },
  mounted() {
    const self = this
    window.addEventListener('resize', this.__resizeHandler)
    if (this.$refs.bettingBox)
      this.$store.dispatch(
        'app/setBettingBoxHeight',
        this.$refs.bettingBox.$el.clientHeight
      )
    this.$store.dispatch('app/setWindowBoxWidth', window.innerWidth)
    this.$store.dispatch('app/setWindowBoxHeight', window.innerHeight)
    this.__resizeHandler()
    // check if you are logged in
    this.$store.dispatch('user/getWalletInfo')
    this.$bus.$on('on-register-success', function() {
      self.showToast(
        'Success',
        'あなたのユーザー登録が成功しました。',
        'success'
      )
    })
    this.$bus.$on('on-change-login', function(param) {
      self.__resizeHandler()
      if (param.isLogIn) {
        self.showToast('Success', 'システムに加入成功しました。', 'success')
      } else {
        self.$store.dispatch('betting/clearBettingFlagInfo')
        self.stopAutoBetting()
      }
    })

    this.$bus.$on('on-change-password', function(param) {
      if (param.type === 1)
        self.showToast(
          'Success',
          'パスワードが正常に変更されました。',
          'success'
        )
      else if (param.type === 2)
        self.showToast(
          'Success',
          'リカバリ E-メールが正常に変更されました。',
          'success'
        )
    })

    this.betTemp = this.storeBetTemp
    this.bet = this.storeBet
  },
  updated() {
    this.__resizeHandler()
  },
  beforeDestroy() {
    window.removeEventListener('resize', this.__resizeHandler)
    // this.$store.dispatch('betting/clearBettingInfo')
  },
  methods: {
    __resizeHandler() {
      this.$nextTick(() => {
        if (this.$refs.bettingBox)
          this.$store.dispatch(
            'app/setBettingBoxHeight',
            this.$refs.bettingBox.$el.clientHeight
          )
        this.$store.dispatch('app/setWindowBoxWidth', window.innerWidth)
        this.$store.dispatch('app/setWindowBoxHeight', window.innerHeight)
      })
      if (this.$root.$el.clientWidth > 1000) this.isUserListShow = true
      else this.isUserListShow = false
    },
    changeBet(inputValue) {
      // this.betTemp = parseInt(inputValue)
      this.$store.dispatch('betting/setBetTemp', inputValue)
    },
    changePayout(inputValue) {
      // this.payOut = inputValue
      this.$store.dispatch('betting/setPayout', inputValue)
    },
    clickBet() {
      if (this.isAutoBetting) {
        this.showToast(
          'Error',
          '貴方は自動賭博が終了する前に一般賭博をすることができません。',
          'error'
        )
        return
      }

      if (
        this.wallet < this.betTemp &&
        (this.btnStatus === 'bet' || this.btnStatus === 'prevbet')
      ) {
        this.showToast(
          'Error',
          'あなたのウォレットが十分ではありません。',
          'error'
        )
        return
      }

      if (this.btnStatus === 'bet') {
        if (this.betTemp > 0) {
          this.betAmount = this.betTemp
          this.doBet()
        }
      } else if (this.btnStatus === 'prevbet') {
        if (this.betTemp > 0) {
          this.isPreBet = true
        }
      } else if (this.btnStatus === 'cancel') {
        this.isPreBet = false
      } else if (this.btnStatus === 'cashout') {
        this.doCashOut()
      } else if (this.btnStatus === 'betting') {
      } else {
      }

      this.updateBtn()
    },
    emitBus(key, data) {
      this.$bus.$emit(key, data)
    },
    doTick(valTick) {
      if (this.state !== 'STARTED') {
        this.startGame({ tick: valTick })
      } else {
        this.tick = valTick
        this.emitBus('do-tick', { tick: valTick })
      }
    },
    updateBtn() {
      if (!this.isAutoBetting) {
        if (this.isBet) {
          if (this.state === 'WAITING') {
            this.titleBetBtn = 'Betting...'
            this.btnStatus = 'betting'
          } else if (this.state === 'STARTED') {
            if (this.isCashOut) {
              if (this.isAutoBettingTemp) {
                this.btnStatus = 'disabled'
                this.titleBetBtn = 'Bet'
                this.isPreBet = false
              } else if (this.isPreBet) {
                this.btnStatus = 'cancel'
                this.titleBetBtn = 'Cancel'
              } else {
                this.btnStatus = 'prevbet'
                this.titleBetBtn = 'Bet'
              }
            } else if (!this.isWaitResponse) {
              this.btnStatus = 'cashout'
              this.titleBetBtn =
                'CashOut @' +
                toThousandFilter(parseInt((this.tick * this.betAmount) / 100))
            }
          } else if (this.state === 'CRASHED') {
            if (this.isPreBet) {
              this.btnStatus = 'cancel'
              this.titleBetBtn = 'Cancel'
            } else {
              this.btnStatus = 'prevbet'
              this.titleBetBtn = 'Bet'
            }
          }
        } else if (this.state === 'STARTED' || this.state === 'CRASHED') {
          if (this.isAutoBettingTemp) {
            this.btnStatus = 'disabled'
            this.titleBetBtn = 'Bet'
            this.isPreBet = false
          } else if (this.isPreBet) {
            this.btnStatus = 'cancel'
            this.titleBetBtn = 'Cancel'
          } else {
            this.btnStatus = 'prevbet'
            this.titleBetBtn = 'Bet'
          }
        } else if (this.state === 'WAITING') {
          if (this.isAutoBettingTemp) this.btnStatus = 'disabled'
          else this.btnStatus = 'bet'
          this.titleBetBtn = 'Bet'
        }
      } else {
        if (this.isAutoBettingTemp) this.btnStatus = 'disabled'
        else this.btnStatus = 'bet'
        this.titleBetBtn = 'Bet'
      }
    },
    changeAuto(autoData) {
      this.autoDataTemp = autoData
      this.$store.dispatch('betting/setBaseBet', autoData.basebet)
      this.$store.dispatch('betting/setAutoBet', autoData.bet)
      this.$store.dispatch('betting/setCashOut', autoData.cash)
      this.$store.dispatch('betting/setStopIf', autoData.stop)
      this.$store.dispatch('betting/setOnWinCond', autoData.isWin)
      this.$store.dispatch('betting/setOnWinValue', autoData.win)
      this.$store.dispatch('betting/setOnLossCond', autoData.isLoss)
      this.$store.dispatch('betting/setOnLossValue', autoData.loss)
    },
    clickAutoBetting() {
      if (this.isAutoBettingTemp) this.stopAutoBetting()
      else this.startAutoBetting()
    },
    stopAutoBetting() {
      this.isAutoBettingTemp = false
      this.$store.dispatch('betting/clearSessionProfit')
      this.updateBtn()
    },
    startAutoBetting() {
      this.isAutoBettingTemp = true
      this.$store.dispatch('betting/setIsAutoTemp', this.isAutoBettingTemp)
      this.updateBtn()
    }
  }
}
</script>
<style lang="scss" scoped>
@import '@/assets/styles/variables.scss';

.main-container {
  background: $gray35;
  padding-top: $mainContainerPaddingTop;
  padding-bottom: $mainContainerPaddingBottom;
}

.main-large {
  width: 100%;
  max-width: $desktopMaxWidth;
  padding: 0px 20px;
  margin: 0px auto;
  position: relative;
}

.graph-col {
  width: 66%;
  padding-left: 10px;
  padding-right: 10px;
}

.list-col {
  width: 34%;
  padding-left: 10px;
  padding-right: 10px;
}

.history-chat-box-root {
  margin-top: $historyChatBoxMarginTop;
}

@media (max-width: 1000px) {
  .graph-col {
    width: 100%;
  }

  .list-col {
    display: none;
  }
}

@media (max-width: 740px) {
  .main-large {
    padding: 0px 0px;
  }
}

@media (max-width: 560px) {
  .graph-col {
    padding-left: 0px;
    padding-right: 0px;
  }

  .list-col {
    padding-left: 0px;
    padding-right: 0px;
  }
}
</style>
