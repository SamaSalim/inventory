<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلبات الإرجاع</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('public/assets/css/warehouse-style.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Cairo', sans-serif;
        }

        body {
            background-color: #EFF8FA;
            margin: 0;
            padding: 0;
        }

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

        .section-title {
            color: #057590;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-title i {
            color: #3AC0C3;
        }

        /* Status Badges */
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-return {
            background: #fff3cd;
            color: #856404;
        }

        .order-status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .order-status-accepted {
            background: #d4edda;
            color: #155724;
        }

        .order-status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .action-btn {
            padding: 6px 14px;
            border-radius: 16px;
            border: 2px solid;
            font-size: 11px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: white;
        }

        .view-btn {
            border-color: #3AC0C3;
            color: #3AC0C3;
        }

        .view-btn:not(:disabled):hover {
            background: linear-gradient(135deg, #3AC0C3, #2aa8ab);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(58, 192, 195, 0.25);
        }

        .accept-btn {
            border-color: #28a745;
            color: #28a745;
        }

        .accept-btn:not(:disabled):hover {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(40, 167, 69, 0.25);
        }

        .reject-btn {
            border-color: #dc3545;
            color: #dc3545;
        }

        .reject-btn:not(:disabled):hover {
            background: linear-gradient(135deg, #dc3545, #fd7e14);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(220, 53, 69, 0.25);
        }

        .action-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            border-color: #ccc;
            color: #999;
        }

        .btn-icon {
            width: 12px;
            height: 12px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }

        .empty-state i {
            font-size: 48px;
            opacity: 0.3;
            margin-bottom: 15px;
            display: block;
        }

        .empty-state p {
            font-size: 14px;
            margin: 0;
        }

        /* Action Form Inline */
        .action-form {
            display: inline;
            margin: 0;
        }
    </style>
</head>

<body>
    <?= $this->include('layouts/header') ?>

    <div class="main-content">
        <div class="header">
            <h1 class="page-title">طلبات الإرجاع</h1>
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
            <!-- Alert Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success show" role="alert">
                    <i class="fas fa-check-circle"></i>
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger show" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('info')): ?>
                <div class="alert alert-warning show" role="alert">
                    <i class="fas fa-info-circle"></i>
                    <?= session()->getFlashdata('info') ?>
                </div>
            <?php endif; ?>


            <!-- Filters Form -->
            <form method="get" action="<?= base_url('return/superWarehouse/returnrequests') ?>">
                <div class="filters-section">
                    <!-- General Search Section -->
                    <div class="main-search-container">
                        <h3 class="search-section-title">
                            <i class="fas fa-search"></i>
                            البحث العام
                        </h3>
                        <div class="search-bar-wrapper">
                            <input type="text" 
                                   class="main-search-input" 
                                   name="general_search"
                                   value="<?= esc($filters['general_search'] ?? '') ?>"
                                   placeholder="ابحث في جميع الحقول...">
                            <i class="fas fa-search search-icon"></i>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="filters-divider">
                        <span>أو استخدم الفلاتر التفصيلية</span>
                    </div>

                    <!-- Detailed Filters -->
                    <div class="detailed-filters">
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-hashtag"></i>
                                رقم الطلب
                            </label>
                            <input type="text" 
                                   class="filter-input" 
                                   name="order_id"
                                   value="<?= esc($filters['order_id'] ?? '') ?>" 
                                   placeholder="أدخل رقم الطلب">
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-id-badge"></i>
                                الرقم الوظيفي
                            </label>
                            <input type="text" 
                                   class="filter-input" 
                                   name="emp_id"
                                   value="<?= esc($filters['emp_id'] ?? '') ?>" 
                                   placeholder="أدخل الرقم الوظيفي">
                        </div>



                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-box"></i>
                                اسم الصنف
                            </label>
                            <input type="text" 
                                   class="filter-input" 
                                   name="item_name"
                                   value="<?= esc($filters['item_name'] ?? '') ?>" 
                                   placeholder="أدخل اسم الصنف">
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-barcode"></i>
                                رقم الأصل
                            </label>
                            <input type="text" 
                                   class="filter-input" 
                                   name="asset_num"
                                   value="<?= esc($filters['asset_num'] ?? '') ?>" 
                                   placeholder="أدخل رقم الأصل">
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-hashtag"></i>
                                الرقم التسلسلي
                            </label>
                            <input type="text" 
                                   class="filter-input" 
                                   name="serial_num"
                                   value="<?= esc($filters['serial_num'] ?? '') ?>" 
                                   placeholder="أدخل الرقم التسلسلي">
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-calendar-alt"></i>
                                من تاريخ
                            </label>
                            <input type="date" 
                                   class="filter-input" 
                                   name="date_from"
                                   value="<?= esc($filters['date_from'] ?? '') ?>">
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-calendar-check"></i>
                                إلى تاريخ
                            </label>
                            <input type="date" 
                                   class="filter-input" 
                                   name="date_to"
                                   value="<?= esc($filters['date_to'] ?? '') ?>">
                        </div>
                    </div>

                    <!-- Filter Actions -->
                    <div class="filter-actions">
                        <div style="display: flex; gap: 15px;">
                            <button type="submit" class="filter-btn search-btn">
                                <i class="fas fa-search"></i>
                                بحث
                            </button>
                            <a href="<?= base_url('return/superWarehouse/returnrequests') ?>" class="filter-btn reset-btn">
                                <i class="fas fa-undo"></i>
                                إعادة تعيين
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Return Requests Table -->
            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>رقم الطلب</th>
                            <th>الرقم الوظيفي</th>
                            <th>اسم المرجع</th>
                            <th>اسم الصنف</th>
                            <th>رقم الأصل</th>
                            <th>الرقم التسلسلي</th>
                            <th>حالة الاستخدام</th>
                            <th>حالة الطلب</th>
                            <th>تاريخ الطلب</th>
                            <th>عمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($returnOrders)): ?>
                            <?php foreach ($returnOrders as $order): ?>
                                <tr>
                                    <td><?= esc($order['item_order_id']) ?></td>
                                    <td><?= esc($order['created_by']) ?></td>
                                    <td><?= esc($order['employee_name'] ?? 'غير محدد') ?></td>
                                    <td><?= esc($order['item_name'] ?? 'غير محدد') ?></td>
                                    <td><?= esc($order['asset_num'] ?? '-') ?></td>
                                    <td><?= esc($order['serial_num'] ?? '-') ?></td>
                                    <td>
                                        <span class="status-badge status-return">
                                            <?= esc($order['usage_status'] ?? 'رجيع') ?>
                                        </span>
                                    </td>
                                <td>
                                    <?php 
                                    if ($order['usage_status_id'] == 2): 
                                    ?>
                                        <span class="status-badge order-status-pending">قيد الانتظار</span>
                                    <?php else: ?>
                                        <?php if (isset($order['order_status_id'])): ?>
                                            <?php if ($order['order_status_id'] == 1): ?>
                                                <span class="status-badge order-status-pending">قيد الانتظار</span>
                                            <?php elseif ($order['order_status_id'] == 2): ?>
                                                <span class="status-badge order-status-accepted">مقبول</span>
                                            <?php elseif ($order['order_status_id'] == 3): ?>
                                                <span class="status-badge order-status-rejected">مرفوض</span>
                                            <?php else: ?>
                                                <span class="status-badge">غير محدد</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="status-badge">غير محدد</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                    <td><?= date('Y-m-d', strtotime($order['created_at'])) ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <!-- View Button -->
                                            <a href="<?= base_url('superWarehouseReturn/view/' . $order['item_order_id']) ?>" 
                                               class="action-btn view-btn">
                                                <i class="fas fa-eye btn-icon"></i>
                                                عرض
                                            </a>
                                            
                                            <?php 
                                            // Enable accept/reject buttons only for pending orders (status_id = 1)
                                            $isPending = isset($order['order_status_id']) && $order['order_status_id'] == 1;
                                            ?>
                                            
                                            <!-- Accept Button -->
                                            <?php if ($isPending): ?>
                                                <form method="post" 
                                                      action="<?= base_url('superWarehouseReturn/accept/' . $order['item_order_id']) ?>" 
                                                      class="action-form"
                                                      onsubmit="return confirm('هل أنت متأكد من قبول هذا الإرجاع؟');">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="action-btn accept-btn">
                                                        <i class="fas fa-check btn-icon"></i>
                                                        قبول
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <button class="action-btn accept-btn" disabled>
                                                    <i class="fas fa-check btn-icon"></i>
                                                    قبول
                                                </button>
                                            <?php endif; ?>
                                            
                                            <!-- Reject Button -->
                                            <?php if ($isPending): ?>
                                                <form method="post" 
                                                      action="<?= base_url('superWarehouseReturn/reject/' . $order['item_order_id']) ?>" 
                                                      class="action-form"
                                                      onsubmit="return confirm('هل أنت متأكد من رفض هذا الإرجاع؟');">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="action-btn reject-btn">
                                                        <i class="fas fa-times btn-icon"></i>
                                                        رفض
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <button class="action-btn reject-btn" disabled>
                                                    <i class="fas fa-times btn-icon"></i>
                                                    رفض
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <p>لا توجد طلبات إرجاع</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });
        });
    </script>
</body>

</html>