import AxiosRequest from '@/api/AxiosRequest'

export function postMsg(data) {
  const request = AxiosRequest.request()
  return request({
    url: '/Chat/post_msg',
    method: 'post',
    data
  })
}

export function listChat() {
  const request = AxiosRequest.request()
  return request({
    url: '/Chat/list',
    method: 'post'
  })
}
