<template>
  <div class="flex-row-hl-vt h-full">
    <span class="flex5 p-t-5 p-b-5 h-29">{{ content }}</span>
    <span class="flex2 fc-grayb3 p-t-5 p-b-5 h-29">
      {{ percent >= 0 ? percent + '%' : '' }}
    </span>
    <span class="flex5 p-t-5 p-b-5 h-29">
      {{ isBits ? 'B ' : '' }}
      {{ value | toThousandFilter }}
    </span>
  </div>
</template>
<script>
export default {
  name: 'StatisticsView',
  components: {},
  props: {
    content: {
      type: String,
      default: ''
    },
    percent: {
      type: Number,
      default: 0
    },
    value: {
      type: Number,
      default: 0
    },
    isBits: {
      type: Boolean,
      default: true
    }
  },
  data() {
    return {
      numValue: this.value
    }
  },
  watch: {
    value(newValue) {
      this.numValue = newValue
    }
  }
}
</script>
<style lang="scss" scoped></style>
