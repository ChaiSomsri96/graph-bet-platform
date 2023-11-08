<template>
  <div class="annot-container h-full flex-row-hc-vc">
    <div
      class="box-container h-full fc-white flex-col-hl-vc p-t-20"
      :style="'background-image: url(' + backImg + ')'"
    >
      <div class="justify-box">
        <div class="annot-box h-full">
          <span class="fs30 bold title-box text-center">
            {{ title }}
          </span>
          <slot name="content-box"></slot>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: 'ImageBackAnnotBox',
  props: {
    title: {
      type: String,
      default: ''
    },
    backImg: {
      type: String,
      default: ''
    }
  }
}
</script>
<style lang="scss" scoped>
@import '@/assets/styles/variables.scss';

.annot-container {
  height: 319px;
  position: relative;
  line-height: 1.5;
}

.img-back {
  position: absolute;
  top: 0px;
}

.box-container {
  background-repeat: no-repeat;
  max-width: 1920px;
  height: 100%;
}

.annot-container:nth-child(even) {
  background: $gray1e;

  .box-container {
    background-position-x: 100%;
  }
}

.annot-container:nth-child(odd) {
  background: $gray1e;
}

.justify-box {
  max-width: 1030px;
  display: flex;
  align-items: center;
}

.annot-container:nth-child(even) .justify-box {
  justify-content: flex-start;
}

.annot-container:nth-child(odd) .justify-box {
  justify-content: flex-end;
}

.annot-box {
  width: calc(100% - 400px);
}

.title-box {
  line-height: 3;
}

@media (max-width: 1024px) {
  .justify-box {
    max-width: 100%;
    padding: 0px 30px;
  }
  .annot-box {
    min-width: 400px;
  }
}

@media (max-width: 640px) {
  .annot-box {
    min-width: 100%;
  }
}
</style>
