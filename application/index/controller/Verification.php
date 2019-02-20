<?php
namespace app\index\controller;

use think\Controller;
use think\Session;
use think\Db;
use think\captcha\Captcha;

class Verification extends Controller
{
  public function test() {
    return $this->fetch('test');
  }

  public function check_captcha() {
    return captcha_check($_POST['captcha']);
  }

  public function get_captcha_img() {
    $config = [
      'length' => 4,
    ];
    $captcha = new Captcha($config);
    return $captcha->entry();
  }
}
