<template>
  <div class="flex-row-hc-vc h-full fs14 user-list-item" :class="classObj">
    <div
      :class="{
        'flex3 text-left': true,
        fw500: mode == 1
      }"
    >
      {{ userInfo.username }}
    </div>
    <div class="flex3 text-center" :class="mode == 1 ? 'fw500' : ''">
      {{ userInfo.cashout ? (userInfo.cashout / 100).toFixed(2) + 'x' : '-' }}
    </div>
    <div class="flex3 text-center" :class="mode == 1 ? 'fw500' : ''">
      {{ userInfo.value | toThousandFilter }}
    </div>
    <div class="flex3 text-center" :class="mode == 1 ? 'fw500' : ''">
      {{ profitValue === '-' ? '-' : profitValue | toThousandFilter }}
    </div>
    <div
      v-if="mode == 1"
      class="flex3 text-center pointer fw500"
      @click="onClick"
    >
      BET #{{ userInfo.betid }}
    </div>
  </div>
</template>
<script>
export default {
  name: 'UserListItem',
  props: {
    index: {
      type: Number,
      default: 0
    },
    userInfo: {
      type: Object,
      default() {
        return {
          id: 0,
          username: '',
          cashout: 0,
          value: 0,
          profit: '-',
          betid: 0
        }
      }
    },
    profitVal: {
      type: String,
      default: '-'
    },
    isCrashed: {
      type: Boolean,
      default: false
    },
    mode: {
      type: Number,
      default: 0
    }
  },
  data() {
    return {
      profitValue: this.profitVal === '-' ? '-' : this.profitVal
    }
  },
  computed: {
    classObj: {
      get() {
        return {
          'main-gray-background': this.index % 2 === 0,
          'main-black-background': this.index % 2 === 1,
          'font-bet-color': this.userInfo.cashout === 0 && !this.isCrashed,
          'font-crash-color': this.userInfo.cashout === 0 && this.isCrashed,
          'font-cashout-color': this.userInfo.cashout !== 0,
          'text-emphasis':
            this.userInfo.userid === this.userId &&
            this.userInfo.username === this.userName &&
            this.userInfo.cashout === 0,
          'text-emphasis-crash':
            this.userInfo.userid === this.userId &&
            this.userInfo.username === this.userName &&
            this.userInfo.cashout !== 0
        }
      }
    },
    userId() {
      const id = this.$store.getters['user/getId']
      return id !== undefined && id !== '' ? parseInt(id) : -1
    },
    userName() {
      const name = this.$store.getters['user/getName']
      return name !== undefined && name !== '' ? name : ''
    }
  },
  watch: {
    profitVal(newVal) {
      this.profitValue = newVal === '-' ? '-' : newVal
    }
  },
  methods: {
    async onClick() {
      await this.$store.dispatch('app/setGameHistoryLogId', this.userInfo.betid)
      this.$bvModal.hide('gameinfo')
      this.$root.$emit('bv::show::modal', 'betinfo')
    }
  }
}
</script>
<style lang="scss" scoped>
@import '@/assets/styles/variables.scss';

.main-gray-background {
  background-color: $gray4f;
}

.main-black-background {
  background-color: $gray1a;
}

.font-bet-color {
  color: $betfontcolor;
}

.font-cashout-color {
  color: $crashbigcolor;
}

.font-crash-color {
  color: $crashsmallcolor;
}

.user-list-item {
  padding-left: 10px;
  padding-right: 5px;
  min-height: 25px;
}

.text-emphasis {
  font-weight: bold;
  font-size: 16px;
  background: #f5e0af;
}

.text-emphasis-crash {
  font-weight: bold;
  font-size: 16px;
  background: #c9d8ac;
}
</style>
