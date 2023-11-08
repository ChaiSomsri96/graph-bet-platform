var express = require('express');
var router = express.Router();
var chatModel = require('./../model/chat');
var userModel = require('./../model/users');
var config = require('./../config');
var dateFormat = require('dateformat');
let moment = require('moment');
const verifyToken = require('../middleware/verify_token');

var axios = require('axios')
var instance = axios.create({
    baseURL: config.main_host_url + ":" + config.chat_port + "/",
    timeout: 5000
});

router.post('/post_msg', verifyToken, async function (req, res) {
    const {
        msg,
        curTime
    } = req.body
    const users = await userModel.detailUsers([{
        key: 'ID',
        val: req.current_user.id
    }])
    if (users.length <= 0) {
        return res.json({
            code: 20000,
            data: {
                error_code: 1
            }
        })
    }
    let userId = users[0]['ID']
    let userName = users[0]['USERNAME']

    var data = {
        msg: msg,
        userName: userName,
        curTime: curTime
    }

    var ret = await instance.post("post_msg", data).then(response => {
        return response.data != null && response.data.error_code == 0
    }).catch(error => {
        return false
    })
    if (ret) {
        var chat_data = {}
        chat_data['userId'] = userId
        chat_data['curTime'] = curTime
        chat_data['msg'] = msg

        chatModel.add(chat_data)
        return res.json({
            code: 20000,
            data: {
                error_code: 0
            }
        })
    } else {
        return res.json({
            code: 20000,
            data: {
                error_code: 1
            }
        })
    }
});

router.post('/list', async function (req, res) {
    var chats = await chatModel.list()
    return res.json({
        code: 20000,
        data: chats.success ? chats.data : []
    })
});

module.exports = router;