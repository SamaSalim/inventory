
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ترجيع الطلب</title>
    <link rel="stylesheet" href="<?= base_url('public/assets/css/order_details.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/assets/css/print_order_details.css') ?>">
    <style>
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s;
        }
        .alert.show {
            opacity: 1;
            transform: translateY(0);
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .select-all-container {
            background: #f8f9fa;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border: 2px solid #e0e6ed;
        }
        .select-all-container label {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
            cursor: pointer;
            user-select: none;
        }
        .master-checkbox {
            width: 22px;
            height: 22px;
            cursor: pointer;
            accent-color: #667eea;
        }
        .item-card {
            position: relative;
            transition: all 0.3s;
        }
        .item-card.selected {
            border-color: #667eea;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
        }
        .item-checkbox-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        .custom-checkbox {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #667eea;
        }
        .delete-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .delete-modal.show {
            opacity: 1;
        }
        .delete-modal-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            animation: modalSlideIn 0.3s ease;
        }
        @keyframes modalSlideIn {
            from { transform: translateY(-50px); }
            to { transform: translateY(0); }
        }
        .delete-modal-title {
            font-size: 22px;
            color: #27ae60;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .delete-modal-message {
            font-size: 16px;
            color: #555;
            margin-bottom: 25px;
            line-height: 1.6;
        }
        .delete-modal-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        .confirm-btn {
            padding: 10px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }
        .confirm-cancel-btn {
            background: #6c757d;
            color: white;
        }
        .confirm-cancel-btn:hover {
            background: #5a6268;
        }
        .confirm-delete-btn {
            background: #27ae60;
            color: white;
        }
        .confirm-delete-btn:hover {
            background: #229954;
        }
        @keyframes popupSlideIn {
            from { transform: translate(-50%, -60%); opacity: 0; }
            to { transform: translate(-50%, -50%); opacity: 1; }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes fadeOut {
            from { opacity: 1; transform: scale(1); }
            to { opacity: 0; transform: scale(0.9); }
        }
        .popup-item:hover {
            background: #f8f9fa !important;
        }
        @media print {
            .select-all-container, .item-checkbox-container, .custom-checkbox, .master-checkbox {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <?= $this->include('layouts/header') ?>

    <div class="main-content no-print">
        <div class="header">
            <h1 class="page-title">إدارة العهد</h1>
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

            <div class="items-section">
                <h3 class="section-title">تحويل العهد</h3>

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
        </div>
    </div>

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

    <div class="delete-modal" id="deleteModal">
        <div class="delete-modal-content">
            <div class="delete-modal-title">
                <i class="fas fa-check-circle"></i>
                تأكيد الترجيع
            </div>
            <div class="delete-modal-message" id="deleteMessage"></div>
            <div class="delete-modal-actions">
                <button class="confirm-btn confirm-cancel-btn" onclick="closeDeleteModal()">إلغاء</button>
                <button class="confirm-btn confirm-delete-btn" onclick="confirmBulkDelete()">
                    <i class="fas fa-undo"></i>
                    تأكيد الترجيع
                </button>
            </div>
        </div>
    </div>
    <script>
let selectedItems = [];
let itemFiles = {};

function updateSelection() {
    selectedItems = [];
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    
    checkboxes.forEach(cb => {
        const card = cb.closest('.item-card');
        const id = card.getAttribute('data-item-id');
        const name = card.querySelector('.item-name').textContent;
        const badge = card.querySelector('.category-badge');
        const vals = card.querySelectorAll('.detail-value');
        
        selectedItems.push({
            id: id,
            name: name,
            category: badge ? badge.textContent : 'غير محدد',
            model: vals[1] ? vals[1].textContent.trim() : 'غير محدد',
            serialNum: vals[2] ? vals[2].textContent.trim() : 'غير محدد',
            assetNum: vals[3] ? vals[3].textContent.trim() : 'غير محدد'
        });
        card.classList.add('selected');
    });
    
    document.querySelectorAll('.item-card').forEach(c => {
        if (!c.querySelector('.item-checkbox').checked) c.classList.remove('selected');
    });
    
    updateMasterCheckbox();
    if (selectedItems.length > 0) showSelectedItemsPopup();
    else closeSelectedItemsPopup();
}

function toggleAllSelection() {
    const master = document.getElementById('masterCheckbox');
    document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = master.checked);
    updateSelection();
}

function updateMasterCheckbox() {
    const master = document.getElementById('masterCheckbox');
    const all = document.querySelectorAll('.item-checkbox');
    const checked = document.querySelectorAll('.item-checkbox:checked');
    
    if (all.length === 0 || checked.length === 0) {
        master.checked = false;
        master.indeterminate = false;
    } else if (checked.length === all.length) {
        master.checked = true;
        master.indeterminate = false;
    } else {
        master.checked = false;
        master.indeterminate = true;
    }
}

function clearSelection() {
    document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = false);
    document.querySelectorAll('.item-card').forEach(c => c.classList.remove('selected'));
    updateSelection();
}

function showSelectedItemsPopup() {
    const existing = document.getElementById('selectedItemsPopup');
    if (existing) existing.remove();
    
    const popup = document.createElement('div');
    popup.id = 'selectedItemsPopup';
    popup.style.cssText = 'position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:white;border-radius:15px;box-shadow:0 10px 40px rgba(0,0,0,0.3);max-width:800px;width:90%;max-height:85vh;overflow:hidden;z-index:1001;animation:popupSlideIn 0.3s ease;';
    
    let html = '';
    selectedItems.forEach((item, i) => {
        html += `<div class="popup-item" style="padding:15px;border-bottom:1px solid #e0e6ed;transition:background 0.2s;">
            <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:12px;">
                <div style="flex:1;">
                    <div style="font-weight:bold;color:#2c3e50;margin-bottom:8px;font-size:16px;">${i+1}. ${item.name}</div>
                    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px;font-size:14px;color:#555;">
                        <div><span style="color:#888;">التصنيف:</span> ${item.category}</div>
                        <div><span style="color:#888;">الموديل:</span> ${item.model}</div>
                        <div><span style="color:#888;">الرقم التسلسلي:</span> ${item.serialNum}</div>
                        <div><span style="color:#888;">رقم الأصل:</span> ${item.assetNum}</div>
                    </div>
                </div>
                <button onclick="removeItemFromSelection('${item.id}')" style="background:#e74c3c;color:white;border:none;border-radius:50%;width:30px;height:30px;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-right:10px;transition:background 0.2s;" onmouseover="this.style.background='#c0392b'" onmouseout="this.style.background='#e74c3c'">✕</button>
            </div>
            <div style="margin-top:10px;">
                <label style="display:block;font-size:13px;font-weight:600;color:#555;margin-bottom:6px;"><i class="fas fa-paperclip" style="margin-left:5px;"></i>إرفاق ملفات:</label>
                <div style="border:2px dashed #e0e6ed;border-radius:8px;padding:15px;text-align:center;background:#f8f9fa;margin-bottom:12px;transition:all 0.2s;" ondragover="event.preventDefault();this.style.borderColor='#667eea';this.style.background='#f0f3ff';" ondragleave="this.style.borderColor='#e0e6ed';this.style.background='#f8f9fa';" ondrop="handleFileDrop(event,'${item.id}')">
                    <input type="file" id="files_${item.id}" multiple accept="image/*,.pdf,.doc,.docx" onchange="handleFileSelect(event,'${item.id}')" style="display:none;">
                    <label for="files_${item.id}" style="cursor:pointer;color:#667eea;font-size:14px;">
                        <i class="fas fa-cloud-upload-alt" style="font-size:24px;display:block;margin-bottom:8px;"></i>
                        <span style="font-weight:600;">اضغط لاختيار الملفات</span>
                        <span style="color:#888;display:block;font-size:12px;margin-top:5px;">أو اسحب الملفات هنا</span>
                        <span style="color:#888;display:block;font-size:11px;margin-top:3px;">(صور، PDF، Word)</span>
                    </label>
                </div>
                <div id="filesList_${item.id}" style="display:none;margin-bottom:12px;"></div>
            </div>
            <div style="margin-top:10px;">
                <label style="display:block;font-size:13px;font-weight:600;color:#555;margin-bottom:6px;">ملاحظات الترجيع:</label>
                <textarea id="comment_${item.id}" placeholder="أضف ملاحظة حول حالة الصنف أو سبب الترجيع..." style="width:100%;min-height:70px;padding:10px;border:2px solid #e0e6ed;border-radius:8px;font-size:14px;font-family:inherit;resize:vertical;transition:border-color 0.2s;box-sizing:border-box;" onfocus="this.style.borderColor='#667eea'" onblur="this.style.borderColor='#e0e6ed'"></textarea>
            </div>
        </div>`;
    });
    
    popup.innerHTML = `<div style="padding:20px;background:linear-gradient(135deg,#27ae60 0%,#229954 100%);color:white;display:flex;justify-content:space-between;align-items:center;">
        <h3 style="margin:0;font-size:20px;"><i class="fas fa-undo-alt" style="margin-left:8px;"></i>العناصر المحددة للترجيع (${selectedItems.length})</h3>
        <button onclick="closeSelectedItemsPopup()" style="background:rgba(255,255,255,0.2);color:white;border:none;border-radius:50%;width:35px;height:35px;cursor:pointer;font-size:20px;display:flex;align-items:center;justify-content:center;transition:background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">✕</button>
    </div>
    <div style="max-height:calc(85vh - 160px);overflow-y:auto;">${html}</div>
    <div style="padding:15px 20px;background:#f8f9fa;border-top:2px solid #e0e6ed;display:flex;justify-content:space-between;gap:10px;">
        <button onclick="clearSelection()" style="flex:1;padding:12px 20px;background:#6c757d;color:white;border:none;border-radius:8px;cursor:pointer;font-weight:bold;font-size:14px;transition:background 0.2s;" onmouseover="this.style.background='#5a6268'" onmouseout="this.style.background='#6c757d'"><i class="fas fa-times" style="margin-left:5px;"></i>إلغاء التحديد</button>
        <button onclick="submitReturn()" style="flex:1;padding:12px 20px;background:#27ae60;color:white;border:none;border-radius:8px;cursor:pointer;font-weight:bold;font-size:14px;transition:background 0.2s;" onmouseover="this.style.background='#229954'" onmouseout="this.style.background='#27ae60'"><i class="fas fa-undo" style="margin-left:5px;"></i>تأكيد الترجيع</button>
    </div>`;
    
    const backdrop = document.createElement('div');
    backdrop.id = 'popupBackdrop';
    backdrop.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1000;animation:fadeIn 0.3s ease;';
    backdrop.onclick = closeSelectedItemsPopup;
    
    document.body.appendChild(backdrop);
    document.body.appendChild(popup);
}

function closeSelectedItemsPopup() {
    const popup = document.getElementById('selectedItemsPopup');
    const backdrop = document.getElementById('popupBackdrop');
    if (popup) popup.remove();
    if (backdrop) backdrop.remove();
}

function removeItemFromSelection(id) {
    const cb = document.querySelector(`.item-card[data-item-id="${id}"] .item-checkbox`);
    if (cb) {
        cb.checked = false;
        updateSelection();
    }
}

function handleFileSelect(e, id) {
    addFilesToItem(id, e.target.files);
}

function handleFileDrop(e, id) {
    e.preventDefault();
    e.currentTarget.style.borderColor = '#e0e6ed';
    e.currentTarget.style.background = '#f8f9fa';
    addFilesToItem(id, e.dataTransfer.files);
}

function addFilesToItem(id, files) {
    if (!itemFiles[id]) itemFiles[id] = [];
    
    for (let f of files) {
        if (f.size > 5*1024*1024) {
            showAlert('warning', `الملف "${f.name}" أكبر من 5 ميجابايت`);
            continue;
        }
        if (itemFiles[id].some(x => x.name === f.name && x.size === f.size)) {
            showAlert('warning', `الملف "${f.name}" مرفق مسبقاً`);
            continue;
        }
        itemFiles[id].push(f);
    }
    displayFiles(id);
}

function displayFiles(id) {
    const list = document.getElementById(`filesList_${id}`);
    if (!list || !itemFiles[id] || itemFiles[id].length === 0) {
        if (list) list.style.display = 'none';
        return;
    }
    
    list.style.display = 'block';
    list.innerHTML = '<div style="font-size:13px;font-weight:600;color:#555;margin-bottom:8px;">الملفات المرفقة:</div>';
    
    itemFiles[id].forEach((f, i) => {
        const div = document.createElement('div');
        div.style.cssText = 'display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:white;border:1px solid #e0e6ed;border-radius:6px;margin-bottom:6px;transition:all 0.2s;';
        
        const icon = getFileIcon(f.name);
        const size = formatFileSize(f.size);
        
        div.innerHTML = `<div style="display:flex;align-items:center;gap:10px;flex:1;min-width:0;">
            <i class="fas ${icon}" style="color:#667eea;font-size:18px;"></i>
            <div style="flex:1;min-width:0;">
                <div style="font-size:13px;font-weight:500;color:#2c3e50;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${f.name}</div>
                <div style="font-size:11px;color:#888;">${size}</div>
            </div>
        </div>
        <button onclick="removeFile('${id}',${i})" style="background:#e74c3c;color:white;border:none;border-radius:4px;width:28px;height:28px;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:background 0.2s;font-size:14px;" onmouseover="this.style.background='#c0392b'" onmouseout="this.style.background='#e74c3c'"><i class="fas fa-times"></i></button>`;
        
        list.appendChild(div);
    });
}

function removeFile(id, i) {
    if (itemFiles[id]) {
        itemFiles[id].splice(i, 1);
        displayFiles(id);
    }
}

function getFileIcon(name) {
    const ext = name.split('.').pop().toLowerCase();
    const icons = {pdf:'fa-file-pdf',doc:'fa-file-word',docx:'fa-file-word',jpg:'fa-file-image',jpeg:'fa-file-image',png:'fa-file-image',gif:'fa-file-image',bmp:'fa-file-image'};
    return icons[ext] || 'fa-file';
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes','KB','MB','GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}
function submitReturn() {
    if (selectedItems.length === 0) {
        showAlert('warning', 'لم يتم تحديد أي عناصر للترجيع');
        return;
    }
    
    // Collect comments for each item
    const returnData = selectedItems.map(item => {
        const commentElement = document.getElementById(`comment_${item.id}`);
        return {
            id: item.id,
            name: item.name,
            comment: commentElement ? commentElement.value.trim() : ''
        };
    });
    
    // Check if any comments are missing (optional validation)
    const missingComments = returnData.filter(item => !item.comment);
    
    if (missingComments.length > 0) {
        const confirmProceed = confirm(
            `يوجد ${missingComments.length} عنصر بدون ملاحظات.\nهل تريد المتابعة؟`
        );
        if (!confirmProceed) return;
    }
    
    // Show confirmation modal
    showReturnConfirmation(returnData);
}

// Show return confirmation modal
function showReturnConfirmation(returnData) {
    const modal = document.getElementById('deleteModal');
    const message = document.getElementById('deleteMessage');
    
    let itemsList = returnData.map((item, index) => 
        `<div style="margin: 8px 0; padding: 8px; background: #f8f9fa; border-radius: 6px;">
            <strong>${index + 1}. ${item.name}</strong>
            ${item.comment ? `<br><small style="color: #666;">📝 ${item.comment}</small>` : '<br><small style="color: #999;">بدون ملاحظات</small>'}
        </div>`
    ).join('');
    
    message.innerHTML = `
        <div style="max-height: 300px; overflow-y: auto; margin: 10px 0;">
            ${itemsList}
        </div>
        <strong style="color: #27ae60; margin-top: 15px; display: block;">
            هل أنت متأكد من ترجيع هذه العناصر؟
        </strong>
    `;
    
    modal.style.display = 'flex';
    setTimeout(() => modal.classList.add('show'), 10);
    
    // Store return data temporarily
    window.tempReturnData = returnData;
    
    closeSelectedItemsPopup();
}

// Confirm return submission
function confirmBulkDelete() {
    const returnData = window.tempReturnData;
    
    if (!returnData) {
        showAlert('danger', 'حدث خطأ في البيانات');
        return;
    }
    
    console.log('Submitting return for items:', returnData);
    
    // Here you would make an AJAX call to process the return
    // Example:
    /*
    fetch('<?= base_url('return/processReturn') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            items: returnData
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', `تم ترجيع ${returnData.length} عنصر بنجاح`);
            // Remove returned items from the page
            returnData.forEach(item => {
                const card = document.querySelector(`.item-card[data-item-id="${item.id}"]`);
                if (card) {
                    card.style.animation = 'fadeOut 0.3s ease';
                    setTimeout(() => card.remove(), 300);
                }
            });
        } else {
            showAlert('danger', 'حدث خطأ أثناء الترجيع: ' + data.message);
        }
    })
    .catch(error => {
        showAlert('danger', 'حدث خطأ في الاتصال بالخادم');
        console.error('Error:', error);
    });
    */
    
    // Simulate API call for demonstration
    setTimeout(() => {
        // Remove items from DOM
        returnData.forEach(item => {
            const card = document.querySelector(`.item-card[data-item-id="${item.id}"]`);
            if (card) {
                card.style.animation = 'fadeOut 0.3s ease';
                setTimeout(() => card.remove(), 300);
            }
        });
        
        showAlert('success', `تم ترجيع ${returnData.length} عنصر بنجاح`);
        
        // Clear selection
        selectedItems = [];
        updateBulkActionsBar();
        closeDeleteModal();
        
        // Clean up temp data
        delete window.tempReturnData;
        
        // Check if no items left
        setTimeout(() => {
            const remainingItems = document.querySelectorAll('.item-card');
            if (remainingItems.length === 0) {
                const itemsGrid = document.querySelector('.items-grid');
                if (itemsGrid) {
                    itemsGrid.innerHTML = '<div class="no-items-msg">تم ترجيع جميع العناصر بنجاح</div>';
                }
                document.querySelector('.select-all-container')?.remove();
            }
        }, 400);
    }, 500);
}

// Close delete modal
function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.remove('show');
    setTimeout(() => modal.style.display = 'none', 300);
}

// Show alert message
function showAlert(type, message) {
    const alertContainer = document.getElementById('alertContainer');
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    
    const icon = type === 'success' ? '✓' : type === 'danger' ? '✕' : '⚠';
    alertDiv.innerHTML = `<strong>${icon}</strong> ${message}`;
    
    alertContainer.appendChild(alertDiv);
    setTimeout(() => alertDiv.classList.add('show'), 10);
    
    setTimeout(() => {
        alertDiv.classList.remove('show');
        setTimeout(() => alertDiv.remove(), 300);
    }, 4000);
}

// Add fadeOut animation
if (!document.getElementById('deleteAnimationStyles')) {
    const style = document.createElement('style');
    style.id = 'deleteAnimationStyles';
    style.textContent = `
        @keyframes fadeOut {
            from { opacity: 1; transform: scale(1); }
            to { opacity: 0; transform: scale(0.9); }
        }
    `;
    document.head.appendChild(style);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Selection system initialized');
});
</script>
</body>
</html>