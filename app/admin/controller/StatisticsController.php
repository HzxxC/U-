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
use app\portal\model\UserOperateModel;
use app\portal\model\PortalPostModel;
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

        $param = $this->request->param();

        $param['status'] = $this->request->param('status', 1, 'intval');
        $param['type'] = 3;

        $userOperateModel = new UserOperateModel();
        $data        = $userOperateModel->userOperateList($param, true);

        $data->appends($param);

        $this->assign('start_time', isset($param['start_time']) ? $param['start_time'] : '');
        $this->assign('end_time', isset($param['end_time']) ? $param['end_time'] : '');
        $this->assign('keyword', isset($param['keyword']) ? $param['keyword'] : '');
        $this->assign('status', isset($param['status']) ? $param['status'] : 1);
        $this->assign('actions', $data->items());
        $this->assign('page', $data->render());

        return $this->fetch();

    }

    /**
     * 一卡通兑换
     * @return [type] [description]
     */
    public function card() {

        $param = $this->request->param();

        $param['type'] = 6;

        $userOperateModel = new UserOperateModel();
        $data        = $userOperateModel->userOperateList($param, true);

        $data->appends($param);

        $this->assign('start_time', isset($param['start_time']) ? $param['start_time'] : '');
        $this->assign('end_time', isset($param['end_time']) ? $param['end_time'] : '');
        $this->assign('keyword', isset($param['keyword']) ? $param['keyword'] : '');
        $this->assign('status', isset($param['status']) ? $param['status'] : 1);
        $this->assign('actions', $data->items());
        $this->assign('page', $data->render());

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

        $param = $this->request->param();

        $param['status'] = $status = $this->request->param('status', 0, 'intval');
        $param['type'] = 8;

        if ($status) {
            $userOperateModel = new UserOperateModel();
            $data        = $userOperateModel->userOperateList($param, true);
        } else {
            $portalPostModel = new PortalPostModel();
            $data        = $portalPostModel->wishList($param, true);
        }

        $data->appends($param);

        $this->assign('start_time', isset($param['start_time']) ? $param['start_time'] : '');
        $this->assign('end_time', isset($param['end_time']) ? $param['end_time'] : '');
        $this->assign('keyword', isset($param['keyword']) ? $param['keyword'] : '');
        $this->assign('status', isset($param['status']) ? $param['status'] : 0);
        $this->assign('actions', $data->items());
        $this->assign('page', $data->render());

        return $this->fetch();
    }

    /**
     * 商品兑换
     * @return [type] [description]
     */
    public function exchange()
    {
        $param           = $this->request->param();
        $userOperateModel = new UserOperateModel();

        if (isset($param['ids']) && isset($param["yes"])) {
            $ids = $this->request->param('ids/a');

            $userOperateModel->where(['id' => ['in', $ids]])->update(['status' => 2, 'exchange_time' => time()]);

            $this->success("商品兑换成功！", '');
        }

        if (isset($param['ids']) && isset($param["no"])) {
            $ids = $this->request->param('ids/a');

            $userOperateModel->where(['id' => ['in', $ids]])->update(['status' => 1]);

            $this->success("取消商品兑换成功！", '');
        }

    }
}
