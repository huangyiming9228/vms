<?php
namespace app\index\controller;

use think\Controller;
use think\Session;
use think\Db;

class Pc extends Controller {
  public function index() {
    $this->redirect('http://clgl.swpu.edu.cn/public/pc');
  }

  public function get_menus($emp_no) {
    $emp_level = Db::table('employee_list')->where('emp_no', $emp_no)->value('emp_level');
    $menus = Db::table('pc_menu_seek')->where('level', $emp_level)->select();
    $menus_info = [];
    foreach ($menus as $key => $value) {
      $res = Db::table('pc_menus')->where('id', $value['menu_id'])->find();
      array_push($menus_info, $res);
    }
    return $menus_info;
  }
}