<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إرجاع الأصل</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: backgroundShift 10s ease-in-out infinite alternate;
        }
        
        @keyframes backgroundShift {
            0% { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); }
            100% { background: linear-gradient(135deg, #e8f4f8 0%, #d1e7dd 100%); }
        }
        
        .sidebar {
            position: fixed;
            right: 0;
            top: 0;
            width: 80px;
            height: 100vh;
            background: linear-gradient(135deg, #2c8aa6, #1e6b7a);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px 10px;
            box-shadow: -2px 0 20px rgba(0,0,0,0.15);
            backdrop-filter: blur(10px);
            z-index: 1000;
        }
        
        .sidebar-icon {
            background: rgba(255,255,255,0.2);
            width: 50px;
            height: 50px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            color: white;
            font-size: 24px;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .sidebar-icon::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s;
        }
        
        .sidebar-icon:hover {
            background: rgba(255,255,255,0.35);
            transform: scale(1.15) rotate(5deg);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        
        .sidebar-icon:hover::before {
            left: 100%;
        }
        
        .main-content {
            margin: 20px 100px;
            padding: 20px;
            max-width: 850px;
            width: 100%;
            animation: slideIn 0.8s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .transfer-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.3s ease;
        }
        
        .transfer-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .transfer-header {
            background: linear-gradient(135deg, #2c8aa6, #1e6b7a);
            color: white;
            padding: 25px;
            text-align: center;
            font-size: 26px;
            font-weight: bold;
            position: relative;
            overflow: hidden;
        }
        
        .transfer-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            animation: shimmer 3s infinite;
        }
        
        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }
        
        .close-btn {
            position: absolute;
            top: 15px;
            left: 20px;
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 10;
        }
        
        .close-btn:hover {
            background: rgba(255,255,255,0.35);
            transform: rotate(90deg) scale(1.1);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .transfer-form {
            padding: 35px;
        }
        
        .form-section {
            margin-bottom: 35px;
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
        }
        
        .form-section:nth-child(2) { animation-delay: 0.1s; }
        .form-section:nth-child(3) { animation-delay: 0.2s; }
        .form-section:nth-child(4) { animation-delay: 0.3s; }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .section-title {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            color: #2c8aa6;
            padding: 18px;
            margin-bottom: 25px;
            border-radius: 12px;
            font-size: 19px;
            font-weight: bold;
            border-right: 5px solid #2c8aa6;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        
        .section-title:hover {
            transform: translateX(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .info-card {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            padding: 25px;
            border-radius: 15px;
            border-right: 5px solid #2196f3;
            margin-bottom: 25px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .info-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .info-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(33, 150, 243, 0.2);
        }
        
        .info-card:hover::before {
            opacity: 1;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
            padding: 10px;
            border-radius: 8px;
            background: rgba(255,255,255,0.3);
            transition: all 0.3s ease;
        }
        
        .info-item:hover {
            background: rgba(255,255,255,0.6);
            transform: scale(1.02);
        }
        
        .info-label {
            font-size: 13px;
            color: #555;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .info-value {
            font-weight: 700;
            color: #2c3e50;
            font-size: 15px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 25px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        label {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 15px;
            transition: color 0.3s ease;
        }
        
        input, select, textarea {
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 14px;
            background: #f9f9f9;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #2c8aa6;
            background: white;
            box-shadow: 0 0 0 4px rgba(44, 138, 166, 0.1);
            transform: translateY(-2px);
        }
        
        .form-group:focus-within label {
            color: #2c8aa6;
            transform: translateY(-2px);
        }
        
        .readonly {
            background: #e9ecef;
            color: #6c757d;
            cursor: not-allowed;
        }
        
        textarea {
            resize: vertical;
            min-height: 120px;
            font-family: inherit;
        }

        .status-display {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            border: 2px solid #f0ad4e;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s ease;
        }

        .status-display:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(240, 173, 78, 0.2);
        }

        .status-icon {
            background: #f0ad4e;
            color: white;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: bold;
        }

        .status-text {
            flex: 1;
        }

        .status-label {
            font-size: 14px;
            color: #856404;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .status-value {
            font-size: 18px;
            color: #2c3e50;
            font-weight: 700;
        }
        
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 35px;
            padding-top: 25px;
            border-top: 2px solid #f0f0f0;
        }
        
        .btn {
            padding: 15px 35px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            min-width: 140px;
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255,255,255,0.3);
            border-radius: 50%;
            transition: all 0.4s ease;
            transform: translate(-50%, -50%);
        }
        
        .btn:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
        }
        
        .btn-cancel {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: white;
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
        }
        
        .btn-cancel:hover {
            background: linear-gradient(135deg, #5a6268, #495057);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(108, 117, 125, 0.4);
        }
        
        .alert {
            padding: 18px;
            margin-bottom: 25px;
            border-radius: 12px;
            font-weight: 500;
            animation: alertSlide 0.5s ease-out;
        }
        
        @keyframes alertSlide {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
            border: 2px solid #28a745;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);
        }
        
        .alert-error {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
            border: 2px solid #dc3545;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.2);
        }

        @media (max-width: 768px) {
            .main-content {
                margin: 20px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                display: none;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .transfer-header {
                font-size: 22px;
                padding: 20px;
            }
            
            .transfer-form {
                padding: 25px;
            }
        }
        
        .btn.loading {
            position: relative;
            color: transparent;
        }
        
        .btn.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
  <?= $this->include('layouts/header') ?>

<div class="main-content">
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle" style="margin-left: 8px;"></i>
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle" style="margin-left: 8px;"></i>
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="transfer-container">
        <div class="transfer-header">
            <button class="close-btn" onclick="window.location.href='<?= base_url('AssetsController') ?>';">
                <i class="fas fa-times"></i>
            </button>
            إرجاع الأصل
        </div>

        <form class="transfer-form" method="post" action="<?= base_url('AssetsController/saveReturn') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="item_order_id" value="<?= $itemOrder->item_order_id ?>">
            <input type="hidden" name="usage_status_id" value="2">
            
            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-info-circle" style="margin-left: 10px;"></i>
                    معلومات الأصل
                </div>
                <div class="info-card">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">اسم الصنف</div>
                            <div class="info-value"><?= esc($itemOrder->item_name) ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">رقم الأصل</div>
                            <div class="info-value"><?= esc($itemOrder->asset_num) ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">الرقم التسلسلي</div>
                            <div class="info-value"><?= esc($itemOrder->serial_num) ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">الموظف المسؤول</div>
                            <div class="info-value"><?= esc($itemOrder->employee_name) ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">العلامة التجارية</div>
                            <div class="info-value"><?= esc($itemOrder->brand) ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">رقم الموديل</div>
                            <div class="info-value"><?= esc($itemOrder->model_num) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-undo" style="margin-left: 10px;"></i>
                    تفاصيل الإرجاع
                </div>

                <div class="status-display">
                    <div class="status-icon">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <div class="status-text">
                        <div class="status-label">الحالة الجديدة للأصل</div>
                        <div class="status-value">رجيع</div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="notes">
                            <i class="fas fa-sticky-note" style="margin-left: 5px;"></i>
                            ملاحظات وسبب الإرجاع
                        </label>
                        <textarea 
                            name="notes" 
                            id="notes"
                            rows="4" 
                            placeholder="يرجى تسجيل سبب الإرجاع والملاحظات الأخرى ذات الصلة..."
                            required
                        ></textarea>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="attach_id">
                            <i class="fas fa-paperclip" style="margin-left: 5px;"></i>
                            رقم المرفق (اختياري)
                        </label>
                        <input 
                            type="number" 
                            name="attach_id" 
                            id="attach_id"
                            placeholder="رقم المرفق إن وجد"
                        >
                    </div>
                </div>
            </div>

            <div class="action-buttons">
                <button type="submit" class="btn btn-primary" onclick="window.location.href='<?= base_url('AssetsController') ?>';">
                    <i class="fas fa-save" style="margin-left: 8px;"></i>
                    تأكيد الإرجاع
                </button>
                <button type="button" class="btn btn-cancel" onclick="window.location.href='<?= base_url('AssetsController') ?>';">
                    <i class="fas fa-times" style="margin-left: 8px;"></i>
                    إلغاء العملية
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.querySelector('form').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('.btn-primary');
    submitBtn.classList.add('loading');
    submitBtn.disabled = true;
});
</script>

</body>
</html>