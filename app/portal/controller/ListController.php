<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\portal\controller;

use cmf\controller\HomeBaseController;
use app\portal\model\PortalCategoryModel;

class ListController extends HomeBaseController
{
    public function index()
    {
        $id                  = $this->request->param('id', 0, 'intval');
        $portalCategoryModel = new PortalCategoryModel();

        $category = $portalCategoryModel->where('id', $id)->where('status', 1)->find();
       
        $this->assign('category', $category);

        $listTpl = empty($category['list_tpl']) ? 'list' : $category['list_tpl'];

        if(cmf_is_wechat())
			$this->assign('signPackage',wechat()->getJsSign(request()->url(true)));	

        return $this->fetch('/' . $listTpl);
    }

    public function ajax_get_list() {
         
         $id  = $this->request->param('id', 0, 'intval');
         $post_type  = $this->request->param('post_type', 0, 'intval');
         $page  = $this->request->param('page', 1, 'intval');

         $limit = 5;

         $data = cmf_get_list_by_cateId($id, $post_type, $page, $limit);

         if (!empty($data['articles'])) {
            return ['code'=>1, 'data'=>$data];
         } else {
            return ['code'=>0, 'msg'=>'没有更多数据了'];
         }
         
     }

}
