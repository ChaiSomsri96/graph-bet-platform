var createError = require('http-errors');
var express = require('express');
var path = require('path');
var cookieParser = require('cookie-parser');
var bodyParser = require('body-parser')
var logger = require('morgan');
var usersRouter = require('../routes/users');
var appChatRouter = require('../routes/app_chat')
var settingRouter = require('../routes/setting')
var bitcoinRouter = require('../routes/bitcoin')
var faqRouter = require('../routes/faq')
var app = express();
// view engine setup
app.set('views', path.join(__dirname, '..', 'views'));
app.set('view engine', 'ejs');
app.use(logger('dev'));
app.use( bodyParser.json() );       // to support JSON-encoded bodies
app.use(bodyParser.urlencoded({     // to support URL-encoded bodies
  extended: true
}));
app.use(cookieParser());
app.use(express.static(path.join(__dirname, '..', 'public')));
// app.use(function (req, res, next) {
//   res.setHeader('Access-Control-Allow-Origin', '*');
//   res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, PATCH, DELETE');
//   res.setHeader('Access-Control-Allow-Headers', 'X-Requested-With,content-type, authorization');
//   res.setHeader('Access-Control-Allow-Credentials', true);
//   next();
// });
app.use('/User', usersRouter);
app.use('/Chat', appChatRouter);
app.use('/Setting', settingRouter);
app.use('/Bitcoin', bitcoinRouter);
app.use('/Faq', faqRouter);
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
module.exports = app;