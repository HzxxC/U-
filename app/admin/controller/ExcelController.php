<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use think\Controller;
use think\Db;
use app\portal\model\UserOperateModel;

class ExcelController extends Controller
{
    
    // 导入


    // 导出 会员列表
    public function exportUser() {

        // 获得用户信息
        $users = Db::name('user')->where('user_type', 2)->field('id, score, user_nickname, user_name, mobile, create_time')->order('id ASC')->select();     //数据库查询
        
        $path = dirname(__FILE__); //找到当前脚本所在路径
        $filename = "用户数据导出列表-".date('YmdHis', time()).".xlsx"; // 文件名称

        // 导入第三方类库
        vendor('phpoffice.phpexcel');//导入类库

        $PHPExcel = new \PHPExcel();
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("用户列表"); //给当前活动sheet设置名称
        $PHPSheet->setCellValue("A1", "ID")
            ->setCellValue("B1", "微信名")
            ->setCellValue("C1", "等级")
            ->setCellValue("D1", "积分")
            ->setCellValue("E1", "姓名")
            ->setCellValue("F1", "联系方式")
            ->setCellValue("G1", "注册时间");
        
        $i = 2;
        foreach($users as $data){
            // 用户等级
            $level = cmf_get_service($data['id']);

            $PHPSheet->setCellValue("A" . $i, $data['id'])
                ->setCellValue("B" . $i, $data['user_nickname'])
                ->setCellValue("C" . $i, $level['level'])
                ->setCellValue("D" . $i, $data['score'])
                ->setCellValue("E" . $i, $data['user_name'])
                ->setCellValue("F" . $i, $data['mobile'])
                ->setCellValue("G" . $i, date('Y-m-d H:i:s', $data['create_time']));
            $i++;   
        }

        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007");
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

    // 导出 未兑换商品列表
    public function exportGoods() {

        // 获得未兑换列表信息
        $param['type'] = 3;
        $param['status'] = 1;
        $userOperateModel = new UserOperateModel();
        $goods = $userOperateModel->userOperateList($param);     //数据库查询
        
        $path = dirname(__FILE__); //找到当前脚本所在路径
        $filename = "未兑换商品导出列表-".date('YmdHis', time()).".xlsx"; // 文件名称

        // 导入第三方类库
        vendor('phpoffice.phpexcel');//导入类库

        $PHPExcel = new \PHPExcel();
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("未兑换商品列表"); //给当前活动sheet设置名称
        $PHPSheet->setCellValue("A1", "ID")
            ->setCellValue("B1", "商品名称")
            ->setCellValue("C1", "会员名称")
            ->setCellValue("D1", "兑换者姓名")
            ->setCellValue("E1", "兑换者手机号")
            ->setCellValue("F1", "消耗积分")
            ->setCellValue("G1", "创建时间");
        
        $i = 2;
        foreach($goods as $data){
            $PHPSheet->setCellValue("A" . $i, $data['id'])
                ->setCellValue("B" . $i, $data['post_title'])
                ->setCellValue("C" . $i, $data['user_nickname'])
                ->setCellValue("D" . $i, $data['more']['username'])
                ->setCellValue("E" . $i, $data['more']['phone'])
                ->setCellValue("F" . $i, $data['score'])
                ->setCellValue("G" . $i, date('Y-m-d H:i:s', $data['create_time']));
            $i++;   
        }

        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007");
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

}