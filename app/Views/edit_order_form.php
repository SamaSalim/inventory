<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* CSS من order_form.php */
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

        .multiple-items-section {
            display: none;
        }

        .item-entry {
            background: rgba(255, 255, 255, 0.05);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .item-entry-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .item-number {
            background: #3b82b6;
            color: white;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }

        .remove-item-btn {
            background: rgba(220, 53, 69, 0.8);
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .add-item-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.3s ease;
            margin: 20px 0;
        }

        .add-item-btn:hover {
            background: #218838;
        }
    </style>
</head>

<body>
    <div class="form-modal" id="orderModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">تعديل الطلب رقم <?= esc($order->order_id) ?></h3>
                <button class="close-btn" onclick="closeOrderForm()">&times;</button>
            </div>

            <form id="editOrderForm" action="<?= base_url('InventoryController/updateOrder/' . $order->order_id) ?>" method="post">
                <input type="hidden" name="from_employee_id" value="<?= esc(session()->get('employee_id')) ?>">
                <input type="hidden" name="order_id" value="<?= esc($order->order_id) ?>">

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
                        <input type="text" name="to_employee_id" id="toEmployeeId" value="<?= esc($toEmployee->emp_id ?? '') ?>" placeholder="أدخل الرقم الوظيفي" required>
                        <div id="employeeLoadingMsg" class="status-message loading-msg" style="display: none;">جاري البحث...</div>
                        <div id="employeeErrorMsg" class="status-message error-msg" style="display: none;">الرقم الوظيفي غير موجود</div>
                        <div id="employeeSuccessMsg" class="status-message success-msg" style="display: none;">تم العثور على الموظف</div>
                    </div>
                    <div class="form-group">
                        <label>اسم المستلم <span class="required">*</span></label>
                        <input type="text" name="receiver_name" id="receiverName" value="<?= esc($toEmployee->name ?? '') ?>" placeholder="أدخل اسم المستلم" required readonly>
                    </div>
                    <div class="form-group">
                        <label>البريد الإلكتروني</label>
                        <input type="email" name="email" id="employeeEmail" value="<?= esc($toEmployee->email ?? '') ?>" placeholder="أدخل البريد الإلكتروني" readonly>
                    </div>
                    <div class="form-group">
                        <label>رقم التحويلة</label>
                        <input type="text" name="transfer_number" id="transferNumber" value="<?= esc($toEmployee->emp_ext ?? '') ?>" placeholder="أدخل رقم التحويلة" readonly>
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
                            <?php foreach ($buildings as $building): ?>
                                <option value="<?= $building->id ?>"
                                    <?= isset($locationInfo) && $locationInfo['building']->id == $building->id ? 'selected' : '' ?>>
                                    <?= esc($building->code) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>الطابق <span class="required">*</span></label>
                        <select name="floor" id="floorSelect" required>
                            <option value="">اختر الطابق</option>
                            <?php if (isset($locationInfo) && !empty($locationInfo['floor'])): ?>
                                <option value="<?= $locationInfo['floor']->id ?>" selected>
                                    <?= esc($locationInfo['floor']->code) ?>
                                </option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>القسم <span class="required">*</span></label>
                        <select name="section" id="sectionSelect" required>
                            <option value="">اختر القسم</option>
                            <?php if (isset($locationInfo) && !empty($locationInfo['section'])): ?>
                                <option value="<?= $locationInfo['section']->id ?>" selected>
                                    <?= esc($locationInfo['section']->code) ?>
                                </option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>رقم الغرفة <span class="required">*</span></label>
                        <select name="room" id="roomSelect" required>
                            <option value="">اختر الغرفة</option>
                            <?php if (isset($locationInfo) && !empty($locationInfo['room'])): ?>
                                <option value="<?= $locationInfo['room']->id ?>" selected>
                                    <?= esc($locationInfo['room']->code) ?>
                                </option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <hr>
                <div class="section-header">
                    <h4>العناصر الحالية في الطلب</h4>
                </div>
                <div id="existingItemsContainer">
                    <?php if (count($orderItems) > 0): ?>
                        <?php foreach ($orderItems as $item): ?>
                            <div class="asset-serial-grid existing-item">
                                <div class="asset-serial-header">الصنف: <?= esc($item->item_name) ?></div>
                                <input type="hidden" name="existing_item_order_id[]" value="<?= esc($item->item_order_id) ?>">
                                <div class="form-group">
                                    <label>رقم الأصول <span class="required">*</span></label>
                                    <input type="text" name="existing_asset_num[]" value="<?= esc($item->asset_num) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>الرقم التسلسلي <span class="required">*</span></label>
                                    <input type="text" name="existing_serial_num[]" value="<?= esc($item->serial_num) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>الماركة</label>
                                    <input type="text" name="existing_brand[]" value="<?= esc($item->brand) ?>" placeholder="أدخل الماركة">
                                </div>
                                <div class="form-group">
                                    <label>رقم الموديل</label>
                                    <input type="text" name="existing_model_num[]" value="<?= esc($item->model_num) ?>" placeholder="أدخل رقم الموديل">
                                </div>
                                <div class="form-group full-width">
                                    <label>ملاحظات العنصر</label>
                                    <textarea name="existing_notes[]" rows="2"><?= esc($item->note) ?></textarea>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="optional-section">
                    <span class="optional-label">إضافة عنصر جديد (اختياري)</span>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>الصنف</label>
                            <div class="search-dropdown">
                                <input type="text" name="new_item" class="search-input" id="newItemSearch" placeholder="ابحث عن الصنف..." autocomplete="off">
                                <div class="dropdown-list" id="newItemDropdown"></div>
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
                        <div class="form-group">
                            <label>الماركة</label>
                            <input type="text" name="new_brand" id="newBrand" placeholder="أدخل الماركة">
                        </div>
                        <div class="form-group">
                            <label>رقم الموديل</label>
                            <input type="text" name="new_model_num" id="newModelNum" placeholder="أدخل رقم الموديل">
                        </div>
                        <div class="form-group full-width">
                            <label>ملاحظات العنصر الجديد</label>
                            <textarea name="new_item_notes" rows="2" placeholder="أدخل أي ملاحظات للعنصر الجديد"></textarea>
                        </div>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label>ملاحظات إضافية على الطلب</label>
                    <textarea name="notes" rows="3" placeholder="أدخل أي ملاحظات إضافية للطلب"><?= esc($order->note) ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="cancel-btn" onclick="window.history.back()">إلغاء</button>
                    <button type="submit" class="submit-btn">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initEmployeeSearch();
            initLocationDropdowns();
            initItemSearch('newItemSearch', 'newItemDropdown');

            document.getElementById('editOrderForm').addEventListener('submit', function(e) {
                e.preventDefault();
                updateOrder();
            });

            openOrderForm();
        });

        function openOrderForm() {
            const modal = document.getElementById('orderModal');
            if (modal) {
                modal.style.display = 'flex';
                loadInitialLocationData();
            }
        }

        function closeOrderForm() {
            const modal = document.getElementById('orderModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        function initEmployeeSearch() {
            const employeeIdInput = document.getElementById('toEmployeeId');
            const receiverNameInput = document.getElementById('receiverName');
            const emailInput = document.getElementById('employeeEmail');
            const transferInput = document.getElementById('transferNumber');
            const loadingMsg = document.getElementById('employeeLoadingMsg');
            const errorMsg = document.getElementById('employeeErrorMsg');
            const successMsg = document.getElementById('employeeSuccessMsg');
            let searchTimeout;

            const initialEmployeeId = employeeIdInput.value;
            if (initialEmployeeId) {
                searchEmployee(initialEmployeeId);
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

                searchTimeout = setTimeout(() => {
                    searchEmployee(employeeId);
                }, 500);
            });

            function searchEmployee(employeeId) {
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
                            receiverNameInput.setAttribute('readonly', true);
                            emailInput.setAttribute('readonly', true);
                            transferInput.setAttribute('readonly', true);
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
            const sectionSelect = document.getElementById('sectionSelect');
            const roomSelect = document.getElementById('roomSelect');

            loadInitialLocationData();

            buildingSelect.addEventListener('change', function() {
                loadFloors(this.value);
            });

            floorSelect.addEventListener('change', function() {
                loadSections(this.value);
            });

            sectionSelect.addEventListener('change', function() {
                loadRooms(this.value);
            });
        }

        async function loadInitialLocationData() {
            const buildingsSelect = document.getElementById('buildingSelect');
            const floorId = "<?= esc($locationInfo['floor']->id ?? '') ?>";
            const sectionId = "<?= esc($locationInfo['section']->id ?? '') ?>";
            const roomId = "<?= esc($locationInfo['room']->id ?? '') ?>";

            const buildingId = buildingsSelect.value;
            if (buildingId) {
                const floorsData = await fetch(`<?= base_url('InventoryController/getfloorsbybuilding') ?>/${buildingId}`).then(res => res.json());
                if (floorsData.success) {
                    const floorSelect = document.getElementById('floorSelect');
                    floorSelect.innerHTML = '<option value="">اختر الطابق</option>';
                    floorsData.data.forEach(floor => {
                        const isSelected = floor.id == floorId ? 'selected' : '';
                        // تم التعديل هنا: استخدام .code بدلاً من ->code
                        floorSelect.innerHTML += `<option value="${floor.id}" ${isSelected}>${floor.code}</option>`;
                    });
                }
            }

            if (floorId) {
                const sectionsData = await fetch(`<?= base_url('InventoryController/getsectionsbyfloor') ?>/${floorId}`).then(res => res.json());
                if (sectionsData.success) {
                    const sectionSelect = document.getElementById('sectionSelect');
                    sectionSelect.innerHTML = '<option value="">اختر القسم</option>';
                    sectionsData.data.forEach(section => {
                        const isSelected = section.id == sectionId ? 'selected' : '';
                        // تم التعديل هنا: استخدام .code بدلاً من ->code
                        sectionSelect.innerHTML += `<option value="${section.id}" ${isSelected}>${section.code}</option>`;
                    });
                }
            }

            if (sectionId) {
                const roomsData = await fetch(`<?= base_url('InventoryController/getroomsbysection') ?>/${sectionId}`).then(res => res.json());
                if (roomsData.success) {
                    const roomSelect = document.getElementById('roomSelect');
                    roomSelect.innerHTML = '<option value="">اختر الغرفة</option>';
                    roomsData.data.forEach(room => {
                        const isSelected = room.id == roomId ? 'selected' : '';
                        // تم التعديل هنا: استخدام .code بدلاً من ->code
                        roomSelect.innerHTML += `<option value="${room.id}" ${isSelected}>${room.code}</option>`;
                    });
                }
            }
        }
        async function loadFloors(buildingId) {
            const floorSelect = document.getElementById('floorSelect');
            const sectionSelect = document.getElementById('sectionSelect');
            const roomSelect = document.getElementById('roomSelect');

            floorSelect.innerHTML = '<option value="">اختر الطابق</option>';
            sectionSelect.innerHTML = '<option value="">اختر القسم</option>';
            roomSelect.innerHTML = '<option value="">اختر الغرفة</option>';

            floorSelect.disabled = !buildingId;
            sectionSelect.disabled = true;
            roomSelect.disabled = true;

            if (!buildingId) return;

            const response = await fetch(`<?= base_url('InventoryController/getfloorsbybuilding') ?>/${buildingId}`);
            const data = await response.json();
            if (data.success && Array.isArray(data.data)) {
                data.data.forEach(floor => {
                    floorSelect.innerHTML += `<option value="${floor.id}">${floor.code}</option>`;
                });
                floorSelect.disabled = false;
            }
        }

        async function loadSections(floorId) {
            const sectionSelect = document.getElementById('sectionSelect');
            const roomSelect = document.getElementById('roomSelect');

            sectionSelect.innerHTML = '<option value="">اختر القسم</option>';
            roomSelect.innerHTML = '<option value="">اختر الغرفة</option>';

            sectionSelect.disabled = !floorId;
            roomSelect.disabled = true;

            if (!floorId) return;

            const response = await fetch(`<?= base_url('InventoryController/getsectionsbyfloor') ?>/${floorId}`);
            const data = await response.json();
            if (data.success && Array.isArray(data.data)) {
                data.data.forEach(section => {
                    sectionSelect.innerHTML += `<option value="${section.id}">${section.code}</option>`;
                });
                sectionSelect.disabled = false;
            }
        }

        async function loadRooms(sectionId) {
            const roomSelect = document.getElementById('roomSelect');
            roomSelect.innerHTML = '<option value="">اختر الغرفة</option>';
            roomSelect.disabled = !sectionId;

            if (!sectionId) return;

            const response = await fetch(`<?= base_url('InventoryController/getroomsbysection') ?>/${sectionId}`);
            const data = await response.json();
            if (data.success && Array.isArray(data.data)) {
                data.data.forEach(room => {
                    roomSelect.innerHTML += `<option value="${room.id}">${room.code}</option>`;
                });
                roomSelect.disabled = false;
            }
        }

        function initItemSearch(inputId, dropdownId) {
            const searchInput = document.getElementById(inputId);
            const dropdown = document.getElementById(dropdownId);
            let searchTimeout;

            if (!searchInput || !dropdown) return;

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.trim();

                if (searchTerm.length < 2) {
                    dropdown.style.display = 'none';
                    return;
                }

                dropdown.innerHTML = '<div class="dropdown-item loading">جاري البحث...</div>';
                dropdown.style.display = 'block';

                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
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
                }, 500);
            });

            dropdown.addEventListener('click', function(e) {
                if (e.target.classList.contains('dropdown-item') && !e.target.classList.contains('loading') && !e.target.classList.contains('no-results') && !e.target.classList.contains('error')) {
                    const itemName = e.target.textContent;
                    searchInput.value = itemName;
                    dropdown.style.display = 'none';
                }
            });

            document.addEventListener('click', function(e) {
                if (!e.target.closest(`#${inputId}`).parentNode.closest('.search-dropdown')) {
                    dropdown.style.display = 'none';
                }
            });
        }

        function updateOrder() {
            const form = document.getElementById('editOrderForm');
            const formData = new FormData(form);

            const existingItems = [];
            const itemOrderIds = Array.from(document.querySelectorAll('input[name="existing_item_order_id[]"]')).map(input => input.value);
            const assetNums = Array.from(document.querySelectorAll('input[name="existing_asset_num[]"]')).map(input => input.value.trim());
            const serialNums = Array.from(document.querySelectorAll('input[name="existing_serial_num[]"]')).map(input => input.value.trim());
            const brands = Array.from(document.querySelectorAll('input[name="existing_brand[]"]')).map(input => input.value.trim());
            const modelNums = Array.from(document.querySelectorAll('input[name="existing_model_num[]"]')).map(input => input.value.trim());
            const notes = Array.from(document.querySelectorAll('textarea[name="existing_notes[]"]')).map(input => input.value.trim());

            for (let i = 0; i < itemOrderIds.length; i++) {
                const item = {
                    id: itemOrderIds[i],
                    asset_num: assetNums[i],
                    serial_num: serialNums[i],
                    brand: brands[i],
                    model_num: modelNums[i],
                    note: notes[i]
                };
                existingItems.push(item);
            }

            let hasEmptyFields = false;
            existingItems.forEach(item => {
                if (!item.asset_num || !item.serial_num) {
                    hasEmptyFields = true;
                }
            });
            if (hasEmptyFields) {
                alert('يجب ملء جميع حقول الأصول والأرقام التسلسلية للعناصر الموجودة.');
                return;
            }

            // إرسال البيانات المجمعة في حقل واحد
            formData.append('existing_items_data', JSON.stringify(existingItems));

            const newItemName = document.getElementById('newItemSearch').value.trim();
            const newAssetNum = document.getElementById('newAssetNum').value.trim();
            const newSerialNum = document.getElementById('newSerialNum').value.trim();

            if (newItemName || newAssetNum || newSerialNum) {
                if (!newItemName || !newAssetNum || !newSerialNum) {
                    alert('يجب ملء جميع حقول العنصر الجديد أو تركها فارغة تماماً.');
                    return;
                }
                const newItemData = {
                    item: newItemName,
                    asset_num: newAssetNum,
                    serial_num: newSerialNum,
                    brand: document.getElementById('newBrand').value.trim(),
                    model_num: document.getElementById('newModelNum').value.trim(),
                    note: document.querySelector('textarea[name="new_item_notes"]').value.trim()
                };
                formData.append('new_item_data', JSON.stringify(newItemData));
            }

            fetch(form.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('تم تحديث الطلب بنجاح');
                        window.location.href = '<?= base_url('InventoryController') ?>';
                    } else {
                        alert('خطأ: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('خطأ في تحديث الطلب:', error);
                    alert('حدث خطأ أثناء تحديث الطلب');
                });
        }
    </script>
</body>

</html>