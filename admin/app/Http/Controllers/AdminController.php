<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Admin;
use App\Setting;
use App\Faq;
use App\FaqCategory;
use App\User;
class AdminController extends Controller
{
    //
    function login(Request $request) {
        return view('login');
    }
    function loginPost(Request $request) {
        $login_id = $request->input("login_id");
        $password = $request->input("password");

        $admin = Admin::where(array("LOGINID" => $login_id , "PASSWORD" => md5($password) , "DEL_YN" => 'N'))->first();
        if($admin != NULL) {
            $request->session()->put("is_login", true);
            $request->session()->put("admin_id", $login_id);
            $request->session()->put("id", $admin->ID);
            $request->session()->put("type", 'admin');
            return redirect('/');
        }
        else {
            $admin = User::where(array("USERNAME" => $login_id , "PASSWORD" => md5($password) , "DEL_YN" => 'N', "AFF_YN" => 'Y'))->first();
            if($admin != NULL) {
                $request->session()->put("is_login", true);
                $request->session()->put("admin_id", $login_id);
                $request->session()->put("id", $admin->ID);
                $request->session()->put("aff_code", $admin->AFFILIATE_CODE);
                $request->session()->put("type", 'user');
                return redirect('/user_affiliates');
            }
            return redirect('/login')->with(['errorCode' => true]);
        }
    }
    function changePassword(Request $request) {
        //$old_
        if($request->session()->get('type') == 'admin') {
            $admin = Admin::where(array('ID' => $request->session()->get('id')))->get()->toArray();
            if(count($admin) < 1) {
                return response()->json(array('status' => 'fail'));
            }
            if( $admin[0]['PASSWORD'] != md5($request->input('old_password')) ) {
                return response()->json(array('status' => 'fail',
                                                'error_msg' => '以前のパスワードが合いません。'));
            }
            Admin::where("ID", $request->session()->get('id'))
                ->update( array("PASSWORD" => md5($request->input('new_password')) ));
        }
        else {
            $admin = User::where(array('ID' => $request->session()->get('id')))->get()->toArray();
            if(count($admin) < 1) {
                return response()->json(array('status' => 'fail'));
            }
            if( $admin[0]['PASSWORD'] != md5($request->input('old_password')) ) {
                return response()->json(array('status' => 'fail',
                                                'error_msg' => '以前のパスワードが合いません。'));
            }
            User::where("ID", $request->session()->get('id'))
                ->update( array("PASSWORD" => md5($request->input('new_password')) ));
        }
        return response()->json(array('status' => 'success'));
    }
    function saveSetting(Request $request) {
        //params['withdraw_fee'] = $("#withdraw_fee").val();
        //params['transaction_limit'] = $("#transaction_limit").val();
        $params['affiliate_fee'] = $request->input("affiliate_fee");
        $params['transaction_limit'] = $request->input("transaction_limit");
        $params['fee'] = $request->input("fee");
        foreach($params as $key => $value) {
            $ret = Setting::where(array('del_yn' => 'N' , 'VARIABLE' => $key))->get()->toArray();
            if(count($ret) < 1) {
                $setting = new Setting();
                $setting->CREATE_TIME = time();
                $setting->UPDATE_TIME = time();
                $setting->VARIABLE = $key;
                $setting->VALUE = $value;
                $setting->save();
            }
            else {
                Setting::where("VARIABLE", $key)
                ->update(array("VALUE" => $value));
            }
        }
        return response()->json(array('status' => 'success'));
    }
    function logout(Request $request) {
        $request->session()->forget('is_login');
        $request->session()->forget('admin_id');
        $request->session()->forget('id');
        return redirect('/login');
    }
    public function faq() {
        $faq_category = FaqCategory::where(array('DEL_YN' => 'N'))
        ->orderBy('ORDER')
        ->get();
        return $this->load_view('admin/faqs', 'FAQ', 'ユーザーサポート', array('category_list' => $faq_category));
    }
    public function faq_category() {
        return $this->load_view('admin/faqs_category', 'Faq カテゴリ', 'ユーザーサポート', array());
    }
    public function terms_service() {
        $ret = Setting::where(array('del_yn' => 'N' , 'VARIABLE' => 'terms_service'))->get()->toArray();
        $html = '';
        if(count($ret) > 0) {
            $html = $ret[0]['VALUE'];
        }
        return $this->load_view('admin/terms_service', 'terms of service', 'ユーザーサポート', array('terms_service' => $html));
    }
    public function profile() {
        return $this->load_view('user/profile', '暗号変更', '', array());
    }
    public function storeTermsService(Request $request) {
        $content = $request->input('content');
        $ret = Setting::where(array('del_yn' => 'N' , 'VARIABLE' => 'terms_service'))->get()->toArray();
        if (count($ret) > 0) {
            Setting::where("VARIABLE", 'terms_service')
                ->update(array("VALUE" => $content));
        }
        else {
            $setting = new Setting();
            $setting->CREATE_TIME = time();
            $setting->UPDATE_TIME = time();
            $setting->VARIABLE = 'terms_service';
            $setting->VALUE = $content;
            $setting->save();
        }
        return redirect('/terms_service');
    }
    public function getCategoryList(Request $request) {
        $ret = array();
        $query = FaqCategory::select('faq_category.*');
        $query = $query->where('faq_category.DEL_YN', '=', 'N');
        $query = $query->orderBy('faq_category.ORDER', 'asc');
        $is_tail_data = false;
        if (null === $request->input('type')) {
            $query = $query->offset($request->input("id"))
                ->limit(10);
            $temp_query = FaqCategory::select('faq_category.*');
            $temp_query = $temp_query->where('faq_category.DEL_YN', '=', 'N');
            $temp_query = $temp_query->orderBy('faq_category.ORDER', 'asc');
            $temp_query = $temp_query->offset( intval($request->input("id")) + 10 )->limit(10);
            $temp_result = $temp_query->get();
            if(count($temp_result) < 1)
                $is_tail_data = false;
            else
                $is_tail_data = true;
        }
        $result = $query->get();
        foreach ($result as $item) {
            $table_item = array(
                'id' => $item->ID,
                'time' =>  $item->CREATE_TIME,
                'name' => $item->NAME,
                'order' => $item->ORDER
            );
            $ret[] = $table_item;
        }
        return response()->json(array('data'=> $ret, 'status' => 'success', 'is_tail_data' => $is_tail_data));
    }

    public function getFaqList(Request $request) {
        $ret = array();
        $query = Faq::select('faqs.*')
        ->leftJoin('faq_category', 'faqs.TYPE', '=', 'faq_category.ID');
        $query = $query->where('faqs.DEL_YN', '=', 'N');
        $query = $query->where('faq_category.DEL_YN', '=', 'N');
        $query = $query->orderBy('faq_category.ORDER', 'asc');
        $query = $query->orderBy('faqs.ORDER', 'asc');
        $is_tail_data = false;
        if (null === $request->input('type')) {
            $query = $query->offset($request->input("id"))
                ->limit(10);
            $temp_query = Faq::select('faqs.*')
            ->leftJoin('faq_category', 'faqs.TYPE', '=', 'faq_category.ID');

            $temp_query = $temp_query->where('faqs.DEL_YN', '=', 'N');
            $temp_query = $temp_query->where('faq_category.DEL_YN', '=', 'N');
            $temp_query = $temp_query->orderBy('faq_category.ORDER', 'asc');
            $temp_query = $temp_query->orderBy('faqs.ORDER', 'asc');

            $temp_query = $temp_query->offset( intval($request->input("id")) + 10 )->limit(10);

            $temp_result = $temp_query->get();
            if(count($temp_result) < 1)
                $is_tail_data = false;
            else
                $is_tail_data = true;
        }
        $result = $query->get();
        foreach ($result as $item) {
            $table_item = array(
                'id' => $item->ID,
                'time' =>  $item->CREATE_TIME,
                'category' => $item->category->NAME,
                'question' => $item->QUESTION,
                'order' => $item->ORDER
            );
            $ret[] = $table_item;
        }
        return response()->json(array('data'=> $ret, 'status' => 'success', 'is_tail_data' => $is_tail_data));
    }

    public function faqCategoryCreate(Request $request) {
        $id = $request->input("ID");
        if (!($id == "" || !isset($id))) {
            $category = FaqCategory::where('id', $id)->update(array(
                'NAME' => $request->input('NAME'),
                'ORDER' => $request->input('ORDER'),
                'UPDATE_TIME' => time()
            ));
        }
        else {
            $bot = new FaqCategory();
            $bot->UPDATE_TIME = time();
            $bot->CREATE_TIME = time();
            $bot->NAME = $request->input('NAME');
            $bot->ORDER = $request->input('ORDER');
            $bot->save();
        }
        return response()->json(array('status' => 'success'));
    }
    public function faqCreate(Request $request) {
        $id = $request->input("ID");
        if (!($id == "" || !isset($id))) {
            $faq = Faq::where('id', $id)->update(array(
                'QUESTION' => $request->input('QUESTION'),
                'ORDER' => $request->input('ORDER'),
                'TYPE' => $request->input('TYPE'),
                'ANSWER' => $request->input('ANSWER'),
                'UPDATE_TIME' => time()
            ));
        }
        else {
            $faq = new Faq();
            $faq->UPDATE_TIME = time();
            $faq->CREATE_TIME = time();
            $faq->TYPE = $request->input('TYPE');
            $faq->QUESTION = $request->input('QUESTION');
            $faq->ANSWER = $request->input('ANSWER');
            $faq->ORDER = $request->input('ORDER');
            $faq->save();
        }
        return response()->json(array('status' => 'success'));
    }
    public function faqCategoryRemove(Request $request) {
        $id = $request->input("id");
        $category = FaqCategory::where('id', $id)->update(array(
            'DEL_YN' => 'Y',
            'UPDATE_TIME' => time()
        ));
        return response()->json(array('status' => 'success'));
    }
    public function faqRemove(Request $request) {
        $id = $request->input("id");
        $category = Faq::where('id', $id)->update(array(
            'DEL_YN' => 'Y',
            'UPDATE_TIME' => time()
        ));
        return response()->json(array('status' => 'success'));
    }
    public function faqCategoryDetail(Request $request) {
        $id = $request->input('id');
        $detail = FaqCategory::find($id);
        return response()->json(array('status' => 'success', 'data'=>$detail));
    }
    public function faqDetail(Request $request) {
        $id = $request->input('id');
        $detail = Faq::find($id);
        return response()->json(array('status' => 'success', 'data'=>$detail));
    }
}
