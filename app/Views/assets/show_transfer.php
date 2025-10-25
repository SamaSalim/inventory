<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل التحويل</title>
    <link rel="stylesheet" href="<?= base_url('public/assets/css/order_details.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/assets/css/components/print_form_style.css') ?>">
</head>
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
            <!-- Transfer Details Section -->
            <div class="order-details-section">
                <div class="order-header">
                    <h2 class="order-title">تفاصيل التحويل للطلب رقم <?= esc($transfer->order_id) ?></h2>
                    <div class="order-id-badge">#<?= esc($transfer->order_id) ?></div>
                </div>

                <div class="order-info-grid">
                    <div class="info-card">
                        <div class="info-label">من</div>
                        <div class="info-value"><?= esc($transfer->from_name) ?></div>
                    </div>
                    <div class="info-card">
                        <div class="info-label">إلى</div>
                        <div class="info-value"><?= esc($transfer->to_name) ?></div>
                    </div>
                    <div class="info-card">
                        <div class="info-label">عدد العناصر</div>
                        <div class="info-value"><?= esc($item_count) ?></div>
                    </div>
                    <div class="info-card">
                        <div class="info-label">تاريخ التحويل</div>
                        <div class="info-value">
                            <?= date('Y-m-d', strtotime($transfer->created_at)) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Section -->
            <div class="items-section">
                <h3 class="section-title">العناصر المحولة</h3>

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
                                        <div class="detail-label">نوع الأصل</div>
                                        <div class="detail-value"><?= esc($item->assets_type) ?: '<span class="empty">غير محدد</span>' ?></div>
                                    </div>
                                    
                                    <div class="detail-item">
                                        <div class="detail-label">الغرفة</div>
                                        <div class="detail-value"><?= esc($item->location_code) ?: '<span class="empty">غير محدد</span>' ?></div>
                                    </div>
                                    
                                    <div class="detail-item">
                                        <div class="detail-label">حالة الصنف</div>
                                        <div class="detail-value">
                                            <span class="status-badge status-active"><?= esc($item->usage_status_name) ?></span>
                                        </div>
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
                                        <div class="detail-value"><?= esc($item->created_at) ?></div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-label">آخر تحديث</div>
                                        <div class="detail-value"><?= esc($item->updated_at) ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-items-msg">لا توجد عناصر محولة.</div>
                <?php endif; ?>

                <!-- Action buttons -->
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

    <!-- Print version -->
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
                        <div class="form-title">نموذج نقل عهدة</div>
                    </div>
                    <div style="width:60px;"></div>
                </div>
                
                <!-- Header fields -->
                <div class="header-fields">
                    <div class="header-field">
                        <span class="field-label">المحول من:</span>
                        <div class="field-line"><?= esc($transfer->from_name) ?></div>
                    </div>
                    <div class="header-field">
                        <span class="field-label">التاريخ:</span>
                        <div class="field-line"><?= date('Y-m-d', strtotime($transfer->created_at)) ?></div>
                    </div>
                </div>
                
                <div class="header-fields">
                    <div class="header-field" style="border-left:none;">
                        <span class="field-label">المحول إلى:</span>
                        <div class="field-line"><?= esc($transfer->to_name) ?></div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <table class="main-table">
                <thead>
                    <tr>
                        <th>م</th>
                        <th colspan="2">رقم الصنف</th>
                        <th rowspan="2">اسم الصنف</th>
                        <th rowspan="2">الوحدة</th>
                        <th rowspan="2">الكمية</th>
                        <th colspan="2">حالة الصنف</th>
                        <th rowspan="2">ملاحظات</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th>KAMC</th>
                        <th>MOH</th>
                        <th>جديد</th>
                        <th>مستعمل</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($items)): ?>
                        <?php $counter = 1; ?>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= $counter++ ?></td>
                                <td><?= esc($item->asset_num) ?: '' ?></td>
                                <td><?= esc($item->old_asset_num) ?: '' ?></td>
                                <td>
                                    <?= esc($item->item_name) ?>
                                    <?php if (!empty($item->model_num)): ?>
                                        <br><small>موديل: <?= esc($item->model_num) ?></small>
                                    <?php endif; ?>
                                    <?php if (!empty($item->brand)): ?>
                                        <br><small>ماركة: <?= esc($item->brand) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td></td>
                                <td></td>
                                <td style="text-align: center;">
                                    <?php if (strtolower($item->usage_status_name) == 'جديد'): ?>
                                        ✓
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php if (strtolower($item->usage_status_name) == 'مستعمل' || strtolower($item->usage_status_name) == 'معاد الصرف'): ?>
                                        ✓
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($item->note) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?php for ($i=1; $i<=10; $i++): ?>
                            <tr class="empty-row">
                                <td><?= $i ?></td>
                                <td></td><td></td><td></td>
                                <td></td><td></td><td></td>
                                <td></td><td></td>
                            </tr>
                        <?php endfor; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Signature Section -->
            <div class="signature-section">
                <table class="signature-table" style="table-layout: fixed; width: 100%;">
                    <tr>
                        <td style="width: 20%; vertical-align: top; padding: 10px;">
                            <div style="font-weight: bold; text-align: center; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 8px;">المسلم</div>
                            <div style="line-height: 2;">
                                الاسم: ..................................<br>
                                التوقيع: ..................................<br>
                                الرقم الوظيفي: ..................................
                            </div>
                        </td>
                        <td style="width: 20%; vertical-align: top; padding: 10px;">
                            <div style="font-weight: bold; text-align: center; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 8px;">المستلم</div>
                            <div style="line-height: 2;">
                                الاسم: ..................................<br>
                                التوقيع: ..................................<br>
                                الرقم الوظيفي: ..................................
                            </div>
                        </td>
                        <td style="width: 20%; vertical-align: top; padding: 10px;">
                            <div style="font-weight: bold; text-align: center; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 8px;">رئيس الجهة المسلمة</div>
                            <div style="line-height: 2;">
                                الاسم: ..................................<br>
                                التوقيع: ..................................<br>
                                الرقم الوظيفي: ..................................
                            </div>
                        </td>
                        <td style="width: 20%; vertical-align: top; padding: 10px;">
                            <div style="font-weight: bold; text-align: center; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 8px;">رئيس الجهة المستلمة</div>
                            <div style="line-height: 2;">
                                الاسم: ..................................<br>
                                التوقيع: ..................................<br>
                                الرقم الوظيفي: ..................................
                            </div>
                        </td>
                        <td style="width: 20%; vertical-align: top; padding: 10px;">
                            <div style="font-weight: bold; text-align: center; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 8px;">إدارة مراقبة المخزون</div>
                            <div style="line-height: 2;">
                                الاسم: ..................................<br>
                                التوقيع: ..................................<br>
                                الرقم الوظيفي: ..................................
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Footer -->
            <div class="form-footer">
                <div class="footer-note">
                   تمت معاينة (الصنف/الأصناف ) الموضحة أعلاه من قبل المسلم و المستلم و بالتوقيع على هذا المحضر أخلى الطرف الأول مسؤوليته من تلك الصنف / الأصناف ، و أصبح الطرف الثاني مسئولا عنها و على هذا جرى التوقيع و المصادقة
                </div>
            </div>
        </div>
    </div>

</body>
</html>