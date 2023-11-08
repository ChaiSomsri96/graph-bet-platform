<template>
  <div class="button-container flex-row-hc-vc fs13" :class="buttonSize">
    <div
      class="button-item flex-row-hc-vc pointer"
      :class="isBits ? 'selected' : ''"
      @click="onBitsSelected()"
    >
      <span>bits</span>
    </div>
    <div
      class="button-item flex-row-hc-vc pointer"
      :class="isBits ? '' : 'selected'"
      @click="onBTCSelected()"
    >
      <span>BTC</span>
    </div>
  </div>
</template>
<script>
export default {
  name: 'BitsBtcButton',
  props: {
    buttonSize: {
      type: String,
      default: 'normal'
    },
    mode: {
      type: Boolean,
      default: true
    }
  },
  data() {
    return {
      isBits: this.mode
    }
  },
  watch: {
    mode: {
      deep: true,
      handler(newVal, oldVal) {
        this.isBits = newVal
      }
    }
  },
  methods: {
    onBitsSelected() {
      this.isBits = true
      this.$emit('modeChanged', this.isBits)
    },
    onBTCSelected() {
      this.isBits = false
      this.$emit('modeChanged', this.isBits)
    }
  }
}
</script>
<style lang="scss" scoped>
@import '@/assets/styles/variables.scss';

.button-container {
  height: 38px;
}

.button-item {
  height: 100%;
  background: $gray35;
  color: $gray82;
}

.button-item.selected {
  background: $darkorange;
  color: black;
}

.button-container.normal .button-item {
  width: 50px;
}

.button-container.small .button-item {
  width: 35px;
}
</style>
