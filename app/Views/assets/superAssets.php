<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سجلات التحويل والإرجاع </title>
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
            <h1 class="page-title">سجلات التحويل والإرجاع</h1>
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
                    <div class="stat-number"><?= number_format($stats['total_transfers'] ?? 0) ?></div>
                    <div class="stat-label">
                        <svg class="stat-icon" viewBox="0 0 24 24">
                            <path d="M6.99 11L3 15l3.99 4v-3H14v-2H6.99v-3zM21 9l-3.99-4v3H10v2h7.01v3L21 9z"/>
                        </svg>
                        عمليات التحويل
                    </div>
                </div>

                <div class="stat-card green">
                    <div class="stat-number"><?= number_format($stats['total_returns'] ?? 0) ?></div>
                    <div class="stat-label">
                        <svg class="stat-icon" viewBox="0 0 24 24">
                            <path d="M9 11H3v2h6v3l5-4-5-4v3zm12-8h-6c-1.1 0-2 .9-2 2v3h2V5h6v14h-6v-3h-2v3c0 1.1.9 2 2 2h6c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z" />
                        </svg>
                        عمليات الإرجاع
                    </div>
                </div>

                <div class="stat-card pink">
                    <div class="stat-number"><?= number_format($stats['total_assets'] ?? 0) ?></div>
                    <div class="stat-label">
                        <svg class="stat-icon" viewBox="0 0 24 24">
                            <path d="M20 6h-2.18c.11-.31.18-.65.18-1a2.996 2.996 0 0 0-5.5-1.65l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2z" />
                        </svg>
                        إجمالي الأصول
                    </div>
                </div>

                <div class="stat-card" style="background: linear-gradient(135deg, #e3f2fd, #90caf9);">
                    <div class="stat-number"><?= number_format($stats['total_operations'] ?? 0) ?></div>
                    <div class="stat-label">
                        <svg class="stat-icon" viewBox="0 0 24 24">
                            <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                        </svg>
                        إجمالي العمليات
                    </div>
                </div>


            </div>

            <div class="section-header">
                <h2 class="section-title">سجلات العمليات</h2>
            </div>

            <!-- قسم البحث والفلاتر -->
            <form method="get" action="<?= base_url('AssetsHistory/superAssets') ?>">
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
                        <!-- رقم الأصل -->
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-hashtag"></i>
                                رقم الأصل
                            </label>
                            <input type="text" class="filter-input" name="asset_number"
                                   value="<?= esc($filters['asset_number'] ?? '') ?>" 
                                   placeholder="رقم الأصل">
                        </div>

                        <!-- اسم الطلب -->
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-box"></i>
                                اسم الطلب
                            </label>
                            <input type="text" class="filter-input" name="item_name"
                                   value="<?= esc($filters['item_name'] ?? '') ?>" 
                                   placeholder="اسم الصنف">
                        </div>

                        <!-- نوع العملية -->
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-exchange-alt"></i>
                                نوع العملية
                            </label>
                            <select class="filter-select" name="operation_type">
                                <option value="">جميع العمليات</option>
                                <option value="transfer" <?= ($filters['operation_type'] ?? '') == 'transfer' ? 'selected' : '' ?>>تحويل</option>
                                <option value="return" <?= ($filters['operation_type'] ?? '') == 'return' ? 'selected' : '' ?>>إرجاع</option>
                            </select>
                        </div>

                        <!-- التاريخ من -->
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-calendar-alt"></i>
                                من تاريخ
                            </label>
                            <input type="date" class="filter-input" name="date_from"
                                   value="<?= esc($filters['date_from'] ?? '') ?>">
                        </div>

                        <!-- التاريخ إلى -->
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-calendar-check"></i>
                                إلى تاريخ
                            </label>
                            <input type="date" class="filter-input" name="date_to"
                                   value="<?= esc($filters['date_to'] ?? '') ?>">
                        </div>
                    </div>

                    <!-- أزرار العمليات -->
                    <div class="filter-actions">
                        <button type="submit" class="filter-btn search-btn">
                            <i class="fas fa-search"></i>
                            بحث
                        </button>
                        <a href="<?= base_url('AssetsHistory/superAssets') ?>" class="filter-btn reset-btn">
                            <i class="fas fa-undo"></i>
                            إعادة تعيين
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-container">
                <table class="custom-table" id="datatable-operations">
                    <thead>
                        <tr class="text-center">
                            <th>رقم الأصل</th>
                            <th>اسم الطلب</th>
                            <th>نوع العملية</th>
                            <th>تاريخ آخر عملية</th>
                            <th>عمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($operations) && !empty($operations)): ?>
                            <?php foreach ($operations as $operation): ?>
                                <tr class="text-center align-middle">
                                    <td><?= esc($operation->asset_number ?? '-') ?></td>
                                    <td><?= esc($operation->item_name ?? '-') ?></td>
                                    <td>
                                        <?php if ($operation->operation_type == 'transfer'): ?>
                                            <span class="badge" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 5px 12px; border-radius: 20px;">
                                                <i class="fas fa-exchange-alt"></i> تحويل
                                            </span>
                                        <?php elseif ($operation->operation_type == 'return'): ?>
                                            <span class="badge" style="background: linear-gradient(135deg, #f093fb, #f5576c); color: white; padding: 5px 12px; border-radius: 20px;">
                                                <i class="fas fa-undo"></i> إرجاع
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($operation->last_operation_date ?? '-') ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="<?= site_url('AssetsHistory/viewDetails/' . $operation->id) ?>" 
                                               class="action-btn view-btn">
                                                <svg class="btn-icon" viewBox="0 0 24 24">
                                                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                                                </svg>
                                                عرض
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">لا توجد بيانات متاحة</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <?= $pager->links('operations', 'custom_arabic') ?>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>