<template>
  <div class="sep-line h-full"></div>
</template>
<script>
export default {
  name: 'SeparatorLine',
  props: {}
}
</script>
<style lang="scss" scoped>
@import '@/assets/styles/variables.scss';

.sep-line {
  min-height: 1px;
  background: $gray30;
}
</style>
