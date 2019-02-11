<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Session;

class System extends Controller
{
  // 渲染权限管理页面
  public function permission_manage() {
    return $this->fetch('permission_manage', [
      'title' => '权限管理',
      'modal_permission_update' => 1,
    ]);
  }

  // 获取权限角色列表
  public function get_role_list() {
    return Db::table('reservation_permission_list')->select();
  }

  // 更新角色预约次数
  public function update_reservation_time() {
    $res = Db::table('reservation_permission_list')->where('id', $_POST['id'])->update(['reservation_time' => $_POST['reservation_time']]);
    if ($res == 1) {
      $this->success('操作成功！');
    } else {
      $this->error('未修改预约次数。');
    }
  }
}
