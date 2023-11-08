<template>
  <div class="scrollbar-wrapper">
    <div class="stat-view flex-col-hc-vc h-full">
      <div class="fs15 fw500 h-full m-t-15">GAME STATISTICS</div>
      <div class="flex-col-hl-vt h-full m-t-15 m-b-30">
        <statistic-item
          v-for="(item, i) in contents"
          :key="i"
          :is-bits="i !== 0 && i !== 1"
          :content="contents[i]"
          :percent="percents[i]"
          :value="values[i]"
        />
      </div>
    </div>
  </div>
</template>
<script>
import StatisticItem from '@/components/pc/dialog/StatisticItem.vue'

export default {
  name: 'StatisticsView',
  components: {
    StatisticItem
  },
  props: {},
  data() {
    return {
      userCount: 0
    }
  },
  computed: {
    contents() {
      return ['Users', 'Bets', 'Wagered', 'Return to Player', 'Commission']
    },
    percents() {
      return [-1, -1, -1, -1, 0]
    },
    values() {
      return [this.userCount, 3254, 1754682, 581458, 0]
    },
    totalUserCount() {
      return this.userCount
    }
  },
  mounted() {
    const self = this

    this.$axios
      .post('/User/getUserCount', {})
      .then((response) => {
        if (response.status === 'success') {
          self.userCount = response.data
        }
      })
      .catch((error) => {
        error = 0
      })
  },
  methods: {}
}
</script>
<style lang="scss" scoped>
@import '@/assets/styles/variables.scss';

.h-29 {
  height: 29px;
}

.stat-view {
  color: white;
  font-size: 13px;
  min-width: 450px;
}
</style>
