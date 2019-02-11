<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Session;

class Export extends Controller
{
  // 渲染预约数据导出页面
  public function reservation_export() {
    return $this->fetch('reservation_export', [
      'title' => '导出预约数据',
    ]);
  }
}
