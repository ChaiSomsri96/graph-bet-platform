<template>
  <div :id="type" :class="classObj" @click="onClick">
    <span class="line"></span>
    <span class="line"></span>
    <span class="line"></span>
  </div>
</template>
<script>
export default {
  name: 'Hamburger',
  props: {
    type: {
      type: String,
      default: 'hamburger-1'
    }
  },
  data() {
    return {
      isActive: false
    }
  },
  computed: {
    classObj() {
      return {
        'hamburger menu-btn': true,
        'is-active': this.isActive
      }
    }
  },
  mounted() {
    const self = this

    this.$bus.$on('menu-collapsed', function() {
      self.isActive = false
    })
  },
  methods: {
    onClick() {
      this.isActive = !this.isActive
      this.$emit('on-click')
    }
  }
}
</script>
<style lang="scss" scoped>
@import '@/assets/styles/variables.scss';
.menu-btn {
  color: $grayb3;
  padding: 15px 0px 15px 15px !important;
}
</style>
