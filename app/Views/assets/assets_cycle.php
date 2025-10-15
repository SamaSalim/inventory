<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تتبع عمليات الاصل - <?= esc($asset_info->asset_num) ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('public/assets/css/order_details.css') ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
  * {
            font-family: "Cairo", sans-serif;
        }

        .action-buttons-container {
            display: flex;
            gap: 12px;
            justify-content: flex-start;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .back-btn {
            background-color: #e9ecef;
            color: #495057;
        }

        .back-btn:hover {
            background-color: #dee2e6;
            color: #212529;
        }

        .print-btn {
            background-color: #057590;
            color: white;
        }

        .print-btn:hover {
            background-color: #045a74;
        }

        .btn-icon {
            width: 18px;
            height: 18px;
        }

        .section-card {
            background-color: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .asset-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
        }

        .asset-info {
            flex: 1;
            min-width: 250px;
        }

        .asset-number {
            background: linear-gradient(135deg, #3ac0c3, #2aa8ab);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            display: inline-block;
        }

        .asset-name {
            font-size: 16px;
            color: #057590;
            font-weight: 600;
            margin-top: 10px;
            margin-right:10px;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }

        .stat-card {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #3ac0c3;
            text-align: center;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #057590;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 12px;
            color: #666;
            font-weight: 500;
        }

        .operations-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .operation-item {
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e9ecef;
            border-right: 4px solid #3ac0c3;
            transition: all 0.3s ease;
        }

        .operation-item:hover {
            box-shadow: 0 4px 12px rgba(5, 117, 144, 0.15);
            border-right-color: #057590;
        }

        .operation-item.returned {
            border-right-color: #28a745;
            background: linear-gradient(135deg, #ffffff, #f0f9f7);
        }

        .operation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
            flex-wrap: wrap;
            gap: 10px;
        }

        .operation-date {
            font-size: 14px;
            color: #6c757d;
            font-weight: 500;
        }

        .operation-status {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-received {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .transfer-flow {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 15px;
            align-items: center;
            margin-bottom: 15px;
        }

        .user-card {
            background: white;
            padding: 15px;
            border-radius: 10px;
            border: 2px solid #e9ecef;
        }

        .user-card.from {
            border-color: #dc3545;
            border-left: 4px solid #dc3545;
        }

        .user-card.to {
            border-color: #28a745;
            border-left: 4px solid #28a745;
        }

        .user-card.warehouse {
            border-color: #057590;
            border-left: 4px solid #057590;
        }

        .user-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .user-label.from {
            color: #dc3545;
        }

        .user-label.to {
            color: #28a745;
        }

        .user-label.warehouse {
            color: #057590;
        }

        .user-name {
            font-size: 14px;
            font-weight: 700;
            color: #212529;
            margin-bottom: 8px;
        }

        .user-detail {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 4px;
        }

        .arrow-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .arrow-icon {
            font-size: 1.8rem;
            color: #3ac0c3;
        }

        .arrow-icon.return {
            color: #28a745;
        }

        .arrow-label {
            font-size: 11px;
            color: #6c757d;
            font-weight: 600;
        }

        .return-flow {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 15px;
            align-items: center;
        }

        .warehouse-card {
            background: white;
            padding: 15px;
            border-radius: 10px;
            border: 2px solid #057590;
            border-left: 4px solid #057590;
            text-align: center;
        }

        .warehouse-card i {
            font-size: 2rem;
            margin-bottom: 10px;
            display: block;
            color: #057590;
        }

        .warehouse-card h4 {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 5px;
            color: #212529;
        }

        .note-section {
            background: rgba(58, 192, 195, 0.05);
            padding: 12px;
            border-radius: 8px;
            border-right: 3px solid #3ac0c3;
            margin-top: 12px;
        }

        .note-label {
            font-size: 11px;
            color: #057590;
            font-weight: 600;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .note-text {
            font-size: 13px;
            color: #555;
            line-height: 1.4;
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 3.5rem;
            margin-bottom: 15px;
            opacity: 0.5;
            display: block;
        }

        .empty-state h3 {
            font-size: 18px;
            margin-bottom: 8px;
        }

        /* ============================================
           PRINT STYLES - EXACT MATCH TO REFERENCE IMAGE
           ============================================ */
        
        .print-only {
            display: none;
        }

        @media print {
            /* Hide everything on screen */
            .no-print,
            body > *:not(.print-only) {
                display: none !important;
            }

            /* Show only print content */
            .print-only {
                display: block !important;
            }

            body {
                background: white;
                padding: 0;
                margin: 0;
            }

            @page {
                size: A4;
                margin: 20mm;
            }

            /* Print Container */
            .print-page {
                width: 100%;
                max-width: 210mm;
                margin: 0 auto;
                background: white;
                padding: 0;
            }

            /* Logo and Title */
            .print-logo-section {
                text-align: center;
                margin-bottom: 20px;
            }

            .print-logo {
                width: 80px;
                height: auto;
                margin-bottom: 10px;
            }

            .print-logo-text {
                font-size: 11pt;
                color: #2a7a8c;
                margin-bottom: 5px;
            }

            .print-main-title {
                font-size: 20pt;
                font-weight: 700;
                color: #3d5a6b;
                text-align: center;
                margin: 20px 0;
                padding-bottom: 15px;
                border-bottom: 2px solid #000;
            }

            /* Header Grid - 3 Rows x 2 Columns */
            .print-info-grid {
                margin: 25px 0;
            }

            .print-info-row {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 0;
                border-bottom: 2px solid #000;
            }

            .print-info-row:last-child {
                border-bottom: none;
            }

            .print-info-cell {
                display: flex;
                align-items: center;
                padding: 10px 15px;
                min-height: 40px;
                border-left: 2px solid #000;
            }

            .print-info-cell:first-child {
                border-left: none;
            }

            .print-label {
                font-weight: 600;
                color: #000;
                margin-left: 10px;
                white-space: nowrap;
            }

            .print-value {
                flex: 1;
                border-bottom: 1px solid #666;
                min-width: 150px;
                min-height: 20px;
            }

            /* Main Table */
            .print-table {
                width: 100%;
                border-collapse: collapse;
                margin: 25px 0;
                border: 2px solid #3d5a6b;
            }

            .print-table th {
                background-color: #3d5a6b;
                color: white;
                padding: 10px 5px;
                font-weight: 600;
                font-size: 9pt;
                text-align: center;
                border: 1px solid #3d5a6b;
            }

            .print-table td {
                padding: 8px 5px;
                text-align: center;
                border: 1px solid #3d5a6b;
                font-size: 8pt;
                min-height: 40px;
                vertical-align: middle;
            }

            .print-table td.text-right {
                text-align: right;
                padding-right: 10px;
            }

            /* Signature Grid - 4 Columns */
            .print-signatures {
                width: 100%;
                border: 2px solid #000;
                border-collapse: collapse;
                margin: 30px 0 20px 0;
            }

            .print-signatures td {
                width: 25%;
                padding: 20px 10px;
                text-align: center;
                border-left: 2px solid #000;
                vertical-align: top;
            }

            .print-signatures td:last-child {
                border-left: none;
            }

            .print-sig-title {
                font-weight: 600;
                font-size: 10pt;
                margin-bottom: 25px;
                color: #000;
            }

            .print-sig-field {
                font-size: 9pt;
                margin: 10px 0;
                text-align: right;
                padding-right: 10px;
            }

            .print-sig-line {
                display: inline-block;
                width: 100px;
                border-bottom: 1px solid #000;
                margin-right: 5px;
            }

            /* Authority Boxes */
            .print-authority-box {
                border: 2px solid #000;
                padding: 15px;
                margin: 10px 0;
                display: flex;
                align-items: center;
            }

            /* Warning Box */
            .print-warning {
                background-color: #f8d7da;
                border: 2px solid #e74c3c;
                padding: 15px;
                text-align: center;
                margin: 20px 0;
            }

            .print-warning-title {
                font-weight: 700;
                color: #c0392b;
                margin-bottom: 8px;
                font-size: 10pt;
            }

            .print-warning-text {
                font-size: 8pt;
                color: #721c24;
            }
        }

        @media (max-width: 768px) {
            .transfer-flow,
            .return-flow {
                grid-template-columns: 1fr;
            }

            .arrow-icon {
                transform: rotate(90deg);
            }

            .asset-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }

            .operation-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <?= $this->include('layouts/header') ?>

    <!-- SCREEN VIEW -->
    <div class="main-content no-print">
        <div class="header">
            <h1 class="page-title">تتبع عمليات الأصل</h1>
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
            <!-- Asset Info Section -->
            <div class="section-card">
                <div class="asset-header">
                    <div class="asset-info">
                        <span class="asset-number">
                            <i class="fas fa-barcode"></i>
                            <?= esc($asset_info->asset_num) ?>
                        </span>
                        <div class="asset-name">
                            <i class="fas fa-cube"></i>
                            <?= esc($asset_info->item_name) ?>
                        </div>
                    </div>
                </div>

                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-value"><?= $total_operations ?></div>
                        <div class="stat-label">إجمالي العمليات</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">
                            <?= count(array_filter($timeline, fn($t) => $t['type'] === 'transfer')) ?>
                        </div>
                        <div class="stat-label">عمليات تحويل</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">
                            <?= count(array_filter($timeline, fn($t) => $t['type'] === 'returned')) ?>
                        </div>
                        <div class="stat-label">عمليات إرجاع</div>
                    </div>
                </div>
            </div>

            <!-- Operations Section -->
            <div class="section-card">
                <div class="section-title">
                    <i class="fas fa-history"></i>
                    سجل العمليات
                </div>

                <?php if (!empty($timeline)): ?>
                    <div class="operations-list">
                        <?php foreach ($timeline as $index => $operation): ?>
                            <?php if ($operation['type'] === 'transfer'): ?>
                                <div class="operation-item">
                                    <div class="operation-header">
                                        <div class="operation-date">
                                            <i class="fas fa-calendar"></i>
                                            <?= date('Y-m-d', strtotime($operation['date'])) ?>
                                        </div>
                                        <div class="operation-status">
                                            <span class="status-badge 
                                                <?php 
                                                    if ($operation['status'] === 'قيد الانتظار') echo 'status-pending';
                                                    elseif ($operation['status'] === 'مقبول') echo 'status-approved';
                                                    else echo 'status-rejected';
                                                ?>
                                            ">
                                                <i class="fas fa-info-circle"></i>
                                                <?= esc($operation['status']) ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="transfer-flow">
                                        <div class="user-card from">
                                            <div class="user-label from">
                                                <i class="fas fa-user-minus"></i>
                                                من
                                            </div>
                                            <div class="user-name"><?= esc($operation['from_user_name'] ?? 'غير محدد') ?></div>
                                            <div class="user-detail"><i class="fas fa-building"></i> <?= esc($operation['from_user_dept'] ?? '-') ?></div>
                                        </div>

                                        <div class="arrow-section">
                                            <i class="fas fa-arrow-left arrow-icon"></i>
                                            <span class="arrow-label">تحويل</span>
                                        </div>

                                        <div class="user-card to">
                                            <div class="user-label to">
                                                <i class="fas fa-user-plus"></i>
                                                إلى
                                            </div>
                                            <div class="user-name"><?= esc($operation['to_user_name'] ?? 'غير محدد') ?></div>
                                            <div class="user-detail"><i class="fas fa-building"></i> <?= esc($operation['to_user_dept'] ?? '-') ?></div>
                                        </div>
                                    </div>

                                    <?php if (!empty($operation['note'])): ?>
                                        <div class="note-section">
                                            <div class="note-label">
                                                <i class="fas fa-sticky-note"></i>
                                                ملاحظة
                                            </div>
                                            <div class="note-text"><?= esc($operation['note']) ?></div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                            <?php elseif ($operation['type'] === 'returned'): ?>
                                <div class="operation-item returned">
                                    <div class="operation-header">
                                        <div class="operation-date">
                                            <i class="fas fa-calendar-check"></i>
                                            <?= date('Y-m-d', strtotime($operation['date'])) ?>
                                        </div>
                                        <div class="operation-status">
                                            <span class="status-badge status-received">
                                                <i class="fas fa-check-circle"></i>
                                                تم الاستلام
                                            </span>
                                        </div>
                                    </div>

                                    <div class="return-flow">
                                        <div class="user-card from">
                                            <div class="user-label from">
                                                <i class="fas fa-user-minus"></i>
                                                من
                                            </div>
                                            <div class="user-name"><?= esc($operation['returned_by_name']) ?></div>
                                            <div class="user-detail"><i class="fas fa-building"></i> <?= esc($operation['returned_by_dept']) ?></div>
                                        </div>

                                        <div class="arrow-section">
                                            <i class="fas fa-arrow-left arrow-icon return"></i>
                                            <span class="arrow-label">إرجاع</span>
                                        </div>

                                        <div class="warehouse-card">
                                            <i class="fas fa-warehouse"></i>
                                            <h4>المستودع</h4>
                                            <p style="font-size: 12px; margin: 0; color: #6c757d;">تم استلام الأصل</p>
                                        </div>
                                    </div>

                                    <?php if (!empty($operation['note'])): ?>
                                        <div class="note-section">
                                            <div class="note-label">
                                                <i class="fas fa-sticky-note"></i>
                                                ملاحظة الإرجاع
                                            </div>
                                            <div class="note-text"><?= esc($operation['note']) ?></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h3>لا توجد عمليات</h3>
                        <p>لم يتم إجراء أي عمليات تحويل على هذا الأصل</p>
                    </div>
                <?php endif; ?>

                <div class="action-buttons-container">
                    <a href="<?= site_url('assetsHistory/superAssets') ?>" class="action-btn back-btn">
                        <svg class="btn-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        <span>العودة</span>
                    </a>
                    <button onclick="window.print()" class="action-btn print-btn">
                        <svg class="btn-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 9V4H18V9M6 14H18V20H6V14Z" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        <span>طباعة</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- PRINT VIEW - EXACT MATCH TO IMAGE -->
    <div class="print-only">
        <div class="print-container">
            <!-- Header with Logo -->
            <div class="print-header">
                <div class="print-logo-container">
                    <img src="<?= base_url('public/assets/images/Kamc Logo Guideline-04.png') ?>" 
                         alt="KAMC Logo" class="print-logo">
                </div>
                <div class="print-form-title">طلب صرف مواد</div>
            </div>

            <!-- Header Fields -->
            <div class="print-header-fields">
                <!-- Row 1 -->
                <div class="print-header-row">
                    <div class="print-header-field">
                        <span class="print-field-label">الوزارة:</span>
                        <div class="print-field-value">مدينة الملك عبدالله الطبية</div>
                    </div>
                    <div class="print-header-field">
                        <span class="print-field-label">المستودع:</span>
                        <div class="print-field-value"><?= esc($asset_info->asset_num ?? '') ?></div>
                    </div>
                </div>

                <!-- Row 2 -->
                <div class="print-header-row">
                    <div class="print-header-field">
                        <span class="print-field-label">إدارة المستودعات:</span>
                        <div class="print-field-value">
                            <?php 
                            $firstOperation = !empty($timeline) ? $timeline[0] : null;
                            echo esc($firstOperation['from_user_dept'] ?? 'غير محدد');
                            ?>
                        </div>
                    </div>
                    <div class="print-header-field">
                        <span class="print-field-label">الرقم الخاص:</span>
                        <div class="print-field-value"><?= esc($asset_info->asset_num ?? '') ?></div>
                    </div>
                </div>

                <!-- Row 3 -->
                <div class="print-header-row">
                    <div class="print-header-field">
                        <span class="print-field-label">الجهة الطالبة:</span>
                        <div class="print-field-value">
                            <?php 
                            $lastTransfer = null;
                            if (!empty($timeline)) {
                                foreach (array_reverse($timeline) as $op) {
                                    if ($op['type'] === 'transfer') {
                                        $lastTransfer = $op;
                                        break;
                                    }
                                }
                            }
                            echo esc($lastTransfer['to_user_dept'] ?? 'غير محدد');
                            ?>
                        </div>
                    </div>
                    <div class="print-header-field">
                        <span class="print-field-label">التاريخ:</span>
                        <div class="print-field-value"><?= date('Y-m-d') ?></div>
                    </div>
                </div>
            </div>

            <!-- Main Table -->
            <div class="print-table-container">
                <table class="print-main-table">
                    <thead>
                        <tr>
                            <th style="width: 8%;">رقم الطلب</th>
                            <th style="width: 10%;">رقم الصنف</th>
                            <th style="width: 20%;">اسم الصنف والمواصفة</th>
                            <th style="width: 8%;">نوع الصنف</th>
                            <th style="width: 8%;">الوحدة</th>
                            <th style="width: 10%;">الكمية المطلوبة</th>
                            <th style="width: 10%;">الكمية المصروفة</th>
                            <th style="width: 8%;">سعر الوحدة</th>
                            <th style="width: 10%;">القيمة الكلية</th>
                            <th style="width: 8%;">ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($timeline)): ?>
                            <?php foreach ($timeline as $index => $operation): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= esc($asset_info->asset_num) ?></td>
                                    <td class="print-item-cell">
                                        <div class="print-item-name"><?= esc($asset_info->item_name) ?></div>
                                        <div class="print-item-details">
                                            <span class="print-operation-badge <?= $operation['type'] === 'transfer' ? 'print-badge-transfer' : 'print-badge-return' ?>">
                                                <?= $operation['type'] === 'transfer' ? 'تحويل' : 'إرجاع' ?>
                                            </span>
                                            <br>
                                            <?php if ($operation['type'] === 'transfer'): ?>
                                                من: <?= esc($operation['from_user_name']) ?><br>
                                                إلى: <?= esc($operation['to_user_name']) ?>
                                            <?php else: ?>
                                                مرتجع من: <?= esc($operation['returned_by_name']) ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td><?= $operation['type'] === 'transfer' ? 'عهدة' : 'مرتجع' ?></td>
                                    <td>1</td>
                                    <td>1</td>
                                    <td>1</td>
                                    <td></td>
                                    <td></td>
                                    <td style="font-size: 8pt; text-align: right; padding: 5px;">
                                        <?= !empty($operation['note']) ? esc(mb_substr($operation['note'], 0, 50)) . '...' : '' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" style="padding: 40px; text-align: center; color: #999;">
                                    لا توجد عمليات مسجلة
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Signature Section -->
            <div class="print-signature-section">
                <table class="print-signature-table">
                    <tr>
                        <td>
                            <div class="print-signature-title">رئيس الجهة الطالبة</div>
                            <div class="print-signature-fields">
                                الاسم: <span class="print-signature-line"></span><br>
                                التاريخ: <span class="print-signature-line"></span>
                            </div>
                        </td>
                        <td>
                            <div class="print-signature-title">إدارة المستودعات</div>
                            <div class="print-signature-fields">
                                الاسم: <span class="print-signature-line"></span><br>
                                التاريخ: <span class="print-signature-line"></span>
                            </div>
                        </td>
                        <td>
                            <div class="print-signature-title">أمين المستودع</div>
                            <div class="print-signature-fields">
                                الاسم: <span class="print-signature-line"></span><br>
                                التاريخ: <span class="print-signature-line"></span>
                            </div>
                        </td>
                        <td>
                            <div class="print-signature-title">المستلم</div>
                            <div class="print-signature-fields">
                                الاسم: <span class="print-signature-line"></span><br>
                                التاريخ: <span class="print-signature-line"></span>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Authority Section -->
            <div class="print-authority-section">
                <div class="print-authority-field">
                    <span class="print-field-label">صاحب الصلاحية:</span>
                    <div class="print-field-value"></div>
                </div>
                <div class="print-authority-field">
                    <span class="print-field-label">التوقيع:</span>
                    <div class="print-field-value"></div>
                </div>
            </div>

            <!-- Notes Section -->
            <div class="print-notes-section">
                <div class="print-notes-header">صاحب المطالبة:</div>
                <div class="print-notes-content">
                    <?php 
                    $allNotes = array_filter(array_column($timeline, 'note'));
                    if (!empty($allNotes)) {
                        echo esc(implode(' | ', $allNotes));
                    }
                    ?>
                </div>
            </div>

            <!-- Footer -->
            <div class="print-footer">
                <div class="print-warning-box">
                    <div class="print-warning-title">تنبيه مهم: يرجى مراجعة جميع البنود بدقة والتأكد من صحة البيانات قبل التوقيع والاستلام</div>
                    <div class="print-warning-text">هذا المستند رسمي ويجب الاحتفاظ به للمراجعة والتدقيق</div>
                </div>
                <div class="print-footer-note">
                    إجمالي العمليات: <?= $total_operations ?> | 
                    عمليات التحويل: <?= count(array_filter($timeline, fn($t) => $t['type'] === 'transfer')) ?> | 
                    عمليات الإرجاع: <?= count(array_filter($timeline, fn($t) => $t['type'] === 'returned')) ?>
                    <br>
                    تم الطباعة بتاريخ: <?= date('Y-m-d H:i:s') ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>