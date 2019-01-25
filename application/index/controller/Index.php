<?php
namespace app\index\controller;

use think\Controller;
use think\Session;
use think\Db;

class Index extends Controller
{
  // 错误路径处理
  // public function _empty() {
  //   $this->error('404!路径不存在.');
  // }

  // 渲染首页
  public function wx_index() {
    return $this->fetch('wx_index', [
      'title' => '首页',
      'emp_no' => Session::get('emp_no'),
    ]);
  }

  // 渲染登录页面
  public function wx_login() {
    // if (Session::has('emp_no')) {
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
    $emp_no = $_POST['emp_no'];
    $psw = $_POST['psw'];
    $emp_info = Db::table('employee_list')->where('emp_no', $emp_no)->find();
    if (!$emp_info) {
      return $this->error('账号不存在！');
    }
    if ($psw == $emp_info['psw']) {
      Session::set('emp_no', $emp_no);
      Session::set('emp_name', $emp_info['emp_name']);
      Session::set('emp_level', $emp_info['emp_level']);
      $this->success('登录成功！', url('wx_index'));
    } else {
      $this->error('密码错误！');
    }
  }
}
