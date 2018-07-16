<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use app\admin\model\AdminMenuModel;

class StatisticsController extends AdminBaseController
{

    /**
     * 统计首页
     */
    public function index()
    {

        return $this->fetch();
    }

    /**
     * 商品兑换
     * @return [type] [description]
     */
    public function goods() {

        $where = [
            'type' => 3
        ];
        $join = [
            [config('prefix').'user u', 'u.id = uo.user_id','LEFT'],
            [config('prefix').'portal_post pp', 'pp.id = uo.pid','LEFT'],
        ];
        $actions = Db::name('user_operate')->alias('uo')
                    ->join($join)
                    ->where($where)
                    ->field('uo.*, u.user_nickname, pp.post_title')
                    ->order('create_time DESC')->paginate(20);
        // 获取分页显示
        $page = $actions->render();

        $this->assign('actions', $actions);
        $this->assign('page', $page);
        // 渲染模板输出
        return $this->fetch();
    }

    /**
     * 一卡通兑换
     * @return [type] [description]
     */
    public function card() {

        $where = [
            'type' => 6
        ];
        $join = [
            [config('prefix').'user u', 'u.id = uo.user_id','LEFT'],
            [config('prefix').'portal_post pp', 'pp.id = uo.pid','LEFT'],
        ];
        $actions = Db::name('user_operate')->alias('uo')
                    ->join($join)
                    ->where($where)
                    ->field('uo.*, u.user_nickname, pp.post_title')
                    ->order('create_time DESC')->paginate(20);
        // 获取分页显示
        $page = $actions->render();

        $this->assign('actions', $actions);
        $this->assign('page', $page);
        // 渲染模板输出
        return $this->fetch();
    }

    /**
     * 活动报名
     * @return [type] [description]
     */
    public function apply() {

        $where = [
            'type' => 4
        ];
        $join = [
            [config('prefix').'user u', 'u.id = uo.user_id','LEFT'],
            [config('prefix').'portal_post pp', 'pp.id = uo.pid','LEFT'],
        ];
        $actions = Db::name('user_operate')->alias('uo')
                    ->join($join)
                    ->where($where)
                    ->field('uo.*, u.user_nickname, pp.post_title')
                    ->order('create_time DESC')->paginate(20);
        // 获取分页显示
        $page = $actions->render();

        $this->assign('actions', $actions);
        $this->assign('page', $page);
        // 渲染模板输出
        return $this->fetch();
    }

    public function track() {

        $where = [
            'uo.type' => 5,
            'uo.create_time' => ['lt', strtotime(date("Y-m-d"),time())]
        ];
        $join = [
            [config('prefix').'user u', 'u.id = uo.user_id','LEFT'],
            [config('prefix').'portal_post pp', 'pp.id = uo.pid','LEFT'],
        ];
        $actions = Db::name('user_operate')->alias('uo')
                    ->join($join)
                    ->where($where)
                    ->field('uo.*, u.user_nickname, pp.post_title')
                    ->order('create_time DESC')->paginate(20);
        // 获取分页显示
        $page = $actions->render();

        $this->assign('actions', $actions);
        $this->assign('page', $page);
        // 渲染模板输出
        return $this->fetch();
    }

    /**
     * 心愿实现
     * @return [type] [description]
     */
    public function wish() {

        $where = [
            'type' => 8
        ];
        $join = [
            [config('prefix').'user u', 'u.id = uo.user_id','LEFT'],
            [config('prefix').'portal_post pp', 'pp.id = uo.pid','LEFT'],
        ];
        $actions = Db::name('user_operate')->alias('uo')
                    ->join($join)
                    ->where($where)
                    ->field('uo.*, u.user_nickname, pp.post_title')
                    ->order('create_time DESC')->paginate(20);
        // 获取分页显示
        $page = $actions->render();

        $this->assign('actions', $actions);
        $this->assign('page', $page);
        // 渲染模板输出
        return $this->fetch();
    }
}
