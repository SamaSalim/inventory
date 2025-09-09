<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تحديث عنصر</title>
    <style>
        :root {
            --primary-color: #168aad;
            --primary-dark: #1d3557;
            --secondary-color: #004b6b;
            --secondary-dark: #00394f;
            --text-dark: #0f172a;
            --text-medium: #334155;
            --text-light: #64748b;
            --border-color: #cbd5e1;
            --bg-light: #f1f5f9;
            --bg-white: #ffffff;
            --error-color: #ef4444;
            --success-color: #10b981;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--bg-white) 0%, #e6f0f6 100%);
            min-height: 100vh;
            color: var(--text-medium);
            line-height: 1.6;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            right: 0;
            top: 0;
            height: 100vh;
            width: 80px;
            background: linear-gradient(180deg, var(--secondary-color), var(--secondary-dark));
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px 0;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
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

        .sidebar-icon svg {
            fill: white;
            width: 20px;
            height: 20px;
        }

        /* Main Content Styles */
        .main-content {
            margin-right: 80px;
            padding: 30px;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* Form Container */
        .form-container {
            background: var(--bg-white);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border-color);
            max-width: 1000px;
            margin: 0 auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .modal-title {
            font-size: 28px;
            font-weight: 600;
            color: var(--text-dark);
        }

        /* Form Elements */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-dark);
            font-size: 14px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            background: var(--bg-light);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 14px 16px;
            color: var(--text-dark);
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
            font-family: inherit;
            width: 100%;
        }

        .form-group input:read-only {
            background: rgba(255, 255, 255, 0.5);
            cursor: not-allowed;
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: var(--text-light);
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            background: var(--bg-white);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(22, 138, 173, 0.2);
        }

        .form-group select option {
            background: var(--bg-white);
            color: var(--text-dark);
            padding: 10px;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        /* Buttons */
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        .submit-btn {
            background: var(--bg-light);
            color: var(--text-dark);
            border: 1px solid var(--border-color);
            padding: 12px 25px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-width: 120px;
            justify-content: center;
        }

        .submit-btn.primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border-color: var(--primary-dark);
            color: white;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .submit-btn.primary:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        .submit-btn.loading::after {
            content: "";
            width: 16px;
            height: 16px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Message Styles */
        .message {
            background: linear-gradient(135deg, #f87171, var(--error-color));
            color: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            font-weight: 600;
            text-align: center;
            border: 1px solid #fca5a5;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 60px;
            }

            .main-content {
                margin-right: 60px;
                padding: 15px;
            }

            .form-container {
                padding: 25px;
            }

            .modal-title {
                font-size: 22px;
            }

            .form-actions {
                flex-direction: column;
                width: 100%;
            }

            .submit-btn {
                width: 100%;
            }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-light);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--text-light);
        }
    </style>
</head>

<body>
    <!-- ========================================
         الشريط الجانبي للتنقل
    ======================================== -->
    <?= $this->include('layouts/header') ?>


    <div class="main-content">
        <div class="form-container">
            <div class="modal-header">
                <h1 class="modal-title">تحديث بيانات العنصر</h1>
                <a href="javascript:history.back()" class="submit-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.42-1.41L7.83 13H20v-2z" />
                    </svg>
                    العودة
                </a>
            </div>

            <?php if (!$item): ?>
                <div class="message">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                    </svg>
                    <span>العنصر غير موجود أو تم حذفه.</span>
                </div>
                <div class="form-actions">
                    <a href="javascript:history.back()" class="submit-btn">العودة للقائمة</a>
                </div>
            <?php else: ?>
                <form action="<?= site_url('warehouse/update/' . $item->id) ?>" method="post" id="updateForm">
                    <!-- <div class="form-grid">
                        <div class="form-group">
                            <label for="itemId">رقم العنصر</label>
                            <input type="text" id="itemId" name="id" value="<?= esc($item->id) ?>" readonly>
                        </div> -->

                    <div class="form-group">
                        <label for="itemName">الصنف</label>
                        <input type="text" id="itemName" name="item" value="<?= esc($item->item) ?>" required>
                    </div>



                    <div class="form-group">
                        <label for="itemCategory">التصنيف</label>
                        <select id="itemCategory" name="category_id" required>
                            <option value="">اختر التصنيف</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category->id ?>" <?= ($item->category_id == $category->id) ? 'selected' : '' ?>>
                                    <?= esc($category->category) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <input type="hidden" name="status_id" value="<?= esc($item->status_id) ?>">
                    <input type="hidden" name="usage_status_id" value="<?= esc($item->usage_status_id) ?>">

                    <div class="form-group">
                        <label for="itemQuantity">الكمية</label>
                        <input type="number" id="itemQuantity" name="quantity" value="<?= esc($item->quantity) ?>" min="0" required>
                    </div>

                    <div class="form-group">
                        <label for="itemModel">رقم الموديل</label>
                        <input type="text" id="itemModel" name="model_num" value="<?= esc($item->model_num) ?>" placeholder="أدخل رقم الموديل">
                    </div>

                    <div class="form-group">
                        <label for="itemSize">الحجم</label>
                        <input type="text" id="itemSize" name="size" value="<?= esc($item->size) ?>" placeholder="أدخل الحجم">
                    </div>

                    <div class="form-group">
                        <label for="itemAsset">رقم الأصول</label>
                        <input type="text" id="itemAsset" name="asset_num" value="<?= esc($item->asset_num) ?>" placeholder="أدخل رقم الأصول">
                    </div>

                    <div class="form-group">
                        <label for="itemSerial">الرقم التسلسلي</label>
                        <input type="text" id="itemSerial" name="serial_num" value="<?= esc($item->serial_num) ?>" placeholder="أدخل الرقم التسلسلي">
                    </div>

                    <div class="form-group">
                        <label for="itemOldAsset">الأصول القديمة</label>
                        <input type="text" id="itemOldAsset" name="old_asset_num" value="<?= esc($item->old_asset_num) ?>" placeholder="أدخل رقم الأصول القديمة">
                    </div>

                    <div class="form-group">
                        <label for="itemCreator">أنشئ بواسطة</label>
                        <input type="text" id="itemCreator" name="created_by" value="<?= esc($item->created_by) ?>" readonly>
                    </div>
        </div>

        <div class="form-group full-width">
            <label for="itemNotes">ملاحظات</label>
            <textarea id="itemNotes" name="note" placeholder="أدخل أي ملاحظات إضافية"><?= esc($item->note) ?></textarea>
        </div>

        <div class="form-actions">
            <a href="javascript:history.back()" class="submit-btn">إلغاء</a>
            <button type="submit" class="submit-btn primary" id="submitBtn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                </svg>
                تحديث
            </button>
        </div>
        </form>
    <?php endif; ?>
    </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form submission handler
            const updateForm = document.getElementById('updateForm');
            if (updateForm) {
                updateForm.addEventListener('submit', function(e) {
                    const submitBtn = document.getElementById('submitBtn');
                    if (submitBtn) {
                        submitBtn.classList.add('loading');
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span>جاري التحديث...</span>';
                    }
                });
            }

            // Auto-resize textarea
            const textarea = document.querySelector('textarea');
            if (textarea) {
                textarea.style.height = 'auto';
                textarea.style.height = (textarea.scrollHeight) + 'px';

                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = (this.scrollHeight) + 'px';
                });
            }

            // Form validation
            document.querySelectorAll('input[required], select[required]').forEach(field => {
                field.addEventListener('blur', function() {
                    if (!this.value.trim()) {
                        this.style.borderColor = 'var(--error-color)';
                        this.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.2)';
                    } else {
                        this.style.borderColor = 'var(--success-color)';
                        this.style.boxShadow = '0 0 0 3px rgba(16, 185, 129, 0.2)';
                    }
                });
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.key === 'Enter' && updateForm) {
                    updateForm.submit();
                }
                if (e.key === 'Escape') {
                    history.back();
                }
            });

            // Focus on first input field
            const firstInput = document.querySelector('input:not([readonly]), select, textarea');
            if (firstInput) {
                firstInput.focus();
            }
        });
    </script>
</body>

</html>