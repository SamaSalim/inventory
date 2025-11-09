<?php

if (!function_exists('getRoleId')) {
    function getRoleId()
    {
        return session()->get('role_id');
    }
}

if (!function_exists('getRoleName')) {
    function getRoleName()
    {
        return session()->get('role');
    }
}

// ===== التحقق من الأدوار =====

if (!function_exists('isAdmin')) {
    function isAdmin()
    {
        return getRoleName() === 'admin';
    }
}

if (!function_exists('isUser')) {
    function isUser()
    {
        return getRoleName() === 'user';
    }
}

if (!function_exists('isWarehouse')) {
    function isWarehouse()
    {
        return getRoleName() === 'warehouse';
    }
}


if (!function_exists('isSuperWarehouse')) {
    function isSuperWarehouse()
    {
        return getRoleName() === 'super_warehouse';
    }
}

if (!function_exists('isAssets')) {
    function isAssets()
    {
        return getRoleName() === 'assets';
    }
}

if (!function_exists('isSuperAssets')) {
    function isSuperAssets()
    {
        return getRoleName() === 'super_assets';
    }
}

// ===== صلاحيات العرض =====

if (!function_exists('canViewAllOrders')) {
    function canViewAllOrders()
    {
        $role = getRoleName();
        return in_array($role, ['admin', 'warehouse', 'super_warehouse']);
    }
}

if (!function_exists('canViewOwnOrders')) {
    function canViewOwnOrders()
    {
        return isUser();
    }
}

// ===== صلاحيات الإضافة/التعديل/الحذف =====

if (!function_exists('canCreateOrder')) {
    function canCreateOrder()
    {
        return isWarehouse();
    }
}

if (!function_exists('canEditOrder')) {
    function canEditOrder()
    {
        return isWarehouse();
    }
}

if (!function_exists('canDeleteOrder')) {
    function canDeleteOrder()
    {
        return isWarehouse();
    }
}

// ===== صلاحيات الموافقة/الرفض =====

if (!function_exists('canApprove')) {
    function canApprove()
    {
        $role = getRoleName();
        return in_array($role, ['user', 'super_warehouse']);
    }
}

if (!function_exists('canReject')) {
    function canReject()
    {
        $role = getRoleName();
        return in_array($role, ['user', 'super_warehouse']);
    }
}

// ===== صلاحيات النقل/الإرجاع =====

if (!function_exists('canTransfer')) {
    function canTransfer()
    {
        return isSuperAssets();
    }
}

if (!function_exists('canReturn')) {
    function canReturn()
    {
        return isSuperAssets();
    }
}

// ===== صلاحيات التتبع =====

if (!function_exists('canTrackTransactions')) {
    function canTrackTransactions()
    {
        return isAssets();
    }
}

if (!function_exists('canReceiveNotifications')) {
    function canReceiveNotifications()
    {
        return isAssets();
    }
}
