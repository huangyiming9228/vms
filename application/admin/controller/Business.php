<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Session;

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

}
