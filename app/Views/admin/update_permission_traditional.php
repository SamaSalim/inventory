<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تحديث صلاحية</title>
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ffffff 0%, #e6f0f6 100%);
            direction: rtl;
            /* اتجاه النص من اليمين لليسار */
            text-align: right;
            min-height: 100vh;
            color: black;
            padding: 20px;
            position: relative;
        }

        /* خلفية تصميمية */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M 20 0 L 0 0 0 20" fill="none" stroke="rgba(0,75,107,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            z-index: -1;
        }

        /* ========================================
           الشريط الجانبي (Sidebar)
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

        /* ========================================
           المحتوى الرئيسي
        ======================================== */
        .main-content {
            margin-right: 80px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        /* ========================================
           حاوية النموذج الرئيسية
        ======================================== */
        .container {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(240, 248, 255, 0.9) 100%);
            backdrop-filter: blur(15px);
            padding: 40px;
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0, 75, 107, 0.15);
            max-width: 650px;
            width: 100%;
            border: 2px solid rgba(0, 75, 107, 0.1);
            position: relative;
            overflow: hidden;
            animation: slideInUp 0.6s ease-out;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #168aad, #1d3557, #004b6b);
        }

        /* ========================================
           العنوان الرئيسي
        ======================================== */
        h1 {
            color: #1d3557;
            text-align: center;
            margin-bottom: 35px;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 0.5px;
            position: relative;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #168aad, #1d3557);
            border-radius: 2px;
        }

        /* ========================================
           تصميم النموذج
        ======================================== */
        form {
            margin-top: 30px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #1d3557;
            font-size: 16px;
            letter-spacing: 0.3px;
        }

        select,
        input[type="hidden"] {
            width: 100%;
            padding: 15px 20px;
            border-radius: 15px;
            border: 2px solid rgba(0, 75, 107, 0.2);
            background: rgba(255, 255, 255, 0.9);
            color: #1d3557;
            font-size: 15px;
            outline: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            appearance: none;
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="%23168aad" stroke-width="2"><polyline points="6,9 12,15 18,9"></polyline></svg>');
            background-repeat: no-repeat;
            background-position: left 15px center;
            background-size: 20px;
            cursor: pointer;
        }

        input[type="hidden"] {
            display: none;
        }

        select:focus {
            border-color: #168aad;
            background: rgba(255, 255, 255, 1);
            box-shadow: 0 0 0 3px rgba(22, 138, 173, 0.1);
            transform: translateY(-2px);
        }

        select option {
            background: white;
            color: #1d3557;
            padding: 10px;
        }

        /* ========================================
           الأزرار
        ======================================== */
        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .submit-btn {
            flex: 1;
            padding: 16px 30px;
            background: linear-gradient(135deg, #168aad 0%, #1d3557 50%, #004b6b 100%);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 8px 25px rgba(22, 138, 173, 0.3);
            letter-spacing: 0.5px;
            text-transform: uppercase;
            position: relative;
            overflow: hidden;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s ease;
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .submit-btn:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 35px rgba(22, 138, 173, 0.4);
            background: linear-gradient(135deg, #1d3557 0%, #004b6b 50%, #168aad 100%);
        }

        .submit-btn:active {
            transform: translateY(-1px) scale(1.01);
        }

        /* ========================================
           قسم زر الحذف
        ======================================== */
        .delete-section {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px dashed rgba(220, 53, 69, 0.3);
            text-align: center;
        }

        .delete-warning {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            color: #856404;
            padding: 15px 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            border: 2px solid #ffc107;
            font-weight: 600;
            animation: pulse 2s infinite;
        }

        .delete-btn {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 50%, #bd2130 100%);
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 50px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.3);
            letter-spacing: 0.5px;
            text-transform: uppercase;
            position: relative;
            overflow: hidden;
            min-width: 200px;
        }

        .delete-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s ease;
        }

        .delete-btn:hover::before {
            left: 100%;
        }

        .delete-btn:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 12px 30px rgba(220, 53, 69, 0.4);
            background: linear-gradient(135deg, #c82333 0%, #bd2130 50%, #dc3545 100%);
        }

        .delete-btn:active {
            transform: translateY(-1px) scale(1.02);
        }

        /* ========================================
           رسائل التنبيه
        ======================================== */
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
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);
        }

        .error {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border-color: #dc3545;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.2);
        }

        .validation-errors {
            color: #dc3545;
            font-size: 14px;
            margin-top: -15px;
            margin-bottom: 15px;
            background: rgba(248, 215, 218, 0.5);
            padding: 10px 15px;
            border-radius: 10px;
            border-right: 4px solid #dc3545;
        }

        .validation-errors ul {
            list-style: none;
            padding: 0;
        }

        .validation-errors li {
            margin-bottom: 5px;
            position: relative;
            padding-right: 20px;
        }

        .validation-errors li::before {
            content: '⚠️';
            position: absolute;
            right: 0;
            top: 0;
        }

        /* ========================================
           رسالة عدم وجود البيانات
        ======================================== */
        .not-found {
            text-align: center;
            color: #6c757d;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 40px;
            border-radius: 20px;
            border: 2px dashed #dee2e6;
            animation: fadeIn 0.6s ease-out;
        }

        .not-found-icon {
            font-size: 48px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .not-found a {
            color: #168aad;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .not-found a:hover {
            color: #1d3557;
            text-decoration: underline;
        }

        /* ========================================
           الحركات والتأثيرات
        ======================================== */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.02);
            }
        }

        /* تأثير التحميل */
        .loading {
            position: relative;
            pointer-events: none;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            transform: translate(-50%, -50%);
        }

        @keyframes spin {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }

            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        /* ========================================
           التصميم المتجاوب
        ======================================== */
        @media (max-width: 768px) {
            .sidebar {
                width: 60px;
            }

            .main-content {
                margin-right: 60px;
                padding: 15px;
            }

            .container {
                padding: 25px;
                margin: 10px;
            }

            h1 {
                font-size: 24px;
            }

            .btn-group {
                flex-direction: column;
            }

            select,
            .submit-btn,
            .delete-btn {
                padding: 12px 15px;
            }
        }

        @media (max-width: 480px) {
            .main-content {
                margin-right: 0;
                padding: 10px;
            }

            .sidebar {
                display: none;
            }

            .container {
                padding: 20px;
                border-radius: 15px;
            }

            h1 {
                font-size: 20px;
            }
        }

        /* ========================================
           تحسينات إضافية
        ======================================== */
        .form-group {
            position: relative;
        }

        .form-group::before {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #168aad, #1d3557);
            transition: width 0.3s ease;
        }

        .form-group:focus-within::before {
            width: 100%;
        }

        /* تأثير نبضة للعناصر المهمة */
        .container {
            animation: slideInUp 0.6s ease-out, glow 2s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from {
                box-shadow: 0 20px 60px rgba(0, 75, 107, 0.15);
            }

            to {
                box-shadow: 0 20px 60px rgba(0, 75, 107, 0.25);
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
        <div class="container">
            <h1>تحديث صلاحية</h1>

            <?php if (!empty(session()->getFlashdata('message'))): ?>
                <div class="message <?= esc(session()->getFlashdata('status')) ?>">
                    <?= esc(session()->getFlashdata('message')) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($permission) && !empty($permission)): ?>
                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="message error validation-errors">
                        <ul>
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?= form_open('AdminController/updatePermission/' . esc($permission->id), ['id' => 'updateForm']) ?>
                <input type="hidden" name="id" value="<?= esc($permission->id) ?>">

                <div class="form-group">
                    <label for="emp_id">الموظف:</label>
                    <select id="emp_id" name="emp_id" required>
                        <option value="">اختر موظف</option>
                        <?php foreach ($employees as $employee): ?>
                            <option value="<?= esc($employee->emp_id) ?>"
                                <?= set_select('emp_id', $employee->emp_id, ($employee->emp_id == old('emp_id', $permission->emp_id))) ?>>
                                <?= esc($employee->name) ?> (<?= esc($employee->email) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="role_id">الدور:</label>
                    <select id="role_id" name="role_id" required>
                        <option value="">اختر دور</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= esc($role->id) ?>"
                                <?= set_select('role_id', $role->id, ($role->id == old('role_id', $permission->role_id))) ?>>
                                <?= esc($role->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="submit-btn" id="updateBtn">
                    تحديث الصلاحية
                </button>
                <?= form_close() ?>

                <!-- قسم حذف الصلاحية -->
                <div class="delete-section">
                    <div class="delete-warning">
                        ⚠️ تحذير: حذف الصلاحية سيؤدي إلى إزالة الدور نهائياً من الموظف
                    </div>
                    <form action="<?= base_url('AdminController/deletePermission/' . esc($permission->id)) ?>" method="post" id="deleteForm">
                        <button type="button" class="delete-btn" onclick="confirmDelete()">
                            🗑️ حذف الصلاحية
                        </button>
                    </form>
                </div>

            <?php else: ?>
                <div class="not-found">
                    <div class="not-found-icon">🔍</div>
                    <p>لم يتم العثور على صلاحية بهذا المعرف.</p>
                    <p>يرجى التأكد من الرابط أو الانتقال إلى
                        <a href="<?= base_url('AdminController/addPermission') ?>">صفحة إضافة الصلاحيات</a>.
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // تأثيرات تفاعلية للنموذج
        document.getElementById('updateForm')?.addEventListener('submit', function(e) {
            const updateBtn = document.getElementById('updateBtn');
            updateBtn.classList.add('loading');
            updateBtn.textContent = 'جاري التحديث...';
        });

        // تأكيد الحذف مع تأثيرات بصرية
        function confirmDelete() {
            // إنشاء نافذة تأكيد مخصصة
            const confirmModal = document.createElement('div');
            confirmModal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.7);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;
                backdrop-filter: blur(5px);
            `;

            confirmModal.innerHTML = `
                <div style="
                    background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(240, 248, 255, 0.9) 100%);
                    padding: 30px;
                    border-radius: 20px;
                    text-align: center;
                    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                    border: 2px solid rgba(220, 53, 69, 0.3);
                    max-width: 400px;
                    animation: slideInScale 0.3s ease-out;
                ">
                    <div style="font-size: 48px; margin-bottom: 20px;">⚠️</div>
                    <h3 style="color: #dc3545; margin-bottom: 15px;">تأكيد الحذف</h3>
                    <p style="color: #666; margin-bottom: 25px;">هل أنت متأكد من رغبتك في حذف هذه الصلاحية نهائياً؟</p>
                    <div style="display: flex; gap: 10px; justify-content: center;">
                        <button onclick="cancelDelete()" style="
                            background: #6c757d;
                            color: white;
                            border: none;
                            padding: 10px 20px;
                            border-radius: 25px;
                            cursor: pointer;
                            transition: all 0.3s ease;
                        ">إلغاء</button>
                        <button onclick="confirmDeleteAction()" style="
                            background: linear-gradient(135deg, #dc3545, #c82333);
                            color: white;
                            border: none;
                            padding: 10px 20px;
                            border-radius: 25px;
                            cursor: pointer;
                            transition: all 0.3s ease;
                        ">حذف نهائي</button>
                    </div>
                </div>
            `;

            document.body.appendChild(confirmModal);

            // إضافة التأثيرات
            const style = document.createElement('style');
            style.textContent = `
                @keyframes slideInScale {
                    from {
                        opacity: 0;
                        transform: scale(0.7) translateY(-50px);
                    }
                    to {
                        opacity: 1;
                        transform: scale(1) translateY(0);
                    }
                }
            `;
            document.head.appendChild(style);

            // وظائف التأكيد والإلغاء
            window.cancelDelete = function() {
                confirmModal.remove();
                style.remove();
            };

            window.confirmDeleteAction = function() {
                document.getElementById('deleteForm').submit();
            };
        }

        // تأثيرات للحقول
        document.querySelectorAll('select').forEach(select => {
            select.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });

            select.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });

        // تأثير نبضة للرسائل
        document.querySelectorAll('.message').forEach(message => {
            message.style.animation = 'slideInDown 0.5s ease-out, pulse 2s infinite';
        });
    </script>
</body>

</html>