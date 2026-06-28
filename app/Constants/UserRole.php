<?php

namespace App\Constants;

class UserRole
{
    const CUSTOMER = 0;
    const ADMIN = 1;
    const RESELLER = 2;

    /**
     * Lấy danh sách các quyền
     */
    public static function getList()
    {
        return [
            self::CUSTOMER => 'Khách hàng',
            self::ADMIN    => 'Admin',
            self::RESELLER => 'Đại lý',
        ];
    }

    /**
     * Lấy tên quyền dựa vào số (0, 1, 2)
     */
    public static function getLabel($role)
    {
        $list = self::getList();
        return $list[$role] ?? 'Không xác định';
    }
}
