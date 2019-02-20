<?php
namespace app\index\controller;

use think\Controller;
use think\Session;
use think\Db;
use think\captcha\Captcha;

class Code extends Controller
{
  public function test() {
    return $this->fetch('test');
  }

  public function check_captcha() {
    return captcha_check($_POST['value']);
  }

  public function get_captcha_img() {
    $captcha = new Captcha();
    return $captcha->entry();
  }
}
