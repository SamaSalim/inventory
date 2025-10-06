<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قائمة الصلاحيات</title>
    <style>
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
            padding: 20px;
        }

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
        }

        .sidebar .logo {
            color: white;
            font-size: 24px;
            margin-bottom: 40px;
        }

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
            transition: all 0.3s ease;
        }

        .sidebar-icon:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(-5px);
        }

        .main-content {
            margin-right: 80px;
            padding: 30px;
        }

        .container {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(240, 248, 255, 0.9) 100%);
            backdrop-filter: blur(15px);
            padding: 40px;
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0, 75, 107, 0.15);
            max-width: 900px;
            margin: 0 auto;
            border: 2px solid rgba(0, 75, 107, 0.1);
        }

        h1 {
            color: #1d3557;
            text-align: center;
            margin-bottom: 35px;
            font-size: 28px;
            font-weight: 700;
        }

        .message {
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 15px;
            text-align: center;
            font-weight: 600;
            font-size: 15px;
            border: 2px solid;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border-radius: 15px;
            overflow: hidden;
        }

        th,
        td {
            padding: 15px;
            text-align: right;
            border-bottom: 1px solid #e0e6ef;
        }

        th {
            background: linear-gradient(90deg, #1d3557 0%, #004b6b 100%);
            color: white;
            font-weight: 600;
            font-size: 16px;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tr:hover {
            background-color: #e9ecef;
            transition: background-color 0.3s ease;
        }

        .action-btn {
            background: linear-gradient(135deg, #168aad 0%, #1d3557 100%);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin-left: 5px;
        }

        .action-btn:hover {
            background: linear-gradient(135deg, #1d3557 0%, #168aad 100%);
            transform: scale(1.05);
        }

        .delete-btn {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .delete-btn:hover {
            background: linear-gradient(135deg, #c82333 0%, #dc3545 100%);
            transform: scale(1.05);
        }

        .no-permissions {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }
    </style>
</head>

<body>
     <!-- ========================================
         الشريط الجانبي للتنقل
    ======================================== -->
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
                <table>
                    <thead>
                        <tr>
                            <th>الرقم الوظيفي</th>
                            <th>الموظف</th>
                            <th>الدور</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($permissions as $permission): ?>
                            <tr>
                                <td><?= esc($permission->emp_id) ?></td>
                                <td><?= esc($permission->emp_name) ?></td>
                                <td><?= esc($permission->role_name) ?></td>
                                <td>
                                    <a href="<?= base_url('AdminController/updatePermission/' . $permission->id) ?>" class="action-btn">
                                        ✏ تعديل
                                    </a>
                                    <form action="<?= base_url('AdminController/deletePermission/' . $permission->id) ?>" method="post" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذه الصلاحية؟')">
                                        <button type="submit" class="delete-btn">
                                            🗑 حذف
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-permissions">
                    <p>لا توجد صلاحيات لعرضها حاليًا.</p>
                    <a href="<?= base_url('AdminController/addPermission') ?>" class="action-btn" style="margin-top: 20px;">
                        إضافة صلاحية جديدة
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>