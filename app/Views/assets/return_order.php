<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ترجيع الطلب</title>
    <link rel="stylesheet" href="<?= base_url('public/assets/css/order_details.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/assets/css/components/multi-select.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>
<body>
    <?= $this->include('layouts/header') ?>

    <div class="main-content no-print">
        <div class="header">
            <h1 class="page-title">ترجيع الطلب</h1>
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
            <div id="alertContainer"></div>

            <div class="bulk-actions" id="bulkActions">
                <div class="bulk-info">
                    <i class="fas fa-undo-alt"></i>
                    <span>تم اختيار</span>
                    <span class="selected-count" id="selectedCount">0</span>
                    <span>عنصر للترجيع</span>
                </div>
                <div class="bulk-buttons">
                    <button class="bulk-btn bulk-return-btn" onclick="submitReturn()">
                        <i class="fas fa-undo"></i>
                        ترجيع جماعي
                    </button>
                    <button class="bulk-btn bulk-cancel-btn" onclick="clearSelection()">
                        <i class="fas fa-times"></i>
                        إلغاء الاختيار
                    </button>
                </div>
            </div>

            <div class="items-section">
                <h3 class="section-title">ارجاع العهد</h3>

                <?php if (!empty($items)): ?>
                    <div class="select-all-container">
                        <input type="checkbox" class="master-checkbox" id="masterCheckbox" onchange="toggleAllSelection()">
                        <label for="masterCheckbox">تحديد الكل</label>
                    </div>

                    <div class="items-grid">
                        <?php foreach ($items as $item): ?>
                            <div class="item-card" data-item-id="<?= esc($item->id ?? $item->item_id) ?>">
                                <div class="item-checkbox-container">
                                    <input type="checkbox" class="custom-checkbox item-checkbox" onchange="updateSelection()">
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
                                         <div class="detail-value"><?= date('Y-m-d', strtotime($item->updated_at)) ?></div>
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
            </div>
            
            <div class="action-buttons-container no-print">
                <a href="<?= site_url('AssetsController') ?>" class="action-btn back-btn">
                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 18L9 12L15 6" stroke="currentColor"/>
                    </svg>
                    <span>العودة</span>
                </a>
            </div>
        </div>
    </div>

    <div class="delete-modal" id="deleteModal">
        <div class="delete-modal-content">
            <div class="delete-modal-title">
                <i class="fas fa-undo-alt"></i>
                تأكيد الترجيع
            </div>
            <div class="delete-modal-message" id="deleteMessage"></div>
            <div class="delete-modal-actions">
                <button class="confirm-btn confirm-cancel-btn" onclick="closeDeleteModal()">
                    إلغاء
                </button>
                <button class="confirm-btn confirm-delete-btn" onclick="confirmBulkReturnWithFiles()">
                    <i class="fas fa-undo"></i>
                    تأكيد الترجيع
                </button>
            </div>
        </div>
    </div>

    <!-- CRITICAL: Inject PHP values before loading external JS -->
    <script>
        // Configuration object for JavaScript to access PHP values
        window.appConfig = {
            baseUrl: "<?= base_url() ?>",
            csrfToken: "<?= csrf_hash() ?>",
            csrfName: "<?= csrf_token() ?>"
        };
    </script>
    
    <!-- Load external JavaScript file AFTER config -->
    <script src="<?= base_url('public/assets/JS/return.js') ?>"></script>

</body>
</html>