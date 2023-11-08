<template>
  <b-form class="h-full scrollbar-wrapper" @submit="onSubmit">
    <div class="flex-col-hl-vc pd-10 h-full fc-white fs16">
      <div class="flex-row-hc-vc h-full">
        <div class="flex-col-hc-vc h-full">
          <div class="flex-row-hc-vc h-full flex-wrap">
            <div class="flex-col-hl-vc flex6 p-l-5 p-r-5 m-w-160">
              <div class="flex-row-hl-vc h-full">
                <svg-icon icon-class="arrow_orange_right" class="icon-box" />
                <div class="fw500 m-l-5 text-no-wrap">BASE BET</div>
              </div>
              <div class="flex-row-hl-vc h-full">
                <input-with-label-box
                  :has-title="false"
                  :is-round="false"
                  :is-required="lossActiveId === 0 || winActiveId === 0"
                  :var-link="basebetTemp"
                  @inputChanged="changeAutoBaseBet"
                />
                <bits-btc-button
                  button-size="small"
                  class="m-l-5"
                  :mode="betValueIsBits"
                  @modeChanged="bitsModeChanged"
                />
              </div>
            </div>
            <div class="flex-col-hl-vc flex6 p-l-5 p-r-5 m-w-160">
              <div class="flex-row-hl-vc h-full">
                <svg-icon icon-class="arrow_orange_right" class="icon-box" />
                <div class="fw500 m-l-5 text-no-wrap">BET</div>
              </div>
              <div class="flex-row-hl-vc h-full">
                <input-with-label-box
                  :var-link="betTemp"
                  :has-title="false"
                  :is-round="false"
                  :is-readonly="true"
                  @inputChanged="changeAutoBet"
                />
              </div>
            </div>
          </div>
          <div class="flex-row-hc-vc h-full flex-wrap">
            <div class="flex-col-hl-vc flex6 p-l-5 p-r-5 m-w-160">
              <div class="flex-row-hl-vc h-full m-t-5">
                <svg-icon icon-class="arrow_orange_right" class="icon-box" />
                <div class="fw500 m-l-5 text-no-wrap">CASHOUT</div>
              </div>
              <div class="flex-row-hl-vc h-full">
                <input-with-label-box
                  :has-title="false"
                  :is-round="false"
                  :var-link="cashout"
                  @inputChanged="changeCashOut"
                />
                <div v-if="false" class="margin-box m-l-5"></div>
              </div>
            </div>
            <div class="flex-col-hl-vc flex6 p-l-5 p-r-5 m-w-160">
              <div class="flex-row-hl-vc h-full m-t-5">
                <svg-icon icon-class="arrow_orange_right" class="icon-box" />
                <div class="fw500 m-l-5 text-no-wrap">Stop if BET is ></div>
              </div>
              <div class="flex-row-hl-vc h-full">
                <input-with-label-box
                  :has-title="false"
                  :is-round="false"
                  :is-required="false"
                  :input-type="betValueIsBits ? 'number' : 'text'"
                  :var-link="stopBetTemp"
                  @inputChanged="changeStopBet"
                />
                <div v-if="false" class="margin-box m-l-5"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="flex-col-space-around-vc p-l-5 p-r-5 m-h-125">
          <div class="flex-col-hc-vc responsive-box">
            <span class="text-center fs14">Session Profit:</span>
            <span class="text-center fs14">
              {{ sessionProfit | toThousandFilter }} bits
            </span>
          </div>
          <button3-d
            class="h-full text-center"
            :title="title"
            type="submit"
            button-size="large"
            :status="title === 'Bet' ? 'bet' : 'cancel'"
            text-size="large"
            :is-min="true"
          />
        </div>
      </div>
      <div class="flex-row-hc-vc h-full flex-wrap">
        <div class="flex-col-hl-vt flex6 p-l-5 p-r-5 m-w-240">
          <div class="flex-row-hl-vc h-full m-t-5">
            <svg-icon icon-class="arrow_orange_right" class="icon-box" />
            <div class="fw500 m-l-5 text-no-wrap">On Win</div>
          </div>
          <div class="flex-row-hl-vc h-full">
            <b-dropdown
              size="md"
              :text="increaseTitles[winActiveId]"
              class="m-r-10"
            >
              <b-dropdown-item-button
                v-for="(item, i) in increaseTitles"
                :key="i"
                :active="winActiveId === i"
                @click="onWinSelected(i)"
              >
                {{ item }}
              </b-dropdown-item-button>
            </b-dropdown>
            <input-with-label-box
              :has-title="false"
              :is-round="false"
              :is-required="false"
              :var-link="winIncreaseTemp"
              :input-type="betValueIsBits ? 'number' : 'text'"
              @inputChanged="changeWinIncrease"
            />
            <div v-if="false" class="margin-box m-l-5"></div>
          </div>
        </div>
        <div class="flex-col-hl-vt flex6 p-l-5 p-r-5 m-w-240">
          <div class="flex-row-hl-vc h-full m-t-5">
            <svg-icon icon-class="arrow_orange_right" class="icon-box" />
            <div class="fw500 m-l-5 text-no-wrap">On Loss</div>
          </div>
          <div class="flex-row-hl-vc h-full">
            <b-dropdown
              size="md"
              :text="increaseTitles[lossActiveId]"
              class="m-r-10"
            >
              <b-dropdown-item-button
                v-for="(item, i) in increaseTitles"
                :key="i"
                :active="lossActiveId === i"
                @click="onLossSelected(i)"
              >
                {{ item }}
              </b-dropdown-item-button>
            </b-dropdown>
            <input-with-label-box
              :has-title="false"
              :is-round="false"
              :is-required="false"
              :var-link="lossIncreaseTemp"
              :input-type="betValueIsBits ? 'number' : 'text'"
              @inputChanged="changeLossIncrease"
            />
            <div v-if="false" class="margin-box m-l-5"></div>
          </div>
        </div>
      </div>
      <div class="flex-space-between-vc fs11 h-full m-t-5 p-l-5 p-r-5">
        <div class="flex-col-hl-vt">
          <div>Target Profit</div>
          <div>Win Chance</div>
        </div>
        <div class="flex-col-hl-vt">
          <div>2bit</div>
          <div>49.3%</div>
        </div>
      </div>
    </div>
  </b-form>
</template>
<script>
import Button3D from '@/components/pc/common/Button3D.vue'
import BitsBtcButton from '@/components/pc/dialog/BitsBtcButton.vue'
import InputWithLabelBox from '@/components/pc/dialog/InputWithLabelBox.vue'

export default {
  name: 'AutoBettingValueBox',
  components: {
    Button3D,
    BitsBtcButton,
    InputWithLabelBox
  },
  props: {
    title: {
      type: String,
      default: 'Bet'
    },
    increaseTitles: {
      type: Array,
      default() {
        return [
          'Return to Base Bet',
          'Increase Bet' // ,
          // 'Decrease Bet',
          // 'Mutiple Bet'
        ]
      }
    }
  },
  data() {
    return {
      betValueIsBits: true,
      basebet: '1000',
      basebetTemp: '1000',
      bet: '1000',
      betTemp: '1000',
      cashout: '',
      stopBet: '',
      stopBetTemp: '',
      winActiveId: 0,
      winIncrease: '',
      winIncreaseTemp: '',
      lossActiveId: 0,
      lossIncrease: '',
      lossIncreaseTemp: '',
      sessionProfit: 0
    }
  },
  computed: {
    storeBaseBet() {
      return this.$store.getters['betting/getBaseBet']
    },
    storeBet() {
      return this.$store.getters['betting/getAutoBet']
    },
    storeCashOut() {
      return this.$store.getters['betting/getCashOut']
    },
    storeStopBet() {
      return this.$store.getters['betting/getStopIf']
    },
    storeWinActId() {
      return this.$store.getters['betting/getOnWinCond']
    },
    storeWinVal() {
      return this.$store.getters['betting/getOnWinValue']
    },
    storeLossActId() {
      return this.$store.getters['betting/getOnLossCond']
    },
    storeLossVal() {
      return this.$store.getters['betting/getOnLossValue']
    },
    storeSessionProfit() {
      return this.$store.getters['betting/getSessionProfit']
    }
  },
  watch: {
    storeBaseBet: {
      handler(val, oldVal) {
        this.basebet = val !== undefined && val !== '' ? val : '1000'
      },
      immediate: true
    },
    storeBet: {
      handler(val, oldVal) {
        this.bet = val !== undefined && val !== '' ? val : '1000'
      },
      immediate: true
    },
    storeCashOut: {
      handler(val, oldVal) {
        this.cashout = val !== undefined && val !== '' ? val : ''
      },
      immediate: true
    },
    storeStopBet: {
      handler(val, oldVal) {
        this.stopBet = val !== undefined && val !== '' ? val : ''
      },
      immediate: true
    },
    storeWinActId: {
      handler(val, oldVal) {
        this.winActiveId = val !== undefined && val !== '' ? val : 0
      },
      immediate: true
    },
    storeWinVal: {
      handler(val, oldVal) {
        this.winIncrease = val !== undefined && val !== '' ? val : ''
      },
      immediate: true
    },
    storeLossActId: {
      handler(val, oldVal) {
        this.lossActiveId = val !== undefined && val !== '' ? val : 0
      },
      immediate: true
    },
    storeLossVal: {
      handler(val, oldVal) {
        this.lossIncrease = val !== undefined && val !== '' ? val : ''
      },
      immediate: true
    },
    storeSessionProfit: {
      handler(val, oldVal) {
        this.sessionProfit = val !== undefined && val !== '' ? val : 0
      },
      immediate: true
    }
  },
  mounted() {
    const self = this

    this.emitChangeValues()

    this.$bus.$on('auto-finish-game', function(param) {
      self.changeOnWinLoss(param.value)
    })

    let temp = this.storeBaseBet !== undefined ? this.storeBaseBet : '1000'
    this.basebetTemp = this.betValueIsBits ? temp : parseInt(temp) * 1000000

    temp = this.storeBet !== undefined ? this.storeBet : '1000'
    this.betTemp = this.betValueIsBits ? temp : parseInt(temp) * 1000000
  },
  methods: {
    bitsModeChanged(isBits) {
      this.betValueIsBits = isBits
      if (this.basebet !== '')
        this.basebetTemp =
          '' + this.basebet / (this.betValueIsBits ? 1 : 1000000)
      else this.basebetTemp = ''

      if (this.bet !== '')
        this.betTemp = '' + this.bet / (this.betValueIsBits ? 1 : 1000000)
      else this.betTemp = ''

      console.log('asdf--', this.stopBet)
      if (this.stopBet !== '')
        this.stopBetTemp =
          '' + this.stopBet / (this.betValueIsBits ? 1 : 1000000)
      else this.stopBetTemp = ''

      if (this.winIncrease !== '')
        this.winIncreaseTemp =
          '' + this.winIncrease / (this.betValueIsBits ? 1 : 1000000)
      else this.winIncreaseTemp = ''

      if (this.lossIncrease !== '')
        this.lossIncreaseTemp =
          '' + this.lossIncrease / (this.betValueIsBits ? 1 : 1000000)
      else this.lossIncreaseTemp = ''
    },
    changeAutoBaseBet(inputValue) {
      this.basebetTemp = inputValue
      if (isNaN(parseFloat(inputValue))) this.basebet = ''
      else
        this.basebet =
          '' +
          Math.floor(
            parseFloat(inputValue) * (this.betValueIsBits ? 1 : 1000000)
          )
      this.emitChangeValues()
    },
    changeAutoBet(inputValue) {
      // const tempBet = parseFloat(inputValue)
      // if (tempBet < 0) this.betTemp = '0'
      // else this.betTemp = tempBet.toFixed(0)
      this.betTemp = inputValue
      this.bet =
        '' +
        Math.floor(
          parseFloat(this.betTemp) * (this.betValueIsBits ? 1 : 1000000)
        )
      this.emitChangeValues()
    },
    changeCashOut(inputValue) {
      this.cashout = inputValue
      this.emitChangeValues()
    },
    changeStopBet(inputValue) {
      // this.stopBet = inputValue
      this.stopBetTemp = inputValue
      if (isNaN(parseFloat(this.stopBetTemp))) this.stopBet = ''
      else
        this.stopBet =
          '' +
          Math.floor(
            parseFloat(this.stopBetTemp) * (this.betValueIsBits ? 1 : 1000000)
          )
      this.emitChangeValues()
    },
    onWinSelected(index) {
      this.winActiveId = index
      this.emitChangeValues()
    },
    changeWinIncrease(inputValue) {
      // this.winIncrease = inputValue
      this.winIncreaseTemp = inputValue
      this.winIncrease =
        '' +
        Math.floor(
          parseFloat(this.winIncreaseTemp) * (this.betValueIsBits ? 1 : 1000000)
        )
      this.emitChangeValues()
    },
    onLossSelected(index) {
      this.lossActiveId = index
      this.emitChangeValues()
    },
    changeLossIncrease(inputValue) {
      // this.lossIncrease = inputValue
      this.lossIncreaseTemp = inputValue
      this.lossIncrease =
        '' +
        Math.floor(
          parseFloat(this.lossIncreaseTemp) *
            (this.betValueIsBits ? 1 : 1000000)
        )
      this.emitChangeValues()
    },
    emitChangeValues() {
      this.$emit('changeAuto', {
        basebet: this.basebet,
        bet: this.bet,
        cash: this.cashout,
        stop: this.stopBet,
        isWin: this.winActiveId,
        win: this.winIncrease,
        isLoss: this.lossActiveId,
        loss: this.lossIncrease
      })
    },
    bitsToCurrentMode(bitsStr) {
      return this.betValueIsBits ? bitsStr : '' + parseInt(bitsStr) / 1000000
    },
    clickAutoBetting() {
      if (this.title === 'Bet')
        this.changeAutoBet(this.bitsToCurrentMode(this.basebet))
      else this.emitChangeValues()
      this.$emit('clickAutoBetting')
    },
    changeOnWinLoss(isWin) {
      if (isWin) {
        if (this.winActiveId === 0) {
          this.changeAutoBet(this.bitsToCurrentMode(this.basebet))
        } else if (this.winActiveId === 1) {
          if (this.winIncrease !== '') {
            this.changeAutoBet(
              this.bitsToCurrentMode(
                (parseInt(this.bet) + parseInt(this.winIncrease)).toFixed(0)
              )
            )
          }
        } else if (this.winActiveId === 2) {
          if (this.winIncrease !== '') {
            this.changeAutoBet(
              this.bitsToCurrentMode(
                (parseInt(this.bet) - parseInt(this.winIncrease)).toFixed(0)
              )
            )
          }
        } else if (this.winIncrease !== '') {
          this.changeAutoBet(
            this.bitsToCurrentMode(
              (parseInt(this.bet) * parseInt(this.winIncrease)).toFixed(0)
            )
          )
        }
      } else if (this.lossActiveId === 0) {
        this.changeAutoBet(this.bitsToCurrentMode(this.basebet))
      } else if (this.lossActiveId === 1) {
        if (this.lossIncrease !== '') {
          this.changeAutoBet(
            this.bitsToCurrentMode(
              (parseInt(this.bet) + parseInt(this.lossIncrease)).toFixed(0)
            )
          )
        }
      } else if (this.lossActiveId === 2) {
        if (this.lossIncrease !== '') {
          this.changeAutoBet(
            this.bitsToCurrentMode(
              (parseInt(this.bet) - parseInt(this.lossIncrease)).toFixed(0)
            )
          )
        }
      } else if (this.lossIncrease !== '') {
        this.changeAutoBet(
          this.bitsToCurrentMode(
            (parseInt(this.bet) * parseInt(this.lossIncrease)).toFixed(0)
          )
        )
      }

      if (
        parseInt(this.stopBet) > 0 &&
        parseInt(this.stopBet) < parseInt(this.bet)
      ) {
        this.clickAutoBetting()
      }
    },
    onSubmit(evt) {
      evt.preventDefault()
      this.clickAutoBetting()
    }
  }
}
</script>
<style lang="scss" scoped>
@import '@/assets/styles/variables.scss';

.icon-box {
  width: 13px;
  height: 15px;
  min-width: 13px;
  min-height: 15px;
}

.margin-box {
  min-width: 70px;
}

.check-box {
  min-width: 13px;
  min-height: 13px;
}

.text-no-wrap {
  white-space: nowrap;
}

.m-w-160 {
  min-width: 160px;
}

.m-w-240 {
  min-width: 240px;
}

.m-h-125 {
  min-height: 125px;
}

@media (max-width: 767px) {
  .responsive-box {
    display: none;
  }
}
</style>
