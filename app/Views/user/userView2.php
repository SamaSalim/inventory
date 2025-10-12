<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>العهد الخاصة بي</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
     <link rel="stylesheet" href="<?= base_url('public/assets/css/transfer-style.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

<style>
    * {
        font-family: 'Cairo', sans-serif;
    }

    body {
        background-color: #EFF8FA;
        margin: 0;
        padding: 0;
    }

    .main-content {
        margin-right: 80px;
        padding: 0;
    }

    .header {
        background-color: white;
        padding: 15px 25px;
        border-bottom: 1px solid #e0e0e0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        min-height: 70px;
    }

    .page-title {
        color: #057590;
        font-size: 22px;
        font-weight: 600;
        margin: 0;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #3AC0C3;
        font-size: 14px;
        cursor: pointer;
    }

    .user-avatar {
        width: 35px;
        height: 35px;
        background-color: #3AC0C3;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 14px;
        font-weight: bold;
    }

    .content-area {
        padding: 25px;
        background-color: #EFF8FA;
        min-height: calc(100vh - 70px);
    }

    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }

    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        display: flex;
        align-items: center;
        gap: 15px;
        transition: transform 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .stat-icon.total {
        background: linear-gradient(135deg, #057590, #3AC0C3);
        color: white;
    }

    .stat-icon.direct {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
    }

    .stat-icon.transferred {
        background: linear-gradient(135deg, #007bff, #17a2b8);
        color: white;
    }

    .stat-info h3 {
        font-size: 28px;
        font-weight: 700;
        color: #057590;
        margin: 0;
    }

    .stat-info p {
        margin: 0;
        color: #666;
        font-size: 14px;
    }

    .custom-btn {
        background-color: #057590;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 14px;
        font-weight: 500;
    }

    .custom-btn:hover {
        background-color: #045d6e;
        transform: translateY(-1px);
    }

    .custom-btn.active {
        background-color: #3AC0C3;
    }

    .table-container {
        background-color: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        margin-top: 20px;
    }

    .custom-table {
        width: 100%;
        margin: 0;
        border-collapse: collapse;
        font-size: 13px;
    }

    .custom-table thead th {
        background-color: #057590;
        color: white;
        font-weight: 600;
        padding: 15px 12px;
        border: none;
        text-align: center;
        white-space: nowrap;
    }

    .custom-table tbody td {
        padding: 14px 12px;
        border-bottom: 1px solid #f0f0f0;
        text-align: center;
        color: #555;
        vertical-align: middle;
        font-size: 12px;
    }

    .custom-table tbody tr:hover {
        background-color: rgba(5, 117, 144, 0.05);
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: center;
    }

    .action-btn {
        padding: 8px 16px;
        border-radius: 16px;
        border: none;
        font-size: 11px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .transfer-btn {
        background: #3AC0C3;
        color: white;
        box-shadow: 0 2px 6px rgba(5, 117, 144, 0.25);
    }

    .return-btn {
        background: #2269a7ff;
        color: white;
        box-shadow: 0 2px 6px rgba(5, 117, 144, 0.25);
        text-decoration: none;
    }

    .btn-icon {
        width: 12px;
        height: 12px;
        fill: currentColor;
    }

    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: bold;
        text-transform: uppercase;
    }

    .status-new {
        background: #d4edda;
        color: #155724;
    }

    .status-transfer {
        background: #cfe2ff;
        color: #084298;
    }

    .order-status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .order-status-accepted {
        background: #d4edda;
        color: #155724;
    }

    .source-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: bold;
    }

    .source-direct {
        background: linear-gradient(135deg, #d4edda, #c3e6cb);
        color: #155724;
        border: 1px solid #b1dfbb;
    }

    .source-transfer {
        background: linear-gradient(135deg, #cfe2ff, #b6d4fe);
        color: #084298;
        border: 1px solid #9ec5fe;
    }

    .filter-buttons {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    /* Return Modal Styles */
    .delete-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .delete-modal.show {
        opacity: 1;
    }

    .delete-modal-content {
        background: white;
        border-radius: 15px;
        padding: 0;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        animation: modalSlideIn 0.3s ease;
    }

    @keyframes modalSlideIn {
        from { transform: translateY(-50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .delete-modal-title {
        background: linear-gradient(135deg, #057590, #3AC0C3);
        color: white;
        padding: 20px;
        border-radius: 15px 15px 0 0;
        font-size: 18px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .delete-modal-message {
        padding: 30px 20px;
        color: #333;
        font-size: 15px;
        max-height: 400px;
        overflow-y: auto;
    }

    .delete-modal-actions {
        padding: 15px 20px;
        background: #f8f9fa;
        border-top: 2px solid #e0e6ed;
        display: flex;
        justify-content: space-between;
        gap: 10px;
        border-radius: 0 0 15px 15px;
    }

    .confirm-btn {
        flex: 1;
        padding: 12px 20px;
        border: none;
        border-radius: 20px;
        cursor: pointer;
        font-weight: bold;
        font-size: 14px;
        transition: all 0.2s;
    }

    .confirm-cancel-btn {
        background: #95a5a6;
        color: white;
    }

    .confirm-cancel-btn:hover {
        background: #7f8c8d;
        transform: translateY(-1px);
    }

    .confirm-delete-btn {
        background: linear-gradient(135deg, #3ac0c3, #2aa8ab);
        color: white;
        box-shadow: 0 2px 8px rgba(58, 192, 195, 0.3);
    }

    .confirm-delete-btn:hover {
        background: linear-gradient(135deg, #2aa8ab, #259a9d);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(58, 192, 195, 0.4);
    }

    .selected-items-popup {
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
    }

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

    #alertContainer {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        max-width: 400px;
    }

    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s ease;
    }

    .alert.show {
        opacity: 1;
        transform: translateX(0);
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border-left: 4px solid #28a745;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border-left: 4px solid #dc3545;
    }

    .alert-warning {
        background: #fff3cd;
        color: #856404;
        border-left: 4px solid #ffc107;
    }
</style>
</head>

<body>
    <?= $this->include('layouts/header') ?>

    <div id="alertContainer"></div>

    <div class="main-content">
        <div class="header">
            <h1 class="page-title"><i class="fas fa-handshake"></i> العهد الخاصة بي</h1>
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
            <?php
            $totalCount = count($orders ?? []);
            $directCount = count(array_filter($orders ?? [], fn($o) => ($o->source_table ?? '') === 'orders'));
            $transferCount = count(array_filter($orders ?? [], fn($o) => ($o->source_table ?? '') === 'transfer_items'));
            ?>

            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-icon total">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $totalCount ?></h3>
                        <p>إجمالي العهد</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon direct">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $directCount ?></h3>
                        <p>العهد المباشرة</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon transferred">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $transferCount ?></h3>
                        <p>العهد المحولة</p>
                    </div>
                </div>
            </div>

            <div class="filter-buttons">
                <button class="custom-btn active" onclick="filterBySource('all')">
                    <i class="fas fa-list"></i> الكل
                </button>
                <button class="custom-btn" onclick="filterBySource('orders')">
                    <i class="fas fa-plus-circle"></i> المباشرة فقط
                </button>
                <button class="custom-btn" onclick="filterBySource('transfer_items')">
                    <i class="fas fa-exchange-alt"></i> المحولة فقط
                </button>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" id="searchInput" class="form-control" placeholder="ابحث في كل الأعمدة...">
                </div>
                <div class="col-md-4">
                    <input type="text" id="startDate" class="form-control" placeholder="من تاريخ">
                </div>
                <div class="col-md-4">
                    <input type="text" id="endDate" class="form-control" placeholder="إلى تاريخ">
                </div>
            </div>

            <div class="table-container">
                <table class="custom-table" id="covenantsTable">
                    <thead>
                        <tr>
                            <th>رقم الأصل</th>
                            <th>الرقم التسلسلي</th>
                            <th>نوع المصدر</th>
                            <th>حالة الاستخدام</th>
                            <th>حالة الطلب</th>
                            <th>تاريخ الإنشاء</th>
                            <th>عمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($orders) && !empty($orders)): ?>
                            <?php foreach ($orders as $order): ?>
                                <tr data-source="<?= esc($order->source_table ?? '') ?>" 
                                    data-item-order-id="<?= esc($order->item_order_id ?? $order->id) ?>"
                                    data-asset-num="<?= esc($order->asset_num ?? '-') ?>"
                                    data-serial-num="<?= esc($order->serial_num ?? '-') ?>">
                                    <td><?= esc($order->asset_num ?? '-') ?></td>
                                    <td><?= esc($order->serial_num ?? '-') ?></td>
                                    <td>
                                        <span class="source-badge source-<?= ($order->source_table ?? '') === 'orders' ? 'direct' : 'transfer' ?>">
                                            <?= ($order->source_table ?? '') === 'orders' ? 'مباشر' : 'محول' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        $usageClass = 'status-new';
                                        $usageStatus = $order->usage_status_name ?? '';
                                        if ($usageStatus == 'تحويل') $usageClass = 'status-transfer';
                                        ?>
                                        <span class="status-badge <?= $usageClass ?>">
                                            <?= esc($usageStatus ?: '-') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        $orderClass = 'order-status-pending';
                                        $orderStatus = $order->order_status_name ?? '';
                                        if ($orderStatus == 'مقبول') $orderClass = 'order-status-accepted';
                                        if ($orderStatus == 'مرفوض') $orderClass = 'order-status-rejected';
                                        ?>
                                        <span class="status-badge <?= $orderClass ?>">
                                            <?= esc($orderStatus ?: '-') ?>
                                        </span>
                                    </td>
                                    <td><?= isset($order->created_at) ? date('d/m/Y', strtotime($order->created_at)) : '-' ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button onclick="openTransferPopup(<?= $order->item_order_id ?? $order->id ?>)" class="action-btn transfer-btn">
                                                <i class="fas fa-exchange-alt"></i>
                                                تحويل
                                            </button>
                                            <button onclick="openReturnPopup(<?= $order->item_order_id ?? $order->id ?>)" class="action-btn return-btn">
                                                <svg class="btn-icon" viewBox="0 0 24 24">
                                                    <path d="M9 11H3v2h6v3l5-4-5-4v3zm12-8h-6c-1.1 0-2 .9-2 2v3h2V5h6v14h-6v-3h-2v3c0 1.1.9 2 2 2h6c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z" />
                                                </svg>
                                                إرجاع
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">لا توجد عهد مسجلة</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Transfer Modal -->
    <div class="transfer-modal" id="transferModal">
        <div class="transfer-modal-content">
            <div class="transfer-modal-title">
                <i class="fas fa-exchange-alt"></i>
                تحويل العهدة
            </div>
            
            <div id="selectedTransferItem" style="max-height: 150px; overflow-y: auto; margin-bottom: 20px; padding: 12px; background: #f8f9fa; border-radius: 8px; border: 2px solid #e0e6ed;"></div>
            
            <div class="form-group">
                <label for="toUserInput">تحويل إلى:</label>
                <div class="search-select-container">
                    <input 
                        type="text" 
                        id="toUserInput" 
                        class="search-select-input"
                        placeholder="ابحث بالاسم أو القسم..."
                        autocomplete="off"
                        oninput="filterTransferUsers()"
                        onfocus="showTransferDropdown()"
                    >
                    <span class="search-icon">🔍</span>
                    <div id="toUserDropdown" class="search-dropdown"></div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="transferNote">ملاحظات التحويل (اختياري):</label>
                <textarea id="transferNote" placeholder="أضف ملاحظات حول سبب التحويل أو حالة العهدة..."></textarea>
            </div>
            
            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                <button onclick="closeTransferModal()" style="padding: 10px 25px; background: #6c757d; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">إلغاء</button>
                <button onclick="submitTransferSingle()" style="padding: 10px 25px; background: #3AC0C3; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">تأكيد التحويل</button>
            </div>
        </div>
    </div>

    <!-- Return Confirmation Modal -->
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
let selectedToUser = null;
let currentTransferItemId = null;

// Users data - you'll need to pass this from PHP
const transferUsersData = <?= json_encode(array_map(function($user) {
    return [
        'user_id' => $user->user_id,
        'name' => $user->name,
        'dept' => $user->user_dept ?? ''
    ];
}, $users ?? [])) ?>;

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.search-select-container')) {
        document.querySelectorAll('.search-dropdown').forEach(d => d.classList.remove('show'));
    }
});

// Transfer functionality
function openTransferPopup(itemOrderId) {
    const row = document.querySelector(`tr[data-item-order-id="${itemOrderId}"]`);
    if (!row) {
        showAlert('error', 'لم يتم العثور على العنصر');
        return;
    }

    const assetNum = row.dataset.assetNum;
    const serialNum = row.dataset.serialNum;
    
    currentTransferItemId = itemOrderId;
    
    const itemInfo = document.getElementById('selectedTransferItem');
    itemInfo.innerHTML = `
        <div style="font-weight: bold; color: #057590; margin-bottom: 8px; font-size: 16px;">
            العهدة المحددة للتحويل:
        </div>
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; font-size: 14px; color: #555;">
            <div><span style="color: #888;">رقم الأصل:</span> ${assetNum}</div>
            <div><span style="color: #888;">الرقم التسلسلي:</span> ${serialNum}</div>
        </div>
    `;
    
    // Reset selections
    selectedToUser = null;
    document.getElementById('toUserInput').value = '';
    document.getElementById('transferNote').value = '';
    
    const modal = document.getElementById('transferModal');
    modal.style.display = 'flex';
    setTimeout(() => modal.classList.add('show'), 10);
}

function closeTransferModal() {
    const modal = document.getElementById('transferModal');
    modal.classList.remove('show');
    setTimeout(() => {
        modal.style.display = 'none';
        currentTransferItemId = null;
        selectedToUser = null;
    }, 300);
    
    document.querySelectorAll('.search-dropdown').forEach(d => d.classList.remove('show'));
}

function showTransferDropdown() {
    filterTransferUsers();
    document.getElementById('toUserDropdown').classList.add('show');
}

function filterTransferUsers() {
    const input = document.getElementById('toUserInput');
    const dropdown = document.getElementById('toUserDropdown');
    const searchTerm = input.value.toLowerCase().trim();
    
    const filteredUsers = transferUsersData.filter(user => {
        const nameMatch = user.name.toLowerCase().includes(searchTerm);
        const deptMatch = user.dept.toLowerCase().includes(searchTerm);
        const idMatch = user.user_id.toLowerCase().includes(searchTerm);
        return nameMatch || deptMatch || idMatch;
    });
    
    if (filteredUsers.length === 0) {
        dropdown.innerHTML = '<div class="no-results">لا توجد نتائج</div>';
    } else {
        dropdown.innerHTML = filteredUsers.map(user => `
            <div class="search-dropdown-item" onclick="selectTransferUser('${user.user_id}', '${user.name}', '${user.dept}')">
                <span class="user-name">${user.name}</span>
                <span class="user-dept">القسم: ${user.dept || 'غير محدد'} | ID: ${user.user_id}</span>
            </div>
        `).join('');
    }
    
    dropdown.classList.add('show');
}

function selectTransferUser(userId, userName, dept) {
    const input = document.getElementById('toUserInput');
    const dropdown = document.getElementById('toUserDropdown');
    
    input.value = `${userName} (${dept || 'بدون قسم'})`;
    dropdown.classList.remove('show');
    
    selectedToUser = userId;
}

function submitTransferSingle() {
    const note = document.getElementById('transferNote').value;
    
    if (!selectedToUser) {
        showAlert('warning', 'يرجى اختيار المستخدم المستلم');
        return;
    }
    
    if (!currentTransferItemId) {
        showAlert('warning', 'لم يتم تحديد العهدة');
        return;
    }
    
    // Get current user ID from session
    const currentUserId = '<?= session()->get('user_id') ?>';
    
    if (selectedToUser === currentUserId) {
        showAlert('warning', 'لا يمكن تحويل العهدة لنفسك');
        return;
    }
    
    const transferData = {
        items: [currentTransferItemId],
        from_user_id: currentUserId,
        to_user_id: selectedToUser,
        note: note
    };
    
    fetch('<?= base_url('AssetsController/processTransfer') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(transferData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'تم إنشاء طلب التحويل بنجاح');
            closeTransferModal();
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showAlert('error', 'حدث خطأ: ' + (data.message || 'فشل التحويل'));
        }
    })
    .catch(error => {
        showAlert('error', 'حدث خطأ في الاتصال بالخادم');
        console.error('Error:', error);
    });
}

// Return popup functionality
function openReturnPopup(itemOrderId) {
    const row = document.querySelector(`tr[data-item-order-id="${itemOrderId}"]`);
    if (!row) {
        showAlert('error', 'لم يتم العثور على العنصر');
        return;
    }

    const assetNum = row.dataset.assetNum;
    const serialNum = row.dataset.serialNum;
    
    selectedItems = [{
        id: itemOrderId,
        name: `أصل ${assetNum}`,
        assetNum: assetNum,
        serialNum: serialNum
    }];
    
    uploadedFiles = {};
    
    showSelectedItemsPopup();
}

function showSelectedItemsPopup() {
    const existingPopup = document.getElementById('selectedItemsPopup');
    if (existingPopup) existingPopup.remove();
    
    const existingBackdrop = document.getElementById('popupBackdrop');
    if (existingBackdrop) existingBackdrop.remove();
    
    const popup = document.createElement('div');
    popup.id = 'selectedItemsPopup';
    popup.className = 'selected-items-popup show';
    
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
                            <div><span style="color: #888;">رقم الأصل:</span> ${item.assetNum}</div>
                            <div><span style="color: #888;">الرقم التسلسلي:</span> ${item.serialNum}</div>
                        </div>
                    </div>
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
                ترجيع العهدة
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
            <button onclick="closeSelectedItemsPopup()" style="
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
                إلغاء
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
    
    document.body.appendChild(backdrop);
    document.body.appendChild(popup);
}

function closeSelectedItemsPopup() {
    const popup = document.getElementById('selectedItemsPopup');
    const backdrop = document.getElementById('popupBackdrop');
    
    if (popup) popup.remove();
    if (backdrop) backdrop.remove();
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
            marginRetryClaude does not have the ability to run the code it generates yet.LContinuejavascript-bottom: 6px;
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
            هل أنت متأكد من ترجيع هذه العهدة؟
        </strong>
    `;
    
    modal.style.display = 'flex';
    setTimeout(() => modal.classList.add('show'), 10);
    
    window.tempReturnData = returnData;
    
    closeSelectedItemsPopup();
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.remove('show');
    setTimeout(() => modal.style.display = 'none', 300);
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

// Table filtering functionality
document.addEventListener('DOMContentLoaded', function() {
    flatpickr("#startDate", {dateFormat: "d/m/Y", allowInput: true, onChange: filterTableFast});
    flatpickr("#endDate", {dateFormat: "d/m/Y", allowInput: true, onChange: filterTableFast});
    document.getElementById("searchInput").addEventListener("keyup", filterTableFast);

    const allRows = Array.from(document.querySelectorAll("#covenantsTable tbody tr"));
    window.fastTableData = allRows.map(row => {
        const cells = row.querySelectorAll("td");
        return {
            row: row,
            text: Array.from(cells).map(td => td.textContent.trim().toLowerCase()).join(" "),
            source: row.dataset.source || "",
            date: parseRowDate(cells[5]?.textContent.trim() || "")
        };
    });
    window.currentSourceFilter = 'all';
});

function parseDMY(d) {
    if (!d) return null;
    const p = d.split('/');
    return p.length === 3 ? new Date(p[2], p[1]-1, p[0]) : null;
}

function parseRowDate(t) {
    if (!t) return null;
    const m = t.match(/(\d{1,2}\/\d{1,2}\/\d{4})/);
    if (m) return parseDMY(m[1]);
    const dt = new Date(t);
    return isNaN(dt.getTime()) ? null : dt;
}

function filterTableFast() {
    const search = document.getElementById("searchInput").value.trim().toLowerCase();
    const startStr = document.getElementById("startDate").value.trim();
    const endStr = document.getElementById("endDate").value.trim();

    let start = startStr ? parseDMY(startStr) : null;
    let end = endStr ? parseDMY(endStr) : null;
    if (end) end.setHours(23,59,59,999);

    fastTableData.forEach(item => {
        let visible = true;
        
        if (search && !item.text.includes(search)) visible = false;
        
        if ((start || end) && item.date) {
            if (start && item.date < start) visible = false;
            if (end && item.date > end) visible = false;
        } else if ((start || end) && !item.date) visible = false;
        
        if (currentSourceFilter !== 'all' && item.source !== currentSourceFilter) visible = false;
        
        item.row.style.display = visible ? "" : "none";
    });
}

function filterBySource(source) {
    currentSourceFilter = source;
    document.querySelectorAll('.filter-buttons .custom-btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    filterTableFast();
}
</script>

</body>
</html>