<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;

class Index extends Controller
{
  public function index(){
    return $this->fetch('index');
  }

  public function welcome() {
    return $this->fetch('welcome');
  }
}
