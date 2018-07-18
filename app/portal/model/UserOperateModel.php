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
namespace app\portal\model;

use think\Db;
use think\Model;

class UserOperateModel extends Model
{

    protected $type = [
        'more' => 'array',
    ];
    
    /**
     * 添加
     * @param  [type] $uid   [description]
     * @param  [type] $param [description]
     * @return [type]        [description]
     */
    public function insertUserOperate($uid, $param)
    {
        

        if (!empty($param)) {

            $param['more']['msg'] =  '无数据';

            if ($param['type'] == 3 ||$param['type'] == 4 || $param['type'] == 8 || $param['type'] == 6) {
                $param['more']['msg'] =  '成功';
                $param['more']['username'] = $param['username'];
                $param['more']['phone'] = $param['phone'];
            } elseif ($param['type'] == 5) {
                $param['more']['msg'] =  '成功';
                $param['more']['join_start_time'] = time();
            }


            $data   = [
                'user_id'      	=> $uid,
                'pid'     		=> $param['pid'],
                'type'          => $param['type'],
                'pm'   			=> cmf_check_pm($param['type']),
                'score'       	=> $param['score'],
                'more'   		=> $param['more'],
                'create_time'   => time(),
            ];
           
            $this->allowField(true)->data($data, true)->isUpdate(false)->save();

            // 变更积分
            cmf_user_score($uid, $param['score'], $param['type']);
            

            return $this;
        }
    }

    /**
     * 更新
     * @param  [type] $param [description]
     * @return [type]        [description]
     */
    public function updateUserOperate($param)
    {
        

        if (!empty($param)) {
           
            $this->allowField(true)->isUpdate(true)->data($param, true)->save();

            return $this;
        }
    }


    public function userOperateList($param, $ispage = false)
    {
        $where = [
            'uo.create_time' => ['>=', 0],
            'uo.type' => $param['type']
        ];

        $join = [
            [config('datebase.prefix').'user u', 'u.id = uo.user_id','LEFT'],
            [config('datebase.prefix').'portal_post pp', 'pp.id = uo.pid','LEFT'],
        ];

        $field = 'uo.*, u.user_nickname, pp.post_title';

        $startTime = empty($param['start_time']) ? 0 : strtotime($param['start_time']);
        $endTime   = empty($param['end_time']) ? 0 : strtotime($param['end_time']);
       
        if (!empty($startTime) && !empty($endTime)) {
            $where['uo.create_time'] = [['>= time', $startTime], ['<= time', $endTime]];
        } else {
            if (!empty($startTime)) {
                $where['uo.create_time'] = ['>= time', $startTime];
            }
            if (!empty($endTime)) {
                $where['uo.create_time'] = ['<= time', $endTime];
            }
        }

        $keyword = empty($param['keyword']) ? '' : $param['keyword'];
        if (!empty($keyword)) {
            $where['uo.more'] = ['like', "%$keyword%"];
        }

        $status = empty($param['status']) ? 0: intval($param['status']);
        if (!empty($status)) {
            $where['uo.status'] = $status;
        } 

        $data        = $this->alias('uo')->field($field)
            ->join($join)
            ->where($where)
            ->order('create_time', 'DESC');

        if ($ispage) {
            $data = $data->paginate(10);
        } else {
            $data = $data->select();
        }

        return $data;

    }

}