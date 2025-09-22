<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل الطلب</title>
    <link rel="stylesheet" href="<?= base_url('public/assets/css/order_details.css') ?>">

</head>
<body>
    <?= $this->include('layouts/header') ?>

    <div class="main-content">
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
<div class="action-buttons-container">
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