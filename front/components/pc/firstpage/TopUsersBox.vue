<template>
  <div class="flex-col-ht-vc h-full mw-720 p-l-10 p-r-10">
    <div class="flex-row-hc-vs title-bar h-full">
      <b-col cols="2" class="text-center b-r-title-box p-t-15 p-b-15 bold">
        <div class="flex-row-hc-vc v-full">
          <span>順位</span>
        </div>
      </b-col>
      <b-col cols="7" class="text-center b-r-title-box p-t-15 p-b-15 bold">
        <div class="flex-row-hc-vc v-full">
          <span>プレイヤー</span>
        </div>
      </b-col>
      <b-col cols="3" class="text-center p-t-15 p-b-15 bold">
        <div class="flex-row-hc-vc v-full">
          <span>利益</span>
        </div>
      </b-col>
    </div>
    <div class="flex-col-hc-vc b-t-l-value-box value-box fc-black h-full">
      <div v-for="i in 5" :key="i" class="flex-row-hc-vc h-full">
        <b-col cols="2" class="text-center b-r-b-value-box p-t-15 p-b-15 bold">
          {{ i }}
        </b-col>
        <b-col cols="7" class="text-center b-r-b-value-box p-t-15 p-b-15 bold">
          {{ topUsers[i - 1] != null ? topUsers[i - 1].name : '' }}
        </b-col>
        <b-col cols="3" class="text-center b-r-b-value-box p-t-15 p-b-15 bold">
          {{ topUsers[i - 1] != null ? topUsers[i - 1].value : '' }}
        </b-col>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: 'TopUsersBox',
  components: {},
  props: {
    topUsers: {
      type: Array,
      default() {
        return []
      }
    }
  }
}
</script>
<style lang="scss" scoped>
@import '@/assets/styles/variables.scss';

.mw-720 {
  max-width: 740px;
}

.w-110 {
  max-width: 110px;
  width: 100%;
}

.w-420 {
  max-width: 420px;
  width: 100%;
}

.w-190 {
  max-width: 190px;
  width: 100%;
}

.title-bar {
  background-color: black;
}

.b-r-title-box {
  border-right: 1px solid $grayec;
}

.value-box {
  background-color: white;
}

.b-t-l-value-box {
  border-top: 1px solid $graya0;
  border-left: 1px solid $graya0;
}

.b-r-b-value-box {
  border-right: 1px solid $graya0;
  border-bottom: 1px solid $graya0;
  height: 46px;
}
</style>
