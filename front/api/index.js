export function toThousandFilter(num) {
  return (+num || 0)
    .toString()
    .replace(/^-?\d+/g, (m) => m.replace(/(?=(?!\b)(\d{3})+$)/g, ','))
}

// export function timeAgo(time) {
//   const between = Date.now() / 1000 - Number(time)
//   if (between < 3600) {
//     return pluralize(~~(between / 60), ' minute')
//   } else if (between < 86400) {
//     return pluralize(~~(between / 3600), ' hour')
//   } else {
//     return pluralize(~~(between / 86400), ' day')
//   }
// }

export function numberFormatter(num, digits) {
  const si = [
    { value: 1000000000000000000, symbol: 'E' },
    { value: 1000000000000000, symbol: 'P' },
    { value: 1000000000000, symbol: 'T' },
    { value: 1000000000, symbol: 'G' },
    { value: 1000000, symbol: 'M' },
    { value: 1000, symbol: 'k' }
  ]
  for (let i = 0; i < si.length; i++) {
    if (num >= si[i].value) {
      const szENum = (num / si[i].value + 0.1).toFixed(digits)
      return szENum.replace(/\.0+$|(\.[0-9]*[1-9])0+$/, '$1') + si[i].symbol
    }
  }
  return num.toString()
}
