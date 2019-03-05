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
    $res = Db::table('reservation_permission_list')->where('id', $_POST['id'])->update([
      'reservation_time' => $_POST['reservation_time'],
      'reservation_days' => $_POST['reservation_days'],
    ]);
    if ($res == 1) {
      $this->success('操作成功！');
    } else {
      $this->error('未修改。');
    }
  }
  
  // 渲染用户管理页面
  public function user_manage() {
    return $this->fetch('user_manage', [
      'title' => '用户管理',
      'modal_user_edit' => 1,
      'modal_user_add' => 1,
    ]);
  }

  // 查询用户列表
  public function query_users() {
    $conditions = [];
    if ($_GET['emp_no']) $conditions['emp_no'] = $_GET['emp_no'];
    if ($_GET['emp_name']) $conditions['emp_name'] = $_GET['emp_name'];
    return Db::table('employee_list')->where($conditions)->select();
  }

  // 删除用户
  public function delete_user() {
    $res = Db::table('employee_list')->where('id', $_POST['id'])->delete();
    if ($res) {
      $this->success('删除成功！');
    } else {
      $this->error('删除失败，请刷新页面重试。');
    }
  }

  // 查询用户详细信息
  public function get_user_data($id) {
    return Db::table('employee_list')->where('id', $id)->find();
  }

  // 更新用户信息
  public function update_user_info() {
    $data = $_POST;
    $id = $data['id'];
    unset($data['id']);
    $res = Db::table('employee_list')->where('id', $id)->update($data);
    if ($res == 1) {
      $this->success('修改成功！');
    } else {
      $this->error('未修改任何信息。');
    }
  }

  // 新增用户
  public function add_user() {
    $data = $_POST;
    $is_emp_no_exist = Db::table('employee_list')->where('emp_no', $data['emp_no'])->find();
    if ($is_emp_no_exist) $this->error('新增失败，该员工号已存在。');
    $data['reg_time'] = date('Y-m-d H:i:s');
    $res = Db::table('employee_list')->insert($data);
    if ($res == 1) {
      $this->success('新增成功！');
    } else {
      $this->error('新增失败，请刷新页面重试。');
    }
  }

  // 后台账号管理
  public function admin_user_manage() {
    return $this->fetch('admin_user_manage', [
      'title' => '后台账号管理',
      'modal_admin_user_add' => 1,
      'station_list' => $this->get_admin_role_list(),
    ]);
  }

  // 获取角色列表
  public function get_admin_role_list() {
    return Db::table('menu_station')->select();
  }

  // 新增后台用户
  public function add_admin_user() {
    $data = $_POST;
    $is_admin_no_exist = Db::table('admin_user')->where('admin_no', $data['admin_no'])->find();
    if ($is_admin_no_exist) $this->error('新增失败，该账号已存在。');
    $res = Db::table('admin_user')->insert($data);
    if ($res == 1) {
      $this->success('新增成功！');
    } else {
      $this->error('新增失败，请刷新页面重试。');
    }
  }

  // 查询后台用户列表
  public function query_admin_users() {
    $conditions = [];
    if ($_GET['admin_no']) $conditions['admin_no'] = $_GET['admin_no'];
    if ($_GET['admin_name']) $conditions['admin_name'] = $_GET['admin_name'];
    $list = Db::table('admin_user')->where($conditions)->select();
    foreach ($list as $key => $value) {
      $list[$key]['station'] = Db::table('menu_station')->where('station_id', $value['station_id'])->value('name');
    }
    return $list;
  }

  // 删除后台用户
  public function delete_admin_user() {
    $res = Db::table('admin_user')->where('id', $_POST['id'])->delete();
    if ($res) {
      $this->success('删除成功！');
    } else {
      $this->error('删除失败，请刷新页面重试。');
    }
  }

}
