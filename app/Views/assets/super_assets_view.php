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
    <style>
        /* Stats Cards Styling */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            display: flex;
            align-items: center;
            gap: 15px;
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }

        .stat-icon.transfer {
            background: linear-gradient(135deg, #057590, #3AC0C3);
            color: white;
        }

        .stat-icon.operations {
            background: linear-gradient(135deg, #007bff, #17a2b8);
            color: white;
        }

        .stat-icon.return {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }

        .stat-icon.assets {
            background: linear-gradient(135deg, #dc3545, #fd7e14);
            color: white;
        }

        .stat-info {
            flex: 1;
        }

        .stat-info h3 {
            font-size: 28px;
            font-weight: 700;
            color: #057590;
            margin: 0;
        }

        .stat-info p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }
    </style>
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
                <!-- Operations Card -->
                <div class="stat-card">
                    <div class="stat-icon operations">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= number_format($stats['total_operations'] ?? 0) ?></h3>
                        <p>إجمالي العمليات</p>
                    </div>
                </div>

                <!-- Assets Card -->
                <div class="stat-card">
                    <div class="stat-icon assets">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= number_format($stats['total_assets'] ?? 0) ?></h3>
                        <p>إجمالي الأصول</p>
                    </div>
                </div>

                <!-- Transfer Card -->
                <div class="stat-card">
                    <div class="stat-icon transfer">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= number_format($stats['total_transfers'] ?? 0) ?></h3>
                        <p>عمليات التحويل</p>
                    </div>
                </div>

                <!-- Return Card -->
                <div class="stat-card">
                    <div class="stat-icon return">
                        <i class="fas fa-undo"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= number_format($stats['total_returns'] ?? 0) ?></h3>
                        <p>عمليات الإرجاع</p>
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