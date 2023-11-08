<template>
  <div class="root-container">
    <navbar />
    <div class="content scrollbar-wrapper">
      <nuxt />
      <footerbar />
    </div>
  </div>
</template>
<script>
import Navbar from '@/components/pc/layout/Navbar.vue'
import Footerbar from '@/components/pc/layout/Footerbar.vue'

export default {
  name: 'Layout',
  components: {
    Navbar,
    Footerbar
  },
  data() {
    return {}
  },
  computed: {},
  methods: {}
}
</script>
<style lang="scss"></style>
