<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل الطلب</title>
    <link rel="stylesheet" href="<?= base_url('public/assets/css/order_details.css') ?>">
        <link rel="stylesheet" href="<?= base_url('public/assets/css/order_details.css') ?>">
        <link rel="stylesheet" href="<?= base_url('public/assets/css/print_order_details.css') ?>">
</head>
<body>
    <?= $this->include('layouts/header') ?>

    <!-- Regular screen content -->
    <div class="main-content no-print">
        <div class="header">
            <h1 class="page-title">إدارة المستودعات</h1>
            <div class="user-info" onclick="location.href='<?= base_url('UserInfo/getUserInfo') ?>'">
                <div class="user-avatar">
                    <?= strtoupper(substr(esc(session()->get('name')), 0, 1)) ?>
                </div>
                <span><?= esc(session()->get('name')) ?></span>
            </div>
        </div>

        <div class="content-area">
            <!-- Order Details Section -->
            <div class="order-details-section">
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
                        <div class="info-value"><?= esc($order->created_at) ?></div>
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
                                        <div class="detail-label">نوع الأصل</div>
                                        <div class="detail-value"><?= esc($item->assets_type) ?: '<span class="empty">غير محدد</span>' ?></div>
                                    </div>
                                    
                                    <div class="detail-item">
                                        <div class="detail-label">الغرفة</div>
                                        <div class="detail-value"><?= esc($item->room_code) ?: '<span class="empty">غير محدد</span>' ?></div>
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
                    <div class="no-items-msg">لا توجد عناصر مرتبطة بهذا الطلب.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

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

                        
                        <div class="form-title">طلب صرف مواد</div>
                    </div>
                    
                    <div style="width:60px;"></div> <!-- Spacer for balance -->
                </div>
                
                <!-- Header fields row 1 -->
                <div class="header-fields">
                    <div class="header-field">
                        <span class="field-label">الوزارة:</span>
                        <div class="field-line"></div>
                    </div>
                    <div class="header-field">
                        <span class="field-label">المستودع:</span>
                        <div class="field-line"></div>
                    </div>
                </div>
                
                <!-- Header fields row 2 -->
                <div class="header-fields">
                    <div class="header-field">
                        <span class="field-label">إدارة المستودعات:</span>
                        <div class="field-line"></div>
                    </div>
                    <div class="header-field">
                        <span class="field-label">الرقم الخاص:</span>
                        <div class="field-line"></div>
                    </div>
                </div>
                
                <!-- Header fields row 3 -->
                <div class="header-fields">
                    <div class="header-field">
                        <span class="field-label">الجهة الطالبة:</span>
                        <div class="field-line"></div>
                    </div>
                    <div class="header-field">
                        <span class="field-label">التاريخ:</span>
                        <div class="field-line"></div>
                    </div>
                </div>
                
            </div>

<table class="main-table">
    <thead>
        <tr>
            <th>رقم الطلب</th>
            <th>رقم الصنف</th>
            <th>اسم الصنف وتصنيفه</th>
            <th>نوع الصنف</th>
            <th>الوحدة</th>
            <th>الكمية المطلوبة</th>
            <th>الكمية المصروفة</th>
            <th>سعر الوحدة</th>
            <th>القيمة الكلية</th>
            <th>ملاحظات</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($items)): ?>
            <?php foreach ($items as $item): ?>
                <tr>
                    <!-- Order ID -->
                    <td><?= esc($item->order_id) ?></td>

                    <!-- Asset Number -->
                    <td><?= esc($item->asset_num) ?></td>

                    <!-- Item Name + Categories -->
                    <td>
                        <?= esc($item->item_name) ?>
                        <?php if (!empty($item->model_num)): ?>
                            <br><small>موديل: <?= esc($item->model_num) ?></small>
                        <?php endif; ?>
                        <?php if (!empty($item->brand)): ?>
                            <br><small>ماركة: <?= esc($item->brand) ?></small>
                        <?php endif; ?>
                        <?php if (!empty($item->minor_category_name) || !empty($item->major_category_name)): ?>
                            <br><small><?= esc($item->minor_category_name) ?> / <?= esc($item->major_category_name) ?></small>
                        <?php endif; ?>
                    </td>

                    <!-- Asset Type -->
                    <td><?= esc($item->assets_type) ?></td>

                    <!-- Unit -->
                    <td></td>

                    <!-- Quantity Requested -->
                    <td><?= esc($item->quantity) ?></td>

                    <!-- Quantity Issued -->
                    <td></td>

                    <!-- Unit Price -->
                    <td></td>

                    <!-- Total Price -->
                    <td></td>

                    <td><?= esc($item->note) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>

            <?php for ($i=1; $i<=6; $i++): ?>
                <tr class="empty-row">
                    <td></td><td></td><td></td><td></td>
                    <td></td><td></td><td></td><td></td>
                    <td></td><td></td><td></td>
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
                            <div class="signature-title-cell">رئيس الجهة الطالبة</div>
                            <div class="signature-fields">
                                الاسم: <span class="signature-line"></span><br>
                                التوقيع: <span class="signature-line"></span><br>
                                التاريخ: <span class="signature-line"></span>
                            </div>
                        </td>


                        <td>
                            <div class="signature-title-cell">إدارة المستودعات</div>
                            <div class="signature-fields">
                                الاسم: <span class="signature-line"></span><br>
                                التوقيع: <span class="signature-line"></span><br>
                                التاريخ: <span class="signature-line"></span>
                            </div>
                        </td>
                                                <td>
                            <div class="signature-title-cell">أمين المستودع</div>
                            <div class="signature-fields">
                                الاسم: <span class="signature-line"></span><br>
                                التوقيع: <span class="signature-line"></span><br>
                                التاريخ: <span class="signature-line"></span>
                            </div>
                        </td>
                                               <td>
                            <div class="signature-title-cell">المستلم</div>
                            <div class="signature-fields">
                                الاسم: <span class="signature-line"></span><br>
                                التوقيع: <span class="signature-line"></span><br>
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
                    تنبيه مهم: يرجى مراجعة جميع البنود بدقة والتأكد من صحة البيانات قبل التوقيع والاستلام
                </div>
                <div class="footer-note">
                    هذا المستند رسمي ويجب الاحتفاظ به للمراجعة والتدقيق
                </div>
            </div>
        </div>
    </div>

    <!-- Action buttons -->
    <div class="action-buttons-container no-print">
        <a href="<?= site_url('inventoryController') ?>" class="action-btn back-btn">
            <svg class="btn-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M15 18L9 12L15 6" stroke="currentColor"/>
            </svg>
            <span>العودة</span>
        </a>

        <button onclick="window.print()" class="action-btn print-btn">
            <svg class="btn-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6 9V4H18V9M6 14H18V20H6V14Z" stroke="currentColor"/>
            </svg>
            <span>طباعة</span>
        </button>
    </div>

</body>
</html>