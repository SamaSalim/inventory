<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>معلومات المستخدم</title>
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
            background: linear-gradient(135deg, #ffffff 0%, #e6f0f6 100%);
            direction: rtl;
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
            background-color: #057590;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px 0;
            z-index: 1000;
            /* أعلى من باقي العناصر */
        }

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


        .sidebar-icon:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(-5px);
        }

        .sidebar-icon.active {
            background: rgba(255, 255, 255, 0.3);
        }

        .sidebar-icon svg {
            fill: white;
            width: 20px;
            height: 20px;
        }

        /* ========================================
           المحتوى الرئيسي للصفحة
        ======================================== */
        .main-content {
            margin-right: 80px;
            padding: 30px;
        }

        /* ========================================
           الشريط العلوي (Top Bar)
        ======================================== */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 15px 25px;
            border-radius: 15px;
            border: 1px solid black;
        }

        .page-title {
            color: black;
            font-size: 28px;
            font-weight: 600;
        }

        .user-info {
            display: flex;
            align-items: center;
            color: black;
            gap: 15px;
            cursor: pointer;
        }

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
           بطاقة معلومات المستخدم
        ======================================== */
        .user-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid black;
            animation: slideIn 0.5s ease-out;
        }

        .user-profile-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, #4facfe, #00c6ff);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            font-weight: bold;
        }

        .profile-basic-info h2 {
            color: black;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .profile-basic-info p {
            color: #666;
            font-size: 14px;
        }

        /* ========================================
           شبكة معلومات المستخدم
        ======================================== */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .info-item {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .info-item:hover {
            transform: translateY(-3px);
        }

        .info-item label {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            color: black;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .info-item .info-value {
            color: black;
            font-size: 16px;
            font-weight: 500;
        }

        .info-icon {
            width: 18px;
            height: 18px;
            fill: currentColor;
        }

        /* ========================================
           قائمة الأدوار
        ======================================== */
        .roles-container {
            grid-column: span 2;
        }

        .roles-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            list-style: none;
            margin-top: 10px;
        }

        .roles-list li {
            background: linear-gradient(135deg, #168aad, #1d3557);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
        }

        /* ========================================
           رسائل الأخطاء والتنبيهات
        ======================================== */
        .error-message {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            font-weight: 600;
            margin: 20px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .no-data-message {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            font-weight: 600;
            margin: 20px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        /* ========================================
           أزرار الإجراءات
        ======================================== */
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 1px;
            justify-content: center;
        }

        .action-btn {
            background: linear-gradient(135deg, #168aad, #1d3557);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .action-btn.secondary {
            background: linear-gradient(135deg, #95a5a6, #7f8c8d);
        }

        /* ========================================
           الحركات والتأثيرات (Animations)
        ======================================== */
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

        /* ========================================
           التصميم المتجاوب (Responsive Design)
        ======================================== */
        @media (max-width: 768px) {
            .sidebar {
                width: 60px;
            }

            .main-content {
                margin-right: 60px;
                padding: 20px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .roles-container {
                grid-column: span 1;
            }

            .user-profile-header {
                flex-direction: column;
                text-align: center;
            }

            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <!-- ========================================
         الشريط الجانبي للتنقل
    ======================================== -->
    <?= $this->include('layouts/header') ?>


    <div class="main-content">
        <div class="top-bar">
            <h1 class="page-title">معلومات المستخدم</h1>
            <?php if (isset($account)): ?>
                <div class="user-info">
                    <div class="user-avatar">
                        <?= strtoupper(substr(esc($account->name), 0, 1)) ?>
                    </div>
                    <span><?= esc($account->name) ?></span>
                </div>
            <?php endif; ?>
        </div>

        <?php if (isset($account)): ?>
            <div class="user-card">
                <div class="user-profile-header">
                    <div class="profile-avatar">
                        <?= strtoupper(substr(esc($account->name), 0, 1)) ?>
                    </div>
                    <div class="profile-basic-info">
                        <h2><?= esc($account->name) ?></h2>
                        <p>الرقم: <?= esc(isset($account->emp_id) ? $account->emp_id : $account->user_id) ?></p>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <label>
                            الاسم الكامل
                        </label>
                        <div class="info-value"><?= esc($account->name) ?></div>
                    </div>

                    <div class="info-item">
                        <label>
                            الرقم الوظيفي / رقم المستخدم
                        </label>
                        <div class="info-value"><?= esc(isset($account->emp_id) ? $account->emp_id : $account->user_id) ?></div>
                    </div>

                    <div class="info-item">
                        <label>
                            البريد الإلكتروني
                        </label>
                        <div class="info-value"><?= esc($account->email) ?></div>
                    </div>

                    <?php if (isset($account->emp_dept)): ?>
                        <div class="info-item">
                            <label>
                                القسم
                            </label>
                            <div class="info-value"><?= esc($account->emp_dept) ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($account->emp_ext)): ?>
                        <div class="info-item">
                            <label>
                                التحويلة
                            </label>
                            <div class="info-value"><?= esc($account->emp_ext) ?></div>
                        </div>
                    <?php endif; ?>

                    <div class="info-item roles-container">
                        <label>
                            الأدوار والصلاحيات
                        </label>
                        <?php if (!empty($account->roles)): ?>
                            <ul class="roles-list">
                                <?php foreach ($account->roles as $role): ?>
                                    <li><?= esc($role['name']) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="info-value">لا توجد أدوار محددة</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        <?php elseif (isset($error_message)): ?>
            <div class="error-message">
                <?= esc($error_message) ?>
            </div>
        <?php else: ?>
            <div class="no-data-message">
                لم يتم العثور على معلومات المستخدم.
            </div>
        <?php endif; ?>

        <div class="action-buttons">
            <?php
            // جلب عنوان URL السابق من الجلسة
            $back_url = session()->getFlashdata('previous_url');

            // إذا لم يكن هناك رابط سابق، ارجع إلى لوحة تحكم المسؤول (كحل احتياطي)
            if (empty($back_url) || strpos($back_url, 'UserInfo/getUserInfo') !== false) {
                $back_url = base_url('AdminController/dashboard');
            }
            ?>
            <a href="<?= esc($back_url) ?>" class="action-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M21 11H6.83l3.58-3.59L9 6l-6 6 6 6 1.41-1.41L6.83 13H21z" />
                </svg>
                العودة
            </a>
        </div>
    </div>
</body>

</html>