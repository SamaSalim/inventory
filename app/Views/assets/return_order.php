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
                <h3 class="section-title"> ارجاع العهد</h3>

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
let uploadedFiles = {}; // Now keyed by assetNum

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
        
        selectedItems.push({
            id: itemId,
            name: itemName,
            category: category,
            model: model,
            serialNum: serialNum,
            assetNum: assetNum  // This is the unique identifier we'll use
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
    updateBulkActionsBar();
    const masterCheckbox = document.getElementById('masterCheckbox');
    if (masterCheckbox) {
        masterCheckbox.checked = false;
        masterCheckbox.indeterminate = false;
    }
}

function handleFileUpload(assetNum, files) {
    if (!uploadedFiles[assetNum]) {
        uploadedFiles[assetNum] = [];
    }
    
    console.log('Uploading files for asset:', assetNum);
    console.log('Number of files:', files.length);
    
    Array.from(files).forEach(file => {
        if (file.size > 5 * 1024 * 1024) {
            showAlert('warning', `الملف ${file.name} أكبر من 5 ميجابايت`);
            return;
        }
        uploadedFiles[assetNum].push(file);
        console.log(`Added file "${file.name}" to asset ${assetNum}`);
    });
    
    updateFileList(assetNum);
    console.log('Current uploadedFiles:', uploadedFiles);
}

function updateFileList(assetNum) {
    const fileList = document.getElementById(`fileList_${assetNum}`);
    if (!fileList) {
        console.warn(`File list element not found for asset: ${assetNum}`);
        return;
    }
    
    const files = uploadedFiles[assetNum] || [];
    
    if (files.length === 0) {
        fileList.innerHTML = '<div style="color: #999; font-size: 13px; padding: 10px; text-align: center;">لم يتم رفع أي ملفات</div>';
        return;
    }
    
    fileList.innerHTML = files.map((file, index) => `
        <div style="
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            background: #f8f9fa;
            border-radius: 6px;
            margin-bottom: 6px;
            border: 1px solid #e0e6ed;
        ">
            <div style="display: flex; align-items: center; gap: 8px; flex: 1; overflow: hidden;">
                <i class="fas fa-file" style="color: #3ac0c3;"></i>
                <span style="font-size: 13px; color: #333; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${file.name}</span>
                <span style="font-size: 11px; color: #999;">(${(file.size / 1024).toFixed(1)} KB)</span>
            </div>
            <button onclick="removeFile('${assetNum}', ${index})" style="
                background: #e74c3c;
                color: white;
                border: none;
                border-radius: 50%;
                width: 24px;
                height: 24px;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 14px;
                flex-shrink: 0;
            ">✕</button>
        </div>
    `).join('');
}

function removeFile(assetNum, fileIndex) {
    if (uploadedFiles[assetNum]) {
        uploadedFiles[assetNum].splice(fileIndex, 1);
        updateFileList(assetNum);
        console.log(`Removed file index ${fileIndex} from asset ${assetNum}`);
    }
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
    popup.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        max-width: 800px;
        width: 90%;
        max-height: 85vh;
        overflow: hidden;
        z-index: 1001;
        animation: popupSlideIn 0.3s ease;
    `;
    
    let itemsHTML = '';
    selectedItems.forEach((item, index) => {
        itemsHTML += `
            <div class="popup-item" style="
                padding: 15px;
                border-bottom: 1px solid #e0e6ed;
                transition: background 0.2s;
            ">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                    <div style="flex: 1;">
                        <div style="font-weight: bold; color: #057590; margin-bottom: 8px; font-size: 16px;">
                            ${index + 1}. ${item.name}
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; font-size: 14px; color: #555;">
                            <div><span style="color: #888;">التصنيف:</span> ${item.category}</div>
                            <div><span style="color: #888;">الموديل:</span> ${item.model}</div>
                            <div><span style="color: #888;">الرقم التسلسلي:</span> ${item.serialNum}</div>
                            <div><span style="color: #888;">رقم الأصل:</span> ${item.assetNum}</div>
                        </div>
                    </div>
                    <button onclick="removeItemFromSelection('${item.id}')" style="
                        background: #95a5a6;
                        color: white;
                        border: none;
                        border-radius: 50%;
                        width: 30px;
                        height: 30px;
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        flex-shrink: 0;
                        margin-right: 10px;
                        transition: background 0.2s;
                    " onmouseover="this.style.background='#7f8c8d'" onmouseout="this.style.background='#95a5a6'">
                        ✕
                    </button>
                </div>
                
                <div style="margin-top: 15px; margin-bottom: 15px;">
                    <label style="
                        display: block;
                        font-size: 13px;
                        font-weight: 600;
                        color: #057590;
                        margin-bottom: 8px;
                    ">
                        <i class="fas fa-paperclip" style="margin-left: 5px;"></i>
                        المرفقات:
                    </label>
                    <div style="
                        border: 2px dashed #e0e6ed;
                        border-radius: 8px;
                        padding: 15px;
                        background: #f8fdff;
                        transition: border-color 0.2s;
                    ">
                        <input 
                            type="file" 
                            id="fileInput_${item.assetNum}"
                            data-asset-num="${item.assetNum}"
                            multiple
                            accept="image/*,.pdf,.doc,.docx"
                            onchange="handleFileUpload(this.dataset.assetNum, this.files)"
                            style="display: none;"
                        />
                        <button 
                            onclick="document.getElementById('fileInput_${item.assetNum}').click()"
                            style="
                                background: linear-gradient(135deg, #3ac0c3, #2aa8ab);
                                color: white;
                                border: none;
                                padding: 10px 20px;
                                border-radius: 8px;
                                cursor: pointer;
                                font-size: 13px;
                                font-weight: 500;
                                width: 100%;
                                transition: all 0.3s;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                gap: 8px;
                            "
                            onmouseover="this.style.background='linear-gradient(135deg, #2aa8ab, #259a9d)'"
                            onmouseout="this.style.background='linear-gradient(135deg, #3ac0c3, #2aa8ab)'"
                        >
                            <i class="fas fa-upload"></i>
                            اختر الملفات
                        </button>
                        <div style="font-size: 11px; color: #999; text-align: center; margin-top: 8px;">
                            الحد الأقصى: 5 ميجابايت لكل ملف
                        </div>
                        <div id="fileList_${item.assetNum}" style="margin-top: 10px;">
                            <div style="color: #999; font-size: 13px; padding: 10px; text-align: center;">لم يتم رفع أي ملفات</div>
                        </div>
                    </div>
                </div>
                
                <div style="margin-top: 15px;">
                    <label style="
                        display: block;
                        font-size: 13px;
                        font-weight: 600;
                        color: #057590;
                        margin-bottom: 6px;
                    ">
                        <i class="fas fa-comment-dots" style="margin-left: 5px;"></i>
                        ملاحظات الترجيع:
                    </label>
                    <textarea 
                        id="comment_${item.id}"
                        placeholder="أضف ملاحظة حول حالة الصنف أو سبب الترجيع..."
                        style="
                            width: 100%;
                            min-height: 70px;
                            padding: 10px;
                            border: 2px solid #e8f4f8;
                            border-radius: 8px;
                            font-size: 14px;
                            font-family: inherit;
                            resize: vertical;
                            transition: border-color 0.2s;
                            box-sizing: border-box;
                            background: linear-gradient(135deg, #ffffff, #f8fdff);
                        "
                        onfocus="this.style.borderColor='#3ac0c3'"
                        onblur="this.style.borderColor='#e8f4f8'"
                    ></textarea>
                </div>
            </div>
        `;
    });
    
    popup.innerHTML = `
        <div style="
            padding: 20px;
            background: linear-gradient(135deg, #057590, #3ac0c3);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        ">
            <h3 style="margin: 0; font-size: 20px;">
                <i class="fas fa-undo-alt" style="margin-left: 8px;"></i>
                العناصر المحددة للترجيع (${selectedItems.length})
            </h3>
            <button onclick="closeSelectedItemsPopup()" style="
                background: rgba(255,255,255,0.2);
                color: white;
                border: none;
                border-radius: 50%;
                width: 35px;
                height: 35px;
                cursor: pointer;
                font-size: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: background 0.2s;
            " onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                ✕
            </button>
        </div>
        <div style="
            max-height: calc(85vh - 160px);
            overflow-y: auto;
        ">
            ${itemsHTML}
        </div>
        <div style="
            padding: 15px 20px;
            background: #f8f9fa;
            border-top: 2px solid #e0e6ed;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        ">
            <button onclick="handleCancelSelection()" style="
                flex: 1;
                padding: 12px 20px;
                background: #95a5a6;
                color: white;
                border: none;
                border-radius: 20px;
                cursor: pointer;
                font-weight: bold;
                font-size: 14px;
                transition: all 0.2s;
            " onmouseover="this.style.background='#7f8c8d'; this.style.transform='translateY(-1px)'" onmouseout="this.style.background='#95a5a6'; this.style.transform='translateY(0)'">
                <i class="fas fa-times" style="margin-left: 5px;"></i>
                إلغاء التحديد
            </button>
            <button onclick="submitReturn()" style="
                flex: 1;
                padding: 12px 20px;
                background: linear-gradient(135deg, #3ac0c3, #2aa8ab);
                color: white;
                border: none;
                border-radius: 20px;
                cursor: pointer;
                font-weight: bold;
                font-size: 14px;
                transition: all 0.2s;
                box-shadow: 0 2px 8px rgba(58, 192, 195, 0.3);
            " onmouseover="this.style.background='linear-gradient(135deg, #2aa8ab, #259a9d)'; this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(58, 192, 195, 0.4)'" onmouseout="this.style.background='linear-gradient(135deg, #3ac0c3, #2aa8ab)'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(58, 192, 195, 0.3)'">
                <i class="fas fa-undo" style="margin-left: 5px;"></i>
                تأكيد الترجيع
            </button>
        </div>
    `;
    
    const backdrop = document.createElement('div');
    backdrop.id = 'popupBackdrop';
    backdrop.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        animation: fadeIn 0.3s ease;
    `;
    backdrop.onclick = closeSelectedItemsPopup;
    
    if (!document.getElementById('popupAnimationStyles')) {
        const style = document.createElement('style');
        style.id = 'popupAnimationStyles';
        style.textContent = `
            @keyframes popupSlideIn {
                from { transform: translate(-50%, -60%); opacity: 0; }
                to { transform: translate(-50%, -50%); opacity: 1; }
            }
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            .popup-item:hover {
                background: #f8f9fa !important;
            }
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
        
        // Find the item's assetNum and clear its files
        const item = selectedItems.find(i => i.id === itemId);
        if (item && item.assetNum) {
            delete uploadedFiles[item.assetNum];
            console.log(`Cleared files for asset: ${item.assetNum}`);
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
        const commentElement = document.getElementById(`comment_${item.id}`);
        return {
            id: item.id,
            name: item.name,
            assetNum: item.assetNum,
            comment: commentElement ? commentElement.value.trim() : '',
            files: uploadedFiles[item.assetNum] || []
        };
    });
    
    const popupExists = document.getElementById('selectedItemsPopup');
    
    if (!popupExists) {
        showSelectedItemsPopup();
        return;
    }
    
    const missingComments = returnData.filter(item => !item.comment);
    
    if (missingComments.length > 0) {
        const confirmProceed = confirm(
            `يوجد ${missingComments.length} عنصر بدون ملاحظات.\nهل تريد المتابعة؟`
        );
        if (!confirmProceed) return;
    }
    
    showReturnConfirmation(returnData);
}

function showReturnConfirmation(returnData) {
    const modal = document.getElementById('deleteModal');
    const message = document.getElementById('deleteMessage');
    
    let itemsList = returnData.map((item, index) => {
        const filesInfo = item.files.length > 0 ? `<br><small style="color: #3ac0c3;">📎 ${item.files.length} مرفق</small>` : '';
        return `<div style="margin: 8px 0; padding: 8px; background: #f8f9fa; border-radius: 6px;">
            <strong>${index + 1}. ${item.name}</strong>
            <br><small style="color: #888;">رقم الأصل: ${item.assetNum}</small>
            ${item.comment ? `<br><small style="color: #666;">📝 ${item.comment}</small>` : '<br><small style="color: #999;">بدون ملاحظات</small>'}
            ${filesInfo}
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

function confirmBulkReturn() {
    const returnData = window.tempReturnData;
    
    if (!returnData) {
        showAlert('warning', 'حدث خطأ في البيانات');
        return;
    }
    
    const confirmBtn = document.querySelector('.confirm-delete-btn');
    const originalText = confirmBtn.innerHTML;
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الترجيع...';
    
    const itemsData = returnData.map(item => ({
        id: item.id,
        assetNum: item.assetNum,
        comment: item.comment
    }));
    
    fetch('<?= base_url("AssetsController/processReturn") ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            items: itemsData
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            returnData.forEach(item => {
                const card = document.querySelector(`.item-card[data-item-id="${item.id}"]`);
                if (card) {
                    card.style.animation = 'fadeOut 0.3s ease';
                    setTimeout(() => card.remove(), 300);
                }
            });
            
            showAlert('success', data.message);
            
            selectedItems = [];
            uploadedFiles = {};
            updateBulkActionsBar();
            closeDeleteModal();
            delete window.tempReturnData;
            
            setTimeout(() => {
                const remainingItems = document.querySelectorAll('.item-card');
                if (remainingItems.length === 0) {
                    const itemsGrid = document.querySelector('.items-grid');
                    if (itemsGrid) {
                        itemsGrid.innerHTML = '<div class="no-items-msg">تم ترجيع جميع العناصر بنجاح</div>';
                    }
                    document.querySelector('.select-all-container')?.remove();
                }
                
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }, 400);
            
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
    
    // Add asset numbers and comments
    returnData.forEach((item, index) => {
        formData.append(`asset_nums[${index}]`, item.assetNum);
        formData.append(`comments[${item.assetNum}]`, item.comment || 'تم الترجيع');
        
        console.log(`Added asset_num: ${item.assetNum}, comment: ${item.comment || 'تم الترجيع'}`);
        

        if (item.files && item.files.length > 0) {
            console.log(`Processing ${item.files.length} files for asset ${item.assetNum}`);
            
            item.files.forEach((file) => {

                const fileKey = `attachments[${item.assetNum}][]`;
                formData.append(fileKey, file, file.name);
                console.log(`Added file: ${file.name} with key: ${fileKey}`);
            });
        } else {
            console.log(`No files for asset ${item.assetNum}`);
        }
    });
    
 
    console.log('=== FormData Contents ===');
    for (let pair of formData.entries()) {
        if (pair[1] instanceof File) {
            console.log(pair[0], ':', pair[1].name, '(', pair[1].size, 'bytes)');
        } else {
            console.log(pair[0], ':', pair[1]);
        }
    }
    console.log('========================');
    
    // UPDATED URL - Point to new Attachment controller
    fetch('<?= base_url("item/attachment/upload") ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Response:', data);
        if (data.success) {
            showAlert('success', data.message);
            
            selectedItems = [];
            uploadedFiles = {};
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

if (!document.getElementById('fadeOutAnimationStyles')) {
    const style = document.createElement('style');
    style.id = 'fadeOutAnimationStyles';
    style.textContent = `
        @keyframes fadeOut {
            from { opacity: 1; transform: scale(1); }
            to { opacity: 0; transform: scale(0.9); }
        }
    `;
    document.head.appendChild(style);
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('Return system initialized with asset number tracking');
});
</script>
</body>
</html>