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
    $is_id_card_exist = Db::table('employee_list')->where('id_card', $data['id_card'])->find();
    if ($is_id_card_exist) $this->error('新增失败，该身份证号已存在。');
    $data['psw'] = 'swpu'.substr($data['id_card'], -4);
    $data['reg_time'] = date('Y-m-d H:i:s');
    $res = Db::table('employee_list')->insert($data);
    if ($res == 1) {
      $this->success('新增成功！');
    } else {
      $this->error('新增失败，请刷新页面重试。');
    }
  }

}
