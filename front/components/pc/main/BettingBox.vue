<template>
  <div class="flex-col-hc-vt top-line-box h-full p-b-25">
    <div class="flex-row-hl-vc h-full fc-grayb3 fs12 pd-10">
      <div class="m-l-30">Max bet: {{ maxBet | toThousandFilter }}</div>
      <div class="m-l-30">Max profit: {{ maxProfit | toThousandFilter }}</div>
    </div>
    <b-row class="flex-row-hc-vt h-full m-l-0 m-r-0">
      <b-col sm="12" md="6">
        <betting-graphic-box :cash-history="cashHistory" />
      </b-col>
      <b-col sm="12" md="6" class="tab-border flex-col-hc-vc" :class="classObj">
        <div v-if="isLogIn" class="flex-row-hc-vc tab-box">
          <div
            class="flex-row-hc-vc tab-item h-full pointer"
            :class="curTabID === 0 ? 'selected' : ''"
            @click="onManualBet()"
          >
            <span class="rotate-text fs16 bold">MANUAL</span>
          </div>
          <div
            class="flex-row-hc-vc tab-item h-full pointer"
            :class="curTabID === 1 ? 'selected' : ''"
            @click="onAutoBet()"
          >
            <span class="rotate-text fs16 bold">AUTO</span>
            <span class="rotate-text fs13">
              ({{ sessionProfit | toThousandFilter }} bits)
            </span>
          </div>
        </div>
        <betting-value-box
          v-if="isLogIn"
          :title="titleBetBtn"
          :btn-status="btnStatus"
          :overlay-visible="overlayVisible"
          :is-disable="isDisable"
          :class="curTabID === 0 ? '' : 'invisible'"
          @changeBet="changeBet"
          @changePayout="changePayout"
          @clickBet="clickBet"
        />
        <div class="scrollbar-wrapper tab-box">
          <auto-betting-value-box
            v-if="isLogIn"
            class="h-full"
            :class="curTabID === 1 ? '' : 'invisible'"
            :title="autoBetBtnTitle"
            @changeAuto="changeAuto"
            @clickAutoBetting="clickAutoBetting"
          />
        </div>
        <div v-if="!isLogIn" class="login-show-box fc-white text-center m-t-20">
          <span v-b-modal.login class="link-box pointer">Login</span>
          <span> </span>
          or
          <span v-b-modal.register class="link-box pointer">Register</span>
          <span> </span>
          to start playing.
        </div>
      </b-col>
    </b-row>
  </div>
</template>
<script>
import BettingGraphicBox from '@/components/pc/main/BettingGraphicBox.vue'
import BettingValueBox from '@/components/pc/main/BettingValueBox.vue'
import AutoBettingValueBox from '@/components/pc/main/AutoBettingValueBox.vue'

export default {
  name: 'BettingBox',
  components: {
    BettingGraphicBox,
    BettingValueBox,
    AutoBettingValueBox
  },
  props: {
    maxBet: {
      type: Number,
      default: 0
    },
    maxProfit: {
      type: Number,
      default: 0
    },
    titleBetBtn: {
      type: String,
      default: 'Bet'
    },
    btnStatus: {
      type: String,
      default: 'bet'
    },
    cashHistory: {
      type: Array,
      default() {
        return []
      }
    },
    overlayVisible: {
      type: Boolean,
      default: false
    },
    isDisable: {
      type: Boolean,
      default: false
    },
    autoBetBtnTitle: {
      type: String,
      default: 'Bet'
    }
  },
  data() {
    return {
      isLogIn: false,
      curTabID: 0,
      sessionProfit: 0
    }
  },
  computed: {
    token() {
      return this.$store.state.user.token
    },
    classObj: {
      get() {
        return {
          visibility: this.$store.state.user.token
        }
      }
    },
    storeSessionProfit() {
      return this.$store.getters['betting/getSessionProfit']
    }
  },
  watch: {
    token: {
      handler(val, oldVal) {
        this.isLogIn = val !== undefined && val !== ''
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
    window.addEventListener('resize', this.__resizeHandler)
    this.__resizeHandler()
  },
  methods: {
    changeBet(inputValue) {
      this.$emit('changeBet', inputValue)
    },
    changePayout(inputValue) {
      this.$emit('changePayout', inputValue)
    },
    clickBet() {
      this.$emit('clickBet')
    },
    onManualBet() {
      this.curTabID = 0
    },
    onAutoBet() {
      this.curTabID = 1
    },
    changeAuto(autoData) {
      this.$emit('changeAuto', autoData)
    },
    clickAutoBetting() {
      const windowBoxWidth = this.$store.getters['app/getWindowBoxWidth']

      if (windowBoxWidth < 768) this.$emit('clickAutoBetting')
    },
    __resizeHandler() {
      const width = this.$root.$el.clientWidth

      if (width >= 768 && this.curTabID === 1) this.curTabID = 0
    }
  }
}
</script>
<style lang="scss" scoped>
@import '@/assets/styles/variables.scss';

.login-show-box {
  min-height: 0px;
}

.link-box {
  color: $darkorange;
}

.link-box:hover {
  color: $darkred;
}

.tab-box {
  width: 100%;
}

.tab-border {
  padding-left: 0px;
  padding-right: 0px;
}

.tab-border.visibility {
  border: 2px solid $gray4f;
}

.tab-item {
  border-bottom: 2px solid $gray4f;
  color: $grayb3;
  background-color: $gray4f;
}

.tab-item.selected {
  color: $darkorange;
  background-color: $gray09;
}

.rotate-text {
  white-space: nowrap;
  text-align: center;
  overflow: hidden;
}

.invisible {
  display: none;
}

@media (min-width: 768px) {
  .tab-box {
    display: none;
  }

  .tab-border.visibility {
    border: 0px;
  }
}
</style>
