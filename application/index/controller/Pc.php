<?php
namespace app\index\controller;

use think\Controller;
use think\Session;
use think\Db;

class Pc extends Controller {
  public function index() {
    $this->redirect('http://clgl.swpu.edu.cn/public/pc');
  }
}