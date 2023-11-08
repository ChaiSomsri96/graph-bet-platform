<template>
  <div class="item-box fc-white flex-row-hl-vc">
    <div class="title-box">{{ title }} :</div>
    <div class="">{{ value | toThousandFilter }} bits</div>
  </div>
</template>
<script>
export default {
  name: 'DataItem',
  props: {
    title: {
      type: String,
      default: ''
    },
    value: {
      type: Number,
      default: 0
    }
  },
  data() {
    return {}
  }
}
</script>
<style lang="scss" scoped>
@import '@/assets/styles/variables.scss';

.item-box {
  display: -webkit-inline-box;
  width: 100%;
  line-height: 2;
  border-bottom: 1px solid $gray52;
  font-size: 15px;
}

.item-box:last-child {
  border-bottom: none;
}

.title-box {
  min-width: 245px;
}

@media (max-width: 480px) {
  .title-box {
    min-width: 190px;
  }
}
@media (max-width: 320px) {
  .item-box {
    font-size: 12px;
  }
  .title-box {
    min-width: 160px;
  }
}
</style>
