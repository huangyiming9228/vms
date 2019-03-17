<?php
namespace app\index\controller;

use think\Controller;
use think\Session;
use think\Db;
use think\Log;

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

  public function save_activity() {
    $data = $_POST;
    $driver_list = $data['driver_list'];
    unset($data['driver_list']);
    $emp_info = Db::table('employee_list')->where('emp_no', $data['submitter_no'])->find();
    $data['submitter'] = $emp_info['emp_name'];
    $data['emp_unit'] = $emp_info['emp_unit'];
    $data['submit_time'] = date('Y-m-d H:i:s');
    $data['status'] = 0;
    $activity_id = Db::table('activity_list')->insertGetId($data);
    foreach ($driver_list as $key => $value) {
      $driver_list[$key]['name'] = $emp_info['emp_name'];
      $driver_list[$key]['start_date'] = $data['start_date'];
      $driver_list[$key]['end_date'] = $data['end_date'];
      $driver_list[$key]['reason'] = $data['activity_name'];
      $driver_list[$key]['activity_id'] = $activity_id;
    }
    $res = Db::table('driver_list')->insertAll($driver_list);
    if (!$res) {
      Db::table('activity_list')->where('id', $activity_id)->delete();
    }
    return $res;
  }

  public function get_activity_list($emp_no) {
    return Db::table('activity_list')->where('submitter_no', $emp_no)->select();
  }
}