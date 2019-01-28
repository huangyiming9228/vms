<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;

class Business extends Controller
{
  // 渲染预约审核页面
  public function reservation_handle(){
    return $this->fetch('reservation_handle', [
      'title' => '预约审核',
    ]);
  }

  public function get_reservation_list() {
    return Db::table('reservation_list')->select();
  }

}
