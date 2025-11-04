<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم المدير</title>
    <style>
        /* ========================================
           أنماط موحدة لـ Admin Dashboard
        ======================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Cairo', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #F4F4F4;
            /* لون الخلفية العام */
            direction: rtl;
            text-align: right;
            min-height: 100vh;
            color: #333;
            padding: 0;
        }

        /* ========================================
           الشريط الجانبي والمحتوى
        ======================================== */
        .sidebar {
            position: fixed;
            right: 0;
            top: 0;
            height: 100vh;
            width: 80px;
            background-color: #057590;
            /* لون الشريط الجانبي */
            z-index: 1000;
        }

        .main-content {
            margin-right: 80px;
            padding: 30px 25px;
            animation: fadeIn 0.8s ease-out;
            /* إضافة حركة عند التحميل */
        }

        /* ========================================
           الشريط العلوي (Top Bar) - تصميم موحد
        ======================================== */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            background-color: white;
            padding: 20px 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border-left: 5px solid #057590;
        }

        .page-title {
            color: #057590;
            font-size: 26px;
            font-weight: 700;
        }

        .user-info {
            display: flex;
            align-items: center;
            color: #1d3557;
            gap: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .user-info:hover {
            opacity: 0.8;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            background: linear-gradient(45deg, #3ac0c3, #057590);
            /* لون جذاب */
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 16px;
        }

        /* ========================================
           شبكة بطاقات الإدارة (Dashboard Grid)
        ======================================== */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .dashboard-item {
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(5, 117, 144, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }

        .dashboard-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 75, 107, 0.2);
        }

        .item-content {
            border-bottom: 1px dashed #e0e0e0;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .item-title {
            color: #1d3557;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .item-description {
            color: #666;
            font-size: 14px;
            min-height: 40px;
        }

        .action-btn {
            background: linear-gradient(135deg, #3ac0c3, #2aa8ab);
            /* زر موحد */
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 25px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            width: 100%;
        }

        .action-btn:hover {
            background: linear-gradient(135deg, #2aa8ab, #259a9d);
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(58, 192, 195, 0.4);
        }

        /* رسائل الخطأ */
        .alert-danger {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #dc3545;
            font-weight: 500;
        }

        /* ========================================
           التصميم المتجاوب (Responsive Design)
        ======================================== */
        @media (max-width: 768px) {
            .main-content {
                margin-right: 0;
                padding: 15px;
            }

            .sidebar {
                display: none;
            }

            .top-bar {
                flex-direction: column;
                gap: 15px;
                padding: 15px;
            }

            .page-title {
                font-size: 22px;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
                gap: 20px;
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

    <?= $this->include('layouts/header') ?>


    <div class="main-content">
        <div class="top-bar">
            <div class="page-title">لوحة تحكم المسؤولين</div>
            <div class="user-info" onclick="location.href='<?= base_url('UserInfo/getUserInfo') ?>'">
                <div class="user-avatar">
                    <?php
                    $userName = session()->get('name') ?? 'م م';
                    $nameParts = explode(' ', trim($userName));
                    $initials = '';

                    if (count($nameParts) >= 2) {
                        $initials = mb_substr($nameParts[0], 0, 1, 'UTF-8') . mb_substr($nameParts[count($nameParts) - 1], 0, 1, 'UTF-8');
                    } else {
                        $initials = mb_substr($nameParts[0], 0, 1, 'UTF-8');
                    }
                    echo strtoupper($initials);
                    ?>
                </div>
                <span><?= esc(session()->get('name')) ?></span>
            </div>
        </div>

        <div class="dashboard-grid">

            <div class="dashboard-item">
                <div class="item-content">
                    <div class="item-title">تحديث صلاحيات الموظفين</div>
                    <div class="item-description">عرض، تعديل، وحذف الصلاحيات والأدوار الحالية للموظفين.</div>
                </div>
                <button class="action-btn" onclick="location.href='<?= base_url('AdminController/listPermissions') ?>'">تحديث الصلاحيات</button>
            </div>

            <div class="dashboard-item">
                <div class="item-content">
                    <div class="item-title">إضافة موظف جديد</div>
                    <div class="item-description">تسجيل موظف جديد وتعيين بياناته الأساسية (الرقم الوظيفي، القسم، الإيميل).</div>
                </div>
                <button class="action-btn" onclick="location.href='<?= base_url('AdminController/addEmployee') ?>'">إضافة الموظف</button>
            </div>

            <div class="dashboard-item">
                <div class="item-content">
                    <div class="item-title">تعيين صلاحية جديدة (ربط دور)</div>
                    <div class="item-description">ربط موظف معين (موظف حالي) بدور محدد (Admin, Warehouse, Assets, etc.).</div>
                </div>
                <button class="action-btn" onclick="location.href='<?= base_url('AdminController/addPermission') ?>'">تعيين صلاحية</button>
            </div>

            <!-- <div class="dashboard-item">
                <div class="item-content">
                    <div class="item-title">تقرير الأصول (PDF)</div>
                    <div class="item-description">عرض وتحميل تقارير شاملة عن حالة الأصول والمخزون في النظام.</div>
                </div>
                <button class="action-btn" onclick="handleViewAssetsPDF()">عرض التقرير</button>
            </div>
        </div> -->
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script>
            // function handleViewAssetsPDF() {
            //     alert("وظيفة عرض تقارير الأصول غير مفعلة حالياً. ");
            // }
        </script>
</body>

</html>