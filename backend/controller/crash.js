var createError = require('http-errors');
var express = require('express');
var path = require('path');
var bodyParser = require('body-parser');
var cookieParser = require('cookie-parser');
var logger = require('morgan');
const verifyToken = require('../middleware/verify_token');

var crashRouter = require('../routes/crash');

var app = express();
var server = require('http').createServer(app);

crashRouter.initializeSocketIO(server);

// view engine setup
app.set('views', path.join(__dirname, '..', 'views'));
app.set('view engine', 'ejs');

app.use(logger('dev'));
app.use(express.json());
app.use(express.urlencoded({
    extended: false
}));
app.use(cookieParser());
app.use(express.static(path.join(__dirname, '..', 'public')));

// app.use(function (req, res, next) {
//     res.setHeader('Access-Control-Allow-Origin', '*');
//     res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, PATCH, DELETE');
//     res.setHeader('Access-Control-Allow-Headers', 'X-Requested-With,content-type, authorization');
//     res.setHeader('Access-Control-Allow-Credentials', true);
//     next();
// });

/* define get my bet in crash */
app.use('/Crash', crashRouter.router);
// app.post('/Crash/bet', verifyToken, crashRouter.bet);
// app.post('/Crash/cashOut', verifyToken, crashRouter.cashOut);

// app.post('/Crash/gameStart', verifyToken, crashRouter.gameStart);
// app.post('/Crash/gameBust', verifyToken, crashRouter.gameBust);
// app.post('/Crash/init', verifyToken, crashRouter.init);
// app.post('/Crash/gameFinishStart', verifyToken, crashRouter.gameFinishStart);

// catch 404 and forward to error handler
app.use(function (req, res, next) {
    next(createError(404));
});

// error handler
app.use(function (err, req, res, next) {
    // set locals, only providing error in development
    res.locals.message = err.message;
    res.locals.error = req.app.get('env') === 'development' ? err : {};

    // render the error page
    res.status(err.status || 500);
    res.render('error');
});

module.exports = {
    app,
    server
};