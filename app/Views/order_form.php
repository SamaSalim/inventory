<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($mode) && $mode === 'edit' ? 'تعديل طلب' : 'نموذج طلب جديد' ?></title>
    <style>
        /* نفس تنسيقات CSS الموجودة في ordersCreate.php */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #3b82b6 100%);
            min-height: 100vh;
            direction: rtl;
        }

        .form-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(216, 236, 251, 0.82);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: linear-gradient(135deg, #0f375cff 0%, #2c455bff 50%, #34495e 100%);
            border-radius: 15px;
            width: 90%;
            max-width: 900px;
            max-height: 95vh;
            overflow-y: auto;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            padding: 20px 30px;
            background: linear-gradient(135deg, #3b82b6 0%, #2a5298 100%);
            border-radius: 15px 15px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(59, 130, 182, 0.3);
        }

        .modal-title {
            color: #fff;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .close-btn {
            background: none;
            border: none;
            color: #fff;
            font-size: 2rem;
            cursor: pointer;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s;
        }

        .close-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        form {
            padding: 30px;
        }

        .section-header {
            background: linear-gradient(135deg, rgba(59, 130, 182, 0.2) 0%, rgba(42, 82, 152, 0.15) 100%);
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #3b82b6;
        }

        .section-header h4 {
            color: white;
            margin: 0;
            font-size: 1.1rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            color: #ecf0f1;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 0.95rem;
        }

        input,
        select,
        textarea {
            padding: 12px 16px;
            border: 2px solid rgba(59, 130, 182, 0.3);
            background: rgba(232, 244, 253, 0.05);
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #3b82b6;
            background: rgba(59, 130, 182, 0.15);
            box-shadow: 0 0 0 3px rgba(59, 130, 182, 0.2);
        }

        input::placeholder,
        textarea::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        input:read-only,
        select:disabled {
            background: rgba(26, 37, 47, 0.3);
            cursor: not-allowed;
            opacity: 0.7;
        }

        select option {
            background: #2c3e50;
            color: #fff;
        }

        .search-dropdown {
            position: relative;
        }

        .search-input {
            width: 100%;
        }

        .dropdown-list {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #1a252f 0%, #2c3e50 100%);
            border: 2px solid rgba(59, 130, 182, 0.4);
            border-top: none;
            border-radius: 0 0 8px 8px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1001;
            display: none;
        }

        .dropdown-item {
            padding: 12px 16px;
            cursor: pointer;
            color: #ecf0f1;
            border-bottom: 1px solid rgba(59, 130, 182, 0.2);
        }

        .dropdown-item:hover {
            background: rgba(59, 130, 182, 0.3);
        }

        .dropdown-item:last-child {
            border-bottom: none;
        }

        .dropdown-item.loading {
            color: #3b82b6;
            cursor: default;
            font-style: italic;
        }

        .dropdown-item.no-results,
        .dropdown-item.error {
            color: #e74c3c;
            cursor: default;
        }

        .quantity-section {
            margin-bottom: 30px;
        }

        .asset-serial-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            padding: 20px;
            background: linear-gradient(135deg, rgba(59, 130, 182, 0.1) 0%, rgba(26, 37, 47, 0.2) 100%);
            border-radius: 8px;
            border: 1px solid rgba(59, 130, 182, 0.3);
            margin-bottom: 15px;
        }

        .asset-serial-header {
            grid-column: 1 / -1;
            color: #3b82b6;
            font-weight: 600;
            margin-bottom: 10px;
            text-align: center;
            font-size: 1rem;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid rgba(59, 130, 182, 0.3);
        }

        .cancel-btn,
        .submit-btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 120px;
        }

        .cancel-btn {
            background: rgba(231, 76, 60, 0.8);
            color: white;
        }

        .cancel-btn:hover {
            background: rgba(231, 76, 60, 1);
            transform: translateY(-2px);
        }

        .submit-btn {
            background: linear-gradient(45deg, #3b82b6, #2a5298);
            color: white;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(59, 130, 182, 0.3);
        }

        hr {
            margin: 30px 0;
            border: 1px solid rgba(59, 130, 182, 0.4);
        }

        .required {
            color: #e74c3c;
        }

        .dynamic-fields {
            display: none;
        }

        .dynamic-fields.show {
            display: block;
        }

        .status-message {
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .loading-msg {
            color: #3b82b6;
        }

        .error-msg {
            color: #e74c3c;
        }

        .success-msg {
            color: #27ae60;
        }

        .debug-info {
            position: fixed;
            top: 10px;
            left: 10px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 10px;
            border-radius: 5px;
            font-size: 12px;
            z-index: 9999;
            max-width: 300px;
        }

        /* إضافة تنسيق للعناصر الاختيارية */
        .optional-section {
            background: rgba(46, 204, 113, 0.1);
            border: 2px dashed rgba(46, 204, 113, 0.3);
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }

        .optional-label {
            color: #2ecc71;
            font-weight: bold;
            margin-bottom: 10px;
            display: block;
            text-align: center;
            font-size: 1.1rem;
        }
    </style>
</head>

<body>
    <div id="debugInfo" class="debug-info" style="display: none;">
        <div>Status: <span id="debugStatus">جاهز</span></div>
        <div>Last Request: <span id="debugRequest">لا يوجد</span></div>
        <div>Response: <span id="debugResponse">لا يوجد</span></div>
    </div>

    <div class="form-modal" id="orderModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                    <?php if (isset($mode) && $mode === 'edit' && !empty($order)): ?>
                        تعديل الطلب رقم <?= esc($order->order_id) ?>
                    <?php else: ?>
                        إنشاء طلب جديد
                    <?php endif; ?>
                </h3>
                <button class="close-btn" onclick="closeOrderForm()">&times;</button>
            </div>

            <form id="orderForm" action="<?= isset($mode) && $mode === 'edit' && !empty($order) ? base_url('InventoryController/updateOrder/' . $order->order_id) : base_url('InventoryController/store') ?>">
                <?php if (isset($mode) && $mode === 'edit'): ?>
                    <input type="hidden" name="mode" value="edit">
                    <input type="hidden" name="order_id" value="<?= $order->order_id ?>">
                <?php endif; ?>

                <input type="hidden" name="from_employee_id" value="<?= esc(session()->get('employee_id')) ?>">

                <div class="section-header">
                    <h4>بيانات المسلم (منشئ الطلب)</h4>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>الرقم الوظيفي</label>
                        <input type="text" value="<?= esc(session()->get('employee_id')) ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>اسم المسلم</label>
                        <input type="text" value="<?= esc(session()->get('name')) ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>البريد الإلكتروني</label>
                        <input type="email" value="<?= esc(session()->get('email')) ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>رقم التحويلة</label>
                        <input type="text" value="<?= esc(session()->get('transfer_number')) ?>" readonly>
                    </div>
                </div>

                <hr>

                <div class="section-header">
                    <h4>بيانات المستلم</h4>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>الرقم الوظيفي <span class="required">*</span></label>
                        <input type="text" name="to_employee_id" id="employeeId" placeholder="أدخل الرقم الوظيفي" required value="<?= isset($order) ? esc($order->to_employee_id) : '' ?>">
                        <div id="employeeLoadingMsg" class="status-message loading-msg" style="display: none;">جاري البحث...</div>
                        <div id="employeeErrorMsg" class="status-message error-msg" style="display: none;">الرقم الوظيفي غير موجود</div>
                        <div id="employeeSuccessMsg" class="status-message success-msg" style="display: none;">تم العثور على الموظف</div>
                    </div>
                    <div class="form-group">
                        <label>اسم المستلم <span class="required">*</span></label>
                        <input type="text" name="receiver_name" id="receiverName" placeholder="أدخل اسم المستلم" required readonly value="<?= isset($order) ? esc($order->name) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label>البريد الإلكتروني</label>
                        <input type="email" name="email" id="employeeEmail" placeholder="أدخل البريد الإلكتروني" readonly value="<?= isset($order) ? esc($order->email) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label>رقم التحويلة</label>
                        <input type="text" name="transfer_number" id="transferNumber" placeholder="أدخل رقم التحويلة" readonly value="<?= isset($order) ? esc($order->transfer_number) : '' ?>">
                    </div>
                </div>

                <hr>

                <div class="section-header">
                    <h4>موقع المستلم</h4>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>المبنى <span class="required">*</span></label>
                        <select name="building" id="buildingSelect" required>
                            <option value="">اختر المبنى</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>الطابق <span class="required">*</span></label>
                        <select name="floor" id="floorSelect" required disabled>
                            <option value="">اختر الطابق</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>رقم الغرفة <span class="required">*</span></label>
                        <select name="room" id="roomSelect" required disabled>
                            <option value="">اختر الغرفة</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>القسم</label>
                        <select name="department" id="departmentSelect">
                            <option value="">اختر القسم</option>
                        </select>
                    </div>
                </div>

                <?php if (isset($mode) && $mode === 'edit' && !empty($orderItems)): ?>
                    <hr>
                    <div class="section-header">
                        <h4>العناصر الحالية في الطلب</h4>
                    </div>
                    <div id="existingItemsContainer">
                        <?php foreach ($orderItems as $item): ?>
                            <div class="asset-serial-grid existing-item" data-item-id="<?= $item->item_order_id ?>">
                                <div class="asset-serial-header">العنصر: <?= esc($item->item_name) ?></div>
                                <input type="hidden" name="existing_item_id[<?= $item->item_order_id ?>]" value="<?= esc($item->item_id) ?>">
                                <div class="form-group">
                                    <label>رقم الأصول <span class="required">*</span></label>
                                    <input type="text" name="existing_asset_num[<?= $item->item_order_id ?>]" value="<?= esc($item->asset_num) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>الرقم التسلسلي <span class="required">*</span></label>
                                    <input type="text" name="existing_serial_num[<?= $item->item_order_id ?>]" value="<?= esc($item->serial_num) ?>" required>
                                </div>
                                <div class="form-group full-width">
                                    <label>ملاحظات العنصر</label>
                                    <textarea name="existing_notes[<?= $item->item_order_id ?>]" rows="2"><?= esc($item->note) ?></textarea>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="optional-section">
                        <span class="optional-label">إضافة عنصر جديد (اختياري)</span>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>الصنف</label>
                                <div class="search-dropdown">
                                    <input type="text" name="new_item" class="search-input" id="itemSearchInput" placeholder="ابحث عن الصنف..." autocomplete="off">
                                    <div class="dropdown-list" id="itemDropdown"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>رقم الأصول</label>
                                <input type="text" name="new_asset_num" id="newAssetNum" placeholder="أدخل رقم الأصول">
                            </div>
                            <div class="form-group">
                                <label>الرقم التسلسلي</label>
                                <input type="text" name="new_serial_num" id="newSerialNum" placeholder="أدخل الرقم التسلسلي">
                            </div>
                            <div class="form-group full-width">
                                <label>ملاحظات العنصر الجديد</label>
                                <textarea name="new_item_notes" rows="2" placeholder="أدخل أي ملاحظات للعنصر الجديد"></textarea>
                            </div>
                        </div>
                    </div>

                <?php else: ?>
                    <hr>
                    <div class="section-header">
                        <h4>بيانات الأصناف</h4>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>الصنف <span class="required">*</span></label>
                            <div class="search-dropdown">
                                <input type="text" name="item" class="search-input" id="itemSearchInput" placeholder="ابحث عن الصنف..." autocomplete="off" required>
                                <div class="dropdown-list" id="itemDropdown"></div>
                                <input type="hidden" name="item_id" id="hiddenItemId">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>التصنيف <span class="required">*</span></label>
                            <select name="category" id="categorySelect" required>
                                <option value="">اختر التصنيف</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>الكمية <span class="required">*</span></label>
                            <input type="number" name="quantity" min="1" max="100" placeholder="أدخل الكمية" required>
                        </div>
                    </div>
                    <div class="dynamic-fields" id="dynamicFields">
                        <div id="assetSerialContainer"></div>
                    </div>
                <?php endif; ?>

                <div class="form-group full-width">
                    <label>ملاحظات إضافية على الطلب</label>
                    <textarea name="notes" rows="3" placeholder="أدخل أي ملاحظات إضافية للطلب"><?= isset($order) ? esc($order->note) : '' ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="cancel-btn" onclick="closeOrderForm()">إلغاء</button>
                    <button type="submit" class="submit-btn"><?= isset($mode) && $mode === 'edit' ? 'حفظ التغييرات' : 'إنشاء الطلب' ?></button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let searchTimeout;
        const debugMode = true;

        function updateDebugInfo(status, request, response) {
            if (!debugMode) return;
            const debugInfo = document.getElementById('debugInfo');
            if (debugInfo) {
                debugInfo.style.display = 'block';
                document.getElementById('debugStatus').textContent = status;
                document.getElementById('debugRequest').textContent = request;
                document.getElementById('debugResponse').textContent = JSON.stringify(response).substring(0, 100) + '...';
            }
        }

        function openOrderForm() {
            const modal = document.getElementById('orderModal');
            if (modal) modal.style.display = 'flex';
        }

        function closeOrderForm() {
            const modal = document.getElementById('orderModal');
            if (modal) modal.style.display = 'none';
        }

        function createAssetSerialFields(quantity) {
            const container = document.getElementById('assetSerialContainer');
            const dynamicSection = document.getElementById('dynamicFields');
            container.innerHTML = '';
            if (quantity > 0) {
                for (let i = 1; i <= quantity; i++) {
                    const fieldDiv = document.createElement('div');
                    fieldDiv.className = 'asset-serial-grid';
                    fieldDiv.innerHTML = `
                        <div class="asset-serial-header">العنصر رقم ${i}</div>
                        <div class="form-group">
                            <label>رقم الأصول <span class="required">*</span></label>
                            <input type="text" name="asset_num_${i}" placeholder="أدخل رقم الأصول" required>
                        </div>
                        <div class="form-group">
                            <label>الرقم التسلسلي <span class="required">*</span></label>
                            <input type="text" name="serial_num_${i}" placeholder="أدخل الرقم التسلسلي" required>
                        </div>
                    `;
                    container.appendChild(fieldDiv);
                }
                dynamicSection.classList.add('show');
            } else {
                dynamicSection.classList.remove('show');
            }
        }

        function initItemSearch() {
            const searchInput = document.getElementById('itemSearchInput');
            const dropdown = document.getElementById('itemDropdown');

            if (!searchInput || !dropdown) return;

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.trim();
                if (searchTerm.length < 2) {
                    dropdown.style.display = 'none';
                    return;
                }
                dropdown.innerHTML = '<div class="dropdown-item loading">جاري البحث...</div>';
                dropdown.style.display = 'block';
                updateDebugInfo('البحث في الأصناف...', `searchitems?term=${searchTerm}`, 'انتظار...');

                fetch(`<?= base_url('InventoryController/searchitems') ?>?term=${encodeURIComponent(searchTerm)}`)
                    .then(response => response.json())
                    .then(data => {
                        dropdown.innerHTML = '';
                        if (data.success && data.data && data.data.length > 0) {
                            dropdown.innerHTML = data.data.map(item => `<div class="dropdown-item" data-item-name="${item}">${item}</div>`).join('');
                            dropdown.style.display = 'block';
                        } else {
                            dropdown.innerHTML = '<div class="dropdown-item no-results">لا توجد نتائج</div>';
                            dropdown.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        console.error('خطأ في البحث عن الأصناف:', error);
                        dropdown.innerHTML = '<div class="dropdown-item error">خطأ في البحث</div>';
                        dropdown.style.display = 'block';
                    });
            });

            dropdown.addEventListener('click', function(e) {
                if (e.target.classList.contains('dropdown-item') && !e.target.classList.contains('loading') && !e.target.classList.contains('no-results') && !e.target.classList.contains('error')) {
                    const itemName = e.target.getAttribute('data-item-name');
                    searchInput.value = itemName;
                    dropdown.style.display = 'none';
                }
            });

            document.addEventListener('click', function(e) {
                if (!e.target.closest('.search-dropdown')) {
                    dropdown.style.display = 'none';
                }
            });
        }

        function initQuantityHandler() {
            const quantityInput = document.querySelector('input[name="quantity"]');
            if (quantityInput) {
                quantityInput.addEventListener('input', function() {
                    const quantity = parseInt(this.value) || 0;
                    createAssetSerialFields(quantity);
                });
            }
        }

        function initEmployeeSearch() {
            const employeeIdInput = document.getElementById('employeeId');
            const receiverNameInput = document.getElementById('receiverName');
            const emailInput = document.getElementById('employeeEmail');
            const transferInput = document.getElementById('transferNumber');
            const loadingMsg = document.getElementById('employeeLoadingMsg');
            const errorMsg = document.getElementById('employeeErrorMsg');
            const successMsg = document.getElementById('employeeSuccessMsg');

            const mode = document.querySelector('input[name="mode"]')?.value;
            if (mode === 'edit' && employeeIdInput.value) {
                searchEmployee(employeeIdInput.value, true);
            }

            employeeIdInput.addEventListener('input', function() {
                const employeeId = this.value.trim();
                loadingMsg.style.display = 'none';
                errorMsg.style.display = 'none';
                successMsg.style.display = 'none';
                receiverNameInput.value = '';
                emailInput.value = '';
                transferInput.value = '';

                if (employeeId.length < 3) return;

                clearTimeout(searchTimeout);
                loadingMsg.style.display = 'block';
                updateDebugInfo('البحث عن موظف...', `searchemployee?emp_id=${employeeId}`, 'انتظار...');

                searchTimeout = setTimeout(() => {
                    searchEmployee(employeeId);
                }, 500);
            });

            function searchEmployee(employeeId, isInitialLoad = false) {
                fetch(`<?= base_url('InventoryController/searchemployee') ?>?emp_id=${encodeURIComponent(employeeId)}`)
                    .then(response => response.json())
                    .then(data => {
                        loadingMsg.style.display = 'none';
                        if (data.success) {
                            receiverNameInput.value = data.data.name || '';
                            emailInput.value = data.data.email || '';
                            transferInput.value = data.data.transfer_number || '';
                            successMsg.style.display = 'block';
                            receiverNameInput.removeAttribute('readonly');
                            emailInput.removeAttribute('readonly');
                            transferInput.removeAttribute('readonly');
                        } else {
                            errorMsg.textContent = data.message || 'الرقم الوظيفي غير موجود';
                            errorMsg.style.display = 'block';
                            if (!isInitialLoad) {
                                receiverNameInput.setAttribute('readonly', true);
                                emailInput.setAttribute('readonly', true);
                                transferInput.setAttribute('readonly', true);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('خطأ في البحث عن الموظف:', error);
                        loadingMsg.style.display = 'none';
                        errorMsg.textContent = 'خطأ في الاتصال بالخادم';
                        errorMsg.style.display = 'block';
                    });
            }
        }

        function initLocationDropdowns() {
            const buildingSelect = document.getElementById('buildingSelect');
            const floorSelect = document.getElementById('floorSelect');
            const roomSelect = document.getElementById('roomSelect');
            const departmentSelect = document.getElementById('departmentSelect');

            if ('<?= isset($order) ? "true" : "false" ?>' === 'true') {
                loadInitialData().then(() => {
                    const orderBuildingId = '<?= isset($order) ? esc($order->building_id) : '' ?>';
                    const orderFloorId = '<?= isset($order) ? esc($order->floor_id) : '' ?>';
                    const orderRoomId = '<?= isset($order) ? esc($order->room_id) : '' ?>';
                    const orderDept = '<?= isset($order) ? esc($order->department) : '' ?>';

                    if (orderBuildingId) {
                        buildingSelect.value = orderBuildingId;
                        loadFloors(orderBuildingId).then(() => {
                            if (orderFloorId) {
                                floorSelect.value = orderFloorId;
                                loadRoomsBySection(orderFloorId).then(() => {
                                    if (orderRoomId) {
                                        roomSelect.value = orderRoomId;
                                    }
                                });
                            }
                        });
                    }
                    if (orderDept) {
                        departmentSelect.value = orderDept;
                    }
                });
            } else {
                loadInitialData();
            }

            buildingSelect.addEventListener('change', function() {
                loadFloors(this.value);
            });

            floorSelect.addEventListener('change', function() {
                loadRoomsBySection(this.value);
            });

            async function loadInitialData() {
                const response = await fetch(`<?= base_url('InventoryController/getformdata') ?>`);
                const data = await response.json();
                if (data.success) {
                    buildingSelect.innerHTML = '<option value="">اختر المبنى</option>' + data.buildings.map(b => `<option value="${b.id}">${b.code || b.name}</option>`).join('');
                    const categorySelect = document.getElementById('categorySelect');
                    if (categorySelect) {
                        categorySelect.innerHTML = '<option value="">اختر التصنيف</option>' + data.categories.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
                    }
                    departmentSelect.innerHTML = '<option value="">اختر القسم</option>' + data.departments.map(d => `<option value="${d}">${d}</option>`).join('');
                }
            }

            async function loadFloors(buildingId) {
                floorSelect.innerHTML = '<option value="">اختر الطابق</option>';
                roomSelect.innerHTML = '<option value="">اختر الغرفة</option>';
                floorSelect.disabled = !buildingId;
                roomSelect.disabled = true;

                if (!buildingId) return;

                const response = await fetch(`<?= base_url('InventoryController/getfloorsbybuilding') ?>/${buildingId}`);
                const data = await response.json();
                if (data.success && Array.isArray(data.data)) {
                    data.data.forEach(floor => floorSelect.innerHTML += `<option value="${floor.id}">${floor.code || floor.name}</option>`);
                    floorSelect.disabled = false;
                }
            }

            async function loadRoomsBySection(floorId) {
                roomSelect.innerHTML = '<option value="">اختر الغرفة</option>';
                roomSelect.disabled = true;

                if (!floorId) return;

                const response = await fetch(`<?= base_url('InventoryController/getsectionsbyfloor') ?>/${floorId}`);
                const data = await response.json();
                if (data.success && data.data && data.data.length > 0) {
                    const sectionId = data.data[0].id;
                    const roomsResponse = await fetch(`<?= base_url('InventoryController/getroomsbysection') ?>/${sectionId}`);
                    const roomsData = await roomsResponse.json();
                    if (roomsData.success && Array.isArray(roomsData.data)) {
                        roomsData.data.forEach(room => roomSelect.innerHTML += `<option value="${room.id}">${room.code || room.name}</option>`);
                        roomSelect.disabled = false;
                    }
                }
            }
        }

        document.getElementById('orderForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const mode = form.querySelector('input[name="mode"]')?.value;
            const formData = new FormData(form);

            if (mode === 'edit') {
                // لا حاجة لتعديل أسماء الحقول - FormData سيتعامل معها تلقائياً
                // فقط التأكد من وجود البيانات المطلوبة

                // التحقق من العناصر الموجودة
                let hasEmptyFields = false;
                document.querySelectorAll('.existing-item').forEach(itemDiv => {
                    const itemId = itemDiv.getAttribute('data-item-id');
                    const assetNum = itemDiv.querySelector(`input[name="existing_asset_num[${itemId}]"]`).value.trim();
                    const serialNum = itemDiv.querySelector(`input[name="existing_serial_num[${itemId}]"]`).value.trim();

                    if (!assetNum || !serialNum) {
                        hasEmptyFields = true;
                    }
                });

                if (hasEmptyFields) {
                    alert('يجب ملء جميع حقول العناصر الموجودة (رقم الأصول والرقم التسلسلي).');
                    return;
                }

                // التحقق من العنصر الجديد (اختياري)
                const newItemName = form.querySelector('input[name="new_item"]')?.value.trim();
                const newAssetNum = form.querySelector('input[name="new_asset_num"]')?.value.trim();
                const newSerialNum = form.querySelector('input[name="new_serial_num"]')?.value.trim();

                // فقط إذا تم إدخال أي بيانات للعنصر الجديد
                if (newItemName || newAssetNum || newSerialNum) {
                    if (!newItemName || !newAssetNum || !newSerialNum) {
                        alert('يجب ملء جميع حقول العنصر الجديد (الصنف، رقم الأصول، الرقم التسلسلي) أو تركها فارغة تماماً.');
                        return;
                    }
                }
            } else {
                // للطلبات الجديدة
                const newQuantityInput = form.querySelector('input[name="quantity"]');
                const newQuantity = parseInt(newQuantityInput.value) || 0;

                if (newQuantity > 0) {
                    const newItemName = form.querySelector('input[name="item"]').value.trim();
                    if (!newItemName) {
                        alert('يجب اختيار صنف للعناصر الجديدة.');
                        return;
                    }
                    formData.set('item', newItemName);

                    for (let i = 1; i <= newQuantity; i++) {
                        const assetNum = form.querySelector(`input[name="asset_num_${i}"]`)?.value.trim();
                        const serialNum = form.querySelector(`input[name="serial_num_${i}"]`)?.value.trim();

                        if (!assetNum || !serialNum) {
                            alert(`يجب ملء حقول الأصول والرقم التسلسلي للعنصر رقم ${i}.`);
                            return;
                        }
                    }
                }
            }

            updateDebugInfo('إرسال الطلب...', form.action, 'انتظار...');

            fetch(form.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    updateDebugInfo('نتيجة الإرسال', '', data);
                    if (data.success) {
                        alert(data.message);
                        window.location.href = '<?= base_url('InventoryController/warehouseDashboard') ?>';
                    } else {
                        alert('خطأ: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('خطأ في إرسال الطلب:', error);
                    updateDebugInfo('خطأ في الإرسال', '', error.message);
                    alert('حدث خطأ في إرسال الطلب: ' + error.message);
                });
        });

        document.addEventListener('DOMContentLoaded', function() {
            initItemSearch();
            initQuantityHandler();
            initEmployeeSearch();
            initLocationDropdowns();

            document.getElementById('orderModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeOrderForm();
                }
            });

            openOrderForm();
        });
    </script>
</body>

</html>