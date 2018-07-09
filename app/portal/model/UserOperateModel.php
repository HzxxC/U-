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

    public function insertUserOperate($uid, $param)
    {
        

        if (!empty($param)) {

            if ($param['type'] == 4) {
                $param['more']['username'] = $param['username'];
                $param['more']['phone'] = $param['phone'];
            } elseif ($param['type'] == 5) {
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

            return $this;
        }
    }
}