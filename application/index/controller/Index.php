<?php
namespace app\index\controller;

use think\Controller;
use think\Session;
use think\Db;

class Index extends Controller
{
  // 渲染首页
  public function wx_index() {
    return $this->fetch('wx_index', [
      'title' => '首页',
      'account' => Session::get('account'),
    ]);
  }

  // 渲染登录页面
  public function wx_login() {
    return $this->fetch('wx_login', [
      'title' => '登录',
    ]);
  }

  // 登录验证
  public function login_check() {
    $account = $_POST['account'];
    $psw = $_POST['psw'];
    if ($account == 'admin' && $psw == 'admin') {
      Session::set('account', $account);
      $this->success('登录成功！', url('wx_index'));
    } else {
      return false;
    }
  }
}
