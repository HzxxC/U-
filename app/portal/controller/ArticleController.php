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
use app\portal\service\PostService;
use app\portal\model\PortalPostModel;
use app\portal\model\UserOperateModel;
use think\Db;

class ArticleController extends HomeBaseController
{
    public function index()
    {

        $portalCategoryModel = new PortalCategoryModel();
        $postService         = new PostService();

        $articleId  = $this->request->param('id', 0, 'intval');
        $categoryId = $this->request->param('cid', 0, 'intval');
        $type       = $this->request->param('type', 1, 'intval');
        $article    = $postService->publishedArticle($articleId, $categoryId, $type);

        if (empty($article)) {
            abort(404, '文章不存在!');
        }


        $prevArticle = $postService->publishedPrevArticle($articleId, $categoryId, $type);
        $nextArticle = $postService->publishedNextArticle($articleId, $categoryId, $type);

        $tplName = 'article';

        if (empty($categoryId)) {
            $categories = $article['categories'];

            if (count($categories) > 0) {
                $this->assign('category', $categories[0]);
            } else {
                abort(404, '文章未指定分类!');
            }

        } else {
            $category = $portalCategoryModel->where('id', $categoryId)->where('status', 1)->find();

            if (empty($category)) {
                abort(404, '文章不存在!');
            }

            $this->assign('category', $category);

            $tplName = empty($category["one_tpl"]) ? $tplName : $category["one_tpl"];
        }

        Db::name('portal_post')->where(['id' => $articleId])->setInc('post_hits');


        $article['active_status'] = cmf_check_user_operate($articleId, $type);

        hook('portal_before_assign_article', $article);

        $this->assign('article', $article);
        $this->assign('prev_article', $prevArticle);
        $this->assign('next_article', $nextArticle);

        $tplName = empty($article['more']['template']) ? $tplName : $article['more']['template'];

        return $this->fetch("/$tplName");
    }

    // 文章点赞
    public function doLike()
    {
        $this->checkUserLogin();
        $articleId = $this->request->param('id', 0, 'intval');


        $canLike = cmf_check_user_action("posts$articleId", 1);

        if ($canLike) {
            Db::name('portal_post')->where(['id' => $articleId])->setInc('post_like');

            $this->success("赞好啦！");
        } else {
            $this->error("您已赞过啦！");
        }
    }

    // 会员操作
    public function user_operate() {

         $data = $this->request->param();

         $userOperate = new UserOperateModel();

         if ($user = cmf_get_current_user()) {
            
            // 校验会员积分是否充足
            if (cmf_check_user_score($user['id'], $data['score'])) {
                return ['is_err' => 0, 'msg' => '积分不足，请继续努力'];
            }

            // 心愿，一卡通，只能实现一次
            if (cmf_check_active($user['id'], $data['pid'], $data['type'])) {
                return ['is_err' => 0, 'msg' => '数据已存在'];
            }

            $userOperate->insertUserOperate($user['id'], $data);

            return ['is_err' => 1, 'msg' => '成功'];

         } else {
            return ['is_err' => 0, 'msg' => '请登录'];
         }

    }

    /**
     * 爱的足迹 位置判断
     * @return [type] [description]
     */
    public function track_location() {

        $userOperate = new UserOperateModel();
        
        if ($this->request->isPost()) {
            
            $data   = $this->request->param();

            $uid = cmf_get_current_user_id();
            $today_track =  cmf_get_track($data['pid']);

            // 判断是否到活动时间
            if (!cmf_check_today_track_time($data['pid'])) {
                return ['code'=>0, 'msg'=>'当前活动还没开始，请耐心等待'];
            }

            // 判断会员是否在区域范围内
            if (cmf_check_user_location($data['pid'], $data['lat'], $data['lng'])) {

                //点击开始
                if ($data['type'] == 'start') { 
                    // 添加用户操作
                    $userOperate->insertUserOperate($uid, $today_track);

                    return ['code'=>1, 'msg'=>'报名成功'];
                }

                //点击结束
                if ($data['type'] == 'end') {
                   
                    $param['id'] = $data['id'];
                    $param['more']['join_start_time'] = $data['join_start_time'];
                    $param['more']['join_end_time'] = time();
                    $param['create_time'] = time();
                    // 添加用户操作
                    $userOperate->updateUserOperate($param);

                    return ['code'=>1, 'msg'=>'已结束服务'];
                }

                
            } else {
                
                //点击开始
                if ($data['type'] == 'start') { 
                    return ['code'=>0, 'msg'=>'您当前在活动区域外，无法开始'];
                }
                //点击结束
                if ($data['type'] == 'end') {
                    return ['code'=>0, 'msg'=>'您当前在活动区域外，无法结束'];
                }
            }

        }

    }

}
