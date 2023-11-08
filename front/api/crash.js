import AxiosRequest from '@/api/AxiosRequest'

export function apiBet(data) {
  const request = AxiosRequest.request()
  return request({
    url: '/Crash/bet',
    method: 'post',
    data
  })
}

export function apiCancelBet(data) {
  const request = AxiosRequest.request()
  return request({
    url: '/Crash/cancelBet',
    method: 'post',
    data
  })
}

export function apiCashOut(data) {
  const request = AxiosRequest.request()
  return request({
    url: '/Crash/cashOut',
    method: 'post',
    data
  })
}

export function apiGetHistory(data) {
  const request = AxiosRequest.request()
  return request({
    url: '/Crash/getHistory',
    method: 'post',
    data
  })
}

export function apiGetStatus() {
  const request = AxiosRequest.request()
  return request({
    url: '/Crash/getStatus',
    method: 'post'
  })
}
