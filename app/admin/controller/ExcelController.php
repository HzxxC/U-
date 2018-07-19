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
use app\portal\model\PortalPostModel;
use app\portal\service\PostService;

class ExcelController extends Controller
{
    
    // 导入
    public function importWish() {
        // 导入第三方类库
        vendor('phpoffice.phpexcel');//导入类库
        
        //获取表单上传文件
        $file = request()->file("excel");
        $categoryId = $this->request->param('category_id', 0, 'intval');

        //上传验证后缀名,以及上传之后移动的地址
        $info = $file->validate(['ext' => 'xlsx,xls,csv'])->move(ROOT_PATH . 'upload'. DS . 'excel');

        if ($info) {
            
            $exclePath = $info->getSaveName();  //获取文件名3

            $file_name = ROOT_PATH . 'upload'. DS . 'excel' . DS . $exclePath;   //上传文件的地址
            // 判断 文件后缀
            if (cmf_check_file_suffix($exclePath)) {
                $objReader =\PHPExcel_IOFactory::createReader('Excel2007');
            } else {
                $objReader =\PHPExcel_IOFactory::createReader('Excel5');
            }
            $obj_PHPExcel =$objReader->load($file_name, $encode = 'utf-8');  //加载文件内容,编码utf-8
         
            $excel_array=$obj_PHPExcel->getsheet(0)->toArray();   //转换为数组格式
            array_shift($excel_array);  //删除第一个数组(标题);
            array_shift($excel_array);  //删除第一个数组(标题);
            
            $wish = [];
            foreach($excel_array as $k=>$v) {
                
                $excel_array[$k] = array_filter($v);
                // 去除空数组
                if (empty($excel_array[$k])) {
                    unset($excel_array[$k]);
                } else {
                    $wish[$k]['post_type'] = 8;
                    $wish[$k]['post_status'] = 0;
                    $wish[$k]['post_title'] = empty($v[4]) ? '错误，请重新填写' : $v[4];
                    $wish[$k]['score'] = empty($v[5]) ? 0 : $v[5];
                    $wish[$k]['create_time'] = time();
                    $wish[$k]['more']['wish_maker'] = empty($v[1]) ? '错误，请重新填写' : $v[1];
                    $wish[$k]['more']['wish_maker_sex'] = empty($v[2]) ? '错误，请重新填写' : $v[2];
                    $wish[$k]['more']['wish_maker_school'] = empty($v[3]) ? '错误，请重新填写' : $v[3];
                }          
            }

            $portalPostModel = new PortalPostModel();

            foreach ($wish as $key => $value) {
               $portalPostModel->adminAddArticle($value, $categoryId);
            }

            $this->success('添加成功!', url('portal/AdminArticle/wish', ['category' => $categoryId, 'type' => 8]));

        } else {
            echo $file->getError();
        }
    }

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

            $PHPSheet->setCellValue("A" . $i, $i-1)
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
            $PHPSheet->setCellValue("A" . $i, $i-1)
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

    // 导出 微心愿列表
    public function exportWish() {

        // 获得微心愿列表信息
        $where['post_type'] = 8;
        $where['post_status'] = 1;
        
        $portalPostModel = new PortalPostModel();
        $wish_list        = $portalPostModel->alias('a')->field("id, post_title, more, score")
            ->where($where)
            ->order('id', 'ASC') -> select();

        
        $path = dirname(__FILE__); //找到当前脚本所在路径
        $filename = "微心愿导出列表-".date('YmdHis', time()).".xlsx"; // 文件名称

        // 导入第三方类库
        vendor('phpoffice.phpexcel');//导入类库

        $PHPExcel = new \PHPExcel();
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("微心愿列表"); //给当前活动sheet设置名称
        $PHPSheet->setCellValue("A1", "序号")
            ->setCellValue("B1", "姓名")
            ->setCellValue("C1", "性别")
            ->setCellValue("D1", "所在学校")
            ->setCellValue("E1", "微心愿")
            ->setCellValue("F1", "积分")
            ->setCellValue("G1", "认领人姓名")
            ->setCellValue("H1", "认领人电话")
            ->setCellValue("I1", "状态");
        
        $i = 2;
        foreach($wish_list as $data){

            // 心愿是否被认领
            $wish = cmf_get_wish_complete($data['id']);

            $PHPSheet->setCellValue("A" . $i, $i-1)
                ->setCellValue("B" . $i, $data['more']['wish_maker'])
                ->setCellValue("C" . $i, $data['more']['wish_maker_sex'])
                ->setCellValue("D" . $i, $data['more']['wish_maker_school'])
                ->setCellValue("E" . $i, $data['post_title'])
                ->setCellValue("F" . $i, $data['score'])
                ->setCellValue("G" . $i, empty($wish['username']) ? "暂无" : $wish['username'])
                ->setCellValue("H" . $i, empty($wish['phone']) ? "暂无" : $wish['phone'])
                ->setCellValue("I" . $i, empty($wish) ? '未认领' : '已认领');
            $i++;   
        }

        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007");
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

}