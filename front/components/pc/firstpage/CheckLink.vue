<template>
  <div class="link-item flex-row-hc-vc fs16">
    <nuxt-link to="#">
      <svg-icon icon-class="icon_check" class="ic-size-20" />
      {{ title }}
    </nuxt-link>
  </div>
</template>
<script>
export default {
  name: 'CheckLink',
  props: {
    title: {
      type: String,
      default: ''
    }
  }
}
</script>
<style lang="scss" scoped>
.link-item a {
  color: white;
  text-decoration: none;
}

.ic-size-20 {
  width: 20px;
  height: 20px;
}
</style>
