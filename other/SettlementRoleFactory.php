<?php

namespace App\Common;

class SettlementRoleFactory {
    public static function create(string $role) {
        switch ($role) {
            case 1:
                return new SettlementType1();
            case 2:
                return new SettlementType2();
            case 3:
                return new SettlementType3();
            default:
                throw new \Exception('结算类型错误');
        }
    }
}