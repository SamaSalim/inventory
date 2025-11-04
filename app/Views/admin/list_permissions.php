<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قائمة الصلاحيات</title>
    <style>
        /* ========================================
           أنماط عامة وموحدة (مستوحاة من warehouse-style)
        ======================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Cairo', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #F4F4F4;
            direction: rtl;
            text-align: right;
            min-height: 100vh;
            color: #333;
            padding: 0;
            /* تم تعديل البادينج */
        }

        /* ========================================
           تصميم الشريط الجانبي والمحتوى (المفترض وجوده)
        ======================================== */
        .sidebar {
            position: fixed;
            right: 0;
            top: 0;
            height: 100vh;
            width: 80px;
            background-color: #057590;
            z-index: 1000;
        }

        .main-content {
            margin-right: 80px;
            padding: 30px 25px;
            /* تم تعديل البادينج */
        }

        .container {
            background-color: white;
            /* توحيد لون الخلفية */
            padding: 30px;
            /* تم تصغير البادينج قليلاً */
            border-radius: 15px;
            /* توحيد نصف القطر */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            /* توحيد الظل */
            max-width: 100%;
            margin: 0 auto;
            border: 1px solid rgba(5, 117, 144, 0.1);
        }

        h1 {
            color: #1d3557;
            text-align: right;
            /* تم تغييرها لتكون يمين */
            margin-bottom: 25px;
            font-size: 24px;
            font-weight: 700;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 10px;
        }

        /* ========================================
           رسائل التنبيه (موحدة)
        ======================================== */
        .message {
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 8px;
            /* توحيد نصف القطر */
            text-align: right;
            font-weight: 500;
            font-size: 14px;
            border: 1px solid;
            animation: slideInDown 0.5s ease-out;
        }

        .success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border-color: #28a745;
        }

        .error {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border-color: #dc3545;
        }

        /* ========================================
           تصميم الجدول (موحد)
        ======================================== */
        .table-container {
            overflow-x: auto;
            margin-bottom: 20px;
            /* مسافة قبل أزرار الإجراءات */
        }

        table {
            width: 100%;
            min-width: 700px;
            border-collapse: collapse;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border-radius: 10px;
            overflow: hidden;
            background-color: white;
        }

        th,
        td {
            padding: 12px 10px;
            /* توحيد البادينج */
            text-align: center;
            border-bottom: 1px solid #f0f0f0;
            font-size: 13px;
            /* توحيد حجم الخط */
            color: #555;
            white-space: nowrap;
        }

        th {
            background-color: #057590;
            /* لون الخلفية الرئيسي */
            color: white;
            font-weight: 600;
            font-size: 14px;
            text-align: center;
        }



        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tr:hover {
            background-color: rgba(5, 117, 144, 0.05);
            /* تأثير عند التمرير */
        }

        /* ========================================
           أزرار الإجراءات (موحدة)
        ======================================== */
        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
            align-items: center;
            flex-wrap: nowrap;
        }

        .action-btn {
            background: linear-gradient(135deg, #057590, #046073);
            /* لون التعديل */
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 16px;
            /* توحيد نصف القطر */
            font-size: 11px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            min-width: 70px;
            justify-content: center;
        }

        .action-btn:hover {
            background: linear-gradient(135deg, #046073, #035a6b);
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(5, 117, 144, 0.35);
        }

        .delete-form {
            display: inline;
            margin: 0;
            padding: 0;
        }

        .delete-btn {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            /* لون الحذف */
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 16px;
            font-size: 11px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            min-width: 70px;
            justify-content: center;
        }

        .delete-btn:hover {
            background: linear-gradient(135deg, #c0392b, #a93226);
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(231, 76, 60, 0.35);
        }

        .no-permissions {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }

        /* ========================================
           تذييل وإجراءات العودة (تمت الإضافة)
        ======================================== */
        .list-actions-footer {
            display: flex;
            justify-content: flex-end;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .back-to-dashboard-btn {
            background: linear-gradient(135deg, #3ac0c3, #2aa8ab);
            /* لون زر العودة */
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .back-to-dashboard-btn:hover {
            background: linear-gradient(135deg, #2aa8ab, #259a9d);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(58, 192, 195, 0.4);
        }

        /* ========================================
           تصميم متجاوب
        ======================================== */
        @media (max-width: 768px) {
            .main-content {
                margin-right: 0;
                padding: 15px;
            }

            .sidebar {
                display: none;
            }

            .container {
                padding: 20px;
            }

            table {
                font-size: 11px;
            }

            th,
            td {
                padding: 10px 6px;
            }

            .action-btn,
            .delete-btn {
                min-width: 50px;
                padding: 6px 10px;
                font-size: 10px;
            }
        }
    </style>
</head>

<body>
    <?= $this->include('layouts/header') ?>


    <div class="main-content">
        <div class="container">
            <h1>قائمة الصلاحيات</h1>

            <?php if (!empty($message)): ?>
                <div class="message <?= esc($status) ?>">
                    <?= esc($message) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($permissions)): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الرقم الوظيفي</th>
                                <th>الموظف</th>
                                <th>الدور</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1;
                            foreach ($permissions as $permission): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= esc($permission->emp_id) ?></td>
                                    <td><?= esc($permission->emp_name) ?></td>
                                    <td><?= esc($permission->role_name) ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="<?= base_url('AdminController/updatePermission/' . $permission->id) ?>" class="action-btn">
                                                تعديل
                                            </a>
                                            <form action="<?= base_url('AdminController/deletePermission/' . $permission->id) ?>" method="post" class="delete-form" onsubmit="return confirm('هل أنت متأكد من حذف هذه الصلاحية؟')">
                                                <button type="submit" class="delete-btn">
                                                    حذف
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-permissions">
                    <p>لا توجد صلاحيات لعرضها حاليًا.</p>
                    <a href="<?= base_url('AdminController/addPermission') ?>" class="back-to-dashboard-btn" style="margin-top: 20px;">
                        إضافة صلاحية جديدة
                    </a>
                </div>
            <?php endif; ?>

            <div class="list-actions-footer">
                <a href="<?= base_url('AdminController/dashboard') ?>" class="back-to-dashboard-btn">
                    العودة إلى لوحة التحكم
                </a>
            </div>
        </div>
    </div>
</body>

</html>