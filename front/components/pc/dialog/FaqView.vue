<template>
  <div>
    <div v-if="!isDetail" class="flex-col-hc-vc fs16 fc-white m-t-20">
      <div v-for="(faqCat, i) in faq" :key="i" class="h-full">
        <div class="bold m-t-15 m-b-15">{{ faqCat.title }}</div>
        <div
          v-for="(faqQ, j) in faqCat.questions"
          :key="j"
          class="fs14 m-l-10 m-b-5 faq-question pointer"
          @click="showFaqDetail(faqCat, faqQ)"
        >
          {{ faqQ.title }}
        </div>
      </div>
    </div>
    <div v-else class="flex-col-hc-vc fs16 fc-white m-t-20">
      <div class="h-full">
        <div class="bold m-t-15 fs20">{{ faqDetail.title }}</div>
        <div :class="classObj">{{ faqDetail.answer }}</div>
        <div class="flex-space-between-vc m-b-35">
          <button3-d title="Go Back" @on-click="gotoList" />
          <button3-d title="Faq Menu" @on-click="gotoList" />
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import Button3D from '@/components/pc/common/Button3D.vue'
export default {
  name: 'FaqView',
  components: {
    Button3D
  },
  props: {},
  data() {
    return {
      faq: [],
      isDetail: false,
      faqDetail: {}
    }
  },
  computed: {
    classObj() {
      return {
        'fs14 m-t-15 m-b-35 textarea-desc': true
      }
    }
  },
  mounted() {
    this.faq = []
    this.isDetail = false
    this.faqDetail = {}
    this.loadFaqs()
  },
  methods: {
    gotoList() {
      this.isDetail = false
    },
    showFaqDetail(faqCat, faqQ) {
      this.faqDetail = {
        title: faqQ.title,
        answer: faqQ.answer
      }
      this.isDetail = true
    },
    async loadFaqs() {
      this.$bus.$emit('show-overlay')
      try {
        const response = await this.$axios.post('/Faq/list')
        if (response.status === 'success') {
          this.faq = response.data
        } else {
          this.$bvToast.toast(response.data, {
            title: 'Quick bit',
            variant: 'danger',
            autoHideDelay: 5000,
            appendToast: true
          })
        }
        this.$bus.$emit('hide-overlay')
      } catch (err) {
        this.faq = []
      }
    }
  }
}
</script>
<style lang="scss" scoped>
@import '@/assets/styles/variables.scss';
.faq-question {
  cursor: pointer;
  &:hover {
    color: $midorangeyellow;
  }
}
.highlight {
  color: $midorangeyellow;
}
.divider {
  background: $gray4a;
  width: 100%;
  height: 1px;
}
</style>
