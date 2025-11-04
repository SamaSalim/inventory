<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة صلاحية</title>
    <style>
        /* ========================================
           أنماط موحدة لصفحات الإدخال (Forms)
        ======================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Cairo', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #F4F4F4;
            /* موحد */
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
            z-index: 1000;
        }

        .main-content {
            margin-right: 80px;
            padding: 30px 25px;
        }

        .container {
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            /* موحد */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            /* موحد */
            max-width: 600px;
            margin: 0 auto;
            border: 1px solid rgba(5, 117, 144, 0.1);
            animation: fadeIn 0.5s ease-out;
        }

        h1 {
            color: #1d3557;
            margin-bottom: 25px;
            font-size: 28px;
            font-weight: 700;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 15px;
        }

        /* ========================================
           أنماط المطلوبة (Required)
        ======================================== */
        .required {
            color: #dc3545;
            /* لون أحمر لتوضيح الإلزامية */
            font-weight: bold;
            margin-right: 3px;
        }

        /* ========================================
           رسائل التنبيه (موحدة)
        ======================================== */
        .message {
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 8px;
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

        .error,
        .validation-error {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border-color: #dc3545;
        }

        .validation-error ul {
            list-style: none;
            margin-top: 10px;
            padding-right: 0;
        }

        /* ========================================
           أنماط النموذج وحقول الإدخال
        ======================================== */
        .form-group {
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1d3557;
            font-size: 15px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
            color: #333;
            transition: border-color 0.3s, box-shadow 0.3s;
            background-color: #ffffff;
            /* لإزالة مظهر المتصفح الافتراضي لـ select */
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        /* تصميم Select مخصص */
        .form-group select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%231d3557'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: left 15px center;
            /* جعل السهم على اليسار */
            padding-left: 45px;
            /* لإفساح مجال للسهم */
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #057590;
            box-shadow: 0 0 0 3px rgba(5, 117, 144, 0.2);
            outline: none;
        }

        /* ========================================
           أزرار الإجراءات (Submit & Back)
        ======================================== */
        .form-actions {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .submit-btn,
        .back-btn {
            flex: 1;
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            justify-content: center;
            align-items: center;
        }

        /* زر الإرسال */
        .submit-btn {
            background: linear-gradient(135deg, #057590, #035a6b);
            color: white;
            box-shadow: 0 4px 10px rgba(5, 117, 144, 0.2);
        }

        .submit-btn:hover {
            background: linear-gradient(135deg, #035a6b, #014553);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(5, 117, 144, 0.3);
        }

        .submit-btn.loading {
            background: #5A7B90;
            cursor: default;
        }

        /* زر العودة/الإلغاء */
        .back-btn {
            background: #a9a9a9;
            color: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            min-width: 150px;
        }

        .back-btn:hover {
            background: #8e8e8e;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        /* ========================================
           تأثيرات CSS الرئيسية
        ======================================== */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
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

        /* Media Queries */
        @media (max-width: 768px) {
            .main-content {
                margin-right: 0;
                padding: 15px;
            }

            .container {
                padding: 20px;
            }

            .form-actions {
                flex-direction: column;
                gap: 10px;
            }

            .submit-btn,
            .back-btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <?= $this->include('layouts/header') ?>

    <div class="main-content">
        <div class="container">
            <h1>إضافة صلاحية جديدة</h1>

            <?php if (!empty(session()->getFlashdata('message'))): ?>
                <div class="message <?= esc(session()->getFlashdata('status')) ?>">
                    <?= esc(session()->getFlashdata('message')) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty(session()->getFlashdata('errors'))): ?>
                <div class="message validation-error">
                    <strong>خطأ في التحقق:</strong>
                    <ul>
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?= form_open('AdminController/addPermission', ['id' => 'permissionForm']) ?>

            <div class="form-group">
                <label for="emp_id">الرقم الوظيفي <span class="required">*</span></label>
                <input type="text" id="emp_id" name="emp_id" value="<?= old('emp_id') ?>" required>
                <?php if (isset($errors['emp_id'])): ?>
                    <p class="text-danger"><?= esc($errors['emp_id']) ?></p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="role_id">اختيار الدور <span class="required">*</span></label>
                <select id="role_id" name="role_id" required>
                    <option value="">اختر دوراً...</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= esc($role->id) ?>" <?= (old('role_id') == $role->id) ? 'selected' : '' ?>>
                            <?= esc($role->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['role_id'])): ?>
                    <p class="text-danger"><?= esc($errors['role_id']) ?></p>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="submit-btn" id="submitBtn">
                    إضافة الصلاحية
                </button>
                <a href="<?= base_url('AdminController/dashboard'); ?>" class="back-btn">
                    عودة إلى لوحة التحكم
                </a>
            </div>
            <?= form_close() ?>
        </div>
    </div>

    <script>
        // تأثيرات تفاعلية للنموذج
        document.getElementById('permissionForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.classList.add('loading');
            submitBtn.textContent = 'جاري الإضافة...';
        });

        // تأثيرات للحقول
        document.querySelectorAll('input, select').forEach(element => {
            element.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });

            element.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });

        // تأثير نبضة للرسائل
        document.querySelectorAll('.message').forEach(message => {
            message.style.animation = 'slideInDown 0.5s ease-out';
            if (message.classList.contains('success')) {
                message.style.animation += ', pulse 2s infinite';
            }
        });
    </script>
</body>

</html>