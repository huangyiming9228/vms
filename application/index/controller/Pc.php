<?php
namespace app\index\controller;

use think\Controller;
use think\Session;
use think\Db;

class Pc extends Controller {
  public function index() {
    return $this->fetch('index');
  }
}