<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة موظف جديد</title>
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
            direction: rtl;
            text-align: right;
            min-height: 100vh;
            color: black;
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
            /* أعلى من باقي العناصر */
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
           المحتوى الرئيسي
        ======================================== */
        /* .main-content {
            margin-right: 80px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        } */
        .main-content {
            margin-right: 80px;
            /* ترك مساحة للشريط الجانبي */
            padding: 30px;
        }

        /* ========================================
           حاوية النموذج (Dashboard Item style)
        ======================================== */
        /* .container {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(240, 248, 255, 0.9) 100%);
            backdrop-filter: blur(15px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 75, 107, 0.15);
            max-width: 650px;
            width: 100%;
            border: 2px solid rgba(0, 75, 107, 0.1);
            position: relative;
            overflow: hidden;
            animation: slideInUp 0.6s ease-out;
        } */

        .container {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(240, 248, 255, 0.9) 100%);
            backdrop-filter: blur(15px);
            padding: 40px;
            border-radius: 0px;
            box-shadow: 0 20px 60px rgba(0, 75, 107, 0.15);
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

        .form-row {
            display: flex;
            gap: 15px;
        }

        .form-row .form-group {
            flex: 1;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #1d3557;
            font-size: 16px;
            letter-spacing: 0.3px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 15px 20px;
            border-radius: 15px;
            border: 2px solid rgba(0, 75, 107, 0.2);
            background: rgba(255, 255, 255, 0.9);
            color: #1d3557;
            font-size: 15px;
            outline: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #168aad;
            background: rgba(255, 255, 255, 1);
            box-shadow: 0 0 0 3px rgba(22, 138, 173, 0.1);
            transform: translateY(-2px);
        }

        /* ========================================
           أزرار النموذج (Dashboard Action Button Style)
        ======================================== */
        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .submit-btn {
            flex: 1;
            background: #057590;
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
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
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
        }

        .submit-btn:active {
            transform: translateY(-1px) scale(1.01);
        }

        /* تعديل تصميم زر العودة ليطابق لوحة التحكم */
        .back-btn {
            flex: 1;
            background: #057590;
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
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .back-btn:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 35px rgba(22, 138, 173, 0.4);
        }

        .back-btn:active {
            transform: translateY(-1px) scale(1.01);
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

            .form-row {
                flex-direction: column;
                gap: 0;
            }

            .button-group {
                flex-direction: column;
            }

            .back-btn {
                flex: 1;
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
    </style>
</head>

<body>
    <!-- الشريط الجانبي للتنقل -->

    <?= $this->include('layouts/header') ?>
    <div class="main-content">
        <div class="container">
            <h1>إضافة موظف جديد</h1>
            <?php if (session()->has('errors')): ?>
                <div class="message error validation-errors">
                    <ul>
                        <?php foreach (session("errors") as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <?= form_open('AdminController/addEmployee', ['id' => 'employeeForm']) ?>
            <div class="form-row">
                <div class="form-group">
                    <label for="emp_id">رقم الموظف:</label>
                    <input type="text"
                        id="emp_id"
                        name="emp_id"
                        value="<?= old('emp_id') ?>"
                        placeholder="أدخل رقم الموظف"
                        required>
                </div>
                <div class="form-group">
                    <label for="name">اسم الموظف:</label>
                    <input type="text"
                        id="name"
                        name="name"
                        value="<?= old('name') ?>"
                        placeholder="أدخل اسم الموظف"
                        required>
                </div>
            </div>
            <div class="form-group">
                <label for="email">البريد الإلكتروني:</label>
                <input type="email"
                    id="email"
                    name="email"
                    value="<?= old('email') ?>"
                    placeholder="أدخل البريد الإلكتروني"
                    required>
            </div>
            <div class="form-group">
                <label for="password">كلمة المرور:</label>
                <input type="password"
                    id="password"
                    name="password"
                    value="<?= old('password') ?>"
                    placeholder="أدخل كلمة المرور"
                    required>
            </div>
            <div class="button-group">
                <button type="submit" class="submit-btn" id="submitBtn">
                    إضافة الموظف
                </button>
                <a href="<?= base_url('AdminController/dashboard'); ?>" class="back-btn">
                    عودة إلى لوحة التحكم
                </a>
            </div>
            <?= form_close() ?>
        </div>
    </div>
    <script>
        document.getElementById('employeeForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.classList.add('loading');
            submitBtn.textContent = 'جاري الإضافة...';
        });
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
        document.querySelectorAll('.message').forEach(message => {
            message.style.animation = 'slideInDown 0.5s ease-out, pulse 2s infinite';
        });
        const style = document.createElement('style');
        style.textContent = `
            @keyframes pulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.02); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>

</html>