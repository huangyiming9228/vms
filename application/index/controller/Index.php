<?php
namespace app\index\controller;

use think\Controller;
use think\Session;
use think\Db;

class Index extends Controller
{
  // 错误路径处理
  public function _empty() {
    $this->error('404!路径不存在.');
  }

  // 渲染首页
  public function wx_index() {
    return $this->fetch('wx_index', [
      'title' => '首页',
      'user_account' => Session::get('user_account'),
    ]);
  }

  // 渲染登录页面
  public function wx_login() {
    // if (Session::has('user_account')) {
    //   $this->redirect('wx_index');
    // } else {
    //   return $this->fetch('wx_login', [
    //     'title' => '登录',
    //   ]);
    // }
    return $this->fetch('wx_login', [
      'title' => '登录',
    ]);
  }

  // 登录验证
  public function login_check() {
    $account = $_POST['account'];
    $psw = $_POST['psw'];
    if ($account == 'admin' && $psw == 'admin') {
      Session::set('user_account', $account);
      $this->success('登录成功！', url('wx_index'));
    } else {
      return false;
    }
  }
}
