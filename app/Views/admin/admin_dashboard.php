<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="<?= base_url('assets/css/custom.css') ?>"> -->

    <title>Admin Dashboard</title>
    <style>
        /* ========================================
           إعدادات عامة وأساسية للصفحة
        ======================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #F4F4F4;
            direction: rtl; /* اتجاه النص من اليمين لليسار */
            text-align: right;
            min-height: 100vh;
            color: black;
        }

        /* ========================================
           تصميم الشريط الجانبي (Sidebar)
        ======================================== */
        .sidebar {
            position: fixed;
            right: 0;
            top: 0;
            height: 100vh;
            width: 80px;
            background-color: #057590 ;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px 0;
            z-index: 1000; /* أعلى من باقي العناصر */
        }

        /* شعار الموقع في الشريط الجانبي */
        .sidebar .logo {
            color: white;
            font-size: 24px;
            margin-bottom: 40px;
        }

        /* أيقونات الشريط الجانبي */
        .sidebar-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            cursor: pointer;
        }

        /* تأثير عند الوقوف على أيقونات الشريط الجانبي */
        .sidebar-icon:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* ========================================
           المحتوى الرئيسي للصفحة
        ======================================== */
        .main-content {
            margin-right: 80px; /* ترك مساحة للشريط الجانبي */
            padding: 30px;
        }

        /* ========================================
           الشريط العلوي (Top Bar)
        ======================================== */
        
        
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px); /* تأثير ضبابي */
            padding: 15px 25px;
            border-radius: 15px;
            border: 1px solid black;
            padding: 30px;
        }

        /* عنوان الصفحة الرئيسي */
        .page-title {
            color: black;
            font-size: 25px;
            font-weight: 600;
        }

        /* معلومات المستخدم */
        .user-info {
            display: flex;
            align-items: center;
            color: black;
            gap: 15px;
            cursor: pointer;
        }

        /* صورة/أيقونة المستخدم */
        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(45deg, #4facfe, #00c6ff);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        /* ========================================
           شبكة بطاقات الإدارة (Dashboard Grid)
        ======================================== */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
            gap: 25px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .dashboard-item {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(240, 248, 255, 0.9) 100%);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            padding: 35px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 2px solid rgba(0, 75, 107, 0.1);
            position: relative;
            overflow: hidden;
        }

    

        .dashboard-item:hover {
            transform: translateY(-8px) scale(1.02);
            border-color: #168aad;
            background: linear-gradient(135deg, rgba(255, 255, 255, 1) 0%, rgba(230, 240, 246, 0.95) 100%);
        }

        .item-content {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .item-title {
            font-size: 22px;
            font-weight: 700;
            color: #1d3557;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }

        .item-description {
            font-size: 14px;
            color: #666;
            font-weight: 500;
            opacity: 0.8;
        }

        .action-btn {
            background: #057590 ;
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 50px;
            font-size: 15px;
            cursor: pointer;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            position: relative;
            overflow: hidden;
            min-width: 130px;
        }

        .action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        }

        .action-btn:hover::before {
            left: 100%;
        }

     

        .action-btn:active {
            transform: translateY(-1px) scale(1.02);
            box-shadow: 0 8px 20px rgba(22, 138, 173, 0.3);
        }


        /* ========================================
           الحركات والتأثيرات (Animations)
        ======================================== */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dashboard-item {
            animation: fadeInUp 0.6s ease forwards;
        }

        .dashboard-item:nth-child(1) { animation-delay: 0.1s; }
        .dashboard-item:nth-child(2) { animation-delay: 0.2s; }
        .dashboard-item:nth-child(3) { animation-delay: 0.3s; }
        .dashboard-item:nth-child(4) { animation-delay: 0.4s; }
        .dashboard-item:nth-child(5) { animation-delay: 0.5s; }

        /* ========================================
           التصميم المتجاوب (Responsive Design)
        ======================================== */
        @media (max-width: 1024px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
                max-width: 600px;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 60px; /* تصغير الشريط الجانبي */
            }

            .main-content {
                margin-right: 60px;
                padding: 20px;
            }
            
            .dashboard-item {
                flex-direction: column;
                gap: 20px;
                text-align: center;
                padding: 25px 20px;
            }

            .item-content {
                text-align: center;
            }

            .action-btn {
                width: 100%;
                min-width: auto;
            }
            
            .top-bar {
                flex-direction: column;
                gap: 20px;
            }

            .page-title {
                font-size: 24px;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }

        @media (max-width: 480px) {
            .main-content {
                padding: 15px;
            }

            .dashboard-item {
                padding: 20px 15px;
            }

            .item-title {
                font-size: 18px;
            }

            .item-description {
                font-size: 13px;
            }

            .action-btn {
                padding: 12px 20px;
                font-size: 14px;
            }
        }

        /* ========================================
           تحسينات إضافية للتصميم
        ======================================== */
        .dashboard-grid {
            animation: slideIn 0.5s ease-out;
        }

        /* حركة الانزلاق من الأسفل */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* تأثير إضافي عند التحميل */
        .main-content {
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
    </style>
</head>
<body>

<?php if (session()->has('error')): ?>
    <div class="alert alert-danger">
        <?= esc(session('error')) ?>
    </div>
<?php endif; ?>
 
     <!-- الشريط الجانبي للتنقل -->
    <?= $this->include('layouts/header') ?>


       <div class="main-content">
    <!-- الشريط العلوي مع العنوان ومعلومات المستخدم -->
           <div class="top-bar">
        <div class="page-title">المسؤولين</div>
      <div class="user-info" onclick="location.href='<?= base_url('UserInfo/getUserInfo') ?>'">
    <div class="user-avatar">
        <?= strtoupper(substr(esc(session()->get('name')), 0, 1)) ?>
    </div>
    <span><?= esc(session()->get('name')) ?></span>
</div>
    </div>

            <div class="dashboard-grid">
                <div class="dashboard-item">
                    <div class="item-content">
                        <div class="item-title">إضافة صلاحية جديدة</div>
                        <div class="item-description">إضافة مستخدمين وصلاحيات جديدة</div>
                    </div>
                    <button class="action-btn" onclick="location.href='<?= base_url('AdminController/addPermission') ?>'">إضافة</button>
                </div>
                
                <div class="dashboard-item">
                    <div class="item-content">
                        <div class="item-title">تحديث صلاحيات</div>
                        <div class="item-description">تعديل المعلومات والصلاحيات الموجودة</div>
                    </div>
                    <button class="action-btn" onclick="location.href='<?= base_url('AdminController/listPermissions') ?>'">تحديث</button>
                </div>
                
                <div class="dashboard-item">
                    <div class="item-content">
                        <div class="item-title">إضافة موظف جديد </div>
                        <!-- <div class="item-description">عرض وتحميل تقارير المستودع</div> -->
                    </div>
                    <button class="action-btn" onclick="location.href='<?= base_url('AdminController/addEmployee') ?>'">إضافة الموظف</button>  
                </div>
                
                <div class="dashboard-item">
                    <div class="item-content">
                        <div class="item-title">تقرير الأصول</div>
                        <div class="item-description">عرض وتحميل تقارير الأصول</div>
                    </div>
                    <button class="action-btn" onclick="handleViewAssetsPDF()">عرض التقرير</button>
                </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>


</body>
</html>