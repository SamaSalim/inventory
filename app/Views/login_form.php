<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - مدينة الملك عبدالله الطبية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="<?= base_url('public/assets/css/login.css') ?>">


</head>

<body>
    <div class="login-container">
        <!-- Left Panel with Logo -->
        <div class="left-panel">
            <div class="logo-container">
            </div>

            <div> <img src="<?= base_url() . 'public\assets\images\kamc1.png' ?>" alt="شعار مدينة الملك عبدالله الطبية">

            </div>
        </div>

        <!-- Right Panel with Login Form -->
        <div class="right-panel">
            <div class="login-form-container">
                <h2 class="login-title">تسجيل الدخول</h2>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= base_url('/login/login') ?>">
                    <div class="mb-4">
                        <label for="employeeId" class="form-label"dir ="rtl">الرقم الوظيفي</label>
                        <input type="text" class="form-control" id="employeeId" name="emp_id" required>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label" dir="rtl">الرقم السري</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required>
                            <span class="input-group-text" id="togglePassword">
                                <i class="fas fa-eye-slash"></i>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login">تسجيل الدخول</button>
                </form>
            </div>
        </div>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            const togglePassword = document.querySelector('#togglePassword');
            const passwordField = document.querySelector('#password');

            togglePassword.addEventListener('click', function(e) {
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        </script>
</body>

</html>