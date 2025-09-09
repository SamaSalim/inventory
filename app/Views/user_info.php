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
            z-index: 1000; /* أعلى من باقي العناصر */
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
            gap: 15px;
            margin-top: 20px;
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
            gap: 8px;
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


    <!-- المحتوى الرئيسي -->
    <div class="main-content">
        <!-- الشريط العلوي -->
        <div class="top-bar">
            <h1 class="page-title">معلومات المستخدم</h1>
            <div class="user-info">
    <div class="user-avatar">
        <?= strtoupper(substr(esc($employee->name), 0, 1)) ?>
    </div>
    <span><?= esc($employee->name) ?></span>
</div>
        </div>

        <!-- محتوى الصفحة -->
        <?php if (isset($employee) && $employee instanceof \App\Entities\Employee): ?>
            <div class="user-card">
                <!-- رأس الملف الشخصي -->
                <div class="user-profile-header">
                    <div class="profile-avatar">
                        <?= strtoupper(substr(esc($employee->name), 0, 1)) ?>
                    </div>
                    <div class="profile-basic-info">
                        <h2><?= esc($employee->name) ?></h2>
                        <p>رقم الموظف: <?= esc($employee->emp_id) ?></p>
                    </div>
                </div>

                <!-- شبكة المعلومات -->
                <div class="info-grid">
                    <div class="info-item">
                        <label>
                            <svg class="info-icon" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                            الاسم الكامل
                        </label>
                        <div class="info-value"><?= esc($employee->name) ?></div>
                    </div>

                    <div class="info-item">
                        <label>
                            <svg class="info-icon" viewBox="0 0 24 24">
                                <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                            </svg>
                            الرقم الوظيفي
                        </label>
                        <div class="info-value"><?= esc($employee->emp_id) ?></div>
                    </div>

                    <div class="info-item">
                        <label>
                            <svg class="info-icon" viewBox="0 0 24 24">
                                <path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z"/>
                            </svg>
                            البريد الإلكتروني
                        </label>
                        <div class="info-value"><?= esc($employee->email) ?></div>
                    </div>

                    <div class="info-item roles-container">
                        <label>
                            <svg class="info-icon" viewBox="0 0 24 24">
                                <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                            </svg>
                            الأدوار والصلاحيات
                        </label>
                        <?php if (!empty($employee->roles)): ?>
                            <ul class="roles-list">
                                <?php foreach ($employee->roles as $role): ?>
                                    <li><?= esc($role['name']) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="info-value">لا توجد أدوار محددة</div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- أزرار الإجراءات
                <div class="action-buttons">
                    <a href="#" class="action-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                        </svg>
                        تعديل البيانات
                    </a>
                    <a href="#" class="action-btn secondary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1H5C3.9 1 3 1.9 3 3V7H21V9Z"/>
                        </svg>
                        طباعة البيانات
                    </a>
                </div>
            </div> -->

        <?php elseif (isset($error_message)): ?>
            <div class="error-message">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
                </svg>
                <?= esc($error_message) ?>
            </div>
            
        <?php else: ?>
            <div class="no-data-message">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,17A1.5,1.5 0 0,1 10.5,15.5A1.5,1.5 0 0,1 12,14A1.5,1.5 0 0,1 13.5,15.5A1.5,1.5 0 0,1 12,17M12,10A1,1 0 0,1 13,11V15A1,1 0 0,1 12,16A1,1 0 0,1 11,15V11A1,1 0 0,1 12,10Z"/>
                </svg>
                لم يتم العثور على معلومات المستخدم.
            </div>
        <?php endif; ?>
        
    </div>
</body>
</html>