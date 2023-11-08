<template>
  <div class="footerbar h-full flex-col-hc-vc">
    <img src="/imgs/brand.png" />
    <span class="fc-white m-t-20 text-center">
      Copyright®Quick bit, Inc. All rights reserved.
    </span>
  </div>
</template>
<script>
export default {
  name: 'Footerbar',
  props: {
    title: {
      type: String,
      default: ''
    }
  }
}
</script>
<style lang="scss" scoped></style>
