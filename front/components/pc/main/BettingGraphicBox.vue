<template>
  <div class="flex-col-hc-vc">
    <score-graph class="graph-box h-full" />
    <betting-history-box :cashes="cashHistory" />
  </div>
</template>
<script>
import BettingHistoryBox from '@/components/pc/main/BettingHistoryBox.vue'
import ScoreGraph from '@/components/pc/main/ScoreGraph.vue'

export default {
  name: 'BettingGraphicBox',
  components: {
    BettingHistoryBox,
    ScoreGraph
  },
  props: {
    cashHistory: {
      type: Array,
      default() {
        return []
      }
    }
  }
}
</script>
<style lang="scss" scoped>
@import '@/assets/styles/variables.scss';

.graph-box {
  // background-color: $gray35;
}
</style>
