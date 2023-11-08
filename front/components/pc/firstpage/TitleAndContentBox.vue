<template>
  <div class="content-back flex-col-hc-vc fc-white">
    <span class="fs40 m-b-10 title-color text-center">{{ title }}</span>
    <span class="fs14 bold">{{ subTitle }}</span>
    <slot name="content-box"></slot>
  </div>
</template>
<script>
export default {
  name: 'TitleAndContentBox',
  props: {
    title: {
      type: String,
      default: ''
    },
    subTitle: {
      type: String,
      default: ''
    }
  }
}
</script>
<style lang="scss" scoped>
@import '@/assets/styles/variables.scss';
.content-back {
  padding: 45px 0px;
  width: 100%;
  background: $gray21;

  img {
    overflow: hidden;
  }
}

.content-back:nth-child(even) {
  background: $gray1e;
}

.title-color {
  color: $lightbrownyellow;
}
</style>
