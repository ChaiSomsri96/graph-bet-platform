<template>
  <div ref="banner" class="banner-container flex-col-hc-vc">
    <div class="flex-row-hc-vc flex-wrap">
      <div class="flex-col-hc-vt p-t-20 p-b-20 m-l-30 m-r-30 text-center">
        <span class="fs30 m-b-15 font-eng">THE ORIGINAL CRASH GAME</span>
        <img src="/imgs/brand.png" class="brand-box m-b-15" />
        <check-link title="ソーシャル＆リアルタイムな取引" class="m-b-10" />
        <check-link title="業界最高水準のシステム" class="m-b-10" />
        <check-link title="高倍率なオッズで資産形成" class="m-b-20" />
        <nuxt-link to="/main">
          <button3-d title="ゲーム開始" />
        </nuxt-link>
      </div>
      <img src="/imgs/firstpage/mobile.png" class="m-l-30 m-r-30" />
    </div>
    <div class="image-box flex-row-hc-vc flex-wrap">
      <img src="/imgs/firstpage/alert1.png" class="img-alert m-t-10 m-b-10" />
      <img src="/imgs/firstpage/alert2.png" class="img-alert m-t-10 m-b-10" />
      <img src="/imgs/firstpage/alert3.png" class="img-alert m-t-10 m-b-10" />
    </div>
  </div>
</template>
<script>
import CheckLink from '@/components/pc/firstpage/CheckLink.vue'
import Button3D from '@/components/pc/common/Button3D.vue'

/**
 * @param {Function} func
 * @param {number} wait
 * @param {boolean} immediate
 * @return {*}
 */
export function debounce(func, wait, immediate) {
  let timeout, args, context, timestamp, result

  const later = function() {
    const last = +new Date() - timestamp

    if (last < wait && last > 0) {
      timeout = setTimeout(later, wait - last)
    } else {
      timeout = null
      if (!immediate) {
        result = func.apply(context, args)
        if (!timeout) context = args = null
      }
    }
  }

  return function(...args) {
    context = this
    timestamp = +new Date()
    const callNow = immediate && !timeout
    if (!timeout) timeout = setTimeout(later, wait)
    if (callNow) {
      result = func.apply(context, args)
      context = args = null
    }

    return result
  }
}

export default {
  name: 'BannerBox',
  components: {
    CheckLink,
    Button3D
  },
  mounted() {
    this.__resizeHandler = debounce(() => {
      if (this.$refs.banner) {
        this.$refs.banner.height = this.$refs.banner.width / 2.5
      }
    }, 100)
    window.addEventListener('resize', this.__resizeHandler)
  },
  beforeDestroy() {
    window.removeEventListener('resize', this.__resizeHandler)
  }
}
</script>
<style lang="scss" scoped>
.banner-container {
  width: 100%;
  min-height: 600px;
  background-size: 100% 100%;
  background-image: url('/imgs/firstpage/banner_back.png');
  color: white;
}

.brand-box {
  height: 75px;
}

.alert-img {
  height: 60px;
}

.image-box {
  width: 100%;

  img {
    overflow: hidden;
  }
}
</style>
