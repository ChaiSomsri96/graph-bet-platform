<template>
  <div class="input-container h-full v-full" :class="classObj">
    <div v-if="hasTitle" :class="titleClassObj">
      <span>{{ title }}</span>
    </div>
    <div class="input-box flex-row-hc-vc" :class="inputClass">
      <b-form-input
        v-if="!isTextarea"
        v-model="inputModel"
        :type="inputType"
        :required="isRequired"
        :readonly="isReadonly"
        :disabled="isDisable"
        @change="onChange"
        @keyup="onChange"
        @keyup.enter="onEnter"
      />
      <b-form-textarea
        v-else
        v-model="inputModel"
        :type="inputType"
        :required="isRequired"
        :readonly="isReadonly"
        :disabled="isDisable"
        @change="onChange"
        @keyup="onChange"
        @keyup.enter="onEnter"
      />
      <slot name="button-content"></slot>
    </div>
  </div>
</template>
<script>
export default {
  name: 'InputWithLabelBox',
  props: {
    title: {
      type: String,
      default: ''
    },
    hasTitle: {
      type: Boolean,
      default: true
    },
    inputType: {
      type: String,
      default: 'text'
    },
    isReadonly: {
      type: Boolean,
      default: false
    },
    isRequired: {
      type: Boolean,
      default: true
    },
    isDisable: {
      type: Boolean,
      default: false
    },
    isRound: {
      type: Boolean,
      default: true
    },
    smallText: {
      type: Boolean,
      default: false
    },
    varLink: {
      type: String,
      default: ''
    },
    isTextarea: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      inputModel: this.varLink
    }
  },
  computed: {
    titleClassObj: {
      get() {
        return {
          'title-box fc-white nowrap': true,
          'is-textarea': this.isTextarea
        }
      }
    },
    classObj: {
      get() {
        return {
          round: this.isRound,
          small: this.smallText,
          normal: !this.smallText,
          'no-title': !this.hasTitle,
          'is-textarea': this.isTextarea
        }
      }
    },
    inputClass: {
      get() {
        return {
          'disable-back': this.isDisable
        }
      }
    }
  },
  watch: {
    varLink(newVal) {
      this.inputModel = newVal
    }
  },
  methods: {
    onChange() {
      this.$emit('inputChanged', this.inputModel)
    },
    onEnter() {
      this.$emit('enterClicked')
    },
    biggerThanZero() {
      return (
        this.inputType === 'number' && (this.inputModel > 0 || !this.isRequired)
      )
    }
  }
}
</script>
<style lang="scss" scoped>
@import '@/assets/styles/variables.scss';

.input-container {
  height: 40px;
  border: 1px solid $gray4a;
  display: -webkit-flex;
  display: flex;
  justify-content: center;
  align-items: center;
  &.is-textarea {
    height: 50px;
  }
}

.title-box {
  height: 38px;
  background: $gray35;
  min-width: 130px;
  border-right: 1px solid $gray4a;
  display: -webkit-flex;
  display: flex;
  align-items: center;
  justify-content: flex-start;
  &.is-textarea {
    height: 48px;
  }
}

.input-box {
  height: 38px;
  flex: 1;
  background: white;

  input,
  textarea {
    width: 100%;
    border: none;
    outline: none;
    border-radius: 0px;
  }
  input {
    max-height: 36px;
  }
  textarea {
    max-height: 50px;
  }
}

.input-container.round {
  border-radius: 2px;

  .input-box {
    border-top-right-radius: 2px;
    border-bottom-right-radius: 2px;

    input,
    textarea {
      border-top-right-radius: 2px;
      border-bottom-right-radius: 2px;
    }
  }
}

.input-container.small {
  font-size: 14px;

  .title-box {
    padding-left: 10px;
    padding-right: 10px;
  }
}

.input-container.normal {
  font-size: 16px;

  .title-box {
    padding-left: 20px;
    padding-right: 20px;
  }
}

.input-box.disable-back {
  background: $disableEditBack;
  input,
  textarea {
    background: $disableEditBack;
  }
}

@media (max-width: 640px) {
  .input-container {
    height: 78px;
    &.is-textarea {
      height: 98px !important;
    }
    flex-direction: column;

    .title-box {
      min-width: 100%;
      border-right: none;
      border-bottom: 1px solid $gray4a;
      justify-content: center;
    }

    .input-box {
      width: 100%;
    }
  }

  .input-container.round {
    .input-box {
      border-top-right-radius: 0px;
      border-bottom-right-radius: 2px;
      border-bottom-left-radius: 2px;

      input,
      textarea {
        border-top-right-radius: 2px;
        border-bottom-right-radius: 2px;
      }
    }
  }

  .input-container.no-title {
    height: 38px;
    border: none;
  }
}
</style>
