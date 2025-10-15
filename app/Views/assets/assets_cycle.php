<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تتبع عمليات الاصل - <?= esc($asset_info->asset_num) ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('public/assets/css/order_details.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/assets/css/asset_tracking_style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/assets/css/print_style.css') ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('public/assets/css/components/print_form_style.css') ?>">
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
                    <button onclick="window.print()" class="action-btn print-btn">
                        <svg class="btn-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 9V4H18V9M6 14H18V20H6V14Z" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        <span>طباعة</span>
                    </button>
                    <a href="<?= site_url('assetsHistory/superAssets') ?>" class="action-btn back-btn">
                        <span>العودة</span>
                        <svg class="btn-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- PRINT VIEW -->
    <div class="print-only">
        <div class="print-container">
            <!-- Header Section -->
            <div class="print-header">
                <div class="ministry-details">
                    <div class="logo-section">
                        <div class="kamc-emblem">
                            <img src="<?= base_url('public/assets/images/Kamc Logo Guideline-04.png') ?>" 
                                alt="KAMC Logo">
                        </div>
                        <div class="form-title">تتبع عمليات الأصل</div>
                    </div>
                    <div style="width:60px;"></div>
                </div>
                
                <!-- Asset Info -->
        <div class="header-fields">
            <div class="header-field">
                <span class="field-label">رقم الأصل:</span>
                <div class="field-value-on-line"><?= esc($asset_info->asset_num) ?></div>
            </div>
            <div class="header-field">
                <span class="field-label">اسم الصنف:</span>
                <div class="field-value-on-line"><?= esc($asset_info->item_name) ?></div>
            </div>
        </div>

        <div class="header-fields">
            <div class="header-field">
                <span class="field-label">إجمالي العمليات:</span>
                <div class="field-value-on-line"><?= $total_operations ?></div>
            </div>
            <div class="header-field">
                <span class="field-label">التاريخ:</span>
                <div class="field-value-on-line"><?= date('Y-m-d') ?></div>
            </div>
        </div>

            <!-- Operations Table -->
            <table class="main-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">رقم العملية</th>
                        <th style="width: 10%;">نوع العملية</th>
                        <th style="width: 15%;">من</th>
                        <th style="width: 15%;">إلى</th>
                        <th style="width: 12%;">تاريخ العملية</th>
                        <th style="width: 10%;">الحالة</th>
                        <th style="width: 12%;">تاريخ الحالة</th>
                        <th style="width: 21%;">ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($timeline)): ?>
                        <?php foreach ($timeline as $index => $operation): ?>
                            <tr>
                                <!-- Operation Number -->
                                <td><?= $index + 1 ?></td>

                                <!-- Operation Type -->
                                <td>
                                    <?php if ($operation['type'] === 'transfer'): ?>
                                        تحويل
                                    <?php else: ?>
                                        إرجاع
                                    <?php endif; ?>
                                </td>

                                <!-- From -->
                                <td>
                                    <?php if ($operation['type'] === 'transfer'): ?>
                                        <?= esc($operation['from_user_name']) ?>
                                        <br><small><?= esc($operation['from_user_dept']) ?></small>
                                    <?php else: ?>
                                        <?= esc($operation['returned_by_name']) ?>
                                        <br><small><?= esc($operation['returned_by_dept']) ?></small>
                                    <?php endif; ?>
                                </td>

                                <!-- To -->
                                <td>
                                    <?php if ($operation['type'] === 'transfer'): ?>
                                        <?= esc($operation['to_user_name']) ?>
                                        <br><small><?= esc($operation['to_user_dept']) ?></small>
                                    <?php else: ?>
                                        المستودع
                                    <?php endif; ?>
                                </td>

                                <!-- Operation Date -->
                                <td><?= date('Y-m-d', strtotime($operation['date'])) ?></td>

                                <!-- Status -->
                                <td><?= esc($operation['status']) ?></td>

                                <!-- Status Date -->
                                <td>
                                    <?php if (isset($operation['status_date'])): ?>
                                        <?= date('Y-m-d', strtotime($operation['status_date'])) ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>

                                <!-- Notes -->
                                <td>
                                    <?php if (!empty($operation['note'])): ?>
                                        <?= esc($operation['note']) ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center;">لا توجد عمليات</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Signature Section -->
            <div class="signature-section">
                <table class="signature-table">
                    <tr>

                        <td>
                            <div class="signature-title-cell">مدير الأصول</div>
                            <div class="signature-fields">
                                الاسم: <span class="signature-line"></span><br>
                                التاريخ: <span class="signature-line"></span>
                            </div>
                        </td>
                        <td>
                            <div class="signature-title-cell">المراجع</div>
                            <div class="signature-fields">
                                الاسم: <span class="signature-line"></span><br>
                                التاريخ: <span class="signature-line"></span>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="header-fields">
                <div class="header-field" style="border-left:none;">
                    <span class="field-label">صاحب الصلاحية:</span>
                    <div class="field-line"></div>
                </div>
            </div>
            
            <div class="header-fields">
                <div class="header-field" style="border-left:none;">
                    <span class="field-label">التوقيع:</span>
                    <div class="field-line"></div>
                </div>
            </div>

            <!-- Footer -->
            <div class="form-footer">
                <div class="footer-warning">
                    تنبيه مهم: يرجى مراجعة جميع العمليات بدقة والتأكد من صحة البيانات
                </div>
                <div class="footer-note">
                    هذا المستند رسمي ويجب الاحتفاظ به للمراجعة والتدقيق
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>