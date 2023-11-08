<template>
  <div class="history-list-container flex-col-hc-vc v-full">
    <div class="flex-row-hc-vc title-bar fc-grayb3 fs16 h-full fw500">
      <div class="flex3 text-center">BUST</div>
      <div class="flex3 text-center">@</div>
      <div class="flex3 text-center">BET</div>
      <div class="flex3 text-center">PROFIT</div>
    </div>
    <div class="flex-col-ht-vc v-full h-full beauty-scrollbar-wrapper">
      <history-list-item
        v-for="(item, i) in historyInfos"
        :key="i"
        :index="i"
        :item-data="item"
      />
    </div>
  </div>
</template>
<script>
import HistoryListItem from '@/components/pc/main/HistoryListItem.vue'

export default {
  name: 'HistoryList',
  components: {
    HistoryListItem
  },
  props: {
    historyInfos: {
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
.history-list-container {
  border-bottom: 1px solid $gray4f;
  border-right: 1px solid $gray4f;
}

.title-bar {
  min-height: 32px;
}
</style>
