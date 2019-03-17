<?php
namespace app\index\controller;

use think\Controller;
use think\Session;
use think\Db;
use think\Log;

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
      'emp_level' => Session::get('emp_level'),
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

  public function login_check_driver() {
    $emp_no = $_POST['emp_no'];
    $psw = $_POST['psw'];
    $emp_info = Db::table('driver_list')->where('emp_no', $emp_no)->find();
    $activity_status = Db::table('activity_list')->where('id', $emp_info['activity_id'])->value('status');
    if (!$emp_info) {
      return $this->error('账号不存在！');
    }
    Log::record($activity_status);
    if ($activity_status != 1) {
      return $this->error('账号不存在！');
    }
    if ($psw == $emp_info['psw']) {
      Session::set('emp_no', $emp_no);
      Session::set('emp_name', $emp_info['emp_name']);
      Session::set('emp_level', $emp_info['emp_level']);
      $this->success('登录成功！', url('index/business/wx_reservation'));
    } else {
      $this->error('密码错误！');
    }
  }

  public function get_muens() {
    $emp_level = Session::get('emp_level');
    $menus = Db::table('wx_menu_seek')->where('level', $emp_level)->select();
    $menus_info = [];
    foreach ($menus as $key => $value) {
      $res = Db::table('wx_menus')->where('id', $value['menu_id'])->find();
      array_push($menus_info, $res);
    }
    return $menus_info;
  }
}
