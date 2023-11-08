import AxiosRequest from '@/api/AxiosRequest'

export function login(data) {
  const request = AxiosRequest.request()
  return request({
    url: '/User/login',
    method: 'post',
    data
  })
}

export function userInfo(data) {
  const request = AxiosRequest.request()
  return request({
    url: '/User/info',
    method: 'post'
  })
}

export function walletInfo() {
  const request = AxiosRequest.request()
  return request({
    url: '/User/wallet',
    method: 'post'
  })
}

export function totalProfit() {
  const request = AxiosRequest.request()
  return request({
    url: '/User/getTotalProfit',
    method: 'post'
  })
}

export function bettingData() {
  const request = AxiosRequest.request()
  return request({
    url: '/User/getBettingData',
    method: 'post'
  })
}

export function genHashTemp(data) {
  const request = AxiosRequest.request()
  return request({
    url: '/User/genHash',
    method: 'post'
  })
}
