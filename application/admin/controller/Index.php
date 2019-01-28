<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;

class Index extends Controller
{
  // 渲染首页
  public function index(){
    return $this->fetch('index');
  }

  // 渲染欢迎页
  public function welcome() {
    return $this->fetch('welcome');
  }
}
