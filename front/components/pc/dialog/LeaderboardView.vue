<template>
  <div class="leaderboard-view flex-col-hc-vt fc-white fs14 scrollbar-wrapper">
    <div class="title-box flex-row-hl-vt fs17 h-full m-t-15">
      <span class="flex1">#</span>
      <span class="flex4">Player</span>
      <span class="flex4">Wagered</span>
      <span class="flex4">Profit</span>
      <span class="flex3">Bets</span>
    </div>
    <div class="flex-col-hl-vt h-full m-b-15">
      <div
        v-for="(item, i) in contents"
        :key="i"
        class="flex-row-hl-vt item-box h-full nowrap"
      >
        <span class="flex1">{{ i + 1 }}</span>
        <span class="flex4">{{ item.player }}</span>
        <span class="flex4">B {{ item.wagered | toThousandFilter }}</span>
        <span class="flex4">B {{ item.profit | toThousandFilter }}</span>
        <span class="flex3">{{ item.bets | toThousandFilter }}</span>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: 'LeaderboardView',
  components: {},
  props: {
    contents: {
      type: Array,
      default() {
        return [
          {
            player: 'Andrew',
            wagered: 582500,
            profit: 653000,
            profitATH: 8900,
            profitATL: -2200,
            bets: 121
          },
          {
            player: 'Kimsoon',
            wagered: 695000,
            profit: 578010,
            profitATH: 48000,
            profitATL: -9300,
            bets: 320
          },
          {
            player: 'Hence',
            wagered: 940400,
            profit: 330900,
            profitATH: 2200,
            profitATL: -10000,
            bets: 220
          },
          {
            player: 'Micky',
            wagered: 820000,
            profit: 320500,
            profitATH: 49000,
            profitATL: -5500,
            bets: 132
          },
          {
            player: 'Rudolf',
            wagered: 765000,
            profit: 298000,
            profitATH: 32500,
            profitATL: -12000,
            bets: 493
          },
          {
            player: 'Crimson',
            wagered: 332980,
            profit: 227220,
            profitATH: 4950,
            profitATL: -1240,
            bets: 193
          },
          {
            player: 'Martin Jackson',
            wagered: 430600,
            profit: 210500,
            profitATH: 12000,
            profitATL: -9000,
            bets: 275
          },
          {
            player: 'Morosov',
            wagered: 928000,
            profit: 201200,
            profitATH: 9000,
            profitATL: -9500,
            bets: 320
          },
          {
            player: 'Whisley',
            wagered: 432050,
            profit: 197240,
            profitATH: 43200,
            profitATL: -5600,
            bets: 38
          }
        ]
      }
    }
  }
}
</script>
<style lang="scss" scoped>
@import '@/assets/styles/variables.scss';

.title-box {
  border-bottom: 2px solid $gray4a;
  padding-top: 5px;
  padding-bottom: 10px;
  min-width: 800px;
  min-height: 29px;
}

.item-box {
  border-bottom: 1px solid $gray4a;
  padding-top: 5px;
  padding-bottom: 5px;
  min-width: 800px;
  min-height: 29px;
}
</style>
