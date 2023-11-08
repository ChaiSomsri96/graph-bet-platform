var main_port = 3001
var base_domain = "localhost"
module.exports = {
    main_host_url: "http://localhost",
    server_port: 3001,
    crash_port: 4202,
    chat_port: 4203,

    mysql: '127.0.0.1',
    mysql_user: 'root',
    mysql_pwd: '',
    mysql_db: 'quickbit',

    access_token_secret: 'my_access_token_secret',
    token_ttl: 1,

    //bicoin
    BTC_SITE_WALLET_ADDRESS : "19ejNRyHmqgotgztP7XeLsXbHD7K5tkTJo",
    BTC_SITE_PRIVATE_KEY : "fb422f196446b71f851240984d92baa2fc229f9fdf71335a68db161cebe75ac9",
    BTC_SITE_PUBLIC_KEY : "0284673d7f6b73990999cf16cf03394069e1bca2b8536213ee3e23b50e7fb71b1c",
    BLOCKCYPHER_TOKEN : "201ee5ad2c344fd38ab13df4e6ad39b6",

    BLOCKCYPHER_CALLBACK_HOST_URL : "http://" + base_domain + ":" + main_port + "/",
    BTC_withdraw_fee: 0.00005,
    BTC_min_withdraw_amount: 0.0001,

    //kyc
    LOCATION_PATH: 'F:\\'
}