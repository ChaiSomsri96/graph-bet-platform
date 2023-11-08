<template>
  <div class="score_graph_container br-sm">
    <canvas ref="canvas" />
  </div>
</template>

<script>
export default {
  name: 'ScoreGraph',
  props: {},
  data() {
    return {
      STATUS_PENDING: 1,
      STATUS_STARTED: 2,
      STATUS_FINISHED: 3,
      STATUS_CONNECTING: 4,
      STATUS_DISCONNECTED: 5,

      status: this.STATUS_CONNECTING,
      start_time: Date.now(),
      prev_tick_time: 0,
      crash: 0,
      duration: 0,
      ctx: null,
      canvasWidth: 0,
      canvasHeight: 0,
      canvas: null,
      animRequest: null,
      onWindowResizeBinded: this.onWindowResize.bind(this),
      onChangeBinded: this.onChange.bind(this),
      plotWidth: 0,
      plotHeight: 0,
      xStart: 0,
      yStart: 0,

      YAxisSizeMultiplier: 2,
      YAxisInitialPlotValue: 'zero',

      XAxisPlotMinValue: 10000,
      YAxisPlotMinValue: 2,

      XAxisPlotValue: 10000,
      YAxisPlotValue: 2,

      widthIncrement: 0,
      heightIncrement: 0,
      currentX: 0,
      milisecondsSeparation: 0,
      XAxisValuesSeparation: 0,
      payoutSeparation: 0,

      currentTime: 0,
      currentGamePayout: 0,
      isRenderBinded: false
    }
  },
  computed: {},
  watch: {},
  created() {},
  mounted() {
    this.create()

    const self = this
    this.$bus.$on('game-created', function(payload) {
      self.status = self.STATUS_PENDING
      self.duration = (payload.duration / 1000).toFixed(1)
      self.prev_tick_time = new Date().getTime() / 1000
    })

    this.$bus.$on('game-started', function(payload) {
      self.crash = payload.crash
      self.prev_tick_time = new Date().getTime() / 1000
      if (self.status !== self.STATUS_STARTED) {
        self.status = self.STATUS_STARTED
        self.start_time =
          Date.now() - Math.ceil(self.inverseGrowth(self.crash + 1))
      }
    })

    this.$bus.$on('game-finished', function(payload) {
      self.status = self.STATUS_FINISHED
      self.prev_tick_time = new Date().getTime() / 1000
      if (payload.crash === 100) {
        self.start_time =
          Date.now() - Math.ceil(self.inverseGrowth(self.crash + 1))
      }
      self.crash = payload.crash
    })

    this.$bus.$on('resize', function() {
      self.onWindowResizeBinded()
    })

    this.$bus.$on('game-error', function() {
      self.status = self.STATUS_DISCONNECTED
    })

    this.$bus.$on('do-tick', function(payload) {
      self.crash = payload.tick
      self.prev_tick_time = new Date().getTime() / 1000
      self.status = self.STATUS_STARTED
      const st = Date.now() - Math.ceil(self.inverseGrowth(payload.tick + 1))
      if (Math.abs(self.start_time - st) > 500) self.start_time = st
    })
  },
  destroyed() {
    this.destroy()
  },
  methods: {
    growth(ms) {
      return Math.floor(100 * Math.exp(0.00006 * ms)) / 100
    },
    inverseGrowth(result) {
      const c = 16666.666667
      return c * Math.log(0.01 * result)
    },
    create() {
      this.status = this.STATUS_DISCONNECTED
      this.canvas = this.$refs.canvas
      this.ctx = this.$refs.canvas.getContext('2d')
      this.onWindowResize()

      this.configPlotSettings()
      if (!this.isRenderBinded) {
        this.isRenderBinded = true
        this.animRequest = window.requestAnimationFrame(this.render.bind(this))
      }
      window.addEventListener('resize', this.onWindowResizeBinded)
    },
    destroy() {
      window.removeEventListener('resize', this.onWindowResizeBinded)
    },
    onChange() {
      this.configPlotSettings()
    },
    render() {
      let curTime = new Date()
      curTime = curTime.getTime() / 1000

      if (curTime !== undefined && curTime - this.prev_tick_time > 6)
        this.status = this.STATUS_DISCONNECTED

      this.isRenderBinded = false
      this.calcGameData()
      this.calculatePlotValues()
      this.clean()
      this.drawArea()
      this.drawAxes()
      this.drawGraph()
      this.drawGameData()
      if (!this.isRenderBinded) {
        this.isRenderBinded = true
        this.animRequest = window.requestAnimationFrame(this.render.bind(this))
      }
    },
    onWindowResize() {
      const parentNode = this.$refs.canvas.parentNode
      this.canvasWidth = parentNode.clientWidth - 10
      this.canvasHeight = 230
      this.configPlotSettings()
    },
    configPlotSettings() {
      this.canvas.width = this.canvasWidth
      this.canvas.height = this.canvasHeight
      this.plotWidth = this.canvasWidth - 50
      this.plotHeight = this.canvasHeight - 40 //  280
      this.xStart = this.canvasWidth - this.plotWidth
      this.yStart = this.canvasHeight - this.plotHeight
      this.XAxisPlotMinValue = 10000 //  10 Seconds
      this.YAxisSizeMultiplier = 2 //  YAxis is x times
      this.YAxisInitialPlotValue = 'zero' //  'zero', 'betSize' //  TODO: ???
    },
    calcGameData() {
      if (this.status === this.STATUS_FINISHED) {
        //  when finished status, we don't need to update currentGamePayout
        return
      }
      if (this.status === this.STATUS_STARTED) {
        this.currentTime = Date.now() - this.start_time
      } else {
        this.currentTime = 0
      }
      this.currentGamePayout = this.growth(this.currentTime)

      this.$bus.$emit('current-payout', { value: this.currentGamePayout })
    },
    calculatePlotValues() {
      // Plot variables
      this.YAxisPlotMinValue = this.YAxisSizeMultiplier
      this.YAxisPlotValue = (this.YAxisPlotMinValue * 6) / 5

      this.XAxisPlotValue = (this.XAxisPlotMinValue * 6) / 5

      // Adjust X Plot's Axis
      if (this.currentTime > this.XAxisPlotMinValue) {
        this.XAxisPlotValue = (this.currentTime * 6) / 5
      }

      // Adjust Y Plot's Axis
      if (this.currentGamePayout > this.YAxisPlotMinValue) {
        this.YAxisPlotValue = (this.currentGamePayout * 6) / 5
      }

      // We start counting from cero to plot
      this.YAxisPlotValue -= 1

      // Graph values
      this.widthIncrement = this.plotWidth / this.XAxisPlotValue
      this.heightIncrement = this.plotHeight / this.YAxisPlotValue
      this.currentX = this.currentTime * this.widthIncrement
    },
    clean() {
      this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height)
    },
    drawGraph() {
      const gradient = this.ctx.createLinearGradient(
        0,
        this.canvas.height,
        this.canvas.width,
        0
      )
      gradient.addColorStop(0, '#2077e4')
      gradient.addColorStop(1, '#ff4a74')

      this.ctx.setLineDash([])
      if (this.status === this.STATUS_FINISHED) {
        this.ctx.strokeStyle = '#ffde00'
      } else if (this.currentGamePayout >= 15) {
        this.ctx.strokeStyle = '#ffde00'
      } else {
        this.ctx.strokeStyle = '#ffde00'
      }

      let prevX = false
      let prevY = false
      /* Draw the graph */
      for (let t = 0, i = 0; t <= this.currentTime; t += 100, i++) {
        const payout = this.growth(t) - 1 // We start counting from one x
        const y = this.plotHeight - payout * this.heightIncrement
        const x = t * this.widthIncrement
        if (prevX === false) prevX = x + this.xStart
        if (prevY === false) prevY = y

        // Fill the under area of the path
        if (prevX !== false && prevY !== false) {
          this.ctx.globalCompositeOperation = 'source-over'
          this.ctx.beginPath()
          this.ctx.lineWidth = 1
          this.ctx.lineCap = 'square'
          this.ctx.lineJoin = 'miter'
          this.ctx.strokeStyle = '#ffcf40'
          this.ctx.moveTo(prevX, prevY)
          this.ctx.lineTo(x + this.xStart, y)
          this.ctx.lineTo(x + this.xStart, this.canvasHeight - this.yStart)
          this.ctx.lineTo(prevX, this.canvasHeight - this.yStart)
          this.ctx.lineTo(prevX, prevY)
          this.ctx.fillStyle = '#ffcf40'
          this.ctx.closePath()
          this.ctx.stroke()
          this.ctx.fill()
        }
        // Draw the path
        this.ctx.beginPath()
        this.ctx.lineWidth = 2
        this.ctx.lineCap = 'round'
        this.ctx.lineJoin = 'round'
        this.ctx.strokeStyle = '#ee683a'
        this.ctx.moveTo(prevX, prevY)
        this.ctx.lineTo(x + this.xStart, y)
        this.ctx.closePath()
        this.ctx.stroke()

        prevX = x + this.xStart
        prevY = y
        /* Avoid crashing the explorer if the cycle is infinite */
        if (i > 5000) {
          break
        }
      }
    },
    stepValues(x, isX = false) {
      let interval = 0
      let step = 0.2

      if (isX) interval = 10
      else interval = 5

      for (let i = 0; i === 0; i = 0) {
        if (x < step * interval + (isX ? 1000 : 1)) return step
        step = step * 2
        if (x < step * interval + (isX ? 1000 : 1)) return step
        step = step * 5
      }
    },
    drawArea() {
      this.ctx.fillStyle = '#2b2b2b'
      this.ctx.fillRect(
        this.xStart,
        0,
        this.canvasWidth - this.xStart,
        this.canvasHeight - this.yStart
      )
      const gradient = this.ctx.createLinearGradient(
        0,
        this.canvas.height,
        this.canvas.width,
        0
      )
      gradient.addColorStop(0, '#2077e4')
      gradient.addColorStop(1, '#ff4a74')

      this.ctx.setLineDash([])
      if (this.status === this.STATUS_FINISHED) {
        this.ctx.strokeStyle = '#ffde00'
      } else if (this.currentGamePayout >= 15) {
        this.ctx.strokeStyle = '#ffde00'
      } else {
        this.ctx.strokeStyle = '#ffde00'
      }

      let prevX = false
      let prevY = false
      /* Draw the graph */
      for (let t = 0, i = 0; t <= this.currentTime; t += 100, i++) {
        const payout = this.growth(t) - 1 // We start counting from one x
        const y = this.plotHeight - payout * this.heightIncrement
        const x = t * this.widthIncrement
        if (prevX === false) prevX = x + this.xStart
        if (prevY === false) prevY = y

        // Fill the under area of left side
        if (prevX !== false && prevY !== false) {
          this.ctx.beginPath()
          this.ctx.lineWidth = 1
          this.ctx.lineCap = 'round'
          this.ctx.lineJoin = 'round'
          this.ctx.strokeStyle = '#414141'
          this.ctx.moveTo(prevX, prevY)
          this.ctx.lineTo(x + this.xStart, y)
          this.ctx.lineTo(x + this.xStart, 0)
          this.ctx.lineTo(prevX, 0)
          this.ctx.lineTo(prevX, prevY)
          this.ctx.fillStyle = '#414141'
          this.ctx.closePath()
          this.ctx.stroke()
          this.ctx.fill()
        }

        prevX = x + this.xStart
        prevY = y
        /* Avoid crashing the explorer if the cycle is infinite */
        if (i > 5000) {
          break
        }
      }
    },
    drawAxes() {
      this.ctx.lineWidth = 1
      this.ctx.strokeStyle = '#212121'
      this.ctx.font = '12px Kanit Light'
      this.ctx.fillStyle = '#cacaca'
      this.ctx.textAlign = 'center'

      this.YAxisPlotMaxValue = this.YAxisPlotMinValue
      this.payoutSeparation = this.stepValues(
        !this.currentGamePayout ? 1 : this.currentGamePayout
      )
      const heightIncrement = this.plotHeight / this.YAxisPlotValue
      for (
        let payout = this.payoutSeparation, i = 0;
        payout < this.YAxisPlotValue;
        payout += this.payoutSeparation, i++
      ) {
        const y = this.plotHeight - payout * heightIncrement
        this.ctx.fillText((payout + 1).toFixed(1) + 'x', 20, y)
        if (payout > 0) {
          this.ctx.beginPath()
          this.ctx.moveTo(this.xStart, y)
          this.ctx.lineTo(this.canvasWidth, y)
          this.ctx.stroke()
        }
        if (i > 100) {
          break
        }
      }

      // Calculate X Axis
      this.milisecondsSeparation = this.stepValues(this.XAxisPlotValue, true)
      this.XAxisValuesSeparation =
        this.plotWidth / (this.XAxisPlotValue / this.milisecondsSeparation)

      // Draw X Axis Values
      for (
        let miliseconds = 0, counter = 0, s = 0;
        miliseconds < this.XAxisPlotValue;
        miliseconds += this.milisecondsSeparation, counter++, s++
      ) {
        const seconds = miliseconds / 1000
        const textWidth = this.ctx.measureText(seconds).width
        const x = counter * this.XAxisValuesSeparation + this.xStart
        this.ctx.beginPath()
        this.ctx.moveTo(x, 0)
        this.ctx.lineTo(x, this.canvasHeight - this.yStart)
        this.ctx.stroke()
        this.ctx.fillText(
          seconds + '',
          x - textWidth / 2,
          this.plotHeight + 22 // XAxis padding
        )
        if (s > 100) {
          break
        }
      }

      // Draw background Axis
      this.ctx.lineWidth = 1
      this.ctx.beginPath()
      this.ctx.moveTo(this.xStart, 0)
      this.ctx.lineTo(this.xStart, this.canvasHeight - this.yStart)
      this.ctx.lineTo(this.canvasWidth, this.canvasHeight - this.yStart)
      this.ctx.stroke()
    },
    drawGameData() {
      // One percent of canvas width
      let baseVal = 0
      if (this.$root.$el.clientWidth > 480) baseVal = this.canvasWidth / 100
      else baseVal = this.canvasWidth / 80
      // Multiply it x times
      function fontSizeNum(times) {
        return baseVal * times
      }
      // Return the font size in pixels of one percent of the width canvas by x times
      function fontSizePx(times) {
        const fontSize = fontSizeNum(times)
        return fontSize.toFixed(2) + 'px'
      }

      this.ctx.textAlign = 'center'
      this.ctx.textBaseline = 'middle'

      const payout = this.growth(this.currentTime) - 1 // We start counting from one x
      const y = this.plotHeight - payout * this.heightIncrement
      const x = this.currentTime * this.widthIncrement + this.xStart

      this.ctx.lineWidth = 1
      this.ctx.strokeStyle = '#676767'
      this.ctx.beginPath()
      for (let i = this.xStart; i < this.canvasWidth; i += 4) {
        this.ctx.moveTo(i, y)
        this.ctx.lineTo(i + 2, y)
        this.ctx.stroke()
      }

      this.ctx.globalAlpha = 1
      this.ctx.fillStyle = '#ffffff'
      this.ctx.beginPath()
      this.ctx.arc(x, y, 3, 0, 2 * Math.PI)
      this.ctx.fill()

      if (this.status === this.STATUS_STARTED) {
        this.ctx.fillStyle = '#ee683a'
        this.ctx.font = fontSizePx(5) + ' Verdana'
        this.ctx.fillText(
          '@' + parseFloat(this.currentGamePayout).toFixed(2) + 'x',
          x,
          y - 20
        )
      }

      // If the engine enters in the room @ ENDED it doesn't have the crash value, so we don't display it
      if (this.status === this.STATUS_FINISHED) {
        this.ctx.font = fontSizePx(7) + ' Verdana'
        this.ctx.fillStyle = '#ff4a74'
        this.ctx.fillText(
          'Crashed @ ' + (this.crash / 100).toFixed(2) + 'x',
          (this.canvasWidth + this.xStart) / 2,
          this.canvasHeight / 2 - 10
        )
      }

      if (this.status === this.STATUS_PENDING) {
        this.ctx.font = fontSizePx(10) + ' Verdana'
        this.ctx.fillStyle = '#ffffff'

        this.ctx.fillText(
          this.duration + 's',
          (this.canvasWidth + this.xStart) / 2,
          this.canvasHeight / 2 - 10
        )
      }

      if (this.status === this.STATUS_DISCONNECTED) {
        this.ctx.font = fontSizePx(7) + ' Verdana'
        this.ctx.fillStyle = '#ff4a74'

        this.ctx.fillText(
          'Disconnected!',
          (this.canvasWidth + this.xStart) / 2,
          this.canvasHeight / 2 - 10
        )
      }
    }
  }
}
</script>

<style lang="scss" scoped>
@import '@/assets/styles/variables.scss';

.score_graph_container {
  background: $gray1a;
}
</style>
