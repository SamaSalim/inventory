<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة العهد</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('public/assets/css/warehouse-style.css') ?>">
</head>

<body>
    <!-- الشريط الجانبي للتنقل -->
    <?= $this->include('layouts/header') ?>

    <div class="main-content">
        <div class="header">
            <h1 class="page-title">إدارة العهد</h1>
            <div class="user-info" onclick="location.href='<?= base_url('UserInfo/getUserInfo') ?>'">
                <div class="user-avatar">
                    <?php
                    $userName = session()->get('name') ?? 'م م';
                    $nameParts = explode(' ', trim($userName));
                    $initials = '';

                    if (count($nameParts) >= 2) {
                        $initials = mb_substr($nameParts[0], 0, 1, 'UTF-8') . mb_substr($nameParts[count($nameParts) - 1], 0, 1, 'UTF-8');
                    } else {
                        $initials = mb_substr($nameParts[0], 0, 1, 'UTF-8');
                    }
                    echo strtoupper($initials);
                    ?>
                </div>
                <span><?= esc(session()->get('name')) ?></span>
            </div>
        </div>

        <div class="content-area">
            <!-- رسائل التنبيه -->
            <div id="alertContainer"></div>

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

                <<div class="stat-card" style="background: linear-gradient(135deg, #e8f5e9, #81c784);">
    <div class="stat-number"><?= number_format($stats['returned_items'] ?? 0) ?></div>
    <div class="stat-label">
        <svg class="stat-icon" viewBox="0 0 24 24">
            <path d="M9 11H3v2h6v3l5-4-5-4v3zm12-8h-6c-1.1 0-2 .9-2 2v3h2V5h6v14h-6v-3h-2v3c0 1.1.9 2 2 2h6c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z" />
        </svg>
        عدد أصناف الرجيع
    </div>
</div>
            </div>

            <div class="section-header">
                <h2 class="section-title">قائمة المخزون</h2>
                <div class="buttons-group">
                </div>
            </div>

            <!-- شريط العمليات الجماعية -->
            <div class="bulk-actions" id="bulkActions">
                <div class="bulk-info">
                    <i class="fas fa-check-circle"></i>
                    <span>تم اختيار</span>
                    <span class="selected-count" id="selectedCount">0</span>
                    <span>طلب</span>
                </div>
                <div class="bulk-buttons">
                    <button class="bulk-btn bulk-delete-btn" onclick="bulkDelete()">
                        <i class="fas fa-trash"></i>
                        حذف جماعي
                    </button>
                    <button class="bulk-btn bulk-cancel-btn" onclick="clearSelection()">
                        <i class="fas fa-times"></i>
                        إلغاء الاختيار
                    </button>
                </div>
            </div>

            <!-- قسم البحث والفلاتر المحدث -->
            <form method="get" action="<?= base_url('AssetsController/index') ?>">
                <div class="filters-section">
                    <!-- شريط البحث الرئيسي -->
                    <div class="main-search-container">
                        <h3 class="search-section-title">
                            <i class="fas fa-search"></i>
                            البحث العام
                        </h3>
                        <div class="search-bar-wrapper">
                            <input type="text" class="main-search-input" name="search" id="mainSearchInput"
                                value="<?= esc($filters['search'] ?? '') ?>"
                                placeholder="ابحث في جميع الحقول...">
                            <i class="fas fa-search search-icon" onclick="document.querySelector('form').submit();" title="بحث"></i>
                        </div>
                    </div>

                    <!-- مقسم الفلاتر -->
                    <div class="filters-divider">
                        <span><i class="fas fa-filter"></i> الفلاتر التفصيلية</span>
                    </div>

                    <!-- الفلاتر التفصيلية -->
                    <div class="detailed-filters">
                        <!-- الصنف -->
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-tag"></i>
                                الصنف
                            </label>
                            <input type="text" class="filter-input" name="item_type"
                                value="<?= esc($filters['item_type'] ?? '') ?>"
                                placeholder="اكتب نوع الصنف">
                        </div>

                        <!-- التصنيف -->
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-layer-group"></i>
                                التصنيف
                            </label>
                            <select class="filter-select" name="category">
                                <option value="">اختر التصنيف</option>
                                <?php if (isset($categories)): ?>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= esc($cat->id) ?>"
                                            <?= ($filters['category'] ?? '') == $cat->id ? 'selected' : '' ?>>
                                            <?= esc($cat->name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- الرقم التسلسلي -->
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-barcode"></i>
                                الرقم التسلسلي
                            </label>
                            <input type="text" class="filter-input" name="serial_number"
                                value="<?= esc($filters['serial_number'] ?? '') ?>"
                                placeholder="رقم تسلسلي محدد">
                        </div>

                        <!-- الرقم الوظيفي -->
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-id-badge"></i>
                                الرقم الوظيفي
                            </label>
                            <input type="text" class="filter-input" name="employee_id"
                                value="<?= esc($filters['employee_id'] ?? '') ?>"
                                placeholder="رقم الموظف">
                        </div>

                        <!-- الموقع -->
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-map-marker-alt"></i>
                                الموقع
                            </label>
                            <input type="text" class="filter-input" name="location"
                                value="<?= esc($filters['location'] ?? '') ?>"
                                placeholder="اكتب اسم الموقع">
                        </div>
                    </div>

                    <!-- أزرار العمليات -->
                    <div class="filter-actions">
                        <button type="submit" class="filter-btn search-btn">
                            <i class="fas fa-search"></i>
                            بحث
                        </button>
                        <a href="<?= base_url('AssetsController/index') ?>" class="filter-btn reset-btn">
                            <i class="fas fa-undo"></i>
                            إعادة تعيين
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-container">
                <table class="custom-table" id="datatable-orders">
                    <thead>
                        <tr class="text-center">
                            </th>
                            <th>رقم الطلب</th>
                            <th>الرقم الوظيفي</th>
                            <th>التحويلة</th>
                            <th>تاريخ الطلب</th>
                            <th>رمز الموقع</th>
                            <th>مدخل البيانات</th>
                            <th>عمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($orders) && !empty($orders)): ?>
                            <?php foreach ($orders as $order): ?>
                                <tr class="text-center align-middle" data-order-id="<?= $order->order_id ?>">
                                    <td><?= esc($order->order_id ?? '-') ?></td>
                                    <td><?= esc($order->employee_id ?? '-') ?></td>
                                    <td><?= esc($order->extension ?? 'na') ?></td>
                                    <td><?= esc($order->created_at ?? '-') ?></td>
                                    <td><?= $order->location_code ?></td>
                                    <td><?= esc($order->created_by_name ?? '-') ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <?php if (canReturn()): ?>
                                                <a href="<?= site_url('AssetsController/orderDetails/' . $order->order_id) ?>" class="action-btn view-btn" title="إرجاع الأصول">
                                                    <svg class="btn-icon" viewBox="0 0 24 24">
                                                        <path d="M9 11H3v2h6v3l5-4-5-4v3zm12-8h-6c-1.1 0-2 .9-2 2v3h2V5h6v14h-6v-3h-2v3c0 1.1.9 2 2 2h6c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z" />
                                                    </svg>
                                                    إرجاع
                                                </a>
                                            <?php else: ?>
                                                <button class="action-btn view-btn" disabled style="opacity: 0.5; cursor: not-allowed;" title="ليس لديك صلاحية الإرجاع">
                                                    <svg class="btn-icon" viewBox="0 0 24 24">
                                                        <path d="M9 11H3v2h6v3l5-4-5-4v3zm12-8h-6c-1.1 0-2 .9-2 2v3h2V5h6v14h-6v-3h-2v3c0 1.1.9 2 2 2h6c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z" />
                                                    </svg>
                                                    إرجاع
                                                </button>
                                            <?php endif; ?>

                                            <?php if (canTransfer()): ?>
                                                <a href="<?= site_url('AssetsController/transferView/' . $order->order_id) ?>" class="action-btn edit-btn" title="تحويل الأصول">
                                                    <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20" height="20">
                                                        <path d="M6.99 11L3 15l3.99 4v-3H14v-2H6.99v-3zM21 9l-3.99-4v3H10v2h7.01v3L21 9z" />
                                                    </svg>
                                                    تحويل
                                                </a>
                                            <?php else: ?>
                                                <button class="action-btn edit-btn" disabled style="opacity: 0.5; cursor: not-allowed;" title="ليس لديك صلاحية التحويل">
                                                    <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20" height="20">
                                                        <path d="M6.99 11L3 15l3.99 4v-3H14v-2H6.99v-3zM21 9l-3.99-4v3H10v2h7.01v3L21 9z" />
                                                    </svg>
                                                    تحويل
                                                </button>
                                            <?php endif; ?>

                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">لا توجد بيانات متاحة</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <?= $pager->links('orders', 'custom_arabic') ?>
            </div>
        </div>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>

    </script>

</body>

</html>