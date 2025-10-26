<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سجلات عمليات العهد</title>
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

        .stat-icon.new {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }

        .stat-icon.transfer {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .stat-icon.operations {
            background: linear-gradient(135deg, #007bff, #17a2b8);
            color: white;
        }

        .stat-icon.return {
            background: linear-gradient(135deg, #f093fb, #f5576c);
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

        /* Enhanced Action Button Styling */
        .action-btn.cycle-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }

        .action-btn.cycle-btn:hover {
            background: linear-gradient(135deg, #5568d3 0%, #663b8e 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .action-btn.cycle-btn i {
            font-size: 16px;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* Print Styles */
        @media print {
            .filters-section,
            .filter-btn,
            .user-info,
            .sidebar,
            .action-buttons {
                display: none !important;
            }
            
            .main-content {
                margin: 0;
                padding: 20px;
            }
            
            .stat-card {
                break-inside: avoid;
            }
            
            body {
                background: white;
            }
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .filter-actions {
            display: flex;
            gap: 10px;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>

<body>
    <!-- الشريط الجانبي للتنقل -->
    <?= $this->include('layouts/header') ?>

    <div class="main-content">
        <div class="header">
            <h1 class="page-title">سجلات عمليات العهد</h1>
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
                <!-- New Items Card -->
                <div class="stat-card">
                    <div class="stat-icon new">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= number_format($stats['total_new'] ?? 0) ?></h3>
                        <p>عهد مباشرة</p>
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
            <form method="get" action="<?= base_url('AssetsHistory/assetsHistory') ?>">
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

                        <!-- اسم الصنف -->
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-box"></i>
                                اسم الصنف
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
                                <option value="">لكل العمليات</option>
                                <option value="new" <?= ($filters['operation_type'] ?? '') == 'new' ? 'selected' : '' ?>>مباشرة</option>
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

                        <!-- الفلتر السادس: الرقم الوظيفي (يظهر فقط لأصحاب الصلاحيات) -->
                        <?php if (in_array($filters['user_role'] ?? '', ['assets', 'super_assets'])): ?>
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-id-badge"></i>
                                الرقم الوظيفي
                            </label>
                            <input type="text" class="filter-input" name="returned_by"
                                   value="<?= esc($filters['returned_by'] ?? '') ?>" 
                                   placeholder="رقم الموظف">
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- أزرار العمليات -->
                    <div class="filter-actions">
                        <div style="display: flex; gap: 10px;">
                            <button type="submit" class="filter-btn search-btn">
                                <i class="fas fa-search"></i>
                                بحث
                            </button>
                            <a href="<?= base_url('AssetsHistory/assetsHistory') ?>" class="filter-btn reset-btn">
                                <i class="fas fa-undo"></i>
                                إعادة تعيين
                            </a>
                        </div>
                        <button 
                            type="button" 
                            onclick="printCombinedReturn()" 
                            class="filter-btn" 
                            style="background: linear-gradient(135deg, #17a2b8, #007bff); color: white;">
                            <i class="fas fa-print"></i>
                            طباعة
                        </button>
                    </div>
                </div>
            </form>

            <div class="table-container">
                <table class="custom-table" id="datatable-operations">
                    <thead>
                        <tr class="text-center">
                            <th>رقم الأصل</th>
                            <th>اسم الصنف</th>
                            <th>آخر عملية</th>
                            <th>تاريخ آخر عملية</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($operations) && !empty($operations)): ?>
                            <?php foreach ($operations as $operation): ?>
                                <tr class="text-center align-middle">
                                    <td><?= esc($operation->asset_number ?? '-') ?></td>
                                    <td><?= esc($operation->item_name ?? '-') ?></td>
                                    <td>
                                        <?php if ($operation->operation_type == 'new'): ?>
                                            <span class="badge" style="background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 5px 12px; border-radius: 20px;">
                                                <i class="fas fa-plus-circle"></i> مباشرة
                                            </span>
                                        <?php elseif ($operation->operation_type == 'transfer'): ?>
                                            <span class="badge" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 5px 12px; border-radius: 20px;">
                                                <i class="fas fa-exchange-alt"></i> تحويل
                                            </span>
                                        <?php elseif ($operation->operation_type == 'return'): ?>
                                            <span class="badge" style="background: linear-gradient(135deg, #f093fb, #f5576c); color: white; padding: 5px 12px; border-radius: 20px;">
                                                <i class="fas fa-undo"></i> إرجاع
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $date = $operation->last_operation_date ?? '-';
                                        if ($date !== '-') {
                                            echo esc(date('Y-m-d', strtotime($date)));
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">لا توجد بيانات متاحة</td>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    let isPrinting = false;
    
    window.printCombinedReturn = function () {
        if (isPrinting) {
            return;
        }
        
        const operationType = document.querySelector('select[name="operation_type"]')?.value;
        
        if (operationType !== 'return') {
            alert('يرجى تحديد نوع العملية: إرجاع أولاً للطباعة');
            return;
        }

        isPrinting = true;

        const params = new URLSearchParams({
            asset_number: document.querySelector('input[name="asset_number"]')?.value || '',
            item_name: document.querySelector('input[name="item_name"]')?.value || '',
            date_from: document.querySelector('input[name="date_from"]')?.value || '',
            date_to: document.querySelector('input[name="date_to"]')?.value || '',
            returned_by: document.querySelector('input[name="returned_by"]')?.value || ''
        });

        const url = '<?= site_url("reports/returnreport") ?>?' + params.toString();
        
        const existingIframe = document.getElementById('printIframe');
        if (existingIframe) {
            existingIframe.remove();
        }
        
        const iframe = document.createElement('iframe');
        iframe.id = 'printIframe';
        iframe.style.position = 'absolute';
        iframe.style.width = '0';
        iframe.style.height = '0';
        iframe.style.border = 'none';
        iframe.style.visibility = 'hidden';
        iframe.style.left = '-9999px';
        
        iframe.addEventListener('load', function() {
            try {
                setTimeout(function() {
                    iframe.contentWindow.print();
                    setTimeout(function() {
                        isPrinting = false;
                    }, 1000);
                }, 500);
            } catch (e) {
                console.error('Print error:', e);
                alert('حدث خطأ أثناء الطباعة. يرجى المحاولة مرة أخرى.');
                isPrinting = false;
            }
        }, { once: true });
        
        document.body.appendChild(iframe);
        iframe.src = url;
    }

    const operationTypeSelect = document.querySelector('select[name="operation_type"]');
    const printButton = document.querySelector('button[onclick="printCombinedReturn()"]');
    
    if (operationTypeSelect && printButton) {
        operationTypeSelect.addEventListener('change', function() {
            if (this.value === 'return') {
                printButton.disabled = false;
                printButton.title = 'طباعة النموذج المجمع';
                printButton.style.opacity = '1';
                printButton.style.cursor = 'pointer';
            } else {
                printButton.disabled = true;
                printButton.title = 'يرجى تحديد نوع العملية: إرجاع';
                printButton.style.opacity = '0.5';
                printButton.style.cursor = 'not-allowed';
            }
        });
        
        operationTypeSelect.dispatchEvent(new Event('change'));
    }
});
</script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

</body>

</html>