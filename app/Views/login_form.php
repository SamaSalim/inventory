<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - مدينة الملك عبدالله الطبية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            background: #057590;
        }
        
        .login-container {
            display: flex;
            height: 100vh;
        }
        
        .left-panel {
            flex: 1;
            background: #057590;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            padding: 2rem;
        }
        
        .logo-container {
            text-align: center;
        }
        
        .logo {
            width: 150px;
            height: 150px;
            margin-bottom: 2rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.2);
        }
        
        .logo svg {
            width: 80px;
            height: 80px;
            fill: white;
        }
        
        .hospital-name {
            font-size: 1.8rem;
            font-weight: 300;
            line-height: 1.4;
            text-align: center;
        }
        
        .hospital-name-ar {
            font-size: 1.6rem;
            margin-bottom: 0.5rem;
        }
        
        .hospital-name-en {
            font-size: 1.4rem;
            opacity: 0.9;
        }
        
        .right-panel {
            flex: 1;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .login-form-container {
            background: white;
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        
        .login-title {
            color: #057590;
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            color: #666;
            font-weight: 500;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.8rem 1rem;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #057590;
            box-shadow: 0 0 0 0.2rem rgba(5, 117, 144, 0.25);
        }
        
        .btn-login {
            background: #057590;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            width: 100%;
            margin-top: 1rem;
            transition: transform 0.2s ease;
        }
        
        .btn-login:hover {
            background: #046274;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(5, 117, 144, 0.3);
        }
        
        .admin-link {
            text-align: center;
            margin-top: 2rem;
        }
        
        .admin-link a {
            color: #6c757d;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .admin-link a:hover {
            color: #057590;
        }
        
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }
            
            .left-panel {
                flex: none;
                height: 30vh;
                padding: 1rem;
            }
            
            .right-panel {
                flex: 1;
                padding: 1rem;
            }
            
            
            
            .hospital-name {
                font-size: 1.2rem;
            }
            
            .hospital-name-ar {
                font-size: 1.1rem;
            }
            
            .hospital-name-en {
                font-size: 1rem;
            }
            
            .login-form-container {
                padding: 2rem;
            }
            .logo img {
             max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            }

            

        }
    </style>
</head>
<body>
 <div class="login-container">
        <!-- Left Panel with Logo -->
        <div class="left-panel">
            <div class="logo-container">
</div>

                     <div>   <img src="<?= base_url().'public\assets\images\kamc1.png' ?>" alt="شعار مدينة الملك عبدالله الطبية">           
                  
                </div>
        </div>

<!-- Right Panel with Login Form -->
<div class="right-panel">
    <div class="login-form-container">
        <h2 class="login-title">تسجيل الدخول</h2>

        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= base_url('/login/login') ?>">
            <div class="mb-4">
                <label for="employeeId" class="form-label">الرقم الوظيفي</label>
                <input type="text" class="form-control" id="employeeId" name="emp_id" required>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">الرقم السري</label>
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

        togglePassword.addEventListener('click', function (e) {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>