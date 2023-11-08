<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/login', 'AdminController@login');
Route::post('/login', 'AdminController@loginPost');
Route::get('/logout', 'AdminController@logout');
Route::get('/', function () {
    if(session()->get('type') != 'admin') {
        return redirect('user_affiliates');
    }
    else {
        return redirect('dashboard');
    }
});
Route::middleware(['admin_permission'])->group(function () {
    //affiliates
    Route::get('/affiliates', 'AffiliateController@index');
    Route::post('/affiliates/get_list', 'AffiliateController@getList');
    Route::post('/affiliates/set_bonus', 'AffiliateController@setBonus');
    //アフィリエイト月別通計機能
    Route::get('/affiliates/monthly_statistics/{user_id}', 'AffiliateController@statistics');
    //報酬支払い履歴ページ
    Route::get('/affiliates/history/{user_id}', 'AffiliateController@history');
    Route::post('/affiliates/history/get_list', 'AffiliateController@getHistoryList');
    Route::post('/affiliates/statistics/get_list', 'AffiliateController@getStatisticList');
    //dashboard
    Route::get('/dashboard', 'DashboardController@index');
    Route::post('/get_dashboard_data', 'DashboardController@getDashboardData');
    //users
    Route::get('/users', 'UserController@index');
    Route::get('/users/history/{user_id}', 'UserController@detail');
    Route::post('/users/get_list', 'UserController@getList');
    Route::post('/users/history/get_list', 'UserController@getHistoryList');
    Route::post('/users/change', 'UserController@change');
    Route::post('/users/reset_password', 'UserController@reset');
    Route::post('/users/get_kyc_images', 'UserController@getKYCImages');
    //bots
    Route::get('/bots', 'BotController@index');
    Route::post('/bots/get_list', 'BotController@getList');
    Route::post('/bots/change', 'BotController@change');
    Route::post('/bots/create', 'BotController@create');
    //wallets
    Route::get('/wallets', 'WalletController@index');
    Route::post('/wallets', 'WalletController@index');
    Route::get('/wallets/history', 'WalletController@history');
    Route::post('/wallets/get_list', 'WalletController@getList');
    //deposit/withdraw
    Route::get('/deposits', 'BitcoinController@index');
    Route::get('/withdraws', 'BitcoinController@withdraws');
    Route::post('/deposits/get_list', 'BitcoinController@getDepositsList');
    Route::post('/withdraws/get_list', 'BitcoinController@getWithdrawsList');
    Route::post('/withdraws/agree_withdraw', 'BitcoinController@agreeWithdraw');
    Route::post('/withdraws/disagree_withdraw', 'BitcoinController@disagreeWithdraw');
    //game_history
    Route::get('/game_history', 'GameHistoryController@index');
    Route::post('/game_history/get_list', 'GameHistoryController@getList');
    Route::post('/game_history/detail', 'GameHistoryController@postDetail');
    //faq , terms_service
    Route::get('/faqs', 'AdminController@faq');
    Route::get('/faqs/category', 'AdminController@faq_category');
    Route::post('/faqs/category/get_list', 'AdminController@getCategoryList');
    Route::post('/faqs/get_list', 'AdminController@getFaqList');
    Route::post('/faqs/category/create', 'AdminController@faqCategoryCreate');
    Route::post('/faqs/create', 'AdminController@faqCreate');
    Route::post('/faqs/category/remove', 'AdminController@faqCategoryRemove');
    Route::post('/faqs/remove', 'AdminController@faqRemove');
    Route::post('/faqs/category/detail', 'AdminController@faqCategoryDetail');
    Route::post('/faqs/detail', 'AdminController@faqDetail');
    Route::get('/terms_service', 'AdminController@terms_service');
    Route::post('/terms_service/create', 'AdminController@storeTermsService');
    //setting
    Route::post('/admin/save_setting', 'AdminController@saveSetting');
});
//user_permission
Route::middleware(['user_permission'])->group(function () {
    //
    Route::get('/user_affiliates', 'UseraffiliateController@index');
    Route::get('/user_affiliates/monthly_list', 'UseraffiliateController@monthlyList');
    Route::post('/user_affiliates/history/get_list', 'UseraffiliateController@getHistoryList');
    Route::post('/user_affiliates/statistics/get_list', 'UseraffiliateController@getStatisticList');
    Route::get('/user_affiliates/user', 'UseraffiliateController@user');
    Route::post('/user_affiliates/user/get_list', 'UseraffiliateController@getUserList');
    Route::post('/user_affiliates/user/create', 'UseraffiliateController@userCreate');
    Route::get('/user_affiliates/affiliate_link', 'UseraffiliateController@affLink');
});
//admin
Route::post('/admin/change_password', 'AdminController@changePassword');
Route::get('/admin/profile', 'AdminController@profile');


