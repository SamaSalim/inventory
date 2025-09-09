<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>404 - الصفحة غير موجودة</title>
    <style>
        body { font-family: Tahoma, sans-serif; background: #f8f9fa; color: #333; text-align: center; padding: 50px; }
        .container { max-width: 600px; margin: auto; }
        h1 { font-size: 48px; color: #dc3545; }
        p { font-size: 18px; }
        a { display: inline-block; margin-top: 20px; text-decoration: none; padding: 10px 20px; background: #007bff; color: white; border-radius: 5px; }
        a:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>404</h1>
        <p><?= esc($message ?? 'الصفحة المطلوبة غير موجودة') ?></p>
        <a href="<?= site_url('/') ?>">العودة للصفحة الرئيسية</a>
    </div>
</body>
</html>
