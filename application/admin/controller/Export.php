<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Session;
use think\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Export extends Controller
{
  // 渲染预约数据导出页面
  public function reservation_export() {
    return $this->fetch('reservation_export', [
      'title' => '预约数据统计',
    ]);
  }

  // 查询预约记录列表
  public function query_reservation_list($status, $start_time, $end_time) {
    $conditions = [];
    if ($status != '99') $conditions['status'] = $status;
    // $conditions['visit_date'] = ['between time', [$start_time, $end_time]];
    $result = [];
    $list = Db::table('reservation_list')->where($conditions)->select();
    foreach ($list as $key => $value) {
      $datetime = $value['visit_date'].' 00:'.$value['visit_time'];
      if ($datetime >= $start_time && $datetime <= $end_time) {
        array_push($result, $value);
      }
    }
    return $result;
  }

  // 导出预约数据excel
  public function export_reservation_excel($status, $start_time, $end_time) {
    // 获取数据
    $data = $this->query_reservation_list($status, $start_time, $end_time);

    $file_name = '预约导出数据';

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    // 单元格样式
    $centerStyle = [
      'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
      'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    ];
    $titleFontStyle = [
      'name' => '微软雅黑',
      'size' => 11,
      'bold' => true,
    ];
    $textFontStyle = [
      'name' => '微软雅黑',
      'size' => 9,
    ];
    $borderStyle = [
      'allBorders' => [
          'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
          'color' => ['argb' => 'OOOOOO'],
      ],
    ];

    // 设置表头
    $sheet->setCellValue('A1', '预约人姓名');
    $sheet->setCellValue('B1', '预约车牌号');
    $sheet->setCellValue('C1', '司机电话');
    $sheet->setCellValue('D1', '预约日期');
    $sheet->setCellValue('E1', '预约时间');
    $sheet->setCellValue('F1', '离校时间');
    $sheet->setCellValue('G1', '来校事由');
    $sheet->setCellValue('H1', '是否申请免费');
    $sheet->setCellValue('I1', '免费理由');
    $sheet->setCellValue('J1', '预约状态');
    $sheet->getStyle('A1:J1')->applyFromArray([
      'alignment' => $centerStyle,
      'font' => $titleFontStyle,
      'borders' => $borderStyle,
    ]);

    // 设置列宽、高度
    $sheet->getRowDimension('1')->setRowHeight(20);
    $sheet->getColumnDimension('A')->setWidth(17);
    $sheet->getColumnDimension('B')->setWidth(17);
    $sheet->getColumnDimension('C')->setWidth(17);
    $sheet->getColumnDimension('D')->setWidth(20);
    $sheet->getColumnDimension('E')->setWidth(17);
    $sheet->getColumnDimension('F')->setWidth(17);
    $sheet->getColumnDimension('G')->setWidth(17);
    $sheet->getColumnDimension('H')->setWidth(17);
    $sheet->getColumnDimension('I')->setWidth(17);
    $sheet->getColumnDimension('J')->setWidth(17);

    // 添加数据
    $row_index = 2;
    foreach ($data as $key => $value) {
      $sheet->setCellValue('A'.$row_index, $value['name']);
      $sheet->setCellValue('B'.$row_index, $value['car_no']);
      $sheet->setCellValue('C'.$row_index, $value['tel']);
      $sheet->setCellValue('D'.$row_index, $value['visit_date']);
      $sheet->setCellValue('E'.$row_index, $value['visit_time']);
      $sheet->setCellValue('F'.$row_index, $value['leave_time']);
      $sheet->setCellValue('G'.$row_index, $value['visit_reason']);
      $sheet->setCellValue('H'.$row_index, $value['is_apply_free'] ? '是' : '否');
      $sheet->setCellValue('I'.$row_index, $value['free_reason']);
      switch ($value['status']) {
        case 0:
          $value['status'] = '待审核';
          break;
        case 1:
          $value['status'] = '审核通过';
          break;
        case 2:
          $value['status'] = '未通过审核';
          break;
        default:
          break;
      }
      $sheet->setCellValue('J'.$row_index, $value['status']);

      $row_index++;
    }
    $row_index--;
    $sheet->getStyle('A2:J'.$row_index)->applyFromArray([
      'alignment' => $centerStyle,
      'font' => $textFontStyle,
      'borders' => $borderStyle,
    ]);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//浏览器输出07Excel文件
    // header('Content-Type:application/vnd.ms-excel');//浏览器输出Excel03版本文件
    header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');//浏览器输出文件名称
    header('Cache-Control: max-age=0');//禁止缓存
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    $spreadsheet->disconnectWorksheets();
    unset($spreadsheet);
    exit;
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

  // 渲染年审数据导出页面
  public function mot_test_export() {
    return $this->fetch('mot_test_export', [
      'title' => '年审数据统计',
    ]);
  }

  // 查询年审记录列表
  public function query_mot_test_list($status, $start_time, $end_time) {
    $conditions = [];
    if ($status != '99') $conditions['status'] = $status;
    $conditions['submit_time'] = ['between time', [$start_time, $end_time]];
    return Db::table('mot_test_list')->where($conditions)->order('submit_time', 'asc')->select();
  }

  // 导出年审数据excel
  public function export_mot_test_excel($status, $start_time, $end_time) {
    // 获取数据
    $data = $this->query_mot_test_list($status, $start_time, $end_time);

    $file_name = '年审导出数据';

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    // 单元格样式
    $centerStyle = [
      'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
      'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    ];
    $titleFontStyle = [
      'name' => '微软雅黑',
      'size' => 11,
      'bold' => true,
    ];
    $textFontStyle = [
      'name' => '微软雅黑',
      'size' => 9,
    ];
    $borderStyle = [
      'allBorders' => [
          'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
          'color' => ['argb' => 'OOOOOO'],
      ],
    ];

    // 设置表头
    $sheet->setCellValue('A1', '申请人姓名');
    $sheet->setCellValue('B1', '车主姓名');
    $sheet->setCellValue('C1', '申请人与车主关系');
    $sheet->setCellValue('D1', '车牌号');
    $sheet->setCellValue('E1', '联系方式');
    $sheet->setCellValue('F1', '排量（升）');
    $sheet->setCellValue('G1', '申请人所在单位');
    $sheet->setCellValue('H1', '申请类别');
    $sheet->setCellValue('I1', '提交时间');
    $sheet->setCellValue('J1', '审核状态');
    $sheet->getStyle('A1:J1')->applyFromArray([
      'alignment' => $centerStyle,
      'font' => $titleFontStyle,
      'borders' => $borderStyle,
    ]);

    // 设置列宽、高度
    $sheet->getRowDimension('1')->setRowHeight(20);
    $sheet->getColumnDimension('A')->setWidth(17);
    $sheet->getColumnDimension('B')->setWidth(17);
    $sheet->getColumnDimension('C')->setWidth(17);
    $sheet->getColumnDimension('D')->setWidth(20);
    $sheet->getColumnDimension('E')->setWidth(17);
    $sheet->getColumnDimension('F')->setWidth(17);
    $sheet->getColumnDimension('G')->setWidth(17);
    $sheet->getColumnDimension('H')->setWidth(17);
    $sheet->getColumnDimension('I')->setWidth(20);
    $sheet->getColumnDimension('J')->setWidth(17);

    // 添加数据
    $row_index = 2;
    foreach ($data as $key => $value) {
      $sheet->setCellValue('A'.$row_index, $value['applicant']);
      $sheet->setCellValue('B'.$row_index, $value['car_owner']);
      $sheet->setCellValue('C'.$row_index, $value['relationship']);
      $sheet->setCellValue('D'.$row_index, $value['car_no']);
      $sheet->setCellValue('E'.$row_index, $value['tel']);
      $sheet->setCellValue('F'.$row_index, $value['displacement']);
      $sheet->setCellValue('G'.$row_index, $value['applicant_unit']);
      $sheet->setCellValue('H'.$row_index, $value['apply_type']);
      $sheet->setCellValue('I'.$row_index, $value['submit_time']);
      switch ($value['status']) {
        case 0:
          $value['status'] = '待审核';
          break;
        case 1:
          $value['status'] = '审核通过';
          break;
        case 2:
          $value['status'] = '未通过审核';
          break;
        default:
          break;
      }
      $sheet->setCellValue('J'.$row_index, $value['status']);
      $row_index++;
    }
    $row_index--;
    $sheet->getStyle('A2:J'.$row_index)->applyFromArray([
      'alignment' => $centerStyle,
      'font' => $textFontStyle,
      'borders' => $borderStyle,
    ]);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//浏览器输出07Excel文件
    // header('Content-Type:application/vnd.ms-excel');//浏览器输出Excel03版本文件
    header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');//浏览器输出文件名称
    header('Cache-Control: max-age=0');//禁止缓存
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    $spreadsheet->disconnectWorksheets();
    unset($spreadsheet);
    exit;
  }

  public function driver_list($activity_id) {
    // 获取数据
    $data = Db::table('driver_list')->where('activity_id', $activity_id)->select();
    $file_name = '司机名单';

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    // 单元格样式
    $centerStyle = [
      'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
      'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    ];
    $titleFontStyle = [
      'name' => '微软雅黑',
      'size' => 11,
      'bold' => true,
    ];
    $textFontStyle = [
      'name' => '微软雅黑',
      'size' => 9,
    ];
    $borderStyle = [
      'allBorders' => [
          'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
          'color' => ['argb' => 'OOOOOO'],
      ],
    ];

    // 设置表头
    $sheet->setCellValue('A1', '手机号');
    $sheet->getStyle('A1')->applyFromArray([
      'alignment' => $centerStyle,
      'font' => $titleFontStyle,
      'borders' => $borderStyle,
    ]);

    // 设置列宽、高度
    $sheet->getRowDimension('1')->setRowHeight(20);
    $sheet->getColumnDimension('A')->setWidth(17);

    // 添加数据
    $row_index = 2;
    foreach ($data as $key => $value) {
      $sheet->setCellValue('A'.$row_index, $value['emp_no']);
      $row_index++;
    }
    $row_index--;
    $sheet->getStyle('A2:A'.$row_index)->applyFromArray([
      'alignment' => $centerStyle,
      'font' => $textFontStyle,
      'borders' => $borderStyle,
    ]);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//浏览器输出07Excel文件
    // header('Content-Type:application/vnd.ms-excel');//浏览器输出Excel03版本文件
    header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');//浏览器输出文件名称
    header('Cache-Control: max-age=0');//禁止缓存
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    $spreadsheet->disconnectWorksheets();
    unset($spreadsheet);
    exit;
  }

  // 导出司机预约名单
  public function export_driver_reservation($id) {
    // 获取数据
    $data = action('admin/business/get_activity_drivers', ['id' => $id]);
    
    $file_name = '预约导出数据';

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    // 单元格样式
    $centerStyle = [
      'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
      'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    ];
    $titleFontStyle = [
      'name' => '微软雅黑',
      'size' => 11,
      'bold' => true,
    ];
    $textFontStyle = [
      'name' => '微软雅黑',
      'size' => 9,
    ];
    $borderStyle = [
      'allBorders' => [
          'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
          'color' => ['argb' => 'OOOOOO'],
      ],
    ];

    // 设置表头
    $sheet->setCellValue('A1', '会议名称');
    $sheet->setCellValue('B1', '预约人姓名');
    $sheet->setCellValue('C1', '预约车牌号');
    $sheet->setCellValue('D1', '司机电话');
    $sheet->setCellValue('E1', '预约时间');
    $sheet->setCellValue('F1', '离校日期');
    $sheet->setCellValue('G1', '来校事由');
    $sheet->setCellValue('H1', '预约状态');
    $sheet->getStyle('A1:H1')->applyFromArray([
      'alignment' => $centerStyle,
      'font' => $titleFontStyle,
      'borders' => $borderStyle,
    ]);

    // 设置列宽、高度
    $sheet->getRowDimension('1')->setRowHeight(20);
    $sheet->getColumnDimension('A')->setWidth(17);
    $sheet->getColumnDimension('B')->setWidth(17);
    $sheet->getColumnDimension('C')->setWidth(17);
    $sheet->getColumnDimension('D')->setWidth(20);
    $sheet->getColumnDimension('E')->setWidth(17);
    $sheet->getColumnDimension('F')->setWidth(17);
    $sheet->getColumnDimension('G')->setWidth(17);
    $sheet->getColumnDimension('H')->setWidth(17);

    // 添加数据
    $row_index = 2;
    foreach ($data as $key => $value) {
      $sheet->setCellValue('A'.$row_index, $value['activity_name']);
      $sheet->setCellValue('B'.$row_index, $value['name']);
      $sheet->setCellValue('C'.$row_index, $value['car_no']);
      $sheet->setCellValue('D'.$row_index, $value['driver_tel']);
      $sheet->setCellValue('E'.$row_index, $value['visit_time']);
      $sheet->setCellValue('F'.$row_index, $value['leave_date']);
      $sheet->setCellValue('G'.$row_index, $value['visit_reason']);
      $sheet->setCellValue('H'.$row_index, '审核通过');
      $row_index++;
    }
    $row_index--;
    $sheet->getStyle('A2:H'.$row_index)->applyFromArray([
      'alignment' => $centerStyle,
      'font' => $textFontStyle,
      'borders' => $borderStyle,
    ]);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//浏览器输出07Excel文件
    // header('Content-Type:application/vnd.ms-excel');//浏览器输出Excel03版本文件
    header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');//浏览器输出文件名称
    header('Cache-Control: max-age=0');//禁止缓存
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    $spreadsheet->disconnectWorksheets();
    unset($spreadsheet);
    exit;
  }

}
