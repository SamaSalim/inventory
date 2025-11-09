<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل طلب متعدد الأصناف</title>
    <!-- تضمين الـ CSS (نفس التنسيق المرفق) -->
    <style>
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
            width: 95%;
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

        /* تنسيق الحقول المعطلة أو للقراءة فقط */
        input:read-only,
        select:disabled {
            background: rgba(26, 37, 47, 0.3);
            cursor: not-allowed;
            opacity: 0.8;
            /* لتقليل التعتيم قليلاً */
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

        .classification-display {
            background: linear-gradient(135deg, rgba(59, 130, 182, 0.15) 0%, rgba(26, 37, 47, 0.2) 100%);
            border: 2px solid rgba(59, 130, 182, 0.4);
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
            display: none;
        }

        .classification-display.show {
            display: block;
        }

        .classification-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding: 8px;
            background: rgba(59, 130, 182, 0.1);
            border-radius: 5px;
        }

        .classification-item:last-child {
            margin-bottom: 0;
        }

        .classification-label {
            color: #3b82b6;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .classification-value {
            color: #ecf0f1;
            font-weight: 500;
        }

        .items-section {
            background: linear-gradient(135deg, rgba(59, 130, 182, 0.1) 0%, rgba(26, 37, 47, 0.2) 100%);
            border: 2px solid rgba(59, 130, 182, 0.3);
            border-radius: 10px;
            margin-bottom: 30px;
            padding: 20px;
        }

        .item-card {
            background: linear-gradient(135deg, rgba(59, 130, 182, 0.15) 0%, rgba(26, 37, 47, 0.3) 100%);
            border: 1px solid rgba(59, 130, 182, 0.4);
            border-radius: 8px;
            margin-bottom: 20px;
            padding: 20px;
            position: relative;
        }

        .item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(59, 130, 182, 0.3);
        }

        .item-title {
            color: #3b82b6;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .remove-item-btn {
            background: rgba(231, 76, 60, 0.8);
            color: white;
            border: none;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            cursor: pointer;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .remove-item-btn:hover {
            background: rgba(231, 76, 60, 1);
            transform: scale(1.1);
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

        .add-item-btn {
            background: linear-gradient(45deg, #27ae60, #2ecc71);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 15px 30px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .add-item-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3);
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
        .clear-btn,
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

        .clear-btn {
            background: rgba(243, 156, 18, 0.8);
            color: white;
        }

        .clear-btn:hover {
            background: rgba(243, 156, 18, 1);
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

        .validation-message {
            font-size: 0.85rem;
            margin-top: 5px;
            padding: 5px 8px;
            border-radius: 4px;
            font-weight: 500;
        }

        .validation-message.loading-msg {
            color: #3b82b6;
            background: rgba(59, 130, 182, 0.1);
            border: 1px solid rgba(59, 130, 182, 0.3);
        }

        .validation-message.error-msg {
            color: #e74c3c;
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid rgba(231, 76, 60, 0.3);
        }

        .validation-message.success-msg {
            color: #27ae60;
            background: rgba(39, 174, 96, 0.1);
            border: 1px solid rgba(39, 174, 96, 0.3);
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loading-spinner {
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            color: #333;
        }
    </style>
</head>

<body>
    <!-- شاشة التحميل -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-spinner">
            <div>جاري تحميل بيانات الطلب...</div>
        </div>
    </div>

    <!--  متغير PHP لحالة الطلب لتمكين استخدامه في JavaScript -->
    <script>
        const ORDER_STATUS_PENDING = 1; // رقم حالة "قيد الانتظار"
        const orderStatusId = <?= $orderStatusId ?? 0; ?>;
    </script>


    <div class="form-modal" id="editForm">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">تعديل طلب رقم : <span id="orderIdDisplay"></span></h3>
                <button class="close-btn" onclick="closeEditForm()">&times;</button>
            </div>

            <form id="orderForm">
                <input type="hidden" id="orderId" name="order_id" value="">

                <!-- قسم بيانات المرسل -->
                <div class="section-header">
                    <h4>بيانات المرسل</h4>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>الرقم الوظيفي</label>
                        <input type="text" value="<?= esc(session()->get('employee_id')) ?>" readonly>
                        <input type="hidden" id="fromUserId" value="<?= esc(session()->get('employee_id')) ?>">
                    </div>
                    <div class="form-group">
                        <label>اسم المرسل</label>
                        <input type="text" id="fromSenderName" value="<?= esc(session()->get('name')) ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>البريد الإلكتروني</label>
                        <input type="email" value="<?= esc($loggedInUser->email ?? '') ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label>رقم التحويلة</label>
                        <input type="text" value="<?= esc($loggedInUser->emp_ext ?? '') ?>" readonly>

                        <input type="hidden" id="fromTransferNumber" value="<?= esc($sender_data->emp_ext ?? '') ?>">
                    </div>
                </div>
                <hr>

                <!-- قسم بيانات المستلم -->
                <div class="section-header" id="recipientHeader">
                    <h4>بيانات المستلم</h4>
                </div>

                <div class="form-grid" id="recipientFields">
                    <div class="form-group">
                        <label>رقم المستخدم <span class="required">*</span></label>
                        <input type="text" name="user_id" id="userId" placeholder="أدخل رقم المستخدم" required>
                        <div id="userLoadingMsg" class="status-message loading-msg" style="display: none;">جاري البحث...</div>
                        <div id="userErrorMsg" class="status-message error-msg" style="display: none;">رقم المستخدم غير موجود</div>
                        <div id="userSuccessMsg" class="status-message success-msg" style="display: none;">تم العثور على المستخدم</div>
                    </div>
                    <div class="form-group">
                        <label>اسم المستلم <span class="required">*</span></label>
                        <input type="text" name="receiver_name" id="receiverName" placeholder="أدخل اسم المستلم" required readonly>
                    </div>
                    <div class="form-group">
                        <label>البريد الإلكتروني</label>
                        <input type="email" name="email" id="userEmail" placeholder="أدخل البريد الإلكتروني" readonly>
                    </div>
                    <div class="form-group">
                        <label>رقم التحويلة</label>
                        <input type="text" name="transfer_number" id="transferNumber" placeholder="أدخل رقم التحويلة" readonly>
                    </div>
                </div>

                <hr>

                <!-- قسم موقع المستلم -->
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
                        <label>القسم <span class="required">*</span></label>
                        <select name="sections" id="sectionSelect" required disabled>
                            <option value="">اختر القسم</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>رقم الغرفة <span class="required">*</span></label>
                        <select name="room" id="roomSelect" required disabled>
                            <option value="">اختر الغرفة</option>
                        </select>
                    </div>
                </div>

                <hr>

                <!-- قسم الأصناف -->
                <div class="section-header">
                    <h4>الأصناف المطلوبة</h4>
                </div>

                <div class="items-section">
                    <button type="button" class="add-item-btn" onclick="addNewItem()">
                        <span>+</span>
                        إضافة صنف جديد
                    </button>

                    <div id="itemsContainer">
                        <!-- سيتم إضافة الأصناف هنا -->
                    </div>
                </div>

                <!-- قسم الملاحظات -->
                <div class="form-group full-width">
                    <label>ملاحظات إضافية</label>
                    <textarea name="notes" id="notesTextarea" rows="3" placeholder="أدخل أي ملاحظات إضافية للطلب"></textarea>
                </div>

                <!-- أزرار العمليات -->
                <div class="form-actions">
                    <button type="button" class="cancel-btn" onclick="closeEditForm()">إلغاء</button>
                    <button type="button" class="clear-btn" onclick="resetToOriginalData()">استعادة البيانات الأصلية</button>
                    <button type="submit" class="submit-btn">حفظ التعديلات</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // احصل على اسم المجلد من URL الحالي تلقائياً
        const pathSegments = window.location.pathname.split('/');
        const projectFolder = pathSegments[1] || '';
        const SITE_URL = window.location.origin + '/' + projectFolder + '/index.php/';

        // الحصول على رقم الطلب من URL
        const currentOrderId = getCurrentOrderId();

        // متغيرات عامة
        let originalOrderData = {};
        let searchTimeout;
        let itemCounter = 0;

        // وظيفة مساعدة لبناء الروابط
        function buildUrl(path) {
            return SITE_URL + path;
        }

        // الحصول على رقم الطلب من URL
        function getCurrentOrderId() {
            const pathParts = window.location.pathname.split('/');
            const editIndex = pathParts.indexOf('editOrder');
            if (editIndex !== -1 && pathParts[editIndex + 1]) {
                return pathParts[editIndex + 1];
            }

            // إذا لم نجد الطلب في URL، نحاول البحث في المتغير الممرر من الكنترولر
            if (typeof orderId !== 'undefined') {
                return orderId;
            }

            return null;
        }

        // تحميل بيانات الطلب
        function loadOrderData() {
            if (!currentOrderId) {
                alert('رقم الطلب غير محدد');
                window.location.href = buildUrl('inventoryController/index');
                return;
            }

            document.getElementById('loadingOverlay').style.display = 'flex';

            fetch(buildUrl(`OrderController/getOrderData/${currentOrderId}`))
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    document.getElementById('loadingOverlay').style.display = 'none';

                    if (data.success) {
                        originalOrderData = data.data;
                        populateForm(data.data);
                        //  تشغيل دالة التعطيل بعد تحميل البيانات
                        disableRecipientFieldsIfPending();
                    } else {
                        alert('خطأ: ' + data.message);
                        window.location.href = buildUrl('inventoryController/index');
                    }
                })
                .catch(error => {
                    document.getElementById('loadingOverlay').style.display = 'none';
                    console.error('خطأ في تحميل بيانات الطلب:', error);
                    alert('حدث خطأ في تحميل بيانات الطلب');
                    window.location.href = buildUrl('inventoryController/index');
                });
        }

        // ==========================================================
        //  الدالة الجديدة لتعطيل حقول المستلم
        // ==========================================================
        function disableRecipientFieldsIfPending() {
            // ORDER_STATUS_PENDING = 1
            if (orderStatusId === ORDER_STATUS_PENDING) {
                const userIdInput = document.getElementById('userId'); // حقل رقم المستخدم للتعطيل
                const receiverNameInput = document.getElementById('receiverName');
                const emailInput = document.getElementById('userEmail');
                const transferInput = document.getElementById('transferNumber');

                // 1. تعطيل حقل رقم المستخدم (لأنه هو الحقل القابل للكتابة والبحث)
                userIdInput.setAttribute('readonly', true);
                userIdInput.style.cursor = 'not-allowed';

                // 2. تعطيل حقول العرض فقط (للتأكيد، وهي غالباً تكون للقراءة فقط بالفعل)
                receiverNameInput.setAttribute('readonly', true);
                emailInput.setAttribute('readonly', true);
                transferInput.setAttribute('readonly', true);

                //  إظهار رسالة توضيحية أسفل قسم المستلم
                const recipientHeader = document.getElementById('recipientHeader');

                if (recipientHeader) {
                    let note = recipientHeader.querySelector('.status-note');
                    if (!note) {
                        note = document.createElement('div');
                        note.className = 'status-note';
                        note.style.cssText = 'color: #f1c40f; margin-top: 10px; font-weight: 500; font-size: 0.9em;';
                        note.textContent = '⚠️ المستلم غير قابل للتعديل لأن الطلب قيد الانتظار.';
                        recipientHeader.appendChild(note);
                    }
                }

                console.log('حقول المستلم تم تعطيلها لأن حالة الطلب قيد الانتظار (1).');
            } else {
                // إذا كانت حالة الطلب مرفوضة (3)، يجب أن تكون الحقول قابلة للتعديل
                const userIdInput = document.getElementById('userId');
                userIdInput.removeAttribute('readonly');
                userIdInput.style.cursor = 'auto';
            }
        }
        // ==========================================================


        // ملء النموذج بالبيانات
        function populateForm(orderData) {
            // تعبئة معرف الطلب - استخدم order_id
            document.getElementById('orderId').value = orderData.order.order_id;
            document.getElementById('orderIdDisplay').textContent = orderData.order.order_id;

            // تعبئة بيانات المستلم
            if (orderData.to_user) {
                document.getElementById('userId').value = orderData.to_user.user_id || '';
                document.getElementById('receiverName').value = orderData.to_user.name || '';
                document.getElementById('userEmail').value = orderData.to_user.email || '';
                document.getElementById('transferNumber').value = orderData.to_user.user_ext || '';

                document.getElementById('userSuccessMsg').style.display = 'block';
            }

            // تعبئة الملاحظات
            document.getElementById('notesTextarea').value = orderData.order.note || '';

            // تحميل بيانات الموقع والأصناف
            loadFormData().then(() => {
                if (orderData.items && orderData.items.length > 0) {
                    orderData.items.forEach((itemGroup, index) => {
                        addItemFromData(itemGroup, index + 1);
                    });

                    if (orderData.items[0]) {
                        const firstItem = orderData.items[0];
                        setLocationFromData(firstItem.building_id, firstItem.floor_id, firstItem.section_id, firstItem.room_id);
                    }
                }
            });
        }


        // وظيفة إضافة صنف من البيانات المحملة
        function addItemFromData(itemGroup, itemNumber) {
            itemCounter = itemNumber;
            const container = document.getElementById('itemsContainer');

            const itemDiv = document.createElement('div');
            itemDiv.className = 'item-card';
            itemDiv.id = `item_${itemCounter}`;

            itemDiv.innerHTML = `
        <div class="item-header">
            <div class="item-title">الصنف رقم ${itemCounter}</div>
            <button type="button" class="remove-item-btn" onclick="removeItem(${itemCounter})" title="إزالة الصنف">
                ×
            </button>
        </div>
        
        <div class="form-grid">
            <div class="form-group full-width">
                <label>الصنف <span class="required">*</span></label>
                <div class="search-dropdown">
                    <input type="text" name="item_${itemCounter}" class="search-input" placeholder="ابحث عن الصنف..." required autocomplete="off" value="${itemGroup.item_name}">
                    <div class="dropdown-list" id="itemDropdown_${itemCounter}"></div>
                </div>
                <div class="classification-display show" id="classificationDisplay_${itemCounter}">
                    <div class="classification-item">
                        <span class="classification-label">التصنيف الرئيسي:</span>
                        <span class="classification-value" id="majorCategory_${itemCounter}">-</span>
                    </div>
                    <div class="classification-item">
                        <span class="classification-label">التصنيف الفرعي:</span>
                        <span class="classification-value" id="minorCategory_${itemCounter}">-</span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>الكمية <span class="required">*</span></label>
                <input type="number" name="quantity_${itemCounter}" min="1" max="100" placeholder="أدخل الكمية" required value="${itemGroup.quantity}" onchange="updateAssetSerialFields(${itemCounter}, this.value)">
            </div>
            <div class="form-group">
                <label>نوع العهدة <span class="required">*</span></label>
                <select name="custody_type_${itemCounter}" class="custody-type-select" required>
                    <option value="">اختر نوع العهدة</option>
                </select>
            </div>
        </div>
        
        <div class="dynamic-fields" id="dynamicFields_${itemCounter}">
            <div class="section-header">
                <h4>أرقام الأصول والأرقام التسلسلية</h4>
            </div>
            <div id="assetSerialContainer_${itemCounter}"></div>
        </div>
    `;

            container.appendChild(itemDiv);

            // تهيئة البحث للصنف
            initItemSearchForElement(itemCounter);

            // تحميل قائمة أنواع العهدة وتعيين القيمة الحالية
            loadCustodyTypesForItem(itemCounter, itemGroup.assets_type);

            // إضافة الحقول الديناميكية مباشرة
            const assetSerialContainer = document.getElementById(`assetSerialContainer_${itemCounter}`);
            const dynamicSection = document.getElementById(`dynamicFields_${itemCounter}`);
            const qty = itemGroup.quantity || 0;

            if (qty > 0 && assetSerialContainer) {
                // جمع بيانات العناصر لهذا الصنف
                for (let i = 1; i <= qty; i++) {
                    const currentItem = itemGroup.items[i - 1] || {};

                    const fieldDiv = document.createElement('div');
                    fieldDiv.className = 'asset-serial-grid';

                    fieldDiv.innerHTML = `
            <div class="asset-serial-header">العنصر رقم ${i}</div>
            <input type="hidden" name="existing_item_id_${itemCounter}_${i}" value="${currentItem.id || ''}">
            <div class="form-group">
                <label>رقم الأصول <span class="required">*</span></label>
                <input type="text" 
                    name="asset_num_${itemCounter}_${i}" 
                    placeholder="أدخل 12 رقم فقط" 
                    pattern="[0-9]{12}" 
                    maxlength="12" 
                    inputmode="numeric"
                    title="يجب إدخال 12 رقم بالضبط"
                    value="${currentItem.asset_num || ''}"
                    required>
                <div class="validation-message asset-validation-${itemCounter}-${i}" style="display: none;"></div>
            </div>
            <div class="form-group">
                <label>الرقم التسلسلي <span class="required">*</span></label>
                <input type="text" 
                    name="serial_num_${itemCounter}_${i}" 
                    placeholder="أدخل الرقم التسلسلي" 
                    value="${currentItem.serial_num || ''}"
                    required>
                <div class="validation-message serial-validation-${itemCounter}-${i}" style="display: none;"></div>
            </div>
            <div class="form-group">
                <label>رقم الموديل</label>
                <input type="text" 
                    name="model_num_${itemCounter}_${i}" 
                    placeholder="أدخل رقم الموديل"
                    value="${currentItem.model_num || ''}">
            </div>
            <div class="form-group">
                <label>رقم الأصول القديمة</label>
                <input type="text" 
                    name="old_asset_num_${itemCounter}_${i}" 
                    placeholder="أدخل رقم الأصول القديمة"
                    value="${currentItem.old_asset_num || ''}">
            </div>
            <div class="form-group">
                <label>البراند</label>
                <input type="text" 
                    name="brand_${itemCounter}_${i}" 
                    placeholder="أدخل اسم البراند"
                    value="${currentItem.brand || ''}">
            </div>
            <div class="form-group">
                <label>السعر</label>
                <input type="text" 
                    name="price_${itemCounter}_${i}" 
                    placeholder="أدخل السعر" 
                    inputmode="decimal"
                    value="${currentItem.price || ''}">
            </div>
        `;

                    assetSerialContainer.appendChild(fieldDiv);

                    // إضافة معالجات التحقق للحقول الجديدة
                    const assetInput = fieldDiv.querySelector(`input[name="asset_num_${itemCounter}_${i}"]`);
                    const serialInput = fieldDiv.querySelector(`input[name="serial_num_${itemCounter}_${i}"]`);

                    const excludeId = currentItem.id || null;

                    assetInput.addEventListener('blur', () => validateAssetSerial(assetInput, itemCounter, i, 'asset', excludeId));
                    serialInput.addEventListener('blur', () => validateAssetSerial(serialInput, itemCounter, i, 'serial', excludeId));
                }

                dynamicSection.style.display = 'block';
            }
        }

        // تعيين الموقع من البيانات
        function setLocationFromData(buildingId, floorId, sectionId, roomId) {
            if (buildingId) {
                const buildingSelect = document.getElementById('buildingSelect');
                buildingSelect.value = buildingId;

                // تحميل الطوابق
                loadFloors(buildingId).then(() => {

                    if (floorId) {
                        const floorSelect = document.getElementById('floorSelect');
                        floorSelect.value = floorId;

                        // تحميل الأقسام
                        loadSections(floorId).then(() => {

                            if (sectionId) {
                                const sectionSelect = document.getElementById('sectionSelect');
                                sectionSelect.value = sectionId;

                                // تحميل الغرف
                                loadRooms(sectionId).then(() => {

                                    if (roomId) {
                                        const roomSelect = document.getElementById('roomSelect');
                                        roomSelect.value = roomId;
                                    }
                                });
                            }
                        });
                    }
                });
            }
        }

        // وظيفة إضافة صنف جديد (نفس الكود الأصلي)
        function addNewItem() {
            itemCounter++;
            const container = document.getElementById('itemsContainer');

            const itemDiv = document.createElement('div');
            itemDiv.className = 'item-card';
            itemDiv.id = `item_${itemCounter}`;

            itemDiv.innerHTML = `
                <div class="item-header">
                    <div class="item-title">الصنف رقم ${itemCounter}</div>
                    <button type="button" class="remove-item-btn" onclick="removeItem(${itemCounter})" title="إزالة الصنف">
                        ×
                    </button>
                </div>
                
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>الصنف <span class="required">*</span></label>
                        <div class="search-dropdown">
                            <input type="text" name="item_${itemCounter}" class="search-input" placeholder="ابحث عن الصنف..." required autocomplete="off">
                            <div class="dropdown-list" id="itemDropdown_${itemCounter}"></div>
                        </div>
                        <div class="classification-display" id="classificationDisplay_${itemCounter}">
                            <div class="classification-item">
                                <span class="classification-label">التصنيف الرئيسي:</span>
                                <span class="classification-value" id="majorCategory_${itemCounter}">-</span>
                            </div>
                            <div class="classification-item">
                                <span class="classification-label">التصنيف الفرعي:</span>
                                <span class="classification-value" id="minorCategory_${itemCounter}">-</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>الكمية <span class="required">*</span></label>
                        <input type="number" name="quantity_${itemCounter}" min="1" max="100" placeholder="أدخل الكمية" required onchange="updateAssetSerialFields(${itemCounter}, this.value)">
                    </div>
                    <div class="form-group">
                        <label>نوع العهدة <span class="required">*</span></label>
                        <select name="custody_type_${itemCounter}" class="custody-type-select" required>
                            <option value="">اختر نوع العهدة</option>
                        </select>
                    </div>
                </div>
                
                <!-- قسم أرقام الأصول والأرقام التسلسلية -->
                <div class="dynamic-fields" id="dynamicFields_${itemCounter}" style="display: none;">
                    <div class="section-header">
                        <h4>أرقام الأصول والأرقام التسلسلية</h4>
                    </div>
                    <div id="assetSerialContainer_${itemCounter}"></div>
                </div>
            `;

            container.appendChild(itemDiv);

            // تهيئة البحث للصنف الجديد
            initItemSearchForElement(itemCounter);

            // تحميل قائمة أنواع العهدة للصنف الجديد
            loadCustodyTypesForItem(itemCounter);
        }

        // باقي الوظائف (نفس الكود الأصلي مع تعديلات طفيفة)
        function removeItem(itemId) {
            if (confirm('هل أنت متأكد من إزالة هذا الصنف؟')) {
                const itemElement = document.getElementById(`item_${itemId}`);
                if (itemElement) {
                    itemElement.remove();
                }
                updateItemTitles();
            }
        }

        function updateItemTitles() {
            const itemCards = document.querySelectorAll('.item-card');
            itemCards.forEach((card, index) => {
                const title = card.querySelector('.item-title');
                if (title) {
                    title.textContent = `الصنف رقم ${index + 1}`;
                }
            });
        }

        // التحقق من تكرار الأرقام (مع استثناء العنصر الحالي)
        function validateAssetSerial(input, itemId, index, type, existingItemId = null) {
            const value = input.value.trim();
            const messageElement = document.querySelector(`.${type}-validation-${itemId}-${index}`);

            // مسح الرسائل السابقة
            if (messageElement) {
                messageElement.style.display = 'none';
                messageElement.className = `validation-message ${type}-validation-${itemId}-${index}`;
            }

            if (!value) {
                return;
            }

            // إظهار رسالة التحميل
            if (messageElement) {
                messageElement.textContent = 'جاري التحقق...';
                messageElement.className += ' loading-msg';
                messageElement.style.display = 'block';
            }

            // إعداد البيانات للإرسال
            const formData = new FormData();
            if (type === 'asset') {
                formData.append('asset_num', value);
                formData.append('check_type', 'asset');
            } else {
                formData.append('serial_num', value);
                formData.append('check_type', 'serial');
            }

            // إضافة معرف العنصر الحالي للاستثناء
            if (existingItemId) {
                formData.append('exclude_item_id', existingItemId);
            }

            fetch(buildUrl('OrderController/validateAssetSerial'), {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (messageElement) {
                        messageElement.style.display = 'none';

                        if (data.success) {
                            // الرقم متاح
                            messageElement.textContent = '✓ متاح';
                            messageElement.className = `validation-message ${type}-validation-${itemId}-${index} success-msg`;
                            messageElement.style.display = 'block';
                            input.style.borderColor = '#27ae60';
                        } else {
                            // يوجد خطأ أو تكرار
                            if (data.errors && data.errors.length > 0) {
                                const error = data.errors.find(e => e.type === type);
                                if (error) {
                                    messageElement.textContent = '✗ ' + error.message;
                                    messageElement.className = `validation-message ${type}-validation-${itemId}-${index} error-msg`;
                                    messageElement.style.display = 'block';
                                    input.style.borderColor = '#e74c3c';
                                }
                            } else {
                                messageElement.textContent = '✗ ' + data.message;
                                messageElement.className = `validation-message ${type}-validation-${itemId}-${index} error-msg`;
                                messageElement.style.display = 'block';
                                input.style.borderColor = '#e74c3c';
                            }
                        }
                    }
                })
                .catch(error => {
                    console.error('خطأ في التحقق:', error);
                    if (messageElement) {
                        messageElement.textContent = 'خطأ في التحقق';
                        messageElement.className = `validation-message ${type}-validation-${itemId}-${index} error-msg`;
                        messageElement.style.display = 'block';
                        input.style.borderColor = '#e74c3c';
                    }
                });
        }

        // البحث عن الأصناف (نفس الكود الأصلي)
        function initItemSearchForElement(itemId) {
            const searchInput = document.querySelector(`input[name="item_${itemId}"]`);
            const dropdown = document.getElementById(`itemDropdown_${itemId}`);
            const classificationDisplay = document.getElementById(`classificationDisplay_${itemId}`);

            if (!searchInput || !dropdown || !classificationDisplay) return;

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.trim();

                if (searchTerm.length < 2) {
                    dropdown.style.display = 'none';
                    classificationDisplay.classList.remove('show');
                    return;
                }

                dropdown.innerHTML = '<div class="dropdown-item loading">جاري البحث...</div>';
                dropdown.style.display = 'block';

                fetch(buildUrl(`OrderController/searchitems?term=${encodeURIComponent(searchTerm)}`))
                    .then(response => response.json())
                    .then(data => {
                        dropdown.innerHTML = '';

                        if (data.success && data.data && data.data.length > 0) {
                            dropdown.innerHTML = data.data
                                .map(item => `<div class="dropdown-item" data-item-name="${item.name}" data-major-category="${item.major_category}" data-minor-category="${item.minor_category}">${item.name}</div>`)
                                .join('');
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

                    const itemName = e.target.dataset.itemName;
                    const majorCategory = e.target.dataset.majorCategory;
                    const minorCategory = e.target.dataset.minorCategory;

                    searchInput.value = itemName;
                    dropdown.style.display = 'none';

                    // عرض التصنيفات
                    document.getElementById(`majorCategory_${itemId}`).textContent = majorCategory || '-';
                    document.getElementById(`minorCategory_${itemId}`).textContent = minorCategory || '-';
                    classificationDisplay.classList.add('show');
                }
            });

            document.addEventListener('click', function(e) {
                if (!e.target.closest(`#item_${itemId} .search-dropdown`)) {
                    dropdown.style.display = 'none';
                }
            });
        }

        // إنشاء حقول الأصول (نفس الكود الأصلي)
        function createAssetSerialFields(itemId, quantity) {
            const container = document.getElementById(`assetSerialContainer_${itemId}`);
            const dynamicSection = document.getElementById(`dynamicFields_${itemId}`);

            if (!container || !dynamicSection) return;

            container.innerHTML = '';

            const qty = parseInt(quantity) || 0;

            if (qty > 0) {
                for (let i = 1; i <= qty; i++) {
                    const fieldDiv = document.createElement('div');
                    fieldDiv.className = 'asset-serial-grid';

                    fieldDiv.innerHTML = `
                        <div class="asset-serial-header">العنصر رقم ${i}</div>
                        <div class="form-group">
                            <label>رقم الأصول <span class="required">*</span></label>
                            <input type="text" 
                                name="asset_num_${itemId}_${i}" 
                                placeholder="أدخل 12 رقم فقط" 
                                pattern="[0-9]{12}" 
                                maxlength="12" 
                                inputmode="numeric"
                                title="يجب إدخال 12 رقم بالضبط"
                                required>
                            <div class="validation-message asset-validation-${itemId}-${i}" style="display: none;"></div>
                        </div>
                        <div class="form-group">
                            <label>الرقم التسلسلي <span class="required">*</span></label>
                            <input type="text" name="serial_num_${itemId}_${i}" placeholder="أدخل الرقم التسلسلي" required>
                            <div class="validation-message serial-validation-${itemId}-${i}" style="display: none;"></div>
                        </div>
                        <div class="form-group">
                            <label>رقم المودل</label>
                            <input type="text" name="model_num_${itemId}_${i}" placeholder="أدخل رقم المودل">
                        </div>
                        <div class="form-group">
                            <label>رقم الأصول القديمة</label>
                            <input type="text" name="old_asset_num_${itemId}_${i}" placeholder="أدخل رقم الأصول القديمة">
                        </div>
                        <div class="form-group">
                            <label>البراند</label>
                            <input type="text" name="brand_${itemId}_${i}" placeholder="أدخل اسم البراند">
                        </div>
                    `;

                    container.appendChild(fieldDiv);

                    const assetInput = fieldDiv.querySelector(`input[name="asset_num_${itemId}_${i}"]`);
                    const serialInput = fieldDiv.querySelector(`input[name="serial_num_${itemId}_${i}"]`);

                    assetInput.addEventListener('blur', () => validateAssetSerial(assetInput, itemId, i, 'asset'));
                    serialInput.addEventListener('blur', () => validateAssetSerial(serialInput, itemId, i, 'serial'));
                }

                dynamicSection.style.display = 'block';
            } else {
                dynamicSection.style.display = 'none';
            }
        }

        // تحميل أنواع العهدة
        function loadCustodyTypesForItem(itemId, selectedValue = null) {
            const custodySelect = document.querySelector(`select[name="custody_type_${itemId}"]`);
            if (!custodySelect) return;

            fetch(buildUrl('OrderController/getformdata'))
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.custody_types && Array.isArray(data.custody_types)) {
                        custodySelect.innerHTML = '<option value="">اختر نوع العهدة</option>';
                        data.custody_types.forEach(type => {
                            const selected = selectedValue && selectedValue === type.id ? 'selected' : '';
                            custodySelect.innerHTML += `<option value="${type.id}" ${selected}>${type.name}</option>`;
                        });
                    }
                })
                .catch(error => {
                    console.error('خطأ في تحميل أنواع العهدة:', error);
                });
        }


        function initUserSearch() {
            const userIdInput = document.getElementById('userId');
            const receiverNameInput = document.getElementById('receiverName');
            const emailInput = document.getElementById('userEmail');
            const transferInput = document.getElementById('transferNumber');
            const loadingMsg = document.getElementById('userLoadingMsg');
            const errorMsg = document.getElementById('userErrorMsg');
            const successMsg = document.getElementById('userSuccessMsg');

            userIdInput.addEventListener('input', function() {
                // منع البحث إذا كان الحقل معطلاً
                if (this.readOnly) return;

                const userId = this.value.trim();

                loadingMsg.style.display = 'none';
                errorMsg.style.display = 'none';
                successMsg.style.display = 'none';

                receiverNameInput.value = '';
                emailInput.value = '';
                transferInput.value = '';

                if (userId.length < 3) return;

                clearTimeout(searchTimeout);

                loadingMsg.style.display = 'block';

                searchTimeout = setTimeout(() => {
                    searchUser(userId);
                }, 500);
            });

            function searchUser(userId) {
                fetch(buildUrl(`OrderController/searchuser?user_id=${encodeURIComponent(userId)}`))
                    .then(response => response.json())
                    .then(data => {
                        loadingMsg.style.display = 'none';

                        if (data.success) {
                            receiverNameInput.value = data.data.name || '';
                            emailInput.value = data.data.email || '';
                            transferInput.value = data.data.transfer_number || '';
                            successMsg.style.display = 'block';
                        } else {
                            errorMsg.textContent = data.message || 'رقم المستخدم غير موجود';
                            errorMsg.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        console.error('خطأ في البحث عن المستخدم:', error);
                        loadingMsg.style.display = 'none';
                        errorMsg.textContent = 'خطأ في الاتصال بالخادم';
                        errorMsg.style.display = 'block';
                    });
            }
        }

        // إدارة المواقع
        function initLocationDropdowns() {
            const buildingSelect = document.getElementById('buildingSelect');
            const floorSelect = document.getElementById('floorSelect');
            const sectionSelect = document.getElementById('sectionSelect');
            const roomSelect = document.getElementById('roomSelect');

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

        function loadFormData() {
            return fetch(buildUrl('OrderController/getformdata'))
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // تحميل المباني
                        if (data.buildings && Array.isArray(data.buildings)) {
                            const buildingSelect = document.getElementById('buildingSelect');
                            buildingSelect.innerHTML = '<option value="">اختر المبنى</option>';
                            data.buildings.forEach(building => {
                                buildingSelect.innerHTML += `<option value="${building.id}">${building.code || building.name}</option>`;
                            });
                        }
                    }
                    return data;
                })
                .catch(error => {
                    console.error('خطأ في تحميل البيانات:', error);
                    return null;
                });
        }

        function loadFloors(buildingId) {
            const floorSelect = document.getElementById('floorSelect');
            const roomSelect = document.getElementById('roomSelect');
            const sectionSelect = document.getElementById('sectionSelect');

            floorSelect.innerHTML = '<option value="">اختر الطابق</option>';
            sectionSelect.innerHTML = '<option value="">اختر القسم</option>';
            roomSelect.innerHTML = '<option value="">اختر الغرفة</option>';

            if (!buildingId) {
                floorSelect.disabled = true;
                roomSelect.disabled = true;
                return Promise.resolve();
            }

            return fetch(buildUrl(`OrderController/getfloorsbybuilding/${buildingId}`))
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data && Array.isArray(data.data)) {
                        data.data.forEach(floor => {
                            floorSelect.innerHTML += `<option value="${floor.id}">${floor.code || floor.name}</option>`;
                        });
                        floorSelect.disabled = false;
                    } else {
                        floorSelect.disabled = true;
                    }
                    return data;
                })
                .catch(error => {
                    console.error('خطأ في تحميل الطوابق:', error);
                    floorSelect.disabled = true;
                    return null;
                });
        }

        function loadSections(floorId) {
            const roomSelect = document.getElementById('roomSelect');
            const sectionSelect = document.getElementById('sectionSelect');


            sectionSelect.innerHTML = '<option value="">اختر القسم</option>';
            roomSelect.innerHTML = '<option value="">اختر الغرفة</option>';

            if (!floorId) {
                roomSelect.disabled = true;
                return Promise.resolve();
            }

            return fetch(buildUrl(`OrderController/getsectionsbyfloor/${floorId}`))
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Sections response:', data);
                    if (data.success && data.data && Array.isArray(data.data)) {
                        data.data.forEach(section => {
                            sectionSelect.innerHTML += `<option value="${section.id}">${section.code || section.name}</option>`;
                        });
                        sectionSelect.disabled = false;
                    } else {
                        sectionSelect.disabled = true;
                    }
                    return data;
                })
                .catch(error => {
                    console.error('خطأ في تحميل الأقسام:', error);
                    sectionSelect.disabled = true;
                });
        }

        function loadRooms(sectionId) {
            const roomSelect = document.getElementById('roomSelect');

            if (!sectionId) {
                roomSelect.disabled = true;
                return Promise.resolve();
            }

            return fetch(buildUrl(`OrderController/getroomsbysection/${sectionId}`))
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data && Array.isArray(data.data)) {
                        roomSelect.innerHTML = '<option value="">اختر الغرفة</option>';
                        data.data.forEach(room => {
                            roomSelect.innerHTML += `<option value="${room.id}">${room.code || room.name}</option>`;
                        });
                        roomSelect.disabled = false;
                    } else {
                        roomSelect.disabled = true;
                    }
                    return data;
                })
                .catch(error => {
                    console.error('خطأ في تحميل الغرف:', error);
                    roomSelect.disabled = true;
                    return null;
                });
        }

        // وظائف النموذج
        function closeEditForm() {
            if (confirm('هل أنت متأكد من الخروج؟ سيتم فقدان أي تعديلات غير محفوظة.')) {
                window.location.href = buildUrl('inventoryController/index');
            }
        }

        function resetToOriginalData() {
            if (confirm('هل أنت متأكد من استعادة البيانات الأصلية؟ سيتم فقدان جميع التعديلات.')) {
                // مسح النموذج الحالي
                document.getElementById('itemsContainer').innerHTML = '';
                itemCounter = 0;

                // إعادة تحميل البيانات الأصلية
                if (originalOrderData && Object.keys(originalOrderData).length > 0) {
                    populateForm(originalOrderData);
                    disableRecipientFieldsIfPending(); //  إعادة تطبيق التعطيل
                } else {
                    // إعادة تحميل من الخادم
                    loadOrderData();
                }
            }
        }

        // معالج إرسال النموذج
        document.getElementById('orderForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // التحقق من وجود أصناف
            const itemCards = document.querySelectorAll('.item-card');
            if (itemCards.length === 0) {
                alert('يجب إضافة صنف واحد على الأقل');
                return;
            }

            // التحقق من وجود أخطاء في التحقق
            const errorMessages = document.querySelectorAll('.validation-message.error-msg');
            if (errorMessages.length > 0) {
                alert('يوجد أخطاء في أرقام الأصول أو الأرقام التسلسلية. يرجى تصحيحها قبل الحفظ.');
                return;
            }

            //  تمكين حقل رقم المستخدم مؤقتًا إذا كان معطلاً للقراءة فقط
            const userIdInput = document.getElementById('userId');
            const wasReadOnly = userIdInput.readOnly;
            if (wasReadOnly) {
                userIdInput.removeAttribute('readonly');
            }


            if (!confirm('هل أنت متأكد من حفظ التعديلات على هذا الطلب؟')) {
                //  إعادة التعطيل إذا لم يتم التأكيد
                if (wasReadOnly) {
                    userIdInput.setAttribute('readonly', true);
                }
                return;
            }

            const formData = new FormData(this);

            // إضافة رقم الطلب للفورم ديتا
            formData.append('order_id', currentOrderId);

            // إظهار شاشة التحميل
            document.getElementById('loadingOverlay').style.display = 'flex';
            document.querySelector('.loading-spinner div').textContent = 'جاري حفظ التعديلات...';

            fetch(buildUrl(`OrderController/updateMultiItem/${currentOrderId}`), {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('loadingOverlay').style.display = 'none';

                    //  إعادة التعطيل إذا كان هناك خطأ في الحفظ لمنع التحرير بعد الفشل
                    if (wasReadOnly) {
                        userIdInput.setAttribute('readonly', true);
                    }

                    if (data.success) {
                        alert('تم حفظ التعديلات بنجاح!');
                        window.location.href = buildUrl('inventoryController/index');
                    } else {
                        alert('خطأ: ' + data.message);
                    }
                })
                .catch(error => {
                    document.getElementById('loadingOverlay').style.display = 'none';
                    console.error('خطأ في حفظ التعديلات:', error);
                    alert('حدث خطأ في حفظ التعديلات: ' + error.message);

                    //  إعادة التعطيل في حالة الخطأ
                    if (wasReadOnly) {
                        userIdInput.setAttribute('readonly', true);
                    }
                });
        });

        // تهيئة جميع الوظائف عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            console.log('تحميل صفحة التعديل');
            console.log('رقم الطلب:', currentOrderId);
            console.log('حالة الطلب (1=قيد الانتظار):', orderStatusId);


            if (!currentOrderId) {
                alert('رقم الطلب غير محدد');
                window.location.href = buildUrl('inventoryController/index');
                return;
            }

            initUserSearch();
            initLocationDropdowns();

            // تحميل بيانات الطلب
            loadOrderData();
        });

        /**
         * وظيفة تحديث/إعادة بناء حقول الأصول والأرقام التسلسلية ديناميكياً.
         * يمكن أن تقبل رقماً جديداً للكمية (من تغيير يدوي) أو مصفوفة بيانات (من تحميل الطلب).
         * @param {number} itemId - معرف الصنف (1، 2، 3، إلخ).
         * @param {number | Array<Object>} quantityOrItems - الكمية الجديدة أو مصفوفة البيانات المحملة.
         */
        function updateAssetSerialFields(itemId, quantityOrItems) {
            const container = document.getElementById(`assetSerialContainer_${itemId}`);
            const dynamicSection = document.getElementById(`dynamicFields_${itemId}`);

            if (!container || !dynamicSection) return;

            let initialItemsData = [];
            let qty = 0;

            if (Array.isArray(quantityOrItems)) {
                // حالة التحميل الأولي: تم تمرير مصفوفة البيانات القديمة (itemGroup.items)
                initialItemsData = quantityOrItems;
                qty = quantityOrItems.length;
            } else {
                // حالة تغيير الكمية: تم تمرير الرقم الجديد (this.value)
                qty = parseInt(quantityOrItems) || 0;

                // 1. جمع البيانات الحالية للحقول الموجودة (للحفاظ على المدخلات اليدوية)
                const existingFieldGrids = container.querySelectorAll('.asset-serial-grid');
                existingFieldGrids.forEach((grid) => {
                    const existingItemId = grid.querySelector('input[type="hidden"]')?.value || null;
                    const assetNum = grid.querySelector('input[name^="asset_num_"]')?.value || '';
                    const serialNum = grid.querySelector('input[name^="serial_num_"]')?.value || '';
                    const modelNum = grid.querySelector('input[name^="model_num_"]')?.value || '';
                    const oldAssetNum = grid.querySelector('input[name^="old_asset_num_"]')?.value || '';
                    const brand = grid.querySelector('input[name^="brand_"]')?.value || '';
                    const price = grid.querySelector('input[name^="price_"]')?.value || '';

                    initialItemsData.push({
                        id: existingItemId,
                        asset_num: assetNum,
                        serial_num: serialNum,
                        model_num: modelNum,
                        old_asset_num: oldAssetNum,
                        brand: brand,
                        price: price //  حفظ حقل السعر

                    });
                });
            }

            // 2. مسح الحقول وإعادة بنائها
            container.innerHTML = '';

            if (qty > 0) {
                for (let i = 1; i <= qty; i++) {
                    const itemData = initialItemsData[i - 1] || { // استخدام البيانات القديمة أو بيانات فارغة
                        id: null,
                        asset_num: '',
                        serial_num: '',
                        model_num: '',
                        old_asset_num: '',
                        brand: '',
                        price: '' //  حقل السعر 
                    };

                    const fieldDiv = document.createElement('div');
                    fieldDiv.className = 'asset-serial-grid';

                    // إذا كان هذا العنصر القديم موجودًا في البيانات المحفوظة، أعد استخدام ID الخاص به
                    const existingItemIdValue = itemData.id || '';

                    fieldDiv.innerHTML = `
                <div class="asset-serial-header">العنصر رقم ${i}</div>
                <input type="hidden" name="existing_item_id_${itemId}_${i}" value="${existingItemIdValue}">
                <div class="form-group">
                    <label>رقم الأصول <span class="required">*</span></label>
                    <input type="text" 
                        name="asset_num_${itemId}_${i}" 
                        placeholder="أدخل 12 رقم فقط" 
                        pattern="[0-9]{12}" 
                        maxlength="12" 
                        inputmode="numeric"
                        title="يجب إدخال 12 رقم بالضبط"
                        value="${itemData.asset_num}"
                        required>
                    <div class="validation-message asset-validation-${itemId}-${i}" style="display: none;"></div>
                </div>
                <div class="form-group">
                    <label>الرقم التسلسلي <span class="required">*</span></label>
                    <input type="text" name="serial_num_${itemId}_${i}" placeholder="أدخل الرقم التسلسلي" value="${itemData.serial_num}" required>
                    <div class="validation-message serial-validation-${itemId}-${i}" style="display: none;"></div>
                </div>
                <div class="form-group">
                    <label>رقم المودل</label>
                    <input type="text" name="model_num_${itemId}_${i}" placeholder="أدخل رقم المودل" value="${itemData.model_num}">
                </div>
                <div class="form-group">
                    <label>رقم الأصول القديمة</label>
                    <input type="text" name="old_asset_num_${itemId}_${i}" placeholder="أدخل رقم الأصول القديمة" value="${itemData.old_asset_num}">
                </div>
                <div class="form-group">
                    <label>البراند</label>
                    <input type="text" name="brand_${itemId}_${i}" placeholder="أدخل اسم البراند" value="${itemData.brand}">
                </div>
   <div class="form-group">
    <label>السعر</label>
    <input type="text" 
        name="price_${itemId}_${i}" 
        placeholder="أدخل السعر مثال: 1500.50" 
        inputmode="decimal" 
        pattern="^[0-9]+(\.[0-9]+)?$"  title="يجب إدخال رقم عشري موجب صالح (باستخدام النقطة كفاصل عشري: 1000.75).">
</div>
            `;

                    container.appendChild(fieldDiv);

                    // إضافة معالجات التحقق للحقول الجديدة
                    const assetInput = fieldDiv.querySelector(`input[name="asset_num_${itemId}_${i}"]`);
                    const serialInput = fieldDiv.querySelector(`input[name="serial_num_${itemId}_${i}"]`);

                    const excludeId = existingItemIdValue || null;

                    assetInput.addEventListener('blur', () => validateAssetSerial(assetInput, itemId, i, 'asset', excludeId));
                    serialInput.addEventListener('blur', () => validateAssetSerial(serialInput, itemId, i, 'serial', excludeId));
                }

                dynamicSection.style.display = 'block'; //  هذا هو السطر الحاسم لإظهار القسم
            } else {
                dynamicSection.style.display = 'none';
            }
        }
    </script>
</body>

</html>