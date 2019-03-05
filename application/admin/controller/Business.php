<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Session;
use think\Log;

class Business extends Controller
{
  // 渲染预约审核页面
  public function reservation_handle(){
    return $this->fetch('reservation_handle', [
      'title' => '预约审核',
      'modal_reservation_handle' => 1,
    ]);
  }

  // 查询预约列表
  public function query_reservations() {
    $conditions = [];
    if ($_GET['status'] != '99') $conditions['status'] = $_GET['status'];
    if ($_GET['submitter_no']) $conditions['submitter_no'] = $_GET['submitter_no'];
    if ($_GET['name']) $conditions['name'] = $_GET['name'];
    return Db::table('reservation_list')->where($conditions)->order('submit_time', 'desc')->select();
  }

  // 获取单条预约记录
  public function get_reservation_data($id) {
    return Db::table('reservation_list')->where('id', $id)->find();
  }

  // 处理预约记录
  public function handle_reservation() {
    $data = $_POST['auditData'];
    $id = $data['id'];
    unset($data['id']);
    $data['audit_time'] = date('Y-m-d H:i:s');
    $data['audit_person'] = Session::get('admin_name');
    $data['audit_person_no'] = Session::get('admin_no');
    return Db::table('reservation_list')->where('id', $id)->update($data);
  }

  // 渲染年审审核页面
  public function mot_test_handle() {
    return $this->fetch('mot_test_handle', [
      'title' => '年审审核',
    ]);
  }

  // 查询年审列表
  public function query_mot_test() {
    $conditions = [];
    if ($_GET['status'] != '99') $conditions['status'] = $_GET['status'];
    if ($_GET['submitter_no']) $conditions['submitter_no'] = $_GET['submitter_no'];
    if ($_GET['applicant']) $conditions['applicant'] = $_GET['applicant'];
    return Db::table('mot_test_list')->where($conditions)->order('submit_time', 'desc')->select();
  }

  // 渲染年审操作页面
  public function mot_test_operation($id) {
    return $this->fetch('mot_test_operation', [
      'title' => '年审操作',
      'id' => $id,
    ]);
  }

  // 获取年审记录信息
  public function get_mot_test_detail($id) {
    $base_data = Db::table('mot_test_list')->where('id', $id)->find();

    // 获取图片
    $images_url['driving_license_url'] = $base_data['driving_license_id'] ? $this->get_image_url($base_data['driving_license_id']) : $base_data['driving_license_id'];
    $images_url['campus_card_url'] = $base_data['campus_card_id'] ? $this->get_image_url($base_data['campus_card_id']) : $base_data['campus_card_id'];
    $images_url['relationship_proof_url'] = $base_data['relationship_proof_id'] ? $this->get_image_url($base_data['relationship_proof_id']) : $base_data['relationship_proof_id'];
    $images_url['payment_proof_url'] = $base_data['payment_proof_id'] ? $this->get_image_url($base_data['payment_proof_id']) : $base_data['payment_proof_id'];
    $images_url['loan_agreement_url'] = $base_data['loan_agreement_id'] ? $this->get_image_url($base_data['loan_agreement_id']) : $base_data['loan_agreement_id'];
    return [
      'base_data' => $base_data,
      'images_url' => $images_url,
    ];
  }

  // 获取图片url
  public function get_image_url($image_id) {
    $res = Db::table('image_list')->where('id', $image_id)->field(['concat(save_path, file_name)' => 'url'])->find();
    return SITE_URL.$res['url'];
  }

  // 处理年审
  public function handle_mot_test() {
    $data = $_POST['auditData'];
    $id = $data['id'];
    unset($data['id']);
    $data['audit_time'] = date('Y-m-d H:i:s');
    $data['audit_person'] = Session::get('admin_name');
    $data['audit_person_no'] = Session::get('admin_no');
    return Db::table('mot_test_list')->where('id', $id)->update($data);
  }

  // 新增活动
  public function activity_apply() {
    return $this->fetch('activity_apply', [
      'title' => '新增活动',
      'modal_activity_add' => 1,
    ]);
  }

  // 查询活动
  public function query_activitys() {
    return Db::table('activity_list')->where('submitter_no', Session::get('admin_no'))
      ->order('submit_time', 'desc')
      ->select();
  }

  // 保存活动
  public function save_activity() {
    $data = $_POST;
    $tel_arr = $data['tel_arr'];
    unset($data['tel_arr']);
    $data['submitter'] = Session::get('admin_name');
    $data['submitter_no'] = Session::get('admin_no');
    $data['submit_time'] = date('Y-m-d H:i:s');
    $data['status'] = 0;
    $activity_id = Db::table('activity_list')->insertGetId($data);

    foreach ($tel_arr as $key => $value) {
      $tel_arr[$key]['emp_no'] = $value['tel'];
      $tel_arr[$key]['emp_name'] = $value['tel'];
      $tel_arr[$key]['psw'] = substr($value['tel'], 5, 6);
      $tel_arr[$key]['emp_level'] = 1;
      $tel_arr[$key]['activity_id'] = $activity_id;
    }
    $res = Db::table('driver_list')->insertAll($tel_arr);
    if (!$res) {
      Db::table('activity_list')->where('id', $activity_id)->delete();
    }
    return $res;
  }

  // 活动审批
  public function activity_audit() {
    return $this->fetch('activity_audit', [
      'title' => '活动审批',
    ]);
  }

  // 查询所有活动
  public function query_all_activiitys() {
    return Db::table('activity_list')->order('submit_time', 'desc')->select();
  }

  // 审批活动
  public function audit_activity() {
    $data = $_POST;
    $id = $data['id'];
    unset($data['id']);
    $data['audit_time'] = date('Y-m-d H:i:s');
    $data['audit_person'] = Session::get('admin_name');
    $data['audit_person_no'] = Session::get('admin_no');
    return Db::table('activity_list')->where('id', $id)->update($data);
  }

}
