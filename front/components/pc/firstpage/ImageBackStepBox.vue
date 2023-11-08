<template>
  <div
    class="step-box flex-col-hc-vc m-l-25 m-r-25 m-b-20"
    :style="'background-image: url(' + backImg + ')'"
  >
    <img :src="titleImg" />
    <span class="fc-darkred fs22 bold">{{ titleString }}</span>
    <span class="fc-black fs16">{{ contentString }}</span>
  </div>
</template>
<script>
export default {
  name: 'ImageBackStepBox',
  props: {
    backImg: {
      type: String,
      default: ''
    },
    titleImg: {
      type: String,
      default: ''
    },
    titleString: {
      type: String,
      default: ''
    },
    contentString: {
      type: String,
      default: ''
    }
  }
}
</script>
<style lang="scss" scoped>
.step-box {
  width: 306px;
  height: 293px;
  background-size: 100% 100%;
  padding: 15px;
  line-height: 1.5;
}
</style>
