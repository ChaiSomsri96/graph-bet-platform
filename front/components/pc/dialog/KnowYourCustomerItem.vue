<template>
  <div class="kyc-box fs13 fc-white">
    <input
      id="file"
      ref="file"
      type="file"
      style="display:none;"
      accept="image/x-png,image/gif,image/jpeg"
      @change="handleFileUpload()"
    />
    <div class="title-box">
      <svg-icon
        :icon-class="imgName"
        class="m-l-10 m-r-10 ic-size-23-29 custom_size"
      />
      <span>{{ title }}</span>
    </div>
    <button3-d
      title="ファイルを選択"
      button-size="small"
      text-size="minismall"
      class="m-l-25 m-r-25 m-t-5 m-b-5 upload_button b_w130"
      @on-click="readFile()"
    />
    <button3-d
      title="アップロード"
      button-size="small"
      text-size="minismall"
      :status="disabled ? 'disabled' : 'bet'"
      class="m-l-25 m-r-25 m-t-5 m-b-5 b_w130"
      @on-click="uploadFile()"
    />
    <div v-if="uploadSuccess || upload_success" class="f-green">
      アップロード済み
    </div>
  </div>
</template>
<script>
import Button3D from '@/components/pc/common/Button3D.vue'
import GlobalMixin from '~/mixins/global'
export default {
  name: 'KnowYourCustomItem',
  components: {
    Button3D
  },
  mixins: [GlobalMixin],
  props: {
    title: {
      type: String,
      default: ''
    },
    imgName: {
      type: String,
      default: ''
    },
    uploadSuccess: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      file: '',
      disabled: true,
      upload_success: false
    }
  },
  methods: {
    readFile() {
      this.$refs.file.click()
    },
    handleFileUpload() {
      this.file = this.$refs.file.files[0]
      this.disabled = false
    },
    uploadFile() {
      let uploadUrl = '/User/upload_kyc_passport'
      const self = this
      const formData = new FormData()
      formData.append('file', this.file)
      if (this.imgName !== 'security_global')
        uploadUrl = '/User/upload_kyc_idcard'
      this.$bus.$emit('show-overlay')
      this.$axios
        .post(uploadUrl, formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
        .then((response) => {
          self.$bus.$emit('hide-overlay')
          self.disabled = true
          if (response.status === 'success') {
            if (this.imgName === 'security_global') {
              self.upload_success = true
              self.showToast(
                'Success',
                '免許証・パスポート アップロードに成功しました。',
                'success'
              )
            } else {
              self.upload_success = true
              self.showToast(
                'Success',
                'IDセルフィーアップロードに成功しました。',
                'success'
              )
            }
          } else {
            self.upload_success = false
            self.showToast('Error', 'アップロードに失敗しました。', 'error')
          }
        })
    }
  }
}
</script>
<style lang="scss" scoped>
.title-box {
  min-width: 170px;
  display: flex;
  align-items: center;
}

.kyc-box {
  display: flex;
  align-items: center;
  justify-content: flex-start;
}

@media (max-width: 580px) {
  .kyc-box {
    flex-direction: column;
    align-items: center;
    justify-content: center;

    .title-box {
      min-width: auto;
    }
  }
}

.ic-size-23-29 {
  width: 23px;
  height: 29px;
}
.custom_size {
  width: 22.58px !important;
  height: 30.47px !important;
}
.upload_button {
  background-color: #ccc;
}
.upload_button:hover {
  background-color: #eee;
}
.b_w130 {
  width: 130px;
}
.f-green {
  color: #28a745;
}
</style>
