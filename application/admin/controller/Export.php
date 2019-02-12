<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Export extends Controller
{
  // 渲染预约数据导出页面
  public function reservation_export() {
    return $this->fetch('reservation_export', [
      'title' => '导出预约数据',
    ]);
  }

  // 查询预约记录列表
  public function query_reservation_list($status, $start_time, $end_time) {
    $conditions = [];
    if ($status != '99') $conditions['status'] = $status;
    $conditions['visit_time'] = ['between time', [$start_time, $end_time]];
    return Db::table('reservation_list')->where($conditions)->order('visit_time', 'asc')->select();
  }

  public function test() {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'Hello World !');
    $filename = 'helloworld.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$filename.'"');
    header('Cache-Control: max-age=0');
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
  }

}
