<template>
  <div class="item-container flex-row-hc-vt">
    <div class="flex1">{{ title }}</div>
    <div class="flex1">
      <span v-if="isVisible">{{ value | toThousandFilter }}</span>
      <span v-if="isVisible && isBitsSuffix">Bits</span>
    </div>
  </div>
</template>
<script>
export default {
  name: 'StatsItem',
  props: {
    title: {
      type: String,
      default: ''
    },
    value: {
      type: Number,
      default: 0
    },
    isBitsSuffix: {
      type: Boolean,
      default: true
    },
    isVisible: {
      type: Boolean,
      default: true
    }
  }
}
</script>
<style lang="scss" scoped>
.item-container {
  flex: 1;
  width: 100%;
  min-width: 300px;
  display: flex;
  padding: 0px 5px;
  font-size: 15px;
}

@media (max-width: 420px) {
  .item-container {
    font-size: 10px;
    min-width: 220px;
  }
}
</style>
