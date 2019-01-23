<?php
namespace app\index\controller;

use think\Controller;
use think\Session;
use think\Db;

class Business extends Controller
{
  // 渲染微信预约页面
  public function wx_cars_reservations() {
    return $this->fetch('wx_cars_reservations', [
      'title' => '车辆预约',
    ]);
  }

  // 渲染微信年审页面
  public function wx_mot_test() {
    return $this->fetch('wx_mot_test', [
      'title' => '年审',
    ]);
  }

  // 渲染微信查看记录页面
  public function wx_book_record() {
    return $this->fetch('wx_book_record', [
      'title' => '预约记录',
    ]);
  }
}
