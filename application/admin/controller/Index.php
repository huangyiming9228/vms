<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Session;

class Index extends Controller
{
  // public function _initialize() {
  //   Session::set('admin_no', 'admin');
  //   Session::set('admin_name', '开发');
  // }

  // 渲染首页
  public function index(){
    if (!Session::has('admin_no')) {
      $this->redirect(BACK_LOGIN_URL);
    }
    return $this->fetch('index');
  }

  // 渲染欢迎页
  public function welcome() {
    return $this->fetch('welcome');
  }

  // 渲染登陆页面
  public function login() {
    return $this->fetch('login');
  }

  // 登录验证
  public function login_check() {
    $admin_no = $_POST['admin_no'];
    $admin_psw = $_POST['admin_psw'];
    $admin_info = Db::table('admin_user')->where('admin_no', $admin_no)->find();
    if (!$admin_info) {
      return $this->error('账号不存在！');
    }
    if ($admin_psw == $admin_info['admin_psw']) {
      Session::set('admin_no', $admin_info['admin_no']);
      Session::set('admin_name', $admin_info['admin_name']);
      $this->success('登录成功！', url('index'));
    } else {
      $this->error('密码错误！');
    }
  }
}
