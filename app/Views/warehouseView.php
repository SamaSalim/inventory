
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المستودعات</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Cairo', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #F4F4F4;
            direction: rtl;
            text-align: right;
            min-height: 100vh;
            color: #333;
            font-size: 14px;
        }

        /* الشريط الجانبي */
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
            font-size: 20px;
            margin-bottom: 30px;
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
            background-color: rgba(255, 255, 255, 0.2);
        }

        /* المحتوى الرئيسي */
        .main-content {
            margin-right: 80px;
            padding: 0;
        }

        .header {
            background-color: white;
            padding: 15px 25px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-height: 70px;
        }

        .page-title {
            color: #057590;
            font-size: 22px;
            font-weight: 600;
            margin: 0;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #3AC0C3;
            font-size: 14px;
            cursor: pointer;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            background-color: #3AC0C3;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            font-weight: bold;
        }

        .content-area {
            padding: 25px;
            background-color: #EFF8FA;
            min-height: calc(100vh - 70px);
        }

        /* بطاقات الإحصائيات */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }

        .stat-card.blue {
            background: linear-gradient(135deg, #d6eaff, #bfdcff);
        }

        .stat-card.pink {
            background: linear-gradient(135deg, #ffe0e5, #ffc9d1);
        }

        .stat-card.green {
            background: linear-gradient(135deg, #e2f0eb, #c9ede0);
        }

        .stat-number {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
            line-height: 1;
        }

        .stat-label {
            color: #666;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .stat-icon {
            width: 18px;
            height: 18px;
            fill: currentColor;
        }

        /* العناوين والأزرار */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-title {
            color: #057590;
            font-size: 18px;
            font-weight: 600;
            margin: 0;
        }

        .add-btn {
            background-color: #3AC0C3;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(58, 192, 195, 0.3);
        }

        .add-btn:hover {
            background-color: #2aa8ab;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(58, 192, 195, 0.4);
        }

        /* قسم الفلاتر */
        .filters-section {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .filter-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .filter-select,
        .search-box {
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 13px;
            background-color: white;
            outline: none;
            transition: all 0.3s ease;
        }

        .filter-select:focus,
        .search-box:focus {
            border-color: #057590;
            box-shadow: 0 0 0 2px rgba(5, 117, 144, 0.1);
        }

        /* الجدول */
        .table-container {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .custom-table {
            width: 100%;
            margin: 0;
            border-collapse: collapse;
            font-size: 12px;
        }

        .custom-table thead th {
            background-color: #057590;
            color: white;
            font-weight: 600;
            padding: 15px 10px;
            border: none;
            font-size: 12px;
            text-align: center;
            white-space: nowrap;
        }

        .custom-table tbody td {
            padding: 12px 8px;
            border-bottom: 1px solid #f0f0f0;
            text-align: center;
            font-size: 11px;
            color: #555;
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .custom-table tbody tr:hover {
            background-color: rgba(5, 117, 144, 0.05);
        }

        .edit-btn {
            color: white;
            background: linear-gradient(135deg, #168aad, #1d3557);
            border: none;
            padding: 6px 12px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 11px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .edit-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(22, 138, 173, 0.4);
            color: white;
        }

        /* النافذة المنبثقة */
        .form-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
            backdrop-filter: blur(3px);
            padding: 20px;
        }

        .modal-content {
            background: linear-gradient(135deg, #168aad, #1d3557);
            padding: 30px;
            border-radius: 20px;
            width: 95%;
            max-width: 700px;
            max-height: 85vh;
            overflow-y: auto;
            color: white;
            position: relative;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .modal-title {
            font-size: 20px;
            font-weight: 600;
            color: white;
        }

        .close-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .close-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* شبكة النموذج */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 6px;
            font-weight: 500;
            color: white;
            font-size: 13px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            background: rgba(42, 61, 85, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            padding: 10px 12px;
            color: white;
            font-size: 13px;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            background: rgba(52, 73, 94, 0.9);
            border-color: rgba(255, 255, 255, 0.4);
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.1);
        }

        .form-group select option {
            background: #2c3e50;
            color: white;
        }

        /* أزرار العمليات */
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        .submit-btn,
        .cancel-btn {
            padding: 12px 25px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }

        .submit-btn {
            background: rgba(58, 192, 195, 0.9);
            color: white;
        }

        .submit-btn:hover {
            background: rgba(58, 192, 195, 1);
            transform: translateY(-1px);
        }

        .cancel-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .cancel-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .full-width {
            grid-column: 1 / -1;
        }

        /* التصميم المتجاوب */
        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .filter-row {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 60px;
            }

            .main-content {
                margin-right: 60px;
            }

            .content-area {
                padding: 15px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .filter-row {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .section-header {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }

            .modal-content {
                padding: 20px;
                margin: 10px;
                width: calc(100% - 20px);
            }

            .custom-table {
                font-size: 10px;
            }

            .custom-table thead th {
                padding: 10px 6px;
                font-size: 10px;
            }

            .custom-table tbody td {
                padding: 8px 4px;
                font-size: 9px;
                max-width: 80px;
            }

            .page-title {
                font-size: 18px;
            }

            .header {
                padding: 12px 15px;
            }
        }

        @media (max-width: 480px) {
            .modal-content {
                max-height: 90vh;
            }

            .form-actions {
                flex-direction: column;
            }

            .submit-btn,
            .cancel-btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <!-- الشريط الجانبي للتنقل -->
    <div class="sidebar">
        <div class="logo">
            <i class="fas fa-warehouse"></i>
        </div>
        <div class="sidebar-icon">
            <i class="fas fa-home" style="color: white;"></i>
        </div>
        <div class="sidebar-icon">
            <i class="fas fa-boxes" style="color: white;"></i>
        </div>
        <div class="sidebar-icon">
            <i class="fas fa-chart-bar" style="color: white;"></i>
        </div>
        <div class="sidebar-icon">
            <i class="fas fa-cog" style="color: white;"></i>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <h1 class="page-title">إدارة المستودعات</h1>
            <div class="user-info" onclick="userInfoView()">
                <!-- استخدم بيانات PHP بدلاً من JavaScript -->
                <span><?= esc(session()->get('name') ?? 'المستخدم') ?></span>
                <div class="user-avatar">
                    <?= strtoupper(substr(esc(session()->get('name') ?? 'م'), 0, 1)) ?>
                </div>
            </div>
        </div>

        <div class="content-area">
            <!-- بطاقات الإحصائيات - استخدم بيانات PHP -->
            <div class="stats-grid">
                <div class="stat-card blue">
                    <div class="stat-number"><?= number_format($stats['total_receipts'] ?? 0) ?></div>
                    <div class="stat-label">
                        <svg class="stat-icon" viewBox="0 0 24 24">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z" />
                        </svg>
                        إجمالي الكميات
                    </div>
                </div>

                <div class="stat-card pink">
                    <div class="stat-number"><?= number_format($stats['available_items'] ?? 0) ?></div>
                    <div class="stat-label">
                        <svg class="stat-icon" viewBox="0 0 24 24">
                            <path d="M20 6h-2.18c.11-.31.18-.65.18-1a2.996 2.996 0 0 0-5.5-1.65l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2z" />
                        </svg>
                        أصناف متوفرة
                    </div>
                </div>

                <div class="stat-card green">
                    <div class="stat-number"><?= number_format($stats['total_entries'] ?? 0) ?></div>
                    <div class="stat-label">
                        <svg class="stat-icon" viewBox="0 0 24 24">
                            <path d="M9 11H7v6h2v-6zm4 0h-2v6h2v-6zm4 0h-2v6h2v-6zm2-7h-3V2h-2v2H8V2H6v2H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2z" />
                        </svg>
                        عدد الإدخالات
                    </div>
                </div>

                <div class="stat-card" style="background: linear-gradient(135deg, #fff3cd, #ffeaa7);">
                    <div class="stat-number"><?= number_format($stats['low_stock'] ?? 0) ?></div>
                    <div class="stat-label">
                        <svg class="stat-icon" viewBox="0 0 24 24">
                            <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" />
                        </svg>
                        مخزون منخفض
                    </div>
                </div>
            </div>

            <div class="section-header">
                <h2 class="section-title">قائمة المخزون</h2>
                <button class="add-btn" onclick="openAddForm()">
                    <i class="fas fa-plus"></i> إضافة مخزون
                </button>
            </div>

            <div class="filters-section">
                <div class="filter-row">
                    <div class="filter-group">
                        <label class="filter-label">البحث</label>
                        <input type="text" class="search-box" id="generalSearch" placeholder="البحث العام">
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">الصنف</label>
                        <input type="text" class="search-box" id="searchId" placeholder="الصنف">
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">التصنيف</label>
                        <select class="filter-select" id="searchCategory">
                            <option value="">اختر التصنيف</option>
                            <?php if (isset($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= esc($cat->category) ?>"><?= esc($cat->category) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">الرقم التسلسلي</label>
                        <input type="text" class="search-box" id="searchSerial" placeholder="الرقم التسلسلي">
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">حالة الاستخدام</label>
                        <select class="filter-select" id="searchStatus">
                            <option value="">اختر الحالة</option>
                            <?php if (isset($usage_statuses)): ?>
                                <?php foreach ($usage_statuses as $status): ?>
                                    <option value="<?= esc($status->usage_status) ?>"><?= esc($status->usage_status) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>الصنف</th>
                            <th>التصنيف</th>
                            <th>الكمية</th>
                            <th>رقم الموديل</th>
                            <th>الحجم</th>
                            <th>رقم الأصول</th>
                            <th>الرقم التسلسلي</th>
                            <th>رقم الأصول القديمة</th>
                            <th>الحالة</th>
                            <th>حالة الاستخدام</th>
                            <th>بواسطة</th>
                            <th>الملاحظات</th>
                            <th>العمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($items) && !empty($items)): ?>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?= esc($item->item ?? 'غير محدد') ?></td>
                                    <td><?= esc($item->category_name ?? 'غير محدد') ?></td>
                                    <td><?= esc($item->quantity ?? 0) ?></td>
                                    <td><?= esc($item->model_num ?? '-') ?></td>
                                    <td><?= esc($item->size ?? '-') ?></td>
                                    <td><?= esc($item->asset_num ?? '-') ?></td>
                                    <td><?= esc($item->serial_num ?? '-') ?></td>
                                    <td><?= esc($item->old_asset_num ?? '-') ?></td>
                                    <td><?= esc($item->status ?? 'غير محدد') ?></td>
                                    <td><?= esc($item->usage_status ?? 'غير محدد') ?></td>
                                    <td><?= esc($item->created_by ?? '-') ?></td>
                                    <td><?= esc($item->note ?? '-') ?></td>
                                    <td>
                                        <a href="<?= site_url('warehouse/edit/' . $item->id) ?>" class="edit-btn">تحديث</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="13">لا توجد بيانات متاحة</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- النافذة المنبثقة لإضافة عنصر جديد -->
    <div class="form-modal" id="addForm">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">إضافة عنصر جديد</h3>
                <button class="close-btn" onclick="closeAddForm()">&times;</button>
            </div>

            <form action="<?= site_url('warehouse/addNewItem') ?>" method="post">
                <div class="form-grid">
                    <div class="form-group">
                        <label>الصنف</label>
                        <input type="text" name="item" placeholder="أدخل الصنف" required>
                    </div>
                    <div class="form-group">
                        <label>التصنيف</label>
                        <select name="category_id" required>
                            <option value="">اختر التصنيف</option>
                            <?php if (isset($categories)): ?>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category->id ?>">
                                        <?= esc($category->category) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>الكمية</label>
                        <input type="number" name="quantity" placeholder="أدخل الكمية" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>رقم الموديل</label>
                        <input type="text" name="model_num" placeholder="أدخل رقم الموديل">
                    </div>
                    <div class="form-group">
                        <label>الحجم</label>
                        <input type="text" name="size" placeholder="أدخل الحجم">
                    </div>
                    <div class="form-group">
                        <label>رقم الأصول</label>
                        <input type="text" name="asset_num" placeholder="أدخل رقم الأصول">
                    </div>
                    <div class="form-group">
                        <label>الرقم التسلسلي</label>
                        <input type="text" name="serial_num" placeholder="أدخل الرقم التسلسلي">
                    </div>
                    <div class="form-group">
                        <label>الأصول القديمة</label>
                        <input type="text" name="old_asset_num" placeholder="أدخل رقم الأصول القديمة">
                    </div>
                    <div class="form-group">
                        <label>الحالة</label>
                        <select name="status_id">
                            <option value="">اختر الحالة</option>
                            <?php if (isset($statuses)): ?>
                                <?php foreach ($statuses as $status): ?>
                                    <option value="<?= $status->id ?>">
                                        <?= esc($status->status) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>حالة الاستخدام</label>
                        <select name="usage_status_id">
                            <option value="">اختر حالة الاستخدام</option>
                            <?php if (isset($usage_statuses)): ?>
                                <?php foreach ($usage_statuses as $usageStatus): ?>
                                    <option value="<?= $usageStatus->id ?>">
                                        <?= esc($usageStatus->usage_status) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label>ملاحظات</label>
                    <textarea name="note" rows="3" placeholder="أدخل أي ملاحظات إضافية"></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="cancel-btn" onclick="closeAddForm()">إلغاء</button>
                    <button type="submit" class="submit-btn">إضافة</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        function userInfoView() {
            window.location.href = "<?= base_url('user/getUserInfo') ?>";
        }

        function openAddForm() {
            const modal = document.getElementById('addForm');
            if (modal) {
                modal.style.display = 'flex';
            }
        }

        function closeAddForm() {
            const modal = document.getElementById('addForm');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        // إغلاق المودال بالنقر خارجه
        document.getElementById('addForm').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddForm();
            }
        });

        // وظيفة البحث المحسنة
        function performSearch() {
            const generalSearch = document.getElementById('generalSearch').value.toLowerCase();
            const searchId = document.getElementById('searchId').value.toLowerCase();
            const searchCategory = document.getElementById('searchCategory').value.toLowerCase();
            const searchSerial = document.getElementById('searchSerial').value.toLowerCase();
            const searchStatus = document.getElementById('searchStatus').value.toLowerCase();

            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                if (row.cells.length < 13) return; // تجاهل صفوف "لا توجد بيانات"

                const itemCell = row.cells[0]?.textContent.toLowerCase() || '';
                const categoryCell = row.cells[1]?.textContent.toLowerCase() || '';
                const serialCell = row.cells[6]?.textContent.toLowerCase() || '';
                const statusCell = row.cells[9]?.textContent.toLowerCase() || '';
                const allText = row.textContent.toLowerCase();

                const matchGeneral = !generalSearch || allText.includes(generalSearch);
                const matchId = !searchId || itemCell.includes(searchId);
                const matchCategory = !searchCategory || categoryCell.includes(searchCategory);
                const matchSerial = !searchSerial || serialCell.includes(searchSerial);
                const matchStatus = !searchStatus || statusCell.includes(searchStatus);

                if (matchGeneral && matchId && matchCategory && matchSerial && matchStatus) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // إضافة مستمعي الأحداث للبحث
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('generalSearch')?.addEventListener('input', performSearch);
            document.getElementById('searchId')?.addEventListener('input', performSearch);
            document.getElementById('searchCategory')?.addEventListener('change', performSearch);
            document.getElementById('searchSerial')?.addEventListener('input', performSearch);
            document.getElementById('searchStatus')?.addEventListener('change', performSearch);
        });

        // إضافة تأثيرات بصرية للجدول
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('.custom-table tbody tr');
            
            rows.forEach(row => {
                row.addEventListener('click', function() {
                    // إزالة التحديد من جميع الصفوف
                    rows.forEach(r => r.classList.remove('selected'));
                    // إضافة التحديد للصف الحالي
                    this.classList.add('selected');
                });
            });
        });

        // إضافة CSS للصف المحدد
        const style = document.createElement('style');
        style.textContent = `
            .custom-table tbody tr.selected {
                background-color: rgba(58, 192, 195, 0.1) !important;
                border-left: 3px solid #3AC0C3;
            }
            .custom-table tbody tr {
                cursor: pointer;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>

</html>