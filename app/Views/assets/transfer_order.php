<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تحويل الأصول</title>
    <link rel="stylesheet" href="<?= base_url('public/assets/css/order_details.css') ?>">
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
            accent-color: #3498db;
        }
        .item-card {
            position: relative;
            transition: all 0.3s;
        }
        .item-card.selected {
            border-color: #3498db;
            background: linear-gradient(135deg, rgba(52, 152, 219, 0.05) 0%, rgba(41, 128, 185, 0.05) 100%);
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.2);
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
            accent-color: #3498db;
        }
        .transfer-modal {
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
        .transfer-modal.show {
            opacity: 1;
        }
        .transfer-modal-content {
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
        .transfer-modal-title {
            font-size: 22px;
            color: #3498db;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        @keyframes fadeOut {
            from { opacity: 1; transform: scale(1); }
            to { opacity: 0; transform: scale(0.9); }
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid #e0e6ed;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.2s;
            box-sizing: border-box;
        }
        .form-group textarea:focus {
            outline: none;
            border-color: #3498db;
        }
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        /* Custom Search Select Styles */
        .search-select-container {
            position: relative;
            width: 100%;
        }
        
        .search-select-input {
            width: 100%;
            padding: 12px 40px 12px 12px;
            border: 2px solid #e0e6ed;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.2s;
            box-sizing: border-box;
            cursor: pointer;
            background: white;
        }
        
        .search-select-input:focus {
            outline: none;
            border-color: #3498db;
        }
        
        .search-select-input::placeholder {
            color: #95a5a6;
        }
        
        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #95a5a6;
            pointer-events: none;
        }
        
        .search-dropdown {
            position: absolute;
            top: calc(100% + 5px);
            left: 0;
            right: 0;
            background: white;
            border: 2px solid #e0e6ed;
            border-radius: 8px;
            max-height: 250px;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            display: none;
        }
        
        .search-dropdown.show {
            display: block;
        }
        
        .search-dropdown-item {
            padding: 12px 15px;
            cursor: pointer;
            transition: background 0.2s;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .search-dropdown-item:last-child {
            border-bottom: none;
        }
        
        .search-dropdown-item:hover {
            background: #f8f9fa;
        }
        
        .search-dropdown-item.selected {
            background: #e3f2fd;
        }
        
        .user-name {
            font-weight: 600;
            color: #2c3e50;
            display: block;
            margin-bottom: 3px;
        }
        
        .user-dept {
            font-size: 12px;
            color: #7f8c8d;
        }
        
        .no-results {
            padding: 15px;
            text-align: center;
            color: #95a5a6;
            font-size: 14px;
        }
        
        .bulk-actions-bar {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            padding: 15px 25px;
            border-radius: 50px;
            box-shadow: 0 8px 25px rgba(52, 152, 219, 0.4);
            display: flex;
            align-items: center;
            gap: 20px;
            z-index: 999;
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
        
        .bulk-actions-bar.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }
        
        .bulk-counter {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 8px 16px;
            border-radius: 25px;
            font-weight: bold;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .bulk-actions-buttons {
            display: flex;
            gap: 10px;
        }
        
        .bulk-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }
        
        .bulk-btn-clear {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }
        
        .bulk-btn-clear:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.05);
        }
        
        .bulk-btn-transfer {
            background: white;
            color: #3498db;
        }
        
        .bulk-btn-transfer:hover {
            background: #ecf0f1;
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body>
    <?= $this->include('layouts/header') ?>

    <div class="main-content">
        <div class="header">
            <h1 class="page-title">تحويل الأصول</h1>
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
                <h3 class="section-title">تحويل الأصول</h3>

                <?php if (!empty($items)): ?>

                    <div class="select-all-container">
                        <input type="checkbox" class="master-checkbox" id="masterCheckbox" onchange="toggleAllSelection()">
                        <label for="masterCheckbox">تحديد الكل</label>
                    </div>

                    <div class="items-grid">
                        <?php foreach ($items as $item): ?>
                            <div class="item-card" data-item-order-id="<?= esc($item->item_order_id) ?>">
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
                                        <div class="detail-label">الحالة</div>
                                        <div class="detail-value">
                                            <span class="status-badge status-active"><?= esc($item->usage_status_name) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-items-msg">لا توجد عناصر متاحة للتحويل.</div>
                <?php endif; ?>

                <div class="action-buttons-container">
                    <a href="<?= site_url('AssetsController') ?>" class="action-btn back-btn">
                        <span>العودة</span>
                    </a>
                </div>
                
            </div>
        </div>
    </div>

   

    <div class="bulk-actions-bar" id="bulkActionsBar">
        <div class="bulk-counter">
            <i class="fas fa-check-circle"></i>
            <span id="selectedCount">0</span> عنصر محدد
        </div>
        <div class="bulk-actions-buttons">
            <button class="bulk-btn bulk-btn-clear" onclick="clearAllSelections()">
                <i class="fas fa-times"></i>
                إلغاء التحديد
            </button>
            <button class="bulk-btn bulk-btn-transfer" onclick="showTransferModal()">
                <i class="fas fa-exchange-alt"></i>
                تحويل العناصر المحددة
            </button>
        </div>
    </div>

    <div class="transfer-modal" id="transferModal">
        <div class="transfer-modal-content">
            <div class="transfer-modal-title">
                <i class="fas fa-exchange-alt"></i>
                تحويل الأصول
            </div>
            
            <div id="selectedItemsList" style="max-height: 200px; overflow-y: auto; margin-bottom: 20px; padding: 10px; background: #f8f9fa; border-radius: 8px;"></div>
            
            <div class="form-group">
                <label for="fromUserInput">من (المستخدم المحول):</label>
                <div class="search-select-container">
                    <input 
                        type="text" 
                        id="fromUserInput" 
                        class="search-select-input"
                        placeholder="ابحث بالاسم أو رقم المستخدم..."
                        autocomplete="off"
                        oninput="filterUsers('from')"
                        onfocus="showDropdown('from')"
                    >
                    <span class="search-icon">🔍</span>
                    <div id="fromUserDropdown" class="search-dropdown"></div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="toUserInput">إلى (المستخدم المستلم):</label>
                <div class="search-select-container">
                    <input 
                        type="text" 
                        id="toUserInput" 
                        class="search-select-input"
                        placeholder="ابحث بالاسم أو رقم المستخدم..."
                        autocomplete="off"
                        oninput="filterUsers('to')"
                        onfocus="showDropdown('to')"
                    >
                    <span class="search-icon">🔍</span>
                    <div id="toUserDropdown" class="search-dropdown"></div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="transferNote">ملاحظات التحويل:</label>
                <textarea id="transferNote" placeholder="أضف ملاحظات حول سبب التحويل أو حالة الأصول..."></textarea>
            </div>
            
            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                <button onclick="closeTransferModal()" style="padding: 10px 25px; background: #6c757d; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">إلغاء</button>
                <button onclick="submitTransfer()" style="padding: 10px 25px; background: #3498db; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">تأكيد التحويل</button>
            </div>
        </div>
    </div>

    <script>
        // Users data from PHP
        const usersData = <?= json_encode(array_map(function($user) {
            return [
                'user_id' => $user->user_id,
                'name' => $user->name,
                'dept' => $user->user_dept ?? ''
            ];
        }, $users)) ?>;
        
        let selectedFromUser = null;
        let selectedToUser = null;
        let selectedItems = [];

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-select-container')) {
                document.querySelectorAll('.search-dropdown').forEach(d => d.classList.remove('show'));
            }
        });

        function showDropdown(type) {
            const dropdown = document.getElementById(type + 'UserDropdown');
            filterUsers(type);
            dropdown.classList.add('show');
        }

        function filterUsers(type) {
            const input = document.getElementById(type + 'UserInput');
            const dropdown = document.getElementById(type + 'UserDropdown');
            const searchTerm = input.value.toLowerCase().trim();
            
            const filteredUsers = usersData.filter(user => {
                const nameMatch = user.name.toLowerCase().includes(searchTerm);
                const deptMatch = user.dept.toLowerCase().includes(searchTerm);
                const idMatch = user.user_id.toLowerCase().includes(searchTerm);
                return nameMatch || deptMatch || idMatch;
            });
            
            if (filteredUsers.length === 0) {
                dropdown.innerHTML = '<div class="no-results">لا توجد نتائج</div>';
            } else {
                dropdown.innerHTML = filteredUsers.map(user => `
                    <div class="search-dropdown-item" onclick="selectUser('${type}', '${user.user_id}', '${user.name}', '${user.dept}')">
                        <span class="user-name">${user.name}</span>
                        <span class="user-dept">القسم: ${user.dept || 'غير محدد'} | ID: ${user.user_id}</span>
                    </div>
                `).join('');
            }
            
            dropdown.classList.add('show');
        }

        function selectUser(type, userId, userName, dept) {
            const input = document.getElementById(type + 'UserInput');
            const dropdown = document.getElementById(type + 'UserDropdown');
            
            input.value = `${userName} (${dept || 'بدون قسم'})`;
            dropdown.classList.remove('show');
            
            if (type === 'from') {
                selectedFromUser = userId;
            } else {
                selectedToUser = userId;
            }
        }

        function updateSelection() {
            selectedItems = [];
            const checkboxes = document.querySelectorAll('.item-checkbox:checked');
            
            checkboxes.forEach(cb => {
                const card = cb.closest('.item-card');
                const itemOrderId = card.getAttribute('data-item-order-id');
                const name = card.querySelector('.item-name').textContent;
                
                selectedItems.push({
                    id: itemOrderId,
                    name: name
                });
                card.classList.add('selected');
            });
            
            document.querySelectorAll('.item-card').forEach(c => {
                if (!c.querySelector('.item-checkbox').checked) c.classList.remove('selected');
            });
            
            updateMasterCheckbox();
            updateTransferButton();
        }

        function updateTransferButton() {
            const bar = document.getElementById('bulkActionsBar');
            const counter = document.getElementById('selectedCount');
            
            counter.textContent = selectedItems.length;
            
            if (selectedItems.length > 0) {
                bar.classList.add('show');
            } else {
                bar.classList.remove('show');
            }
        }

        function clearAllSelections() {
            document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = false);
            document.querySelectorAll('.item-card').forEach(c => c.classList.remove('selected'));
            document.getElementById('masterCheckbox').checked = false;
            selectedItems = [];
            updateTransferButton();
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

        function showTransferModal() {
            const modal = document.getElementById('transferModal');
            const list = document.getElementById('selectedItemsList');
            
            list.innerHTML = '<strong>الأصول المحددة:</strong><br>' + 
                selectedItems.map((item, i) => `${i + 1}. ${item.name}`).join('<br>');
            
            // Reset selections
            selectedFromUser = null;
            selectedToUser = null;
            document.getElementById('fromUserInput').value = '';
            document.getElementById('toUserInput').value = '';
            document.getElementById('transferNote').value = '';
            
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('show'), 10);
        }

        function closeTransferModal() {
            const modal = document.getElementById('transferModal');
            modal.classList.remove('show');
            setTimeout(() => modal.style.display = 'none', 300);
            
            document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = false);
            document.querySelectorAll('.item-card').forEach(c => c.classList.remove('selected'));
            document.querySelectorAll('.search-dropdown').forEach(d => d.classList.remove('show'));
            selectedItems = [];
            updateMasterCheckbox();
            updateTransferButton();
        }

        function submitTransfer() {
            const note = document.getElementById('transferNote').value;
            
            if (!selectedFromUser) {
                showAlert('warning', 'يرجى اختيار المستخدم المحول');
                return;
            }
            
            if (!selectedToUser) {
                showAlert('warning', 'يرجى اختيار المستخدم المستلم');
                return;
            }
            
            if (selectedFromUser === selectedToUser) {
                showAlert('warning', 'لا يمكن تحويل الأصول لنفس المستخدم');
                return;
            }
            
            if (selectedItems.length === 0) {
                showAlert('warning', 'لم يتم تحديد أي أصول');
                return;
            }
            
            const transferData = {
                items: selectedItems.map(item => item.id),
                from_user_id: selectedFromUser,
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
                    showAlert('success', 'تم إنشاء طلب التحويل بنجاح. في انتظار قبول المستلم.');
                    closeTransferModal();
                    
                    selectedItems.forEach(item => {
                        const card = document.querySelector(`.item-card[data-item-order-id="${item.id}"]`);
                        if (card) {
                            card.style.animation = 'fadeOut 0.3s ease';
                            setTimeout(() => card.remove(), 300);
                        }
                    });
                } else {
                    showAlert('danger', 'حدث خطأ: ' + (data.message || 'فشل التحويل'));
                }
            })
            .catch(error => {
                showAlert('danger', 'حدث خطأ في الاتصال بالخادم');
                console.error('Error:', error);
            });
        }

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
            }, 5000);
        }
    </script>
</body>
</html>