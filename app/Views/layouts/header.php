  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عنوان الصفحة</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
        }

        .sidebar:hover { width: 200px; }

        .sidebar .logo { color:white; font-size:20px; font-weight:bold; margin-bottom:30px; text-align:center; white-space:nowrap; overflow:hidden; }

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
            justify-content:center;
            width:100%;
            white-space:nowrap;
            overflow:hidden;
            text-align:center;
        }

        .sidebar a:hover { background-color: rgba(255,255,255,0.2); }

        .sidebar a.active { background-color:white; color:#057590; font-weight:bold; }

        /* المحتوى الرئيسي */
        .main-content {
            margin-right: 80px; /* عرض الشريط المضغوط */
            transition: margin-right 0.3s ease;
        }

        .sidebar:hover ~ .main-content { margin-right: 200px; } /* عند hover */

        .header { background-color:white; padding:15px 25px; border-bottom:1px solid #e0e0e0; display:flex; justify-content:space-between; align-items:center; min-height:70px; }
        .page-title { color:#057590; font-size:22px; font-weight:600; margin:0; }
        .content-area { padding:25px; background-color:#EFF8FA; min-height:calc(100vh - 70px); }


                     .sidebar .logo {
    text-align: center; /*  الصورة بالوسط */
    margin-bottom: 20px; 
}

.sidebar-logo {
    width: 70px;      /* عرض  */
    height: 70px;     /* ارتفاع  */
    object-fit: contain; /* يحافظ على نسبة الأبعاد */
}

    </style>
   <!-- الشريط الجانبي -->
     <div class="sidebar">
    <div class="logo">
        <img src="<?= base_url('public/assets/images/kamc1.png') ?>" alt="Logo" class="sidebar-logo">
    </div>

        <a href="<?= base_url('inventoryController/index') ?>" 
           class="<?= (service('uri')->getSegment(1) == 'inventoryController') ? 'active' : '' ?>">
           إدارة المستودعات
        </a>

        <a href="<?= base_url('AssetsController/dashboard') ?>" 
           class="<?= (service('uri')->getSegment(1) == 'AssetsController') ? 'active' : '' ?>">
           إدارة العهد
        </a>

       <a href="<?= base_url('UserController/userView2') ?>" 
       class="<?= (service('uri')->getSegment(1) == 'UserController' && service('uri')->getSegment(2) == 'userView2') ? 'active' : '' ?>">
        طلبات العهد
       </a>

       <a href="<?= base_url('UserController/dashboard') ?>" 
        class="<?= (service('uri')->getSegment(1) == 'UserController' && service('uri')->getSegment(2) == 'dashboard') ? 'active' : '' ?>">
          العهد الخاصة بي
        </a>


        <a href="<?= base_url('AdminController/dashboard') ?>" 
           class="<?= (service('uri')->getSegment(1) == 'AdminController') ? 'active' : '' ?>">
           الصلاحيات
        </a>

        <a href="<?= base_url('login') ?>">تسجيل الخروج</a>
    </div>
