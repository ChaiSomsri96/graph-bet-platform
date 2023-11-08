import axios from 'axios'
import Cookies from '~/node_modules/js-cookie'
const tokenKey = 'bustabit_token_key'

const onOk = (response) => {
  return response.data
}

const onError = (err) => {
  return Promise.reject(err.response)
}

axios.defaults.baseURL =
  'http://' + process.env.APP_HOST + ':' + process.env.APP_PORT + '/'
axios.defaults.prefix =
  'http://' + process.env.APP_HOST + ':' + process.env.APP_PORT + '/'
axios.defaults.timeout = 10000
// axios.defaults.headers.common.Authorization = 'bearer ' + Cookies.get(tokenKey) || ''
axios.interceptors.response.use(onOk, onError)

class AxiosRequest {
  static request(withBearer = true) {
    if (withBearer) {
      axios.defaults.headers.common.Authorization =
        'bearer ' +
        (Cookies.get(tokenKey) === undefined ? '' : Cookies.get(tokenKey))
    }
    const service = axios.create({
      timeout: 10000
    })
    service.interceptors.response.use(onOk, onError)
    return service
  }
}

export default AxiosRequest
