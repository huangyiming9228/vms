<?php
namespace app\index\controller;

use think\Controller;
use think\Session;
use think\Db;

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
      'account' => Session::get('emp_no'),
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

  /*
   * @desc 保存预约数据
   * @status 0 - 未审核
   *         1 - 审核通过
   *         2 - 审核未通过
   */
  public function save_reservation() {
    $data = $_POST;
    $data['status'] = 0;
    $data['submitter_no'] = Session::get('emp_no');
    // $data['submitter'] = Session::get('user_name');
    $data['submitter'] = 'admin';
    $data['submit_time'] = Date('Y-m-d H:i:s');
    $res = Db::table('reservation_list')->insert($data);
    if ($res) {
      $this->success('提交成功，请耐心等待审核。', url('index/index/wx_index'));
    } else {
      $this->error('预约失败！您今天已没有预约次数了，请明天再预约。');
    }
  }
}
