<template>
  <div class="flex-col-hc-vt fc-white fs16">
    <user-info-box class="m-t-35 m-b-20" />
    <span class="bold">BALANCE</span>
    <div class="flex-row-hl-vc h-full m-t-10 m-b-10">
      <div class="bits-box">
        <svg-icon icon-class="icon_bits" class="ic-size-16" />
        <span class="m-l-5">BITS:</span>
      </div>
      <span>{{ wallet }}</span>
    </div>
    <separator-line class="m-t-15 m-b-15" />
    <span class="bold">BREAKDOWN</span>
    <data-list :ary-data="aryProfileData" class="m-t-25 m-b-40" />
  </div>
</template>
<script>
import UserInfoBox from '@/components/pc/dialog/UserInfoBox.vue'
import SeparatorLine from '@/components/pc/dialog/SeparatorLine.vue'
import DataList from '@/components/pc/dialog/DataList.vue'

export default {
  name: 'AccountOverview',
  components: {
    UserInfoBox,
    SeparatorLine,
    DataList
  },
  props: {},
  data() {
    return {}
  },
  computed: {
    aryProfileData() {
      return [
        {
          content: 'Deposits',
          value: 0
        },
        {
          content: 'Withdrawals',
          value: 0
        },
        {
          content: 'Tips Received',
          value: 0
        },
        {
          content: 'Tips Sent',
          value: 0
        },
        {
          content: 'Game Profit',
          value: this.totalProfit
        },
        {
          content: '= BALANCE',
          value: this.wallet
        }
      ]
    },
    wallet() {
      const val = this.$store.state.user.walletInfo
      return val !== undefined && val !== '' ? val : 0
    },
    totalProfit() {
      const val = this.$store.state.user.totalProfit
      return val !== undefined && val !== '' ? val : 0
    }
  }
  // watch: {
  //   wallet: {
  //     handler(val, oldVal) {
  //       this.bitsCount = val !== undefined && val !== '' ? val : 0
  //       this.aryProfileData[7].value = this.bitsCount
  //     }
  //   },
  //   totalProfit: {
  //     handler(val, oldVal) {
  //       this.aryProfileData[6].value = val !== undefined && val !== '' ? val : 0
  //     }
  //   }
  // }
}
</script>
<style lang="scss" scoped>
.bits-box {
  margin-right: 150px;
}

@media (max-width: 480px) {
  .bits-box {
    margin-right: 120px;
  }
}

.ic-size-16 {
  width: 16px;
  height: 16px;
}
</style>
