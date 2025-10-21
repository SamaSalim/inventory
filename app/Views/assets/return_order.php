<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ترجيع الطلب</title>
    <link rel="stylesheet" href="<?= base_url('public/assets/css/order_details.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/assets/css/components/multi-select.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .action-checkbox-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin: 15px 0;
            padding: 15px;
            background: linear-gradient(135deg, #f8fdff, #e8f4f8);
            border-radius: 10px;
            border: 2px solid #3ac0c3;
        }
        
        .action-checkbox-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            padding: 12px 20px;
            border-radius: 8px;
            transition: all 0.3s;
            background: white;
            border: 2px solid #e0e6ed;
            user-select: none;
        }
        
        .action-checkbox-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(58, 192, 195, 0.2);
            border-color: #3ac0c3;
        }
        
        .action-checkbox-item.selected {
            background: linear-gradient(135deg, #3ac0c3, #2aa8ab);
            border-color: #2aa8ab;
            color: white;
        }
        
        .action-checkbox-item input[type="checkbox"] {
            display: none;
        }
        
        .action-checkbox-item .icon {
            font-size: 24px;
            transition: transform 0.3s;
            pointer-events: none;
        }
        
        .action-checkbox-item.selected .icon {
            transform: scale(1.2);
        }
        
        .action-checkbox-item .label {
            font-size: 13px;
            font-weight: 600;
            pointer-events: none;
        }
        
        .form-preview-container {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e0e6ed;
        }
        
        .form-preview-title {
            font-size: 14px;
            font-weight: 600;
            color: #057590;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fadeOut { from { opacity: 1; } to { opacity: 0; } }
    </style>
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

<script>
let selectedItems = [];
let uploadedFiles = {};
let itemActions = {};
let globalReturnReasons = {};

function updateSelection() {
    selectedItems = [];
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    
    checkboxes.forEach(checkbox => {
        const itemCard = checkbox.closest('.item-card');
        const itemId = itemCard.getAttribute('data-item-id');
        const itemName = itemCard.querySelector('.item-name').textContent;
        const categoryBadge = itemCard.querySelector('.category-badge');
        const category = categoryBadge ? categoryBadge.textContent : 'غير محدد';
        const detailValues = itemCard.querySelectorAll('.detail-value');
        const model = detailValues[1] ? detailValues[1].textContent.trim() : 'غير محدد';
        const serialNum = detailValues[2] ? detailValues[2].textContent.trim() : 'غير محدد';
        const assetNum = detailValues[3] ? detailValues[3].textContent.trim() : 'غير محدد';
        const oldAssetNum = detailValues[4] ? detailValues[4].textContent.trim() : 'غير محدد';
        const brand = detailValues[5] ? detailValues[5].textContent.trim() : 'غير محدد';
        const assetType = detailValues[6] ? detailValues[6].textContent.trim() : 'غير محدد';
        
        const minorCategoryName = category.split('/')[1]?.trim() || '';
        
        selectedItems.push({
            id: itemId,
            name: itemName,
            category: category,
            minorCategory: minorCategoryName,
            model: model,
            serialNum: serialNum,
            assetNum: assetNum,
            oldAssetNum: oldAssetNum,
            brand: brand,
            assetType: assetType
        });
        
        itemCard.classList.add('selected');
    });
    
    document.querySelectorAll('.item-card').forEach(card => {
        const checkbox = card.querySelector('.item-checkbox');
        if (!checkbox.checked) {
            card.classList.remove('selected');
        }
    });
    
    updateBulkActionsBar();
    updateMasterCheckbox();
}

function toggleAllSelection() {
    const masterCheckbox = document.getElementById('masterCheckbox');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    
    itemCheckboxes.forEach(checkbox => {
        checkbox.checked = masterCheckbox.checked;
    });
    
    updateSelection();
}

function updateMasterCheckbox() {
    const masterCheckbox = document.getElementById('masterCheckbox');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const checkedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
    
    if (itemCheckboxes.length === 0) {
        masterCheckbox.checked = false;
        masterCheckbox.indeterminate = false;
    } else if (checkedCheckboxes.length === 0) {
        masterCheckbox.checked = false;
        masterCheckbox.indeterminate = false;
    } else if (checkedCheckboxes.length === itemCheckboxes.length) {
        masterCheckbox.checked = true;
        masterCheckbox.indeterminate = false;
    } else {
        masterCheckbox.checked = false;
        masterCheckbox.indeterminate = true;
    }
}

function updateBulkActionsBar() {
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');
    const popupExists = document.getElementById('selectedItemsPopup');
    
    if (selectedItems.length > 0 && !popupExists) {
        bulkActions.classList.add('show');
        bulkActions.style.position = 'relative';
        bulkActions.style.bottom = 'auto';
        bulkActions.style.left = 'auto';
        bulkActions.style.right = 'auto';
        bulkActions.style.transform = 'none';
        selectedCount.textContent = selectedItems.length;
    } else {
        bulkActions.classList.remove('show');
    }
}

function clearSelection() {
    document.querySelectorAll('.item-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.querySelectorAll('.item-card').forEach(card => {
        card.classList.remove('selected');
    });
    selectedItems = [];
    uploadedFiles = {};
    itemActions = {};
    globalReturnReasons = {};
    updateBulkActionsBar();
    const masterCheckbox = document.getElementById('masterCheckbox');
    if (masterCheckbox) {
        masterCheckbox.checked = false;
        masterCheckbox.indeterminate = false;
    }
}

function handleFileUpload(assetNum, files, minorCategory) {
    if (!uploadedFiles[assetNum]) {
        uploadedFiles[assetNum] = [];
    }
    
    const isIT = minorCategory === 'IT';
    
    Array.from(files).forEach(file => {
        if (file.size > 5 * 1024 * 1024) {
            showAlert('warning', `الملف ${file.name} أكبر من 5 ميجابايت`);
            return;
        }
        
        if (!isIT) {
            const allowedImageTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!allowedImageTypes.includes(file.type)) {
                showAlert('warning', `الملف ${file.name} ليس صورة. يرجى رفع صور فقط`);
                return;
            }
        }
        
        uploadedFiles[assetNum].push(file);
    });
    
    updateFileList(assetNum, minorCategory);
}

function updateFileList(assetNum, minorCategory) {
    const fileList = document.getElementById(`fileList_${assetNum}`);
    if (!fileList) return;
    
    const files = uploadedFiles[assetNum] || [];
    const isIT = minorCategory === 'IT';
    
    if (files.length === 0) {
        const emptyMessage = isIT ? 'لم يتم رفع أي ملفات' : 'لم يتم رفع أي صور';
        fileList.innerHTML = `<div style="color: #999; font-size: 13px; padding: 10px; text-align: center;">${emptyMessage}</div>`;
        return;
    }
    
    fileList.innerHTML = files.map((file, index) => {
        const icon = file.type.startsWith('image/') ? 'fa-image' : 'fa-file';
        const iconColor = isIT ? '#3ac0c3' : '#ff6b6b';
        return `
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; background: #f8f9fa; border-radius: 6px; margin-bottom: 6px; border: 1px solid #e0e6ed;">
            <div style="display: flex; align-items: center; gap: 8px; flex: 1; overflow: hidden;">
                <i class="fas ${icon}" style="color: ${iconColor};"></i>
                <span style="font-size: 13px; color: #333; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${file.name}</span>
                <span style="font-size: 11px; color: #999;">(${(file.size / 1024).toFixed(1)} KB)</span>
            </div>
            <button onclick="removeFile('${assetNum}', ${index})" style="background: #e74c3c; color: white; border: none; border-radius: 50%; width: 24px; height: 24px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">✕</button>
        </div>
    `}).join('');
}

function removeFile(assetNum, fileIndex) {
    if (uploadedFiles[assetNum]) {
        uploadedFiles[assetNum].splice(fileIndex, 1);
        const item = selectedItems.find(i => i.assetNum === assetNum);
        updateFileList(assetNum, item?.minorCategory || '');
    }
}

function toggleAction(assetNum, action, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    if (!itemActions[assetNum]) {
        itemActions[assetNum] = {};
    }
    
    ['fix', 'sell', 'destroy'].forEach(act => {
        itemActions[assetNum][act] = false;
        const otherCheckbox = document.getElementById(`action_${act}_${assetNum}`);
        const otherLabel = otherCheckbox?.closest('.action-checkbox-item');
        if (otherCheckbox) otherCheckbox.checked = false;
        if (otherLabel) otherLabel.classList.remove('selected');
    });
    
    itemActions[assetNum][action] = true;
    
    const checkbox = document.getElementById(`action_${action}_${assetNum}`);
    const label = checkbox.closest('.action-checkbox-item');
    
    label.classList.add('selected');
    checkbox.checked = true;
    
    updateFormPreview(assetNum);
}

function toggleGlobalReason(reason, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    Object.keys(globalReturnReasons).forEach(key => {
        globalReturnReasons[key] = false;
        const otherCheckbox = document.getElementById(`reason_${key}`);
        const otherLabel = otherCheckbox?.closest('.action-checkbox-item');
        if (otherCheckbox) otherCheckbox.checked = false;
        if (otherLabel) otherLabel.classList.remove('selected');
    });
    
    globalReturnReasons[reason] = true;
    
    const checkbox = document.getElementById(`reason_${reason}`);
    const label = checkbox.closest('.action-checkbox-item');
    
    label.classList.add('selected');
    checkbox.checked = true;
}

function updateFormPreview(assetNum) {
    const preview = document.getElementById(`formPreview_${assetNum}`);
    if (!preview) return;
    
    const item = selectedItems.find(i => i.assetNum === assetNum);
    if (!item) return;
    
    const actions = itemActions[assetNum] || {};
    const selectedActions = Object.keys(actions).filter(key => actions[key]);
    
    if (selectedActions.length === 0) {
        preview.innerHTML = '<div style="color: #999; font-style: italic;">لم يتم تحديد أي إجراء</div>';
        return;
    }
    
    const actionLabels = {
        fix: 'للإصلاح',
        sell: 'للبيع',
        destroy: 'للإتلاف'
    };
    
    preview.innerHTML = `
        <div style="background: white; padding: 12px; border-radius: 6px; border: 1px solid #3ac0c3;">
            <strong style="color: #057590;">الإجراءات المحددة:</strong>
            <div style="margin-top: 8px; display: flex; gap: 8px; flex-wrap: wrap;">
                ${selectedActions.map(action => `
                    <span style="background: #3ac0c3; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px;">
                        ${actionLabels[action]}
                    </span>
                `).join('')}
            </div>
            <div style="margin-top: 12px; font-size: 12px; color: #666;">
                <i class="fas fa-info-circle" style="color: #3ac0c3;"></i>
                سيتم إنشاء النموذج تلقائياً مع بيانات الصنف
            </div>
        </div>
    `;
}

function getFileUploadSection(item) {
    const isIT = item.minorCategory === 'IT';
    
    if (isIT) {
        return `
            <div style="margin-top: 15px; margin-bottom: 15px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #057590; margin-bottom: 8px;">
                    <i class="fas fa-file-alt" style="margin-left: 5px;"></i>
                    نموذج الترجيع (سيتم إنشاؤه تلقائياً):
                </label>
                <div style="border: 2px dashed #e0e6ed; border-radius: 8px; padding: 15px; background: #f8fdff; transition: border-color 0.2s;">
                    <div style="margin-bottom: 15px;">
                        <strong style="color: #057590; font-size: 14px; display: block; margin-bottom: 10px;">
                            <i class="fas fa-tasks" style="margin-left: 5px;"></i>
                            حدد توصياتك:
                        </strong>
                        <div class="action-checkbox-group">
                            <label class="action-checkbox-item" onclick="toggleAction('${item.assetNum}', 'fix', event)">
                                <input type="radio" name="action_${item.assetNum}" id="action_fix_${item.assetNum}" style="display: none;">
                                <div class="label">للإصلاح</div>
                            </label>
                            <label class="action-checkbox-item" onclick="toggleAction('${item.assetNum}', 'sell', event)">
                                <input type="radio" name="action_${item.assetNum}" id="action_sell_${item.assetNum}" style="display: none;">
                                <div class="label">للبيع</div>
                            </label>
                            <label class="action-checkbox-item" onclick="toggleAction('${item.assetNum}', 'destroy', event)">
                                <input type="radio" name="action_${item.assetNum}" id="action_destroy_${item.assetNum}" style="display: none;">
                                <div class="label">للإتلاف</div>
                            </label>
                        </div>
                    </div>
                    <div class="form-preview-container">
                        <div class="form-preview-title">
                            <i class="fas fa-check-circle"></i>
                            الإجراءات المحددة:
                        </div>
                        <div class="form-preview-content" id="formPreview_${item.assetNum}">
                            <div style="color: #999; font-style: italic;">لم يتم تحديد أي إجراء</div>
                        </div>
                    </div>
                    
                    <div style="font-size: 11px; color: #3ac0c3; text-align: center; margin-top: 12px;">
                        <i class="fas fa-magic"></i>
                        سيتم إنشاء النموذج تلقائياً بجميع بيانات الصنف عند الترجيع
                    </div>
                </div>
            </div>
        `;
    } else {
        return `
            <div style="margin-top: 15px; margin-bottom: 15px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #057590; margin-bottom: 8px;">
                    <i class="fas fa-image" style="margin-left: 5px;"></i>
                    الصور المرفقة:
                </label>
                <div style="border: 2px dashed #e0e6ed; border-radius: 8px; padding: 15px; background: #f8fdff; transition: border-color 0.2s;">
                    <input 
                        type="file" 
                        id="fileInput_${item.assetNum}"
                        data-asset-num="${item.assetNum}"
                        data-minor-category="${item.minorCategory}"
                        multiple
                        accept="image/*"
                        onchange="handleFileUpload(this.dataset.assetNum, this.files, this.dataset.minorCategory)"
                        style="display: none;"
                    />
                    <button 
                        onclick="document.getElementById('fileInput_${item.assetNum}').click()"
                        style="background: linear-gradient(135deg, #3ac0c3, #2aa8ab); color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 500; width: 100%; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 8px;"
                        onmouseover="this.style.background='linear-gradient(135deg, #2aa8ab, #259a9d)'"
                        onmouseout="this.style.background='linear-gradient(135deg, #3ac0c3, #2aa8ab)'"
                    >
                        <i class="fas fa-camera"></i>
                        اختر الصور
                    </button>
                    <div style="font-size: 11px; color: #999; text-align: center; margin-top: 8px;">
                        الحد الأقصى: 5 ميجابايت لكل صورة | صور فقط (JPG, PNG)
                    </div>
                    <div id="fileList_${item.assetNum}" style="margin-top: 10px;">
                        <div style="color: #999; font-size: 13px; padding: 10px; text-align: center;">لم يتم رفع أي صور</div>
                    </div>
                </div>
            </div>
        `;
    }
}

function printAllForms() {
    const itItems = selectedItems.filter(item => item.minorCategory === 'IT');
    
    if (itItems.length === 0) {
        showAlert('warning', 'لا توجد عناصر IT لمعاينة نماذج');
        return;
    }
    
    const itemsWithoutActions = itItems.filter(item => {
        const actions = itemActions[item.assetNum] || {};
        return Object.keys(actions).filter(k => actions[k]).length === 0;
    });
    
    if (itemsWithoutActions.length > 0) {
        showAlert('warning', `يجب تحديد إجراء لجميع عناصر IT (${itemsWithoutActions.length} عنصر بدون إجراء)`);
        return;
    }
    
    const hasAnyReasonSelected = Object.keys(globalReturnReasons).some(key => globalReturnReasons[key]);
    if (!hasAnyReasonSelected) {
        showAlert('warning', 'يجب تحديد سبب واحد للإرجاع');
        return;
    }
    
    const printBtn = event.target;
    const originalBtnContent = printBtn.innerHTML;
    printBtn.disabled = true;
    printBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحميل...';
    
    const itItemsData = itItems.map(item => {
        const itemActionsData = itemActions[item.assetNum] || {};
        const commentElement = document.getElementById(`comment_${item.assetNum}`);
        const itemNotes = commentElement ? commentElement.value.trim() : '';
        
        return {
            assetNum: item.assetNum,
            name: item.name,
            category: item.category,
            assetType: item.assetType || 'غير محدد',
            notes: itemNotes,
            actions: {
                fix: itemActionsData.fix ? '1' : '0',
                sell: itemActionsData.sell ? '1' : '0',
                destroy: itemActionsData.destroy ? '1' : '0'
            }
        };
    });
    
    const formData = new FormData();
    formData.append('asset_num', itItems[0].assetNum);
    formData.append('item_data[name]', itItems[0].name);
    formData.append('item_data[serial_num]', itItems[0].serialNum || 'غير محدد');
    formData.append('item_data[model]', itItems[0].model || 'غير محدد');
    formData.append('item_data[brand]', itItems[0].brand || 'غير محدد');
    formData.append('item_data[old_asset_num]', itItems[0].oldAssetNum || 'غير محدد');
    formData.append('item_data[asset_type]', itItems[0].assetType || 'غير محدد');
    formData.append('item_data[category]', itItems[0].category);
    
    const firstActions = itemActions[itItems[0].assetNum] || {};
    formData.append('actions[fix]', firstActions.fix ? '1' : '0');
    formData.append('actions[sell]', firstActions.sell ? '1' : '0');
    formData.append('actions[destroy]', firstActions.destroy ? '1' : '0');
    
    formData.append('reasons[purpose_end]', globalReturnReasons.purpose_end ? '1' : '0');
    formData.append('reasons[excess]', globalReturnReasons.excess ? '1' : '0');
    formData.append('reasons[unfit]', globalReturnReasons.unfit ? '1' : '0');
    formData.append('reasons[damaged]', globalReturnReasons.damaged ? '1' : '0');
    
    itItemsData.forEach((item, index) => {
        formData.append(`all_items[${index}][assetNum]`, item.assetNum);
        formData.append(`all_items[${index}][name]`, item.name);
        formData.append(`all_items[${index}][category]`, item.category);
        formData.append(`all_items[${index}][assetType]`, item.assetType);
        formData.append(`all_items[${index}][notes]`, item.notes);
        formData.append(`all_items[${index}][actions][fix]`, item.actions.fix);
        formData.append(`all_items[${index}][actions][sell]`, item.actions.sell);
        formData.append(`all_items[${index}][actions][destroy]`, item.actions.destroy);
    });
    
    const printFrame = document.createElement('iframe');
    printFrame.style.position = 'fixed';
    printFrame.style.right = '0';
    printFrame.style.bottom = '0';
    printFrame.style.width = '0';
    printFrame.style.height = '0';
    printFrame.style.border = 'none';
    printFrame.style.visibility = 'hidden';
    document.body.appendChild(printFrame);
    
    fetch('<?= base_url("return/attachment/printForm") ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            return response.text().then(html => ({ success: true, html: html }));
        }
    })
    .then(data => {
        if (data.success && data.html) {
            const iframeDoc = printFrame.contentWindow.document;
            iframeDoc.open();
            iframeDoc.write(data.html);
            iframeDoc.close();
            
            printFrame.onload = function() {
                try {
                    printFrame.contentWindow.focus();
                    printFrame.contentWindow.print();
                    
                    setTimeout(() => {
                        if (printFrame && printFrame.parentNode) {
                            document.body.removeChild(printFrame);
                        }
                    }, 1000);
                } catch (e) {
                    console.error('Print error:', e);
                    showAlert('error', 'حدث خطأ أثناء الطباعة');
                    if (printFrame && printFrame.parentNode) {
                        document.body.removeChild(printFrame);
                    }
                }
            };
        } else {
            showAlert('error', data.message || 'فشل إنشاء النموذج');
            if (printFrame && printFrame.parentNode) {
                document.body.removeChild(printFrame);
            }
        }
    })
    .catch(error => {
        console.error('Error details:', error);
        showAlert('error', 'حدث خطأ في الاتصال بالخادم: ' + error.message);
        if (printFrame && printFrame.parentNode) {
            document.body.removeChild(printFrame);
        }
    })
    .finally(() => {
        printBtn.disabled = false;
        printBtn.innerHTML = originalBtnContent;
    });
}

function showSelectedItemsPopup() {
    if (selectedItems.length === 0) {
        showAlert('warning', 'لم يتم تحديد أي عناصر للترجيع');
        return;
    }
    
    const bulkActions = document.getElementById('bulkActions');
    bulkActions.classList.remove('show');
    
    const existingPopup = document.getElementById('selectedItemsPopup');
    if (existingPopup) {
        existingPopup.remove();
    }
    
    const popup = document.createElement('div');
    popup.id = 'selectedItemsPopup';
    popup.className = 'selected-items-popup show';
    popup.style.cssText = 'position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); max-width: 800px; width: 90%; max-height: 85vh; overflow: hidden; z-index: 1001; animation: popupSlideIn 0.3s ease; display: flex; flex-direction: column;';
    
    let itemsHTML = '';
    selectedItems.forEach((item, index) => {
        const categoryBadgeColor = item.minorCategory === 'IT' ? '#3a61c3ff' : '#ff6b6b';
        itemsHTML += `
            <div class="popup-item" style="padding: 15px; border-bottom: 1px solid #e0e6ed; transition: background 0.2s;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                            <div style="font-weight: bold; color: #057590; font-size: 16px;">
                                ${index + 1}. ${item.name}
                            </div>
                            <span style="background: ${categoryBadgeColor}; color: white; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600;">${item.minorCategory}</span>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; font-size: 14px; color: #555;">
                            <div><span style="color: #888;">التصنيف:</span> ${item.category}</div>
                            <div><span style="color: #888;">الموديل:</span> ${item.model}</div>
                            <div><span style="color: #888;">الرقم التسلسلي:</span> ${item.serialNum}</div>
                            <div><span style="color: #888;">رقم الأصل:</span> ${item.assetNum}</div>
                        </div>
                    </div>
                    <button onclick="removeItemFromSelection('${item.id}')" style="background: #95a5a6; color: white; border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-right: 10px; transition: background 0.2s;" onmouseover="this.style.background='#7f8c8d'" onmouseout="this.style.background='#95a5a6'">✕</button>
                </div>
                
                ${getFileUploadSection(item)}
                
                <div style="margin-top: 15px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #057590; margin-bottom: 6px;">
                        <i class="fas fa-comment-dots" style="margin-left: 5px;"></i>
                        ملاحظات الترجيع:
                    </label>
                    <textarea 
                        id="comment_${item.assetNum}"
                        placeholder="أضف ملاحظة حول حالة الصنف أو سبب الترجيع..."
                        style="width: 100%; min-height: 70px; padding: 10px; border: 2px solid #e8f4f8; border-radius: 8px; font-size: 14px; font-family: inherit; resize: vertical; transition: border-color 0.2s; box-sizing: border-box; background: linear-gradient(135deg, #ffffff, #f8fdff);"
                        onfocus="this.style.borderColor='#3ac0c3'"
                        onblur="this.style.borderColor='#e8f4f8'"
                    ></textarea>
                </div>
            </div>
        `;
    });
    
    const hasITItems = selectedItems.some(item => item.minorCategory === 'IT');
    
    const returnReasonsHTML = hasITItems ? `
        <div style="padding: 20px; background: linear-gradient(135deg, #f8fdff, #e8f4f8); border-bottom: 2px solid #3ac0c3;">
            <h4 style="margin: 0 0 15px 0; color: #057590; font-size: 16px; text-align: center;">
                <i class="fas fa-clipboard-list" style="margin-left: 5px;"></i>
                أسباب الإرجاع
            </h4>
            <div class="action-checkbox-group" style="margin: 0;">
                <label class="action-checkbox-item"onclick="toggleGlobalReason('purpose_end', event)">
                    <input type="radio" name="global_reason" id="reason_purpose_end" style="display: none;">
                    <div class="label">انتهاء الغرض</div>
                </label>
                <label class="action-checkbox-item" onclick="toggleGlobalReason('excess', event)">
                    <input type="radio" name="global_reason" id="reason_excess" style="display: none;">
                    <div class="label">فائض</div>
                </label>
                <label class="action-checkbox-item" onclick="toggleGlobalReason('unfit', event)">
                    <input type="radio" name="global_reason" id="reason_unfit" style="display: none;">
                    <div class="label">عدم الصلاحية</div>
                </label>
                <label class="action-checkbox-item" onclick="toggleGlobalReason('damaged', event)">
                    <input type="radio" name="global_reason" id="reason_damaged" style="display: none;">
                    <div class="label">تالف</div>
                </label>
            </div>
        </div>
    ` : '';
    
    const printButtonSection = hasITItems ? `
        <div style="padding: 15px 20px; background: #fffbf0; border-top: 2px solid #f4d03f; border-bottom: 1px solid #e0e6ed;">
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 15px;">
                <div style="flex: 1;">
                    <div style="font-size: 13px; color: #7d6608; line-height: 1.5;">
                        <i class="fas fa-info-circle" style="margin-left: 5px; color: #f39c12;"></i>
                        <strong>معاينة التقرير المُنشأ تلقائياً قبل إرساله لموظفي مستودع الإرجاع</strong>
                    </div>
                </div>
                <button onclick="printAllForms()" style="padding: 10px 20px; background: linear-gradient(135deg, #5f97d6, #3a7bc8); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s; box-shadow: 0 3px 8px rgba(58, 123, 200, 0.3); white-space: nowrap; flex-shrink: 0;" onmouseover="this.style.background='linear-gradient(135deg, #3a7bc8, #2563a8)'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 5px 12px rgba(58, 123, 200, 0.4)'" onmouseout="this.style.background='linear-gradient(135deg, #5f97d6, #3a7bc8)'; this.style.transform='translateY(0)'; this.style.boxShadow='0 3px 8px rgba(58, 123, 200, 0.3)'">
                    <i class="fas fa-eye" style="margin-left: 5px;"></i>
                    عرض التقرير
                </button>
            </div>
        </div>
    ` : '';
    
    popup.innerHTML = `
        <div style="padding: 20px; background: linear-gradient(135deg, #057590, #3ac0c3); color: white; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 20px;">
                <i class="fas fa-undo-alt" style="margin-left: 8px;"></i>
                العناصر المحددة للترجيع (${selectedItems.length})
            </h3>
            <button onclick="closeSelectedItemsPopup()" style="background: rgba(255,255,255,0.2); color: white; border: none; border-radius: 50%; width: 35px; height: 35px; cursor: pointer; font-size: 20px; display: flex; align-items: center; justify-content: center; transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">✕</button>
        </div>
        
        ${returnReasonsHTML}
        
        <div style="flex: 1; overflow-y: auto; min-height: 0;">
            ${itemsHTML}
        </div>
        
        ${printButtonSection}
        
        <div style="padding: 15px 20px; background: #f8f9fa; border-top: 2px solid #e0e6ed; display: flex; justify-content: space-between; gap: 10px; flex-shrink: 0;">
            <button onclick="handleCancelSelection()" style="flex: 1; padding: 12px 20px; background: #95a5a6; color: white; border: none; border-radius: 20px; cursor: pointer; font-weight: bold; font-size: 14px; transition: all 0.2s;" onmouseover="this.style.background='#7f8c8d'; this.style.transform='translateY(-1px)'" onmouseout="this.style.background='#95a5a6'; this.style.transform='translateY(0)'">
                <i class="fas fa-times" style="margin-left: 5px;"></i>
                إلغاء التحديد
            </button>
            <button onclick="submitReturn()" style="flex: 1; padding: 12px 20px; background: linear-gradient(135deg, #3ac0c3, #2aa8ab); color: white; border: none; border-radius: 20px; cursor: pointer; font-weight: bold; font-size: 14px; transition: all 0.2s; box-shadow: 0 2px 8px rgba(58, 192, 195, 0.3);" onmouseover="this.style.background='linear-gradient(135deg, #2aa8ab, #259a9d)'; this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(58, 192, 195, 0.4)'" onmouseout="this.style.background='linear-gradient(135deg, #3ac0c3, #2aa8ab)'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(58, 192, 195, 0.3)'">
                <i class="fas fa-undo" style="margin-left: 5px;"></i>
                تأكيد الترجيع
            </button>
        </div>
    `;
    
    const backdrop = document.createElement('div');
    backdrop.id = 'popupBackdrop';
    backdrop.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; animation: fadeIn 0.3s ease;';
    backdrop.onclick = closeSelectedItemsPopup;
    
    if (!document.getElementById('popupAnimationStyles')) {
        const style = document.createElement('style');
        style.id = 'popupAnimationStyles';
        style.textContent = `
            @keyframes popupSlideIn { from { transform: translate(-50%, -60%); opacity: 0; } to { transform: translate(-50%, -50%); opacity: 1; } }
            @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
            .popup-item:hover { background: #f8f9fa !important; }
        `;
        document.head.appendChild(style);
    }
    
    document.body.appendChild(backdrop);
    document.body.appendChild(popup);
}

function handleCancelSelection() {
    clearSelection();
    closeSelectedItemsPopup();
}

function closeSelectedItemsPopup() {
    const popup = document.getElementById('selectedItemsPopup');
    const backdrop = document.getElementById('popupBackdrop');
    
    if (popup) popup.remove();
    if (backdrop) backdrop.remove();
    
    updateBulkActionsBar();
}

function removeItemFromSelection(itemId) {
    const checkbox = document.querySelector(`.item-card[data-item-id="${itemId}"] .item-checkbox`);
    if (checkbox) {
        checkbox.checked = false;
        
        const item = selectedItems.find(i => i.id === itemId);
        if (item && item.assetNum) {
            delete uploadedFiles[item.assetNum];
            delete itemActions[item.assetNum];
        }
        
        updateSelection();
        
        if (selectedItems.length === 0) {
            closeSelectedItemsPopup();
        } else {
            closeSelectedItemsPopup();
            setTimeout(() => showSelectedItemsPopup(), 100);
        }
    }
}

function submitReturn() {
    if (selectedItems.length === 0) {
        showAlert('warning', 'لم يتم تحديد أي عناصر للترجيع');
        return;
    }
    
    const returnData = selectedItems.map(item => {
        const commentElement = document.getElementById(`comment_${item.assetNum}`);
        const isIT = item.minorCategory === 'IT';
        
        return {
            id: item.id,
            name: item.name,
            assetNum: item.assetNum,
            serialNum: item.serialNum,
            model: item.model,
            brand: item.brand,
            oldAssetNum: item.oldAssetNum,
            assetType: item.assetType,
            category: item.category,
            minorCategory: item.minorCategory,
            comment: commentElement ? commentElement.value.trim() : '',
            files: uploadedFiles[item.assetNum] || [],
            actions: isIT ? (itemActions[item.assetNum] || {}) : null,
            generateForm: isIT
        };
    });
    
    const popupExists = document.getElementById('selectedItemsPopup');
    
    if (!popupExists) {
        showSelectedItemsPopup();
        return;
    }
    
    const itItemsWithoutActions = returnData.filter(item => 
        item.generateForm && (!item.actions || Object.keys(item.actions).filter(k => item.actions[k]).length === 0)
    );
    
    if (itItemsWithoutActions.length > 0) {
        showAlert('warning', `يجب تحديد إجراء واحد لعناصر IT (${itItemsWithoutActions.length} عنصر)`);
        return;
    }
    
    const hasAnyITItem = returnData.some(item => item.generateForm);
    const hasAnyReasonSelected = Object.keys(globalReturnReasons).some(key => globalReturnReasons[key]);
    
    if (hasAnyITItem && !hasAnyReasonSelected) {
        showAlert('warning', 'يجب تحديد سبب واحد للإرجاع');
        return;
    }
    
    const missingComments = returnData.filter(item => !item.comment);
    
    if (missingComments.length > 0) {
        const confirmProceed = confirm(`يوجد ${missingComments.length} عنصر بدون ملاحظات.\nهل تريد المتابعة؟`);
        if (!confirmProceed) return;
    }
    
    showReturnConfirmation(returnData);
}

function showReturnConfirmation(returnData) {
    const modal = document.getElementById('deleteModal');
    const message = document.getElementById('deleteMessage');
    
    let itemsList = returnData.map((item, index) => {
        let attachmentInfo = '';
        
        if (item.generateForm) {
            const actions = item.actions || {};
            const selectedActions = Object.keys(actions).filter(k => actions[k]);
            const actionLabels = { fix: 'للإصلاح', sell: 'للبيع', destroy: 'للإتلاف' };
            const actionsText = selectedActions.map(a => actionLabels[a]).join(', ');
            attachmentInfo = `<br><small style="color: #3ac0c3;">📄 نموذج تلقائي: ${actionsText}</small>`;
        } else if (item.files.length > 0) {
            attachmentInfo = `<br><small style="color: #ff6b6b;">📷 ${item.files.length} صورة</small>`;
        }
        
        return `<div style="margin: 8px 0; padding: 8px; background: #f8f9fa; border-radius: 6px;">
            <strong>${index + 1}. ${item.name}</strong>
            <br><small style="color: #888;">رقم الأصل: ${item.assetNum}</small>
            ${item.comment ? `<br><small style="color: #666;">📝 ${item.comment}</small>` : '<br><small style="color: #999;">بدون ملاحظات</small>'}
            ${attachmentInfo}
        </div>`;
    }).join('');
    
    message.innerHTML = `
        <div style="max-height: 300px; overflow-y: auto; margin: 10px 0;">
            ${itemsList}
        </div>
        <strong style="color: #057590; margin-top: 15px; display: block;">
            هل أنت متأكد من ترجيع هذه العناصر؟
        </strong>
    `;
    
    modal.style.display = 'flex';
    setTimeout(() => modal.classList.add('show'), 10);
    
    window.tempReturnData = returnData;
    
    closeSelectedItemsPopup();
}

function confirmBulkReturnWithFiles() {
    const returnData = window.tempReturnData;
    
    if (!returnData) {
        showAlert('warning', 'حدث خطأ في البيانات');
        return;
    }
    
    const confirmBtn = document.querySelector('.confirm-delete-btn');
    const originalText = confirmBtn.innerHTML;
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الترجيع...';
    
    const formData = new FormData();
    
    formData.append('reasons[purpose_end]', globalReturnReasons.purpose_end ? '1' : '0');
    formData.append('reasons[excess]', globalReturnReasons.excess ? '1' : '0');
    formData.append('reasons[unfit]', globalReturnReasons.unfit ? '1' : '0');
    formData.append('reasons[damaged]', globalReturnReasons.damaged ? '1' : '0');
    
    returnData.forEach((item, index) => {
        formData.append(`asset_nums[${index}]`, item.assetNum);
        formData.append(`comments[${item.assetNum}]`, item.comment || '');
        
        if (item.generateForm) {
            formData.append(`generate_form[${item.assetNum}]`, '1');
            formData.append(`item_data[${item.assetNum}][name]`, item.name);
            formData.append(`item_data[${item.assetNum}][serial_num]`, item.serialNum);
            formData.append(`item_data[${item.assetNum}][model]`, item.model);
            formData.append(`item_data[${item.assetNum}][brand]`, item.brand);
            formData.append(`item_data[${item.assetNum}][old_asset_num]`, item.oldAssetNum);
            formData.append(`item_data[${item.assetNum}][asset_type]`, item.assetType);
            formData.append(`item_data[${item.assetNum}][category]`, item.category);
            
            const actions = item.actions || {};
            formData.append(`actions[${item.assetNum}][fix]`, actions.fix ? '1' : '0');
            formData.append(`actions[${item.assetNum}][sell]`, actions.sell ? '1' : '0');
            formData.append(`actions[${item.assetNum}][destroy]`, actions.destroy ? '1' : '0');
        }
        
        if (item.files && item.files.length > 0) {
            item.files.forEach((file) => {
                const fileKey = `attachments[${item.assetNum}][]`;
                formData.append(fileKey, file, file.name);
            });
        }
    });
    
    fetch('<?= base_url("return/attachment/upload") ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            
            selectedItems = [];
            uploadedFiles = {};
            itemActions = {};
            globalReturnReasons = {};
            closeDeleteModal();
            delete window.tempReturnData;
            
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showAlert('error', data.message || 'حدث خطأ أثناء الترجيع');
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'حدث خطأ في الاتصال بالخادم');
        confirmBtn.disabled = false;
        confirmBtn.innerHTML = originalText;
    });
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.remove('show');
    setTimeout(() => modal.style.display = 'none', 300);
}

function showAlert(type, message) {
    const alertContainer = document.getElementById('alertContainer');
    
    const existingAlerts = alertContainer.querySelectorAll('.alert');
    for (let alert of existingAlerts) {
        if (alert.textContent.includes(message)) {
            return;
        }
    }
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    
    const icon = type === 'success' ? '✓' : type === 'warning' ? '⚠' : '✕';
    alertDiv.innerHTML = `<strong>${icon}</strong> ${message}`;
    
    alertContainer.appendChild(alertDiv);
    setTimeout(() => alertDiv.classList.add('show'), 10);
    
    setTimeout(() => {
        alertDiv.classList.remove('show');
        setTimeout(() => alertDiv.remove(), 300);
    }, 4000);
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('Return system initialized - View button repositioned after last item');
});
</script>
</body>
</html>