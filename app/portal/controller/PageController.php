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
use app\portal\service\PostService;

class PageController extends HomeBaseController
{
    public function index()
    {
        $postService = new PostService();
        $pageId      = $this->request->param('id', 0, 'intval');
        $page        = $postService->publishedPage($pageId);

        if (empty($page)) {
            abort(404, ' 页面不存在!');
        }

        $this->assign('page', $page);

        $more = $page['more'];

        $tplName = empty($more['template']) ? 'page' : $more['template'];

        $this->assign('is_swear', session('user.is_swear'));

        return $this->fetch("/$tplName");
    }


    /**
     * 会员同意宣誓
     * @return [type] [description]
     */
    public function user_swear() {

        if ($user = cmf_get_current_user()) {
            
            cmf_user_swear($user['id']);

            return ['is_err' => 1, 'msg' => '宣誓成功'];

         } else {

            return ['is_err' => 0, 'msg' => '请登录'];
         }
    }

}
