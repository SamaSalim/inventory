<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل الطلب</title>
    <link rel="stylesheet" href="<?= base_url('public/assets/css/order_details.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/assets/css/order_details.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/assets/css/components/print_form_style.css') ?>">
</head>
<style>
    .header-field {
        display: flex;
        align-items: center;
        gap: 5px;
        justify-content: flex-end;
    }

    .field-label {
        white-space: nowrap;
    }

    .field-value-text {
        border-bottom: 1px solid #000;
        padding: 2px 5px;
        min-width: 200px;
        text-align: center;
    }

    .signature-fields {
        text-align: right;
    }

    .signature-value {
        border-bottom: 1px solid #000;
        display: inline-block;
        min-width: 150px;
        padding: 2px 5px;
        text-align: center;
        margin-right: 5px;
    }

    /* لإخفاء زر الطباعة في العرض العادي إذا لم يتم القبول */
    .print-btn.disabled {
        pointer-events: none;
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>

<body>
    <?= $this->include('layouts/header') ?>

    <!-- Regular screen content -->
    <div class="main-content no-print">
        <div class="header">
            <h1 class="page-title">إدارة المستودعات</h1>
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
            <!-- Order Details Section -->
            <div class="order-details-section">
                <!-- ... (Order Header and Info Grid remains the same) ... -->
                <div class="order-header">
                    <h2 class="order-title">تفاصيل الطلب رقم <?= esc($order->order_id) ?></h2>
                    <div class="order-id-badge">#<?= esc($order->order_id) ?></div>
                </div>

                <div class="order-info-grid">
                    <div class="info-card">
                        <div class="info-label">من</div>
                        <div class="info-value"><?= esc($order->from_name) ?></div>
                    </div>
                    <div class="info-card">
                        <div class="info-label">إلى</div>
                        <div class="info-value"><?= esc($order->to_name) ?></div>
                    </div>
                    <div class="info-card">
                        <div class="info-label">عدد العناصر</div>
                        <div class="info-value"><?= esc($item_count) ?></div>
                    </div>
                    <div class="info-card">
                        <div class="info-label">تاريخ الإنشاء</div>
                        <div class="info-value">
                            <?= date('Y-m-d', strtotime($order->created_at)) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Section -->
            <div class="items-section">
                <h3 class="section-title">العناصر المطلوبة</h3>

                <?php if (!empty($items)): ?>
                    <div class="items-grid">
                        <?php foreach ($items as $item): ?>
                            <div class="item-card">
                                <div class="item-card-header">
                                    <h4 class="item-name"><?= esc($item->item_name) ?></h4>
                                </div>

                                <div class="item-details">
                                    <div class="detail-item">
                                        <div class="detail-label">التصنيف</div>
                                        <div class="detail-value">
                                            <span class="category-badge"><?= esc($item->major_category_name . ' / ' . $item->minor_category_name) ?></span>
                                        </div>
                                    </div>

                                    <div class="detail-item">
                                        <div class="detail-label">الموديل</div>
                                        <div class="detail-value"><?= esc($item->model_num) ?: '<span class="empty">غير محدد</span>' ?></div>
                                    </div>

                                    <div class="detail-item">
                                        <div class="detail-label">الرقم التسلسلي</div>
                                        <div class="detail-value"><?= esc($item->serial_num) ?: '<span class="empty">غير محدد</span>' ?></div>
                                    </div>

                                    <div class="detail-item">
                                        <div class="detail-label">رقم الأصل</div>
                                        <div class="detail-value"><?= esc($item->asset_num) ?: '<span class="empty">غير محدد</span>' ?></div>
                                    </div>

                                    <div class="detail-item">
                                        <div class="detail-label">الرقم القديم</div>
                                        <div class="detail-value"><?= esc($item->old_asset_num) ?: '<span class="empty">غير محدد</span>' ?></div>
                                    </div>

                                    <div class="detail-item">
                                        <div class="detail-label">العلامة التجارية</div>
                                        <div class="detail-value"><?= esc($item->brand) ?: '<span class="empty">غير محدد</span>' ?></div>
                                    </div>
                                    
                                    <div class="detail-item">
                                        <div class="detail-label">السعر</div>
                                        <div class="detail-value"><?= !empty($item->price) ? esc($item->price) . ' ريال' : '<span class="empty">غير محدد</span>' ?></div>
                                    </div>

                                    <div class="detail-item">
                                        <div class="detail-label">نوع الأصل</div>
                                        <div class="detail-value"><?= esc($item->assets_type) ?: '<span class="empty">غير محدد</span>' ?></div>
                                    </div>

                                    <div class="detail-item">
                                        <div class="detail-label">الغرفة</div>
                                        <div class="detail-value"><?= esc($item->location_code) ?: '<span class="empty">غير محدد</span>' ?></div>
                                    </div>

                                    <div class="detail-item">
                                        <div class="detail-label">الحالة</div>
                                        <div class="detail-value">
                                            <span class="status-badge status-active"><?= esc($item->usage_status_name) ?></span>
                                        </div>
                                    </div>

                                    <div class="detail-item">
                                        <div class="detail-label">أنشئ بواسطة</div>
                                        <div class="detail-value"><?= esc($item->created_by_name) ?: '<span class="empty">غير محدد</span>' ?></div>
                                    </div>
                                </div>


                                <?php if (!empty($item->note)): ?>
                                    <div class="item-notes">
                                        <div class="notes-label">ملاحظات</div>
                                        <div class="notes-text"><?= esc($item->note) ?></div>
                                    </div>
                                <?php endif; ?>

                                <div class="item-timestamps">
                                    <div class="detail-item">
                                        <div class="detail-label">تاريخ الإنشاء</div>
                                        <div class="detail-value"><?= date('Y-m-d', strtotime($item->created_at)) ?></div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-label">آخر تحديث</div>
                                        <div class="detail-value"><?= date('Y-m-d', strtotime($item->updated_at)) ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-items-msg">لا توجد عناصر مرتبطة بهذا الطلب.</div>
                <?php endif; ?>

                <!-- Action buttons -->
                <div class="action-buttons-container">
                    <?php if (isset($order->order_status_id) && $order->order_status_id == 2): ?>
                        <button onclick="window.print()" class="action-btn print-btn">
                            <svg class="btn-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 9V4H18V9M6 14H18V20H6V14Z" stroke="currentColor" stroke-width="2" />
                            </svg>
                            <span>طباعة نموذج الاستلام</span>
                        </button>
                    <?php else: ?>
                        <button class="action-btn print-btn disabled" title="لا يمكن الطباعة قبل موافقة المستلم (الحالة: قيد الانتظار)">
                            <i class="fas fa-print"></i>
                            <span>طباعة نموذج الاستلام</span>
                        </button>
                    <?php endif; ?>

                    <?php $back_url = previous_url();  ?>
                    <!--   استخدام المسار السابق مباشرة    -->
                    <a href="<?= esc($back_url)  ?>" class="action-btn back-btn">
                        <span>العودة</span>
                        <svg class="btn-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- START: Print Only Content (Form) -->
    <?php if (isset($order->order_status_id) && $order->order_status_id == 2): ?>
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
                            <div class="form-title">نموذج استلام عهدة (طلب صرف مواد)</div>
                        </div>
                        <div style="width:60px;"></div>
                    </div>

                    <!-- Header fields row 1 -->
                    <div class="header-fields">
                        <div class="header-field">
                            <span class="field-label">الجهة الطالبة:</span>
                            <div class="field-value-text">ادارة العهد</div>
                        </div>
                        <div class="header-field">
                            <span class="field-label">التاريخ:</span>
                            <div class="field-value-text"><?= date('Y-m-d', strtotime($order->created_at)) ?></div>
                        </div>
                    </div>
                </div>

                <table class="main-table">
                    <thead>
                        <tr>
                            <th>رقم الطلب</th>
                            <th>رقم الصنف</th>
                            <th>الرقم التسلسلي</th>
                            <th>اسم الصنف وتصنيفه</th>
                            <th>الموديل</th>
                            <th>الماركة</th>
                            <th>نوع الصنف</th>
                            <th>ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($items)): ?>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?= esc($item->order_id) ?></td>
                                    <td><?= esc($item->asset_num) ?></td>
                                    <td><?= esc($item->serial_num) ?></td>
                                    <td>
                                        <?= esc($item->item_name) ?>
                                        <?php if (!empty($item->minor_category_name) || !empty($item->major_category_name)): ?>
                                            <br><small><?= esc($item->minor_category_name) ?> / <?= esc($item->major_category_name) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($item->model_num) ?></td>
                                    <td><?= esc($item->brand) ?></td>
                                    <td><?= esc($item->assets_type) ?></td>
                                    <td><?= esc($item->note) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?php for ($i = 1; $i <= 6; $i++): ?>
                                <tr class="empty-row">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            <?php endfor; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Signature Section -->
                <div class="signature-section">
                    <table class="signature-table">
                        <tr>
                            <td>
                                <div class="signature-title-cell">أمين المستودع</div>
                                <div class="signature-fields">
                                    الاسم: <span class="signature-value"><?= esc($order->from_name) ?></span><br>
                                    التاريخ: <span class="signature-value"><?= date('Y-m-d', strtotime($order->created_at)) ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="signature-title-cell">المستلم</div>
                                <div class="signature-fields">
                                    الاسم: <span class="signature-line"><?= esc($order->to_name) ?></span><br>
                                    التاريخ: <span class="signature-line"><?= date('Y-m-d', strtotime($order->created_at)) ?></span>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Footer -->
                <div class="form-footer">
                    <div class="footer-warning">
                        تنبيه مهم: يرجى مراجعة جميع البنود بدقة والتأكد من صحة البيانات قبل الاستلام
                    </div>
                    <div class="footer-note">
                        هذا المستند رسمي ويجب الاحتفاظ به للمراجعة والتدقيق
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <!-- END: Print Only Content (Form) -->
</body>

</html>