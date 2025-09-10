<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المستودعات</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('public/assets/css/warehouse.css') ?>">

</head>

<body>

    <!-- الشريط الجانبي للتنقل -->
    <?= $this->include('layouts/header') ?>

    <div class="main-content">
        <div class="header">
            <h1 class="page-title">إدارة المستودعات</h1>
            <div class="user-info" onclick="location.href='<?= base_url('user/getUserInfo') ?>'">
                <div class="user-avatar">
                    <?= strtoupper(substr(esc(session()->get('name')), 0, 1)) ?>
                </div>
                <span><?= esc(session()->get('name')) ?></span>
            </div>
        </div>

        <div class="content-area">
            <div class="stats-grid">
                <div class="stat-card blue">
                    <div class="stat-number"><?= number_format($stats['total_receipts'] ?? 0) ?></div>
                    <div class="stat-label">
                        <svg class="stat-icon" viewBox="0 0 24 24">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z" />
                        </svg>
                        إجمالي الكميات
                    </div>
                </div>

                <div class="stat-card pink">
                    <div class="stat-number"><?= number_format($stats['available_items'] ?? 0) ?></div>
                    <div class="stat-label">
                        <svg class="stat-icon" viewBox="0 0 24 24">
                            <path d="M20 6h-2.18c.11-.31.18-.65.18-1a2.996 2.996 0 0 0-5.5-1.65l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2z" />
                        </svg>
                        أصناف متوفرة
                    </div>
                </div>

                <div class="stat-card green">
                    <div class="stat-number"><?= number_format($stats['total_entries'] ?? 0) ?></div>
                    <div class="stat-label">
                        <svg class="stat-icon" viewBox="0 0 24 24">
                            <path d="M9 11H7v6h2v-6zm4 0h-2v6h2v-6zm4 0h-2v6h2v-6zm2-7h-3V2h-2v2H8V2H6v2H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2z" />
                        </svg>
                        عدد الإدخالات
                    </div>
                </div>

                <div class="stat-card" style="background: linear-gradient(135deg, #fff3cd, #ffeaa7);">
                    <div class="stat-number"><?= number_format($stats['low_stock'] ?? 0) ?></div>
                    <div class="stat-label">
                        <svg class="stat-icon" viewBox="0 0 24 24">
                            <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" />
                        </svg>
                        مخزون منخفض
                    </div>
                </div>
            </div>

            <div class="section-header">
                <h2 class="section-title">قائمة المخزون</h2>
                <button class="add-btn" onclick="openOrderForm()">
                    <i class="fas fa-plus"></i> إنشاء طلب جديد
                </button>
            </div>

            <div class="filters-section">
                <div class="filter-row">
                    <div class="filter-group">
                        <label class="filter-label">البحث</label>
                        <input type="text" class="search-box" id="generalSearch" placeholder="البحث العام">
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">الصنف</label>
                        <input type="text" class="search-box" id="searchId" placeholder="الصنف">
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">التصنيف</label>
                        <select class="filter-select" id="searchCategory">
                            <option value="">اختر التصنيف</option>
                            <?php if (isset($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= esc($cat->name) ?>"><?= esc($cat->name) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">الرقم التسلسلي</label>
                        <input type="text" class="search-box" id="searchSerial" placeholder="الرقم التسلسلي">
                    </div>
                    <!-- <div class="filter-group">
                        <label class="filter-label">حالة الاستخدام</label>
                        <select class="filter-select" id="searchStatus">
                            <option value="">اختر الحالة</option>
                            </?php if (isset($usage_statuses)): ?>
                                </?php foreach ($usage_statuses as $status): ?>
                                    <option value="</?= esc($status->usage_status) ?>"></?= esc($status->usage_status) ?></option>
                                </?php endforeach; ?>
                            </?php endif; ?>
                        </select>
                    </div> -->
                </div>
            </div>

            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>رقم الطلب</th>
                            <!-- <th>تاريخ الإنشاء</th> -->
                            <th>الصنف</th>
                            <th>التصنيف</th>
                            <!-- <th>الكمية</th> -->
                            <!-- <th>رقم الموديل</th> -->
                            <!-- <th>الحجم</th> -->
                            <th>رقم الأصول</th>
                            <th>الرقم التسلسلي</th>
                            <!-- <th>رقم الأصول القديمة</th>
                            <th>الحالة</th>
                            <th>حالة الاستخدام</th> -->
                            <th>بواسطة</th>
                            <th>الملاحظات</th>
                            <th>العمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($items) && !empty($items)): ?>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?= esc($item->order_id ?? '-') ?></td>

                                    <td><?= esc($item->item_name ?? 'غير محدد') ?></td>
                                    <td><?= esc($item->category_name ?? 'غير محدد') ?></td>
                                    <!-- <td></?= esc($item->quantity ?? 0) ?></td>
                                    <td></?= esc($item->model_num ?? '-') ?></td>
                                    <td></?= esc($item->size ?? '-') ?></td> -->
                                    <td><?= esc($item->asset_num ?? '-') ?></td>
                                    <td><?= esc($item->serial_num ?? '-') ?></td>
                                    <!-- <td></?= esc($item->old_asset_num ?? '-') ?></td>
                                    <td></?= esc($item->status ?? 'غير محدد') ?></td>
                                    <td></?= esc($item->usage_status ?? 'غير محدد') ?></td> -->
                                    <td><?= esc($item->created_by_name ?? '-') ?></td>
                                    <td><?= esc($item->note ?? '-') ?></td>
                                    <td>
                                        <a href="<?= site_url('InventoryController/editOrder/' . $item->order_id) ?>" class="edit-btn">تحديث</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="13">لا توجد بيانات متاحة</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="form-modal" id="orderModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">إنشاء طلب جديد</h3>
                <button class="close-btn" onclick="closeOrderForm()">&times;</button>
            </div>

            <form id="orderForm" action="<?= base_url('InventoryController/store') ?>">

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
                        <input type="text" name="to_employee_id" id="toEmployeeId" placeholder="أدخل الرقم الوظيفي" required>
                        <div id="employeeLoadingMsg" class="status-message loading-msg" style="display: none;">جاري البحث...</div>
                        <div id="employeeErrorMsg" class="status-message error-msg" style="display: none;">الرقم الوظيفي غير موجود</div>
                        <div id="employeeSuccessMsg" class="status-message success-msg" style="display: none;">تم العثور على الموظف</div>
                    </div>
                    <div class="form-group">
                        <label>اسم المستلم <span class="required">*</span></label>
                        <input type="text" name="receiver_name" id="receiverName" placeholder="أدخل اسم المستلم" required readonly>
                    </div>
                    <div class="form-group">
                        <label>البريد الإلكتروني</label>
                        <input type="email" name="email" id="employeeEmail" placeholder="أدخل البريد الإلكتروني" readonly>
                    </div>
                    <div class="form-group">
                        <label>رقم التحويلة</label>
                        <input type="text" name="transfer_number" id="transferNumber" placeholder="أدخل رقم التحويلة" readonly>
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

                <hr>
                <div class="section-header">
                    <h4>تفاصيل الطلب</h4>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>الصنف <span class="required">*</span></label>
                        <div class="search-dropdown">
                            <input type="text" name="item" class="search-input" placeholder="ابحث عن الصنف..." autocomplete="off">
                            <div class="dropdown-list" id="itemDropdown"></div>
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

                <div class="form-group full-width">
                    <label>ملاحظات إضافية</label>
                    <textarea name="notes" rows="3" placeholder="أدخل أي ملاحظات إضافية للطلب"></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="cancel-btn" onclick="closeOrderForm()">إلغاء</button>
                    <button type="submit" class="submit-btn">إنشاء الطلب</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // متغيرات عامة
        let savedFormData = {};
        let searchTimeout;
        let debugMode = true;

        // وظائف النموذج المنبثق
        function openOrderForm() {
            const modal = document.getElementById('orderModal');
            if (modal) {
                modal.style.display = 'flex';
                restoreFormData();
            }
        }

        function closeOrderForm() {
            saveFormData();
            const modal = document.getElementById('orderModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        function saveFormData() {
            const form = document.querySelector('#orderModal form');
            if (form) {
                const formData = new FormData(form);
                savedFormData = {};
                for (let [key, value] of formData.entries()) {
                    savedFormData[key] = value;
                }
            }
        }

        function restoreFormData() {
            const form = document.querySelector('#orderModal form');
            if (form && Object.keys(savedFormData).length > 0) {
                for (let [key, value] of Object.entries(savedFormData)) {
                    const input = form.querySelector(`[name="${key}"]`);
                    if (input) {
                        input.value = value;
                    }
                }
                const quantity = parseInt(savedFormData.quantity) || 0;
                if (quantity > 0) {
                    createAssetSerialFields(quantity);
                }
            }
        }

        function clearSavedData() {
            savedFormData = {};
        }

        // وظيفة البحث المُحسَّنة مع تحديث فهارس الأعمدة
        function performSearch() {
            const generalSearch = document.getElementById('generalSearch').value.toLowerCase();
            const searchId = document.getElementById('searchId').value.toLowerCase();
            const searchCategory = document.getElementById('searchCategory').value.toLowerCase();
            const searchSerial = document.getElementById('searchSerial').value.toLowerCase();

            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                // التأكد من وجود خلايا كافية في الصف
                if (row.cells.length < 7) return;

                // تحديث الفهارس حسب ترتيب الأعمدة الجديد في الجدول
                const orderIdCell = row.cells[0]?.textContent.toLowerCase() || ''; // رقم الطلب
                const itemCell = row.cells[1]?.textContent.toLowerCase() || ''; // الصنف
                const categoryCell = row.cells[2]?.textContent.toLowerCase() || ''; // التصنيف
                const assetCell = row.cells[3]?.textContent.toLowerCase() || ''; // رقم الأصول
                const serialCell = row.cells[4]?.textContent.toLowerCase() || ''; // الرقم التسلسلي
                const createdByCell = row.cells[5]?.textContent.toLowerCase() || ''; // بواسطة
                const noteCell = row.cells[6]?.textContent.toLowerCase() || ''; // الملاحظات

                // النص الكامل للصف للبحث العام
                const allText = row.textContent.toLowerCase();

                // شروط البحث
                const matchGeneral = !generalSearch || allText.includes(generalSearch);
                const matchId = !searchId || itemCell.includes(searchId);
                const matchCategory = !searchCategory || categoryCell.includes(searchCategory);
                const matchSerial = !searchSerial || serialCell.includes(searchSerial);

                // إظهار أو إخفاء الصف حسب النتائج
                if (matchGeneral && matchId && matchCategory && matchSerial) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // وظيفة البحث في الأصناف
        function initItemSearch() {
            const searchInput = document.querySelector('input[name="item"]');
            const dropdown = document.getElementById('itemDropdown');

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.trim();
                if (searchTerm.length < 2) {
                    dropdown.style.display = 'none';
                    return;
                }

                dropdown.innerHTML = '<div class="dropdown-item loading">جاري البحث...</div>';
                dropdown.style.display = 'block';

                fetch(`<?= base_url('InventoryController/searchitems') ?>?term=${encodeURIComponent(searchTerm)}`)
                    .then(response => response.json())
                    .then(data => {
                        dropdown.innerHTML = '';
                        if (data.success && data.data && data.data.length > 0) {
                            dropdown.innerHTML = data.data.map(item => `<div class="dropdown-item">${item}</div>`).join('');
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
                if (e.target.classList.contains('dropdown-item') &&
                    !e.target.classList.contains('loading') &&
                    !e.target.classList.contains('no-results') &&
                    !e.target.classList.contains('error')) {
                    searchInput.value = e.target.textContent;
                    dropdown.style.display = 'none';
                }
            });

            document.addEventListener('click', function(e) {
                if (!e.target.closest('.search-dropdown')) {
                    dropdown.style.display = 'none';
                }
            });
        }

        // وظيفة إنشاء حقول الأصول والأرقام التسلسلية
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

        // إعداد معالج تغيير الكمية
        function initQuantityHandler() {
            const quantityInput = document.querySelector('input[name="quantity"]');
            quantityInput.addEventListener('input', function() {
                const quantity = parseInt(this.value) || 0;
                createAssetSerialFields(quantity);
            });
        }

        // وظيفة البحث عن الموظف
        function initEmployeeSearch() {
            const employeeIdInput = document.getElementById('toEmployeeId');
            const receiverNameInput = document.getElementById('receiverName');
            const emailInput = document.getElementById('employeeEmail');
            const transferInput = document.getElementById('transferNumber');
            const loadingMsg = document.getElementById('employeeLoadingMsg');
            const errorMsg = document.getElementById('employeeErrorMsg');
            const successMsg = document.getElementById('employeeSuccessMsg');

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
            });
        }

        // وظائف إدارة المواقع
        function initLocationDropdowns() {
            const buildingSelect = document.getElementById('buildingSelect');
            const floorSelect = document.getElementById('floorSelect');
            const roomSelect = document.getElementById('roomSelect');
            const departmentSelect = document.getElementById('departmentSelect');
            loadInitialData();
            buildingSelect.addEventListener('change', function() {
                loadFloors(this.value);
            });
            floorSelect.addEventListener('change', function() {
                loadRoomsBySection(this.value);
            });

            function loadInitialData() {
                fetch(`<?= base_url('InventoryController/getformdata') ?>`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (data.buildings && Array.isArray(data.buildings)) {
                                buildingSelect.innerHTML = '<option value="">اختر المبنى</option>';
                                data.buildings.forEach(building => {
                                    buildingSelect.innerHTML += `<option value="${building.id}">${building.code || building.name}</option>`;
                                });
                            }
                            const categorySelect = document.getElementById('categorySelect');
                            if (data.categories && Array.isArray(data.categories)) {
                                categorySelect.innerHTML = '<option value="">اختر التصنيف</option>';
                                data.categories.forEach(category => {
                                    categorySelect.innerHTML += `<option value="${category.name}">${category.name}</option>`;
                                });
                            }
                            if (data.departments && Array.isArray(data.departments)) {
                                departmentSelect.innerHTML = '<option value="">اختر القسم</option>';
                                data.departments.forEach(dept => {
                                    departmentSelect.innerHTML += `<option value="${dept}">${dept}</option>`;
                                });
                            }
                        }
                    });
            }

            function loadFloors(buildingId) {
                floorSelect.innerHTML = '<option value="">اختر الطابق</option>';
                roomSelect.innerHTML = '<option value="">اختر الغرفة</option>';
                floorSelect.disabled = !buildingId;
                roomSelect.disabled = true;
                if (!buildingId) return;
                fetch(`<?= base_url('InventoryController/getfloorsbybuilding') ?>/${buildingId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data && Array.isArray(data.data)) {
                            data.data.forEach(floor => {
                                floorSelect.innerHTML += `<option value="${floor.id}">${floor.code || floor.name}</option>`;
                            });
                            floorSelect.disabled = false;
                        }
                    });
            }

            function loadRoomsBySection(floorId) {
                roomSelect.innerHTML = '<option value="">اختر الغرفة</option>';
                roomSelect.disabled = true;
                if (!floorId) return;
                fetch(`<?= base_url('InventoryController/getsectionsbyfloor') ?>/${floorId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data && Array.isArray(data.data) && data.data.length > 0) {
                            const sectionId = data.data[0].id;
                            fetch(`<?= base_url('InventoryController/getroomsbysection') ?>/${sectionId}`)
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success && data.data && Array.isArray(data.data)) {
                                        data.data.forEach(room => {
                                            roomSelect.innerHTML += `<option value="${room.id}">${room.code || room.name}</option>`;
                                        });
                                        roomSelect.disabled = false;
                                    }
                                });
                        }
                    });
            }
        }

        // معالج إرسال النموذج
        document.getElementById('orderForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);

            // جمع بيانات العناصر الجديدة
            const newItems = [];
            const newQuantityInput = form.querySelector('input[name="quantity"]');
            const newItemSearchInput = form.querySelector('input[name="item"]');

            const newQuantity = parseInt(newQuantityInput.value) || 0;
            const newItemName = newItemSearchInput.value.trim();

            if (newQuantity > 0 && newItemName === '') {
                alert('يجب اختيار صنف للعناصر الجديدة.');
                return;
            }

            for (let i = 1; i <= newQuantity; i++) {
                const assetNum = form.querySelector(`input[name="asset_num_${i}"]`)?.value.trim();
                const serialNum = form.querySelector(`input[name="serial_num_${i}"]`)?.value.trim();

                if (!assetNum || !serialNum) {
                    alert(`يجب ملء حقول الأصول والرقم التسلسلي للعنصر رقم ${i}.`);
                    return;
                }

                newItems.push({
                    item: newItemName,
                    asset_num: assetNum,
                    serial_num: serialNum
                });
            }
            formData.append('new_items', JSON.stringify(newItems));

            fetch(form.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert('خطأ: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('خطأ في إرسال الطلب:', error);
                    alert('حدث خطأ في إرسال الطلب: ' + error.message);
                });
        });

        // تهيئة جميع الوظائف عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            initItemSearch();
            initQuantityHandler();
            initEmployeeSearch();
            initLocationDropdowns();

            // ربط أحداث البحث بالحقول الصحيحة
            document.getElementById('generalSearch')?.addEventListener('input', performSearch);
            document.getElementById('searchId')?.addEventListener('input', performSearch);
            document.getElementById('searchCategory')?.addEventListener('change', performSearch);
            document.getElementById('searchSerial')?.addEventListener('input', performSearch);
        });

        // إضافة تأثيرات بصرية للجدول
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('.custom-table tbody tr');

            rows.forEach(row => {
                row.addEventListener('click', function() {
                    rows.forEach(r => r.classList.remove('selected'));
                    this.classList.add('selected');
                });
            });
        });

        const style = document.createElement('style');
        style.textContent = `
            .custom-table tbody tr.selected {
                background-color: rgba(58, 192, 195, 0.1) !important;
                border-left: 3px solid #3AC0C3;
            }
            .custom-table tbody tr {
                cursor: pointer;
            }
        `;
        document.head.appendChild(style);
        // معالج إرسال نموذج التحديث
        function handleUpdateForm(form) {
            const formData = new FormData(form);

            // جمع بيانات العناصر الموجودة
            const existingItemsContainer = form.querySelector('#existingItemsContainer');
            if (existingItemsContainer) {
                const existingItems = existingItemsContainer.querySelectorAll('.existing-item');
                existingItems.forEach(item => {
                    const itemId = item.dataset.itemId;
                    const assetInput = item.querySelector(`[name="asset_num"]`);
                    const serialInput = item.querySelector(`[name="serial_num"]`);
                    const notesInput = item.querySelector(`[name="notes"]`);

                    if (assetInput && serialInput) {
                        formData.append(`existing_asset_num_${itemId}`, assetInput.value);
                        formData.append(`existing_serial_num_${itemId}`, serialInput.value);
                        formData.append(`existing_notes_${itemId}`, notesInput ? notesInput.value : '');
                    }
                });
            }

            return formData;
        }
    </script>

</body>

</html>