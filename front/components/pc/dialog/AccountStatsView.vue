<template>
  <div class="flex-col-hc-vt fc-white fs16">
    <user-info-box class="m-t-35 m-b-20" />
    <span class="bold">PERFORMANCE</span>
    <div class="flex-row-hc-vt flex-wrap h-full m-t-10 m-b-5">
      <stats-item
        title="GAMES PLAYED:"
        :value="totalBetInfo.totalCount"
        :is-bits-suffix="false"
      />
      <stats-item title="TOTAL WAGERED:" :value="totalBetInfo.totalBet" />
    </div>
    <separator-line class="m-t-5 m-b-5" />
    <div class="flex-row-hc-vt flex-wrap h-full m-t-5">
      <stats-item title="NET PROFIT:" :value="totalProfit" />
      <stats-item title="PROFIT ALL TIME HIGH:" :value="totalBetInfo.max" />
    </div>
    <div class="flex-row-hc-vt flex-wrap h-full m-b-5">
      <stats-item title="PROFIT ALL TIME LOW:" :value="totalBetInfo.min" />
      <stats-item :is-visible="false" />
    </div>
    <div class="h-full scrollbar-wrapper">
      <img src="imgs/dialog/graph.png" class="m-t-15 m-b-10" />
    </div>
    <div class="flex-row-hc-vc h-full m-b-40 flex-wrap">
      <span class="navigation-btn pointer">First</span>
      <span class="navigation-btn pointer">{{ leftS }}Previous</span>
      <span class="navigation-btn pointer">Next{{ rightS }}</span>
      <span class="navigation-btn pointer">Last</span>
    </div>
  </div>
</template>
<script>
import UserInfoBox from '@/components/pc/dialog/UserInfoBox.vue'
import StatsItem from '@/components/pc/dialog/StatsItem.vue'
import SeparatorLine from '@/components/pc/dialog/SeparatorLine.vue'

export default {
  name: 'AccountStatsView',
  components: {
    UserInfoBox,
    StatsItem,
    SeparatorLine
  },
  props: {},
  data() {
    return {
      leftS: '<',
      rightS: '>'
    }
  },
  computed: {
    totalBetInfo() {
      const value = this.$store.state.user.totalBetInfo
      if (value !== undefined && value !== '') {
        return {
          totalCount: value.totalCount,
          totalBet: value.totalBet,
          max: value.maxProfit,
          min: value.minProfit
        }
      } else {
        return {
          totalCount: 0,
          totalBet: 0,
          max: 0,
          min: 0
        }
      }
    },
    totalProfit() {
      const val = this.$store.state.user.totalProfit
      return val !== undefined && val !== '' ? val : 0
    }
  }
  // watch: {
  //   totalBetInfo: {
  //     handler(val, oldVal) {
  //       const value = val !== undefined && val !== '' ? val : 0
  //       this.bettingInfo.totalCount = value.totalCount
  //       this.bettingInfo.totalBet = value.totalBet
  //       this.bettingInfo.max = value.maxProfit
  //       this.bettingInfo.min = value.minProfit
  //     }
  //   },
  //   totalProfit: {
  //     handler(val, oldVal) {
  //       const value = val !== undefined && val !== '' ? val : 0
  //       this.profit = value
  //     }
  //   }
  // }
}
</script>
<style lang="scss" scoped>
@import '@/assets/styles/variables.scss';

.navigation-btn {
  color: $whiteyellow;
  margin: 0px 20px;
}
</style>
