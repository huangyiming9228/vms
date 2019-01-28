<?php
namespace app\index\controller;

use think\Controller;
use think\Session;
use think\Db;
use think\Log;

class Business extends Controller
{
  // 控制器初始化
  public function _initialize() {
    if (!Session::has('emp_no')) {
      $this->error('登录过期，请重新登录！');
    }
  }

  // 渲染微信预约页面
  public function wx_cars_reservations() {
    return $this->fetch('wx_cars_reservations', [
      'title' => '车辆预约',
      'emp_no' => Session::get('emp_no'),
      'rest_time' => $this->get_rest_reservation_time()
    ]);
  }

  // 获取用户剩余预约次数
  private function get_rest_reservation_time() {
    $emp_level = Session::get('emp_level');
    $availabel_time = Db::table('reservation_permission_list')->where('level', $emp_level)->value('reservation_time');
    $already_time = Db::table('reservation_list')->where('submitter_no', Session::get('emp_no'))->count();
    $rest_time = $emp_level == 3 ? '无限': ($availabel_time - $already_time);
    return $rest_time;
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
      'emp_no' => Session::get('emp_no'),
    ]);
  }

  /*
   * @desc 保存预约数据
   * @status 0 - 未审核
   *         1 - 审核通过
   *         2 - 审核未通过
   */
  public function save_reservation() {
    $availabel_time = Db::table('reservation_permission_list')->where('level', Session::get('emp_level'))->value('reservation_time');
    $already_time = Db::table('reservation_list')->where('submitter_no', Session::get('emp_no'))->count();
    if ($already_time >= $availabel_time) {
      return $this->error('预约失败！您今天的预约次数已用完，请明天再预约。');
    }
    $data = $_POST;
    $data['status'] = 0;
    $data['submitter_no'] = Session::get('emp_no');
    $data['submitter'] = Session::get('emp_name');
    $data['submit_time'] = Date('Y-m-d H:i:s');
    $res = Db::table('reservation_list')->insert($data);
    if ($res) {
      $this->success('提交成功，请耐心等待审核。', url('index/index/wx_index'));
    } else {
      $this->error('服务器错误，提交失败！');
    }
  }

  // 获取预约记录
  public function get_reservation_list($emp_no) {
    return Db::table('reservation_list')
              ->where('submitter_no', $emp_no)
              ->order('visit_time', 'desc')
              ->select();
  }
}
