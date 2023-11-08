<template>
  <div class="flex-row-hc-vc h-full fs14 history-list-item" :class="classObj">
    <div class="flex3 text-center pointer" @click="onClick">
      {{ (itemData.BUST / 100).toFixed(2) }}x
    </div>
    <div class="flex3 text-center fc-white">
      {{ cashOutVal }}
    </div>
    <div class="flex3 text-center fc-white">
      {{ betVal | toThousandFilter }}
    </div>
    <div class="flex3 text-center fc-white">
      {{ profitVal | toThousandFilter }}
    </div>
  </div>
</template>
<script>
export default {
  name: 'HistoryListItem',
  props: {
    index: {
      type: Number,
      default: 0
    },
    itemData: {
      type: Object,
      default() {
        return {
          GAME_ID: 0,
          CASHOUT: 0,
          BUST: 0,
          BET: 0,
          PROFIT: 0
        }
      }
    }
  },
  data() {
    return {}
  },
  computed: {
    cashOutVal() {
      let ret = this.itemData.CASHOUT
      if (ret === null || ret === undefined) ret = '-'
      return ret
    },
    betVal() {
      let ret = this.itemData.BET
      if (ret === null || ret === undefined) ret = '-'
      return ret
    },
    profitVal() {
      let ret = this.itemData.PROFIT
      if (ret === null || ret === undefined) ret = '-'
      else if (ret === 0) ret = -this.itemData.BET
      else ret = this.itemData.PROFIT
      return ret
    },
    classObj: {
      get() {
        return {
          'main-gray-background': this.index % 2 === 0,
          'main-black-background': this.index % 2 === 1,
          'fc-crashsmallcolor': this.itemData.BUST < 200,
          'fc-crashbigcolor': this.itemData.BUST >= 200,
          pointer: true
        }
      }
    }
  },
  methods: {
    async onClick() {
      await this.$store.dispatch('app/setGameHistory', {
        BUST: this.itemData.BUST,
        GAME_ID: this.itemData.GAME_ID
      })
      this.$root.$emit('bv::show::modal', 'gameinfo')
    }
  }
}
</script>
<style lang="scss" scoped>
@import '@/assets/styles/variables.scss';

.main-gray-background {
  background-color: $gray35;
}

.main-black-background {
  background-color: $gray21;
}

.font-orange-color {
  color: $darkorange;
}

.font-green-color {
  color: $yellowgreen;
}

.history-list-item {
  min-height: 25px;
}
</style>
