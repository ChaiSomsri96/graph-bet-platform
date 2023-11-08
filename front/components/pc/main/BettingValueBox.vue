<template>
  <b-form
    class="flex-col-hl-vc h-full fc-white fs16 p-l-15 p-r-15"
    @submit="onSubmit"
  >
    <b-overlay :show="overlayVisible" variant="dark" class="h-full">
      <div class="bettng-btn-box h-full">
        <div class="flex-col-hc-vc h-full">
          <div class="flex-row-hl-vc h-full">
            <svg-icon icon-class="arrow_orange_right" class="icon-box" />
            <div class="fw500 m-l-5">BET</div>
          </div>
          <div class="flex-row-hl-vc h-full">
            <input-with-label-box
              :has-title="false"
              :is-round="false"
              :is-disable="isDisable"
              :var-link="betValueTemp"
              @inputChanged="changeBet"
            />
            <bits-btc-button
              class="m-l-5"
              button-size="small"
              :mode="bettingValueIsBits"
              @modeChanged="bitsModeChanged"
            />
          </div>
          <div class="flex-row-hl-vc h-full m-t-10">
            <svg-icon icon-class="arrow_green_right" class="icon-box" />
            <div class="fw500 m-l-5">PAYOUT</div>
          </div>
          <div class="flex-row-hl-vc h-full">
            <input-with-label-box
              :has-title="false"
              :is-round="false"
              :is-required="false"
              :is-disable="isDisable"
              :var-link="payoutValue"
              @inputChanged="changePayout"
            />
            <div v-if="false" class="margin-box m-l-5"></div>
          </div>
        </div>
        <button3-d
          class="text-center betting-btn"
          :title="title"
          type="submit"
          :status="btnStatus"
          text-size="large"
          button-size="large"
          :is-response="true"
        />
      </div>
      <div class="flex-space-between-vc fs12 h-full m-t-5">
        <div class="flex-col-hl-vt">
          <div>Target Profit</div>
          <div>Win Chance</div>
        </div>
        <div class="flex-col-hl-vt">
          <div>{{ targetProfit | toThousandFilter }}bits</div>
          <div>{{ winChange }}%</div>
        </div>
      </div>
    </b-overlay>
  </b-form>
</template>
<script>
import Button3D from '@/components/pc/common/Button3D.vue'
import BitsBtcButton from '@/components/pc/dialog/BitsBtcButton.vue'
import InputWithLabelBox from '@/components/pc/dialog/InputWithLabelBox.vue'

export default {
  name: 'BettingValueBox',
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
    btnStatus: {
      type: String,
      default: 'bet'
    },
    overlayVisible: {
      type: Boolean,
      default: false
    },
    isDisable: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      betValue: '1000',
      betValueTemp: '1000',
      payoutValue: '2',
      targetProfit: 1000,
      winChange: 49.5,
      bettingValueIsBits: true
    }
  },
  computed: {
    storeBetTemp() {
      return this.$store.getters['betting/getBetTemp']
    },
    storePayout() {
      return this.$store.getters['betting/getPayout']
    }
  },
  watch: {
    storeBetTemp: {
      handler(val, oldVal) {
        this.betValue = val !== undefined ? val : '1000'
        // this.betValueTemp = this.bettingValueIsBits
        //   ? this.betValue
        //   : parseInt(this.betValue) * 1000000
        if (
          this.payoutValue === '' ||
          this.betValue === '' ||
          parseFloat(this.payoutValue) <= 1
        ) {
          this.targetProfit = '0'
          this.winChange = '0'
        } else {
          this.targetProfit = parseInt(
            (parseFloat(this.payoutValue) - 1) * parseFloat(this.betValue)
          )
          this.winChange = parseFloat(
            ((parseFloat(this.betValue) * 0.99) /
              (parseFloat(this.betValue) * parseFloat(this.payoutValue))) *
              100
          ).toFixed(1)
        }
      },
      immediate: true
    },
    storePayout: {
      handler(val, oldVal) {
        this.payoutValue = val !== undefined ? val : '2'
        if (
          this.payoutValue === '' ||
          this.betValue === '' ||
          parseFloat(this.payoutValue) <= 1
        ) {
          this.targetProfit = '0'
          this.winChange = '0'
        } else {
          this.targetProfit = parseInt(
            (parseFloat(this.payoutValue) - 1) * parseFloat(this.betValue)
          )
          this.winChange = parseFloat(
            ((parseFloat(this.betValue) * 0.99) /
              (parseFloat(this.betValue) * parseFloat(this.payoutValue))) *
              100
          ).toFixed(1)
        }
      },
      immediate: true
    }
  },
  mounted() {
    const temp = this.storeBetTemp !== undefined ? this.storeBetTemp : '1000'
    this.betValueTemp = this.bettingValueIsBits
      ? temp
      : parseInt(temp) * 1000000
  },
  methods: {
    bitsModeChanged(isBits) {
      this.bettingValueIsBits = isBits
      this.betValueTemp =
        '' + parseInt(this.betValue) / (this.bettingValueIsBits ? 1 : 1000000)
    },
    changeBet(inputValue) {
      this.$emit(
        'changeBet',
        '' +
          Math.floor(
            parseFloat(inputValue) * (this.bettingValueIsBits ? 1 : 1000000)
          )
      )
    },
    changePayout(inputValue) {
      this.$emit('changePayout', inputValue)
    },
    onSubmit(evt) {
      evt.preventDefault()
      this.$emit('clickBet')
    }
  }
}
</script>
<style lang="scss" scoped>
@import '@/assets/styles/variables.scss';

.icon-box {
  width: 13px;
  height: 15px;
}

.margin-box {
  min-width: 70px;
}

.bettng-btn-box {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.betting-btn {
  width: 100%;
  margin-top: 15px;
  margin-left: 0px;
}

@media (max-width: 767px) {
  .bettng-btn-box {
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
  }

  .betting-btn {
    width: initial;
    margin-top: 0px;
    margin-left: 10px;
  }
}
</style>
