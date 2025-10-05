<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">


<!-- Bootstrap -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.rtl.min.css" rel="stylesheet">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    * { font-family: 'Cairo', sans-serif; margin:0; padding:0; box-sizing:border-box; }
    body { background-color:#F4F4F4; direction:rtl; text-align:right; min-height:100vh; color:#333; font-size:14px; }

    /* الشريط الجانبي */
    .sidebar {
        position: fixed;
        right: 0;
        top: 0;
        height: 100vh;
        width: 80px;
        background-color: #057590;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px 0;
        z-index: 1000;
        transition: width 0.3s ease;
        overflow: hidden;
    }

    .sidebar:hover { width: 200px; }

    .sidebar .logo { margin-bottom: 30px; }

    .sidebar-logo {
        width: 70px;
        height: 70px;
        object-fit: contain;
    }

    .sidebar a {
        color:white;
        text-decoration:none;
        font-size:14px;
        padding:10px 15px;
        margin-bottom:10px;
        border-radius:8px;
        transition: all 0.3s ease;
        display:flex;
        align-items:center;
        justify-content:center; /* العناصر بالنص */
        width:100%;
        white-space:nowrap;
        overflow:hidden;
    }

    .sidebar a i {
        font-size: 18px;
        transition: opacity 0.3s ease;
    }

    .sidebar span {
        display: none;
        font-size:14px;
        text-align:center;
        width:100%;
    }

    /* عند hover: نخفي الايقونة ونظهر النص بالنص */
    .sidebar:hover a i {
        display: none;
    }

    .sidebar:hover a span {
        display: block;
    }

    .sidebar a:hover { background-color: rgba(255,255,255,0.2); }

    .sidebar a.active { background-color:white; color:#057590; font-weight:bold; }

    /* المحتوى الرئيسي */
    .main-content {
        margin-right: 80px;
        transition: margin-right 0.3s ease;
    }

    .sidebar:hover ~ .main-content { margin-right: 200px; }

    .header { background-color:white; padding:15px 25px; border-bottom:1px solid #e0e0e0; display:flex; justify-content:space-between; align-items:center; min-height:70px; }
    .page-title { color:#057590; font-size:22px; font-weight:600; margin:0; }
    .content-area { padding:25px; background-color:#EFF8FA; min-height:calc(100vh - 70px); }


    
</style>

<!-- الشريط الجانبي -->
<div class="sidebar">
    <div class="logo">
        <img src="<?= base_url('public/assets/images/kamc1.png') ?>" alt="Logo" class="sidebar-logo">
    </div>

    <?php $role = service('session')->get('role'); // جلب الدور من الجلسة ?>

    <?php if ($role === 'admin' || $role === 'warehouse'): ?>
        <a href="<?= base_url('inventoryController/index') ?>"
           class="<?= (service('uri')->getSegment(1) == 'inventoryController') ? 'active' : '' ?>">
            <i class="fa-solid fa-warehouse"></i> <span>إدارة المستودعات</span>
        </a>
    <?php endif; ?>

    <?php if ($role === 'admin' || $role === 'assets'): ?>
        <a href="<?= base_url('AssetsController/index') ?>"
           class="<?= (service('uri')->getSegment(1) == 'AssetsController') ? 'active' : '' ?>">
            <i class="fa-solid fa-boxes-stacked"></i> <span>إدارة العهد</span>
        </a>
    <?php endif; ?>

    <?php if ($role === 'admin' || $role === 'assets'): ?>
        <a href="<?= base_url('UserController/userView2') ?>"
           class="<?= (service('uri')->getSegment(1) == 'UserController' && service('uri')->getSegment(2) == 'userView2') ? 'active' : '' ?>">
            <i class="fa-solid fa-file-circle-plus"></i> <span>طلبات العهد</span>
        </a>
    <?php endif; ?>

    <?php if ($role === 'admin' || $role === 'assets' || $role === 'user'): ?>
        <a href="<?= base_url('UserController/dashboard') ?>"
           class="<?= (service('uri')->getSegment(1) == 'UserController' && service('uri')->getSegment(2) == 'dashboard') ? 'active' : '' ?>">
            <i class="fa-solid fa-id-card"></i> <span>العهد الخاصة بي</span>
        </a>
    <?php endif; ?>

    <?php if ($role === 'admin'): ?>
        <a href="<?= base_url('AdminController/dashboard') ?>"
           class="<?= (service('uri')->getSegment(1) == 'AdminController') ? 'active' : '' ?>">
            <i class="fa-solid fa-user-shield"></i> <span>الصلاحيات</span>
        </a>
    <?php endif; ?>

    <a href="<?= base_url('AssetsHistory') ?>"
   class="<?= (service('uri')->getSegment(1) == 'AssetsHistory') ? 'active' : '' ?>">
    <i class="fa-solid fa-archive"></i> <span>عمليات الإرجاع </span>
</a>


    <a href="<?= base_url('login') ?>">
        <i class="fa-solid fa-right-from-bracket"></i> <span>تسجيل الخروج</span>
    </a>
</div>