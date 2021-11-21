<?php
/**
 *
 * User: Administrator
 * Date: 2021/11/21 11:00
 * Email: <coderqiqin@aliyun.com>
 **/

namespace app\Models;

use bootstrap\core\model;

class MemberModel extends model
{
    protected $table = 'members';
    public function list()
    {
//        $sql = 'select * from members';
//        $res = $this->query($sql);
//        //        dd($res->rowCount());
//        return $res->fetchAll();
        
        return $this->select($this->table, ['id','realname','gender','mobile','money','balance','created_at','updated_at']);
    }
}