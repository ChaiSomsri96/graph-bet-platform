
var socketIO = require('socket.io');
var io = null;
var app = null;

var initializeSocketIO = function (_app, server) {
    io = socketIO(server)
    app = _app
    io.on('connection', function (socket) {

        console.log('A new Chat-User');

        var construct_func = function (req, res, next) {
            next();
        }

        app.use(construct_func);

        app.post('/post_msg', function (req, res) {
            var json = JSON.stringify({
                msg: req.body.msg,
                username: req.body.userName,
                curTime: req.body.curTime
            });
            //broadcast messages to every body
            io.emit('chat_message', json);
            res.json({
                "error_code": 0
            });
        });
        socket.on('disconnect', function (data) {});
    });
}

module.exports = {
    initializeSocketIO
}