<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Session;

class Index extends Controller
{
  public function _intialize() {
    Session::set('admin_no', 'admin');
    Session::set('admin_name', '开发');
  }
  
  // 渲染首页
  public function index(){
    return $this->fetch('index');
  }

  // 渲染欢迎页
  public function welcome() {
    return $this->fetch('welcome');
  }
}
