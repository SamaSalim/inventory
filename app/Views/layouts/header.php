
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">


<div class="container">
    <div class="sidebar">
        <!-- شعار الموقع -->
        <div class="logo">★</div>

        <?php $role = session()->get('role'); ?>

    <!-- إدارة المستودعات: فقط للـ admin أو موظف المستودع -->
    <?php if ($role === 'admin' || $role === 'warehouse'): ?>
      <div class="sidebar-icon" onclick="location.href='<?= base_url('inventoryController/index') ?>'" title="إدارة المستودعات">
         🗂️
      </div>
   <?php endif; ?>

   <!-- إدارة العهد: فقط للـ admin أو موظف العهد -->
   <?php if ($role === 'admin' || $role === 'assets'): ?>
      <div class="sidebar-icon" onclick="location.href='<?= base_url('AssetsController/dashboard') ?>'" title="إدارة العهد">
         📋
      </div>
   <?php endif; ?>
      <!-- الموظفين العاديين : فقط للـ admin أو موظف العهد -->
       <?php if ($role === 'admin' || $role === 'user'): ?>
      <div class="sidebar-icon" onclick="location.href='<?= base_url('UserController/dashboard') ?>'" title=" الموظفين العاديين">
         👤
      </div>
   <?php endif; ?>
   <!-- الادمن  -->
    <?php if ($role === 'admin'): ?>
      <div class="sidebar-icon" onclick="location.href='<?= base_url('AdminController/dashboard') ?>'" title="لوحة التحكم">
         👨‍💼
      </div>
   <?php endif; ?>

   <!-- تسجيل الخروج: يظهر للجميع -->
   <div class="sidebar-icon" onclick="location.href='<?= base_url('login/logout') ?>'" title="تسجيل الخروج">
      ⚙
   </div>
</div>
 

