<template>
  <div class="profile-view h-full">
    <div class="scrollbar-wrapper">
      <data-item
        v-for="(item, i) in aryData"
        :key="i"
        :title="item.content"
        :value="item.value"
      />
    </div>
  </div>
</template>
<script>
import DataItem from '@/components/pc/dialog/DataItem.vue'

export default {
  name: 'UserProfileList',
  components: {
    DataItem
  },
  props: {
    aryData: {
      type: Array,
      default() {
        return []
      }
    }
  },
  data() {
    return {}
  },
  watch: {
    aryData: {
      deep: true,
      handler(newVal) {}
    }
  },
  mounted() {}
}
</script>
<style lang="scss" scoped>
@import '@/assets/styles/variables.scss';

.profile-view {
  overflow: auto;
  background: $gray35;
  border: 1px solid $gray82;
  padding: 10px;
}
</style>
