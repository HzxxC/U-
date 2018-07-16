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
}