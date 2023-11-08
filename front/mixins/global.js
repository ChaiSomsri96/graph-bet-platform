export default {
  methods: {
    showToast(title, message, type) {
      if (type === 'error') {
        this.$toast.error({
          title,
          message,
          position: 'top right',
          type,
          progressBar: true,
          showMethod: 'shake',
          color: '#BE2739',
          showDuration: 1000,
          hideDuration: 1000,
          timeOut: 5000
        })
      } else {
        this.$toast.success({
          title,
          message,
          position: 'top right',
          type,
          progressBar: true,
          showMethod: 'fadeIn',
          color: '#51A351',
          showDuration: 1000,
          hideDuration: 1000,
          timeOut: 5000
        })
      }
    }
  }
}
