export function toThousandFilter(num) {
  if (num === '-') return '-'
  return (+num || 0)
    .toString()
    .replace(/^-?\d+/g, (m) => m.replace(/(?=(?!\b)(\d{3})+$)/g, ','))
}

export function parseTime(time, cFormat) {
  if (arguments.length === 0) {
    return null
  }
  const format = cFormat || '{y}-{m}-{d} {h}:{i}:{s}'
  let date
  if (typeof time === 'object') {
    date = time
  } else {
    if (typeof time === 'string' && /^[0-9]+$/.test(time)) {
      time = parseInt(time)
    }
    if (typeof time === 'number' && time.toString().length === 10) {
      time = time * 1000
    }
    date = new Date(time)
  }
  const formatObj = {
    y: date.getFullYear(),
    m: date.getMonth() + 1,
    d: date.getDate(),
    h: date.getHours(),
    i: date.getMinutes(),
    s: date.getSeconds(),
    a: date.getDay()
  }
  const timeStr = format.replace(/{(y|m|d|h|i|s|a)+}/g, (result, key) => {
    let value = formatObj[key]
    // Note: getDay() returns 0 on Sunday
    if (key === 'a') {
      return ['日', '一', '二', '三', '四', '五', '六'][value]
    }
    if (result.length > 0 && value < 10) {
      value = '0' + value
    }
    return value || 0
  })
  return timeStr
}

export function formatTime(time, option) {
  if (('' + time).length === 10) {
    time = parseInt(time) * 1000
  } else {
    time = +time
  }
  const d = new Date(time)
  const now = Date.now()

  let diff = (now - d) / 1000
  const day = 3600 * 24

  if (diff < 30) {
    return 'few second ago'
  } else if (diff < 3600) {
    // less 1 hour
    return Math.ceil(diff / 60) + ' min ago'
  } else if (diff < 3600 * 24) {
    return Math.ceil(diff / 3600) + ' hour ago'
  } else if (diff < 3600 * 24 * 2) {
    return '1 day ago'
  } else {
    diff = parseInt(diff / day)
    if (diff < 30) {
      return diff + ' days ago'
    } else if (diff < 365) {
      return parseInt(diff / 30) + ' months ago'
    } else {
      return parseInt(diff / 365) + ' years ago'
    }
  }

  // if (option) {
  //   return parseTime(time, option)
  // } else {
  //   return (
  //     d.getMonth() +
  //     1 +
  //     '月' +
  //     d.getDate() +
  //     '日' +
  //     d.getHours() +
  //     '时' +
  //     d.getMinutes() +
  //     '分'
  //   )
  // }
}
