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
  public function wx_reservation() {
    return $this->fetch('wx_reservation', [
      'title' => '车辆预约',
      'emp_no' => Session::get('emp_no'),
      'rest_time' => $this->get_rest_reservation_time()
    ]);
  }

  // 渲染微信临时预约页面
  public function wx_temp_reservation() {
    return $this->fetch('wx_temp_reservation', [
      'title' => '车辆预约',
      'emp_no' => Session::get('emp_no'),
      'rest_time' => $this->get_rest_reservation_time()
    ]);
  }

  // 获取用户剩余预约次数
  public function get_rest_reservation_time() {
    $emp_level = Session::get('emp_level');
    $availabel_time = Db::table('reservation_permission_list')->where('level', $emp_level)->value('reservation_time');
    $start_time = date('Y-m-d').' 00:00:00';
    $end_time = date('Y-md').' 24:00:00';
    $already_time = Db::table('reservation_list')->where('submitter_no', Session::get('emp_no'))
                                                 ->where('submit_time', '>', $start_time)
                                                 ->where('submit_time', '<', $end_time)
                                                 ->count();
    $rest_time = $emp_level == 3 ? '无限': ($availabel_time - $already_time);
    return $rest_time;
  }

  // 获取用户可预约天数
  public function get_reservation_days() {
    $emp_level = Session::get('emp_level');
    $reservation_days = Db::table('reservation_permission_list')->where('level', $emp_level)->value('reservation_days');
    return $reservation_days;
  }

  // 渲染微信查看记录页面
  public function wx_reservation_record() {
    return $this->fetch('wx_reservation_record', [
      'title' => '预约记录',
      'emp_no' => Session::get('emp_no'),
    ]);
  }

  // 渲染微信记录详情页面
  public function wx_reservation_detail($id) {
    return $this->fetch('wx_reservation_detail', [
      'title' => '记录详情',
      'id' => $id,
    ]);
  }

  /*
   * @desc 保存预约数据
   * @status 0 - 未审核
   *         1 - 审核通过
   *         2 - 审核未通过
   */
  public function save_reservation() {
    $start_time = date('Y-m-d').' 00:00:00';
    $end_time = date('Y-md').' 24:00:00';
    $availabel_time = Db::table('reservation_permission_list')->where('level', Session::get('emp_level'))->value('reservation_time');
    $already_time = Db::table('reservation_list')->where('submitter_no', Session::get('emp_no'))
      ->where('submit_time', '>', $start_time)
      ->where('submit_time', '<', $end_time)
      ->count();
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
      $this->success('提交成功！请耐心等待审核。', url('index/index/wx_index'));
    } else {
      $this->error('服务器错误，提交失败！');
    }
  }

  public function save_reservation_driver() {
    $start_time = date('Y-m-d').' 00:00:00';
    $end_time = date('Y-md').' 24:00:00';
    $availabel_time = Db::table('reservation_permission_list')->where('level', Session::get('emp_level'))->value('reservation_time');
    $already_time = Db::table('reservation_list')->where('submitter_no', Session::get('emp_no'))
      ->where('submit_time', '>', $start_time)
      ->where('submit_time', '<', $end_time)
      ->count();
    if ($already_time >= $availabel_time) {
      return $this->error('预约失败！您今天的预约次数已用完，请明天再预约。');
    }
    $data = $_POST;
    $data['status'] = 1;
    $data['submitter_no'] = Session::get('emp_no');
    $data['submitter'] = Session::get('emp_name');
    $data['submit_time'] = Date('Y-m-d H:i:s');
    $data['audit_time'] = Date('Y-m-d H:i:s');
    $data['audit_person'] = 'system';
    $data['audit_person_no'] = 'system';
    $data['audit_remark'] = '通过';
    $res = Db::table('reservation_list')->insert($data);
    if ($res) {
      $this->success('提交成功！', url('index/business/wx_reservation'));
    } else {
      $this->error('服务器错误，提交失败！');
    }
  }

  /*
   * @desc 保存预约数据
   * @status 0 - 未审核
   *         1 - 审核通过
   *         2 - 审核未通过
   */
  public function save_temp_reservation() {
    $data = $_POST;
    $data['status'] = 0;
    $data['submit_time'] = Date('Y-m-d H:i:s');
    $data['visit_time'] = Date('Y-m-d').' '.$data['visit_time'].':00';
    $data['leave_time'] = Date('Y-m-d').' '.$data['leave_time'].':00';
    $res = Db::table('temp_reservation_list')->insert($data);
    if ($res) {
      $this->success('提交成功！', url('index/index/wx_index'));
    } else {
      $this->error('服务器错误，提交失败！');
    }
  }

  // 获取预约记录列表
  public function get_reservation_list($emp_no) {
    return Db::table('reservation_list')
              ->where('submitter_no', $emp_no)
              ->order('visit_time', 'desc')
              ->select();
  }

  // 获取预约记录详情
  public function get_reservation_detail($id) {
    return Db::table('reservation_list')->where('id', $id)->find();
  }

  // 渲染微信年审页面
  public function wx_mot_test() {
    return $this->fetch('wx_mot_test', [
      'title' => '年审',
      'emp_no' => Session::get('emp_no'),
    ]);
  }

  // 保存图片，返回file_id
  public function image_upload() {
    $file = request()->file('image');
    $ext_allow = 'jpg,gif,png,jpeg';
    $save_path = IMAGE_PATH.date('Y-m').'/';
    $info = $file->validate(['ext' => $ext_allow])->rule('uniqid')->move($save_path);
    if ($info) {
      $image_data = [
        'save_path' => $save_path,
        'file_name' => $info->getFilename(),
        'create_time' => date('Y-m-d H:i:s'),
        'is_used' => 0,
      ];
      return Db::table('image_list')->insertGetId($image_data);
    } else {
      Log::record($file->getError());
      return false;
    }
  }

  /*
   * @desc 保存年审数据
   * @status 0 - 未审核
   *         1 - 审核通过
   *         2 - 审核未通过
   */
  public function save_mot_test() {
    $temp_data = $_POST['data'];
    $data = [];
    foreach ($temp_data as $key => $value) {
      $data[$value['name']] = $value['value'];
    }
    $images = $_POST['images'];
    try {
      foreach ($images as $key => $value) {
        Db::table('image_list')->where('id', $value)->update(['is_used' => 1]);
      }
    } catch (\Throwable $th) {
      //throw $th;
      $this->error('服务器错误，提交失败！');
    }

    $data['status'] = 0;
    $data['submitter'] = Session::get('emp_name');
    $data['submitter_no'] = Session::get('emp_no');
    $data['submit_time'] = date('Y-m-d H:i:s');
    $res = Db::table('mot_test_list')->insert($data);
    if ($res) {
      $this->success('提交成功！请耐心等待审批。', url('index/index/wx_index'));
    } else {
      $this->error('服务器错误，提交失败！');
    }
  }

  // 渲染年审记录页面
  public function wx_mot_test_record() {
    return $this->fetch('wx_mot_test_record', [
      'title' => '年审记录',
      'emp_no' => Session::get('emp_no'),
    ]);
  }

  // 获取年审记录列表
  public function get_moe_test_list($emp_no) {
    return Db::table('mot_test_list')
            ->where('submitter_no', $emp_no)
            ->order('submit_time', 'desc')
            ->select();
  }

  // 渲染年审记录详情页面
  public function wx_mot_test_detail($id) {
    return $this->fetch('wx_mot_test_detail', [
      'title' => '年审记录详情',
      'id' => $id,
    ]);
  }

  // 获取年审记录详情
  public function get_mot_test_detail($id) {
    return Db::table('mot_test_list')->where('id', $id)->find();
  }

  public function get_emp_level($emp_no) {
    return Db::table('employee_list')->where('emp_no', $emp_no)->value('emp_level');
  }

  public function get_emp_info($emp_no) {
    return Db::table('employee_list')->where('emp_no', $emp_no)->find();
  }

}
