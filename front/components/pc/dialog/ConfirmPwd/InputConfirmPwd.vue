<template>
  <b-form-input
    v-model="pwd"
    placeholder="Confirm password"
    type="password"
  ></b-form-input>
</template>
<script>
export default {
  name: 'InputConfirmPwd',
  components: {},
  props: {},
  data() {
    return {
      pwd: this.confirmPwd
    }
  },
  computed: {
    confirmPwd() {
      const val = this.$store.state.app.confirmPwd
      return val !== undefined ? val : ''
    }
  },
  watch: {
    pwd: {
      handler(val, oldVal) {
        this.$store.dispatch('app/setConfirmPwd', val)
      }
    }
  }
}
</script>
<style lang="scss" scoped></style>
