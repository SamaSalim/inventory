<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'login::index');

$routes->setAutoRoute(true);
$routes->post('InventoryController/updateOrderStatus/(:num)', 'InventoryController::updateOrderStatus/$1');
// ===========================================
// المسارات الأساسية (Dashboard & List)
// ===========================================
$routes->get('admin/dashboard', 'AdminController::dashboard');
$routes->get('admin/listPermissions', 'AdminController::listPermissions');


// ===========================================
// إضافة موظف (Add Employee)
// ===========================================
// GET: لعرض نموذج إضافة الموظف
$routes->get('admin/addEmployee', 'AdminController::addEmployee');
// POST: لمعالجة إرسال نموذج إضافة الموظف
$routes->post('admin/addEmployee', 'AdminController::addEmployee');


// ===========================================
// إضافة صلاحية (Add Permission)
// ===========================================
// GET: لعرض نموذج إضافة الصلاحية
$routes->get('admin/addPermission', 'AdminController::addPermission');
// POST: لمعالجة إرسال نموذج إضافة الصلاحية
$routes->post('admin/addPermission', 'AdminController::addPermission');


// ===========================================
// تحديث/تعديل صلاحية (Update Permission)
// ===========================================
// GET: لعرض نموذج تحديث صلاحية بمعرف محدد
$routes->get('admin/updatePermission/(:num)', 'AdminController::updatePermission/$1');
// POST: لمعالجة إرسال نموذج التحديث لمعرف محدد
$routes->post('admin/updatePermission/(:num)', 'AdminController::updatePermission/$1');


// ===========================================
// حذف صلاحية (Delete Permission)
// ===========================================
// POST: لمعالجة طلب حذف صلاحية بمعرف محدد
$routes->post('admin/deletePermission/(:num)', 'AdminController::deletePermission/$1');