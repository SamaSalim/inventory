<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعادة الصرف</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('public/assets/css/warehouse-style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/assets/css/super_warehouse_style.css') ?>">
</head>
<style>
    /* Selected Items Popup Styles */
    .selected-items-popup {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        max-width: 700px;
        width: 90%;
        max-height: 85vh;
        overflow: hidden;
        z-index: 1001;
        animation: popupSlideIn 0.3s ease;
        display: none;
        flex-direction: column;
    }

    .selected-items-popup.show {
        display: flex;
    }

    @keyframes popupSlideIn {
        from {
            opacity: 0;
            transform: translate(-50%, -45%);
        }
        to {
            opacity: 1;
            transform: translate(-50%, -50%);
        }
    }

    .popup-header {
        padding: 20px;
        background: linear-gradient(135deg, #057590, #3ac0c3);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .popup-header h3 {
        margin: 0;
        font-size: 20px;
    }

    .popup-close-btn {
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
    }

    .popup-close-btn:hover {
        background: rgba(255,255,255,0.3);
    }

    .popup-body {
        flex: 1;
        overflow-y: auto;
        min-height: 0;
    }

    .receiver-section {
        padding: 20px;
        background: #f8f9fa;
        border-bottom: 2px solid #e0e6ed;
        margin-bottom: 0;
    }

    .receiver-section h4 {
        color: #057590;
        margin-bottom: 15px;
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .receiver-form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }

    .receiver-form-group {
        display: flex;
        flex-direction: column;
    }

    .receiver-form-group label {
        color: #555;
        margin-bottom: 5px;
        font-weight: 500;
        font-size: 13px;
    }

    .receiver-form-group input {
        padding: 10px;
        border: 2px solid #e0e6ed;
        border-radius: 8px;
        font-size: 13px;
        transition: all 0.3s ease;
    }

    .receiver-form-group input:focus {
        outline: none;
        border-color: #3AC0C3;
    }

    .receiver-form-group input:read-only {
        background: #f5f5f5;
        cursor: not-allowed;
    }

    .status-message {
        display: none;
        font-size: 12px;
        margin-top: 5px;
        padding: 3px 0;
    }

    .status-message.show {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .status-message.loading {
        color: #3AC0C3;
    }

    .status-message.error {
        color: #e74c3c;
    }

    .status-message.success {
        color: #27ae60;
    }

    .required {
        color: #e74c3c;
    }

    .popup-item {
        padding: 15px 20px;
        border-bottom: 1px solid #e0e6ed;
        transition: background 0.2s;
    }

    .popup-item:hover {
        background: #f8f9fa;
    }

    .popup-item:last-child {
        border-bottom: none;
    }

    .item-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 8px;
    }

    .item-name {
        font-weight: bold;
        color: #057590;
        font-size: 16px;
    }

    .item-number {
        background: linear-gradient(135deg, #3AC0C3, #2aa8ab);
        color: white;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }

    .item-details {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
        font-size: 14px;
        color: #555;
        margin-top: 10px;
    }

    .item-detail {
        display: flex;
        gap: 5px;
    }

    .item-detail-label {
        color: #888;
        font-weight: 500;
    }

    .popup-footer {
        padding: 15px 20px;
        background: #f8f9fa;
        border-top: 2px solid #e0e6ed;
        display: flex;
        justify-content: space-between;
        gap: 10px;
        flex-shrink: 0;
    }

    .popup-btn {
        flex: 1;
        padding: 12px 20px;
        border: none;
        border-radius: 20px;
        cursor: pointer;
        font-weight: bold;
        font-size: 14px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .popup-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .popup-btn-cancel {
        background: #95a5a6;
        color: white;
    }

    .popup-btn-cancel:hover {
        background: #7f8c8d;
    }

    .popup-btn-send {
        background: linear-gradient(135deg, #3ac0c3, #2aa8ab);
        color: white;
        box-shadow: 0 2px 8px rgba(58, 192, 195, 0.3);
    }

    .popup-btn-send:hover:not(:disabled) {
        background: linear-gradient(135deg, #2aa8ab, #259a9d);
        box-shadow: 0 4px 12px rgba(58, 192, 195, 0.4);
        transform: translateY(-1px);
    }

    #popupBackdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        display: none;
    }

    #popupBackdrop.show {
        display: block;
    }

    @media (max-width: 768px) {
        .main-content {
            margin-right: 0;
        }

        .selected-items-popup {
            max-width: 95%;
            max-height: 90vh;
        }

        .receiver-form-grid,
        .item-details {
            grid-template-columns: 1fr;
        }

        .popup-footer {
            flex-direction: column;
        }
    }
</style>
<body>
<?= $this->include('layouts/header') ?>
    <div class="main-content">
        <div class="header">
            <h1 class="page-title">إعادة الصرف</h1>
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
            <!-- قسم الأزرار -->
            <div class="section-header" style="display: flex; justify-content: flex-end;">
                <div class="buttons-group">
                    <button class="add-btn" id="reissueSelectedBtn">
                        <i class="fas fa-redo-alt"></i> إعادة الصرف
                    </button>
                </div>
            </div>

            <!-- قسم الفلاتر -->
            <div class="filters-section">
                <form method="get" action="<?= base_url('Return/SuperWarehouse/ReissueItems') ?>" id="filterForm">
                    <!-- البحث العام -->
                    <div class="main-search-container">
                        <h3 class="search-section-title">
                            <i class="fas fa-search"></i>
                            البحث العام
                        </h3>
                        <div class="search-bar-wrapper">
                            <input type="text" 
                                   name="general_search" 
                                   class="main-search-input" 
                                   placeholder="ابحث في جميع الحقول..." 
                                   value="<?= esc($filters['general_search'] ?? '') ?>">
                            <i class="fas fa-search search-icon"></i>
                        </div>
                    </div>

                    <!-- الفاصل -->
                    <div class="filters-divider">
                        <span>أو استخدم الفلاتر التفصيلية</span>
                    </div>

                    <!-- الفلاتر التفصيلية -->
                    <div class="detailed-filters">
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-hashtag"></i>
                                رقم الطلب
                            </label>
                            <input type="text" 
                                   name="order_id" 
                                   class="filter-input" 
                                   placeholder="أدخل رقم الطلب"
                                   value="<?= esc($filters['order_id'] ?? '') ?>">
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-barcode"></i>
                                رقم الأصل
                            </label>
                            <input type="text" 
                                   name="asset_num" 
                                   class="filter-input" 
                                   placeholder="أدخل رقم الأصل"
                                   value="<?= esc($filters['asset_num'] ?? '') ?>">
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-hashtag"></i>
                                الرقم التسلسلي
                            </label>
                            <input type="text" 
                                   name="serial_num" 
                                   class="filter-input" 
                                   placeholder="أدخل الرقم التسلسلي"
                                   value="<?= esc($filters['serial_num'] ?? '') ?>">
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-box"></i>
                                اسم الصنف
                            </label>
                            <input type="text" 
                                   name="item_name" 
                                   class="filter-input" 
                                   placeholder="أدخل اسم الصنف"
                                   value="<?= esc($filters['item_name'] ?? '') ?>">
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-tag"></i>
                                العلامة التجارية
                            </label>
                            <input type="text" 
                                   name="brand" 
                                   class="filter-input" 
                                   placeholder="أدخل العلامة التجارية"
                                   value="<?= esc($filters['brand'] ?? '') ?>">
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-info-circle"></i>
                                رقم الموديل
                            </label>
                            <input type="text" 
                                   name="model" 
                                   class="filter-input" 
                                   placeholder="أدخل رقم الموديل"
                                   value="<?= esc($filters['model'] ?? '') ?>">
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-calendar-alt"></i>
                                من تاريخ
                            </label>
                            <input type="date" 
                                   name="date_from" 
                                   class="filter-input"
                                   value="<?= esc($filters['date_from'] ?? '') ?>">
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-calendar-check"></i>
                                إلى تاريخ
                            </label>
                            <input type="date" 
                                   name="date_to" 
                                   class="filter-input"
                                   value="<?= esc($filters['date_to'] ?? '') ?>">
                        </div>
                    </div>

                    <!-- أزرار الفلتر -->
                    <div class="filter-actions">
                        <button type="submit" class="filter-btn search-btn">
                            <i class="fas fa-search"></i>
                            بحث
                        </button>
                        <a href="<?= base_url('Return/SuperWarehouse/ReissueItems') ?>" class="filter-btn reset-btn">
                            <i class="fas fa-redo-alt"></i>
                            إعادة تعيين
                        </a>
                    </div>
                </form>
            </div>

            <!-- جدول البيانات - عرض العناصر الفردية -->
            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th class="checkbox-cell">
                                <input type="checkbox" id="selectAll" class="master-checkbox" title="تحديد الكل">
                            </th>
                            <th>رقم الطلب</th>
                            <th>رقم الأصل</th>
                            <th>الرقم التسلسلي</th>
                            <th>اسم العنصر</th>
                            <th>العلامة التجارية</th>
                            <th>رقم الموديل</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($returnOrders)): ?>
                            <?php foreach ($returnOrders as $item): ?>
                                <tr>
                                    <td class="checkbox-cell">
                                        <input type="checkbox" class="custom-checkbox row-check" 
                                               name="selected_items[]" 
                                               value="<?= esc($item['item_order_id']) ?>"
                                               data-item-name="<?= esc($item['item_name']) ?>"
                                               data-asset-num="<?= esc($item['asset_num']) ?>"
                                               data-serial-num="<?= esc($item['serial_num']) ?>"
                                               data-brand="<?= esc($item['brand'] ?? '') ?>"
                                               data-model="<?= esc($item['model'] ?? '') ?>">
                                    </td>
                                    <td><?= esc($item['item_order_id'] ?? '-') ?></td>
                                    <td><?= esc($item['asset_num'] ?? '-') ?></td>
                                    <td><?= esc($item['serial_num'] ?? '-') ?></td>
                                    <td><?= esc($item['item_name'] ?? '-') ?></td>
                                    <td><?= esc($item['brand'] ?? '-') ?></td>
                                    <td><?= esc($item['model'] ?? '-') ?></td>
                                    <td><?= isset($item['created_at']) ? esc(date('Y-m-d', strtotime($item['created_at']))) : '-' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <p>لا توجد بيانات متاحة</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Backdrop -->
    <div id="popupBackdrop"></div>

    <!-- Selected Items Popup -->
    <div id="selectedItemsPopup" class="selected-items-popup"></div>

<script>
// ========================================
// Global Variables
// ========================================
let selectedItems = [];
let searchTimeout;

// Base URL configuration
const pathSegments = window.location.pathname.split('/');
const projectFolder = pathSegments[1] || '';
const baseUrl = window.location.origin + '/' + projectFolder + '/index.php/';

// ========================================
// Initialize on Page Load
// ========================================
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.row-check');
    const reissueBtn = document.getElementById('reissueSelectedBtn');

    // Select all functionality
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = selectAll.checked;
                updateRowSelection(cb);
            });
            updateSelectedItems();
        });
    }

    // Individual checkbox functionality
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            updateRowSelection(cb);
            updateSelectAllState();
            updateSelectedItems();
        });
    });

    // Reissue button click
    if (reissueBtn) {
        reissueBtn.addEventListener('click', showSelectedItemsPopup);
    }

    // Backdrop click to close
    const backdrop = document.getElementById('popupBackdrop');
    if (backdrop) {
        backdrop.addEventListener('click', closeSelectedItemsPopup);
    }
});

// ========================================
// Row Selection Functions
// ========================================
function updateRowSelection(checkbox) {
    const row = checkbox.closest('tr');
    if (checkbox.checked) {
        row.classList.add('selected');
    } else {
        row.classList.remove('selected');
    }
}

function updateSelectAllState() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.row-check');
    const allChecked = [...checkboxes].every(c => c.checked);
    selectAll.checked = allChecked;
}

function updateSelectedItems() {
    const checkboxes = document.querySelectorAll('.row-check:checked');
    const reissueBtn = document.getElementById('reissueSelectedBtn');
    
    selectedItems = [];
    
    checkboxes.forEach(cb => {
        selectedItems.push({
            id: cb.value,
            item_name: cb.dataset.itemName,
            asset_num: cb.dataset.assetNum,
            serial_num: cb.dataset.serialNum,
            brand: cb.dataset.brand || '-',
            model: cb.dataset.model || '-'
        });
    });

    if (reissueBtn) {
        reissueBtn.disabled = selectedItems.length === 0;
    }
}

// ========================================
// Popup Functions
// ========================================
function showSelectedItemsPopup() {
    if (selectedItems.length === 0) {
        alert('الرجاء تحديد العناصر أولاً');
        return;
    }

    const popup = document.getElementById('selectedItemsPopup');
    const backdrop = document.getElementById('popupBackdrop');

    if (!popup) return;

    // Build items HTML
    let itemsHTML = '';
    selectedItems.forEach((item, index) => {
        itemsHTML += `
            <div class="popup-item">
                <div class="item-header">
                    <div>
                        <div class="item-name">${index + 1}. ${item.item_name}</div>
                    </div>
                    <span class="item-number">${item.id}</span>
                </div>
                <div class="item-details">
                    <div class="item-detail">
                        <span class="item-detail-label">رقم الأصل:</span>
                        <span>${item.asset_num}</span>
                    </div>
                    <div class="item-detail">
                        <span class="item-detail-label">الرقم التسلسلي:</span>
                        <span>${item.serial_num}</span>
                    </div>
                    <div class="item-detail">
                        <span class="item-detail-label">العلامة التجارية:</span>
                        <span>${item.brand}</span>
                    </div>
                    <div class="item-detail">
                        <span class="item-detail-label">رقم الموديل:</span>
                        <span>${item.model}</span>
                    </div>
                </div>
            </div>
        `;
    });

    // Set popup content with receiver form
    popup.innerHTML = `
        <div class="popup-header">
            <h3>
                <i class="fas fa-redo-alt" style="margin-left: 8px;"></i>
                إعادة صرف العناصر (${selectedItems.length})
            </h3>
            <button class="popup-close-btn" onclick="closeSelectedItemsPopup()">✕</button>
        </div>
        
        <div class="popup-body">
            <!-- قسم بيانات المستلم -->
            <div class="receiver-section">
                <h4>
                    <i class="fas fa-user"></i>
                    بيانات المستلم
                </h4>
                <div class="receiver-form-grid">
                    <div class="receiver-form-group">
                        <label>رقم المستخدم <span class="required">*</span></label>
                        <input type="text" id="receiverUserId" placeholder="أدخل رقم المستخدم" required autocomplete="off">
                        <div id="receiverLoadingMsg" class="status-message loading">
                            <i class="fas fa-spinner fa-spin"></i> جاري البحث...
                        </div>
                        <div id="receiverErrorMsg" class="status-message error">
                            <i class="fas fa-exclamation-circle"></i> <span id="errorText">رقم المستخدم غير موجود</span>
                        </div>
                        <div id="receiverSuccessMsg" class="status-message success">
                            <i class="fas fa-check-circle"></i> تم العثور على المستخدم
                        </div>
                    </div>
                    <div class="receiver-form-group">
                        <label>اسم المستلم <span class="required">*</span></label>
                        <input type="text" id="receiverName" placeholder="اسم المستلم" readonly required>
                    </div>
                    <div class="receiver-form-group">
                        <label>البريد الإلكتروني</label>
                        <input type="email" id="receiverEmail" placeholder="البريد الإلكتروني" readonly>
                    </div>
                    <div class="receiver-form-group">
                        <label>رقم التحويلة</label>
                        <input type="text" id="receiverTransfer" placeholder="رقم التحويلة" readonly>
                    </div>
                </div>
            </div>

            <!-- قائمة العناصر المحددة -->
            <div style="max-height: 300px; overflow-y: auto;">
                ${itemsHTML}
            </div>
        </div>
        
        <div class="popup-footer">
            <button class="popup-btn popup-btn-cancel" onclick="closeSelectedItemsPopup()">
                <i class="fas fa-times"></i>
                إلغاء
            </button>
            <button class="popup-btn popup-btn-send" id="submitReissueBtn" disabled onclick="handleSendItems()">
                <i class="fas fa-paper-plane"></i>
                إرسال
            </button>
        </div>
    `;

    // Show popup and backdrop
    popup.classList.add('show');
    backdrop.classList.add('show');
    
    // Initialize receiver search after popup is shown
    initReceiverSearch();
}

function closeSelectedItemsPopup() {
    const popup = document.getElementById('selectedItemsPopup');
    const backdrop = document.getElementById('popupBackdrop');

    if (popup) popup.classList.remove('show');
    if (backdrop) backdrop.classList.remove('show');
}

// ========================================
// Receiver Search Functions
// ========================================
function initReceiverSearch() {
    const userIdInput = document.getElementById('receiverUserId');
    const receiverNameInput = document.getElementById('receiverName');
    const emailInput = document.getElementById('receiverEmail');
    const transferInput = document.getElementById('receiverTransfer');
    const loadingMsg = document.getElementById('receiverLoadingMsg');
    const errorMsg = document.getElementById('receiverErrorMsg');
    const successMsg = document.getElementById('receiverSuccessMsg');
    const submitBtn = document.getElementById('submitReissueBtn');

    if (!userIdInput) return;

    userIdInput.addEventListener('input', function() {
        const userId = this.value.trim();

        // Hide all messages
        loadingMsg.classList.remove('show');
        errorMsg.classList.remove('show');
        successMsg.classList.remove('show');

        // Clear receiver fields
        receiverNameInput.value = '';
        emailInput.value = '';
        transferInput.value = '';
        
        // Disable submit button
        submitBtn.disabled = true;

        if (userId.length < 3) return;

        clearTimeout(searchTimeout);

        // Show loading message
        loadingMsg.classList.add('show');

        searchTimeout = setTimeout(() => {
            searchReceiver(userId);
        }, 500);
    });

    function searchReceiver(userId) {
        fetch(baseUrl + `Return/SuperWarehouse/ReissueItems/searchUser?user_id=${encodeURIComponent(userId)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                // Hide loading message
                loadingMsg.classList.remove('show');

                if (data.success) {
                    // Clear any previous error messages
                    errorMsg.classList.remove('show');
                    
                    // Fill in the receiver data
                    receiverNameInput.value = data.data.name || '';
                    emailInput.value = data.data.email || '';
                    transferInput.value = data.data.transfer_number || '';
                    
                    // Show success message
                    successMsg.classList.add('show');
                    
                    // Enable submit button when user is found
                    submitBtn.disabled = false;
                } else {
                    // Clear any previous success messages and data
                    successMsg.classList.remove('show');
                    receiverNameInput.value = '';
                    emailInput.value = '';
                    transferInput.value = '';
                    
                    // Show error message
                    document.getElementById('errorText').textContent = data.message || 'رقم المستخدم غير موجود';
                    errorMsg.classList.add('show');
                    submitBtn.disabled = true;
                }
            })
            .catch(error => {
                console.error('خطأ في البحث عن المستخدم:', error);
                
                // Hide loading and success messages
                loadingMsg.classList.remove('show');
                successMsg.classList.remove('show');
                
                // Clear receiver data
                receiverNameInput.value = '';
                emailInput.value = '';
                transferInput.value = '';
                
                // Show error message
                document.getElementById('errorText').textContent = 'خطأ في الاتصال بالخادم';
                errorMsg.classList.add('show');
                submitBtn.disabled = true;
            });
    }
}

// ========================================
// Send Items Function
// ========================================
function handleSendItems() {
    const receiverUserId = document.getElementById('receiverUserId').value.trim();
    const receiverName = document.getElementById('receiverName').value.trim();

    if (!receiverUserId || !receiverName) {
        alert('⚠️ يجب إدخال بيانات المستلم بشكل صحيح');
        return;
    }

    const submitBtn = document.getElementById('submitReissueBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإرسال...';

    // ✅ جمع معرفات العناصر المحددة (item_order_id)
    const itemOrderIds = selectedItems.map(item => item.id);

    // إعداد البيانات للإرسال
    const formData = new FormData();
    formData.append('receiver_user_id', receiverUserId);
    formData.append('item_order_ids', JSON.stringify(itemOrderIds)); // ✅ إرسال جميع العناصر كمصفوفة واحدة

    // ✅ إرسال الطلب - سيتم إنشاء order واحد فقط لجميع العناصر
    fetch(baseUrl + 'Return/SuperWarehouse/ReissueItems/sendReissueOrder', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ تم إرسال الطلب بنجاح!\nرقم الطلب: ' + data.order_id + '\nعدد العناصر: ' + data.items_count);
            closeSelectedItemsPopup();
            // إعادة تحميل الصفحة لتحديث القائمة
            window.location.reload();
        } else {
            alert('❌ فشل في إرسال الطلب: ' + data.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> إرسال';
        }
    })
    .catch(error => {
        console.error('خطأ في إرسال الطلب:', error);
        alert('❌ حدث خطأ في الاتصال بالخادم');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> إرسال';
    });
}
</script>

</body>
</html>