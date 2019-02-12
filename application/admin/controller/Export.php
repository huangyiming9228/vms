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

  // 查询预约记录列表
  public function query_reservation_list($status, $start_time, $end_time) {
    $conditions = [];
    if ($status != '99') $conditions['status'] = $status;
    $conditions['visit_time'] = ['between time', [$start_time, $end_time]];
    return Db::table('reservation_list')->where($conditions)->order('visit_time', 'asc')->select();
  }

}
