
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نموذج طلب جديد</title>
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

        input, select, textarea {
            padding: 12px 16px;
            border: 2px solid rgba(59, 130, 182, 0.3);
            background: rgba(232, 244, 253, 0.05);
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #3b82b6;
            background: rgba(59, 130, 182, 0.15);
            box-shadow: 0 0 0 3px rgba(59, 130, 182, 0.2);
        }

        input::placeholder, textarea::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        input:read-only, select:disabled {
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

        .cancel-btn, .clear-btn, .submit-btn {
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

        .debug-info {
            position: fixed;
            top: 10px;
            left: 10px;
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 10px;
            border-radius: 5px;
            font-size: 12px;
            z-index: 9999;
            max-width: 300px;
        }
    </style>
</head>
<body>
    <!-- معلومات التصحيح -->
    <div id="debugInfo" class="debug-info" style="display: none;">
        <div>Status: <span id="debugStatus">جاهز</span></div>
        <div>Last Request: <span id="debugRequest">لا يوجد</span></div>
        <div>Response: <span id="debugResponse">لا يوجد</span></div>
    </div>

    <div class="form-modal" id="addForm">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">إنشاء طلب جديد</h3>
                <button class="close-btn" onclick="closeAddForm()">&times;</button>
            </div>

            <form id="orderForm">
                <!-- قسم بيانات الطلب -->
                <div class="section-header">
                    <h4>بيانات الطلب</h4>
                </div>
                
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>الصنف <span class="required">*</span></label>
                        <div class="search-dropdown">
                            <input type="text" name="item" class="search-input" placeholder="ابحث عن الصنف..." required autocomplete="off">
                            <div class="dropdown-list" id="itemDropdown"></div>
                        </div>
                        <div class="classification-display" id="classificationDisplay">
                            <div class="classification-item">
                                <span class="classification-label">التصنيف الرئيسي:</span>
                                <span class="classification-value" id="majorCategory">-</span>
                            </div>
                            <div class="classification-item">
                                <span class="classification-label">التصنيف الفرعي:</span>
                                <span class="classification-value" id="minorCategory">-</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>الكمية <span class="required">*</span></label>
                        <input type="number" name="quantity" min="1" max="100" placeholder="أدخل الكمية" required>
                    </div>
                    <div class="form-group">
                        <label>نوع العهدة <span class="required">*</span></label>
                        <select name="custody_type" id="custodyTypeSelect" required>
                            <option value="">اختر نوع العهدة</option>
                        </select>
                    </div>
                </div>

                <!-- قسم أرقام الأصول والأرقام التسلسلية -->
                <div class="dynamic-fields" id="dynamicFields">
                    <div class="section-header">
                        <h4>أرقام الأصول والأرقام التسلسلية</h4>
                    </div>
                    <div id="assetSerialContainer"></div>
                </div>

                <hr>

                <!-- قسم بيانات المرسل -->
                <div class="section-header">
                    <h4>بيانات المرسل</h4>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>رقم المستخدم المرسل <span class="required">*</span></label>
                        <input type="text" name="from_user_id" id="fromUserId" placeholder="أدخل رقم المستخدم المرسل" required>
                        <div id="fromUserLoadingMsg" class="status-message loading-msg" style="display: none;">جاري البحث...</div>
                        <div id="fromUserErrorMsg" class="status-message error-msg" style="display: none;">رقم المستخدم غير موجود</div>
                        <div id="fromUserSuccessMsg" class="status-message success-msg" style="display: none;">تم العثور على المستخدم</div>
                    </div>
                    <div class="form-group">
                        <label>اسم المرسل <span class="required">*</span></label>
                        <input type="text" name="from_sender_name" id="fromSenderName" placeholder="أدخل اسم المرسل" required readonly>
                    </div>
                    <div class="form-group">
                        <label>البريد الإلكتروني</label>
                        <input type="email" name="from_email" id="fromUserEmail" placeholder="أدخل البريد الإلكتروني" readonly>
                    </div>
                    <div class="form-group">
                        <label>رقم التحويلة</label>
                        <input type="text" name="from_transfer_number" id="fromTransferNumber" placeholder="أدخل رقم التحويلة" readonly>
                    </div>
                </div>

                <hr>

                <!-- قسم بيانات المستلم -->
                <div class="section-header">
                    <h4>بيانات المستلم</h4>
                </div>
                
                <div class="form-grid">
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
                        <label>القسم</label>
                        <select name="department" id="departmentSelect">
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

                <!-- قسم الملاحظات -->
                <div class="form-group full-width">
                    <label>ملاحظات إضافية</label>
                    <textarea name="notes" rows="3" placeholder="أدخل أي ملاحظات إضافية للطلب"></textarea>
                </div>

                <!-- أزرار العمليات -->
                <div class="form-actions">
                    <button type="button" class="cancel-btn" onclick="closeAddForm()">إلغاء</button>
                    <button type="button" class="clear-btn" onclick="clearForm()">محو النموذج</button>
                    <button type="submit" class="submit-btn">إنشاء الطلب</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // متغيرات عامة
        let savedFormData = {};
        let searchTimeout;
        let debugMode = false;

        // وظيفة لإظهار معلومات التصحيح
        function updateDebugInfo(status, request, response) {
            if (!debugMode) return;
            
            document.getElementById('debugInfo').style.display = 'block';
            document.getElementById('debugStatus').textContent = status;
            document.getElementById('debugRequest').textContent = request;
            document.getElementById('debugResponse').textContent = JSON.stringify(response).substring(0, 100) + '...';
        }

        // وظيفة البحث في الأصناف مع جلب التصنيفات
        function initItemSearch() {
            const searchInput = document.querySelector('input[name="item"]');
            const dropdown = document.getElementById('itemDropdown');
            const classificationDisplay = document.getElementById('classificationDisplay');

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.trim();
                
                if (searchTerm.length < 2) {
                    dropdown.style.display = 'none';
                    classificationDisplay.classList.remove('show');
                    return;
                }

                dropdown.innerHTML = '<div class="dropdown-item loading">جاري البحث...</div>';
                dropdown.style.display = 'block';

                updateDebugInfo('البحث في الأصناف...', `searchitems?term=${searchTerm}`, 'انتظار...');

                fetch(`searchitems?term=${encodeURIComponent(searchTerm)}`)
                    .then(response => {
                        console.log('Response status:', response.status);
                        updateDebugInfo('استلام الاستجابة', `searchitems?term=${searchTerm}`, `Status: ${response.status}`);
                        
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Search response:', data);
                        updateDebugInfo('نجح البحث', `searchitems?term=${searchTerm}`, data);
                        
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
                        updateDebugInfo('خطأ في البحث', `searchitems?term=${searchTerm}`, error.message);
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
                    document.getElementById('majorCategory').textContent = majorCategory || '-';
                    document.getElementById('minorCategory').textContent = minorCategory || '-';
                    classificationDisplay.classList.add('show');
                }
            });

            document.addEventListener('click', function(e) {
                if (!e.target.closest('.search-dropdown')) {
                    dropdown.style.display = 'none';
                }
            });

            // إخفاء التصنيفات عند مسح حقل البحث
            searchInput.addEventListener('input', function() {
                if (this.value.trim() === '') {
                    classificationDisplay.classList.remove('show');
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
                        <input type="text" 
                            name="asset_num_${i}" 
                            placeholder="أدخل 12 رقم فقط" 
                            pattern="[0-9]{12}" 
                            maxlength="12" 
                            inputmode="numeric"
                            title="يجب إدخال 12 رقم بالضبط"
                            required>
                        <div class="validation-message asset-validation-${i}" style="display: none;"></div>
                        </div>
                        <div class="form-group">
                            <label>الرقم التسلسلي <span class="required">*</span></label>
                            <input type="text" name="serial_num_${i}" placeholder="أدخل الرقم التسلسلي" required>
                            <div class="validation-message serial-validation-${i}" style="display: none;"></div>
                        </div>
                        <div class="form-group">
                            <label>رقم المودل</label>
                            <input type="text" name="model_num_${i}" placeholder="أدخل رقم المودل">
                        </div>
                        <div class="form-group">
                            <label>رقم الأصول القديمة</label>
                            <input type="text" name="old_asset_num_${i}" placeholder="أدخل رقم الأصول القديمة">
                        </div>
                        <div class="form-group">
                        <label>البراند</label>
                        <input type="text" name="brand_${i}" placeholder="أدخل اسم البراند">
                        </div>
                    `;
                    
                    container.appendChild(fieldDiv);
                    
                    // إضافة معالجات التحقق للحقول الجديدة
                    const assetInput = fieldDiv.querySelector(`input[name="asset_num_${i}"]`);
                    const serialInput = fieldDiv.querySelector(`input[name="serial_num_${i}"]`);
                    
                    assetInput.addEventListener('blur', () => validateAssetSerial(assetInput, i, 'asset'));
                    serialInput.addEventListener('blur', () => validateAssetSerial(serialInput, i, 'serial'));
                }
                
                dynamicSection.classList.add('show');
            } else {
                dynamicSection.classList.remove('show');
            }
        }

        // وظيفة التحقق من تكرار الأرقام
        function validateAssetSerial(input, index, type) {
            const value = input.value.trim();
            const messageElement = document.querySelector(`.${type}-validation-${index}`);
            
            // مسح الرسائل السابقة
            messageElement.style.display = 'none';
            messageElement.className = `validation-message ${type}-validation-${index}`;
            
            if (!value) {
                return;
            }

            // إظهار رسالة التحميل
            messageElement.textContent = 'جاري التحقق...';
            messageElement.className += ' loading-msg';
            messageElement.style.display = 'block';
            
            // إعداد البيانات للإرسال
            const formData = new FormData();
            if (type === 'asset') {
                formData.append('asset_num', value);
                formData.append('check_type', 'asset');
            } else {
                formData.append('serial_num', value);
                formData.append('check_type', 'serial');
            }

            fetch('validateAssetSerial', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                messageElement.style.display = 'none';
                
                if (data.success) {
                    // الرقم متاح
                    messageElement.textContent = '✓ متاح';
                    messageElement.className = `validation-message ${type}-validation-${index} success-msg`;
                    messageElement.style.display = 'block';
                    input.style.borderColor = '#27ae60';
                } else {
                    // يوجد خطأ أو تكرار
                    if (data.errors && data.errors.length > 0) {
                        const error = data.errors.find(e => e.type === type);
                        if (error) {
                            messageElement.textContent = '✗ ' + error.message;
                            messageElement.className = `validation-message ${type}-validation-${index} error-msg`;
                            messageElement.style.display = 'block';
                            input.style.borderColor = '#e74c3c';
                        }
                    } else {
                        messageElement.textContent = '✗ ' + data.message;
                        messageElement.className = `validation-message ${type}-validation-${index} error-msg`;
                        messageElement.style.display = 'block';
                        input.style.borderColor = '#e74c3c';
                    }
                }
            })
            .catch(error => {
                console.error('خطأ في التحقق:', error);
                messageElement.textContent = 'خطأ في التحقق';
                messageElement.className = `validation-message ${type}-validation-${index} error-msg`;
                messageElement.style.display = 'block';
                input.style.borderColor = '#e74c3c';
            });
        }

        // إعداد معالج تغيير الكمية
        function initQuantityHandler() {
            const quantityInput = document.querySelector('input[name="quantity"]');
            
            quantityInput.addEventListener('input', function() {
                const quantity = parseInt(this.value) || 0;
                createAssetSerialFields(quantity);
            });
        }

        // وظيفة البحث عن المستخدم المرسل
        function initFromUserSearch() {
            const fromUserIdInput = document.getElementById('fromUserId');
            const fromSenderNameInput = document.getElementById('fromSenderName');
            const fromEmailInput = document.getElementById('fromUserEmail');
            const fromTransferInput = document.getElementById('fromTransferNumber');
            const fromLoadingMsg = document.getElementById('fromUserLoadingMsg');
            const fromErrorMsg = document.getElementById('fromUserErrorMsg');
            const fromSuccessMsg = document.getElementById('fromUserSuccessMsg');

            fromUserIdInput.addEventListener('input', function() {
                const userId = this.value.trim();
                
                fromLoadingMsg.style.display = 'none';
                fromErrorMsg.style.display = 'none';
                fromSuccessMsg.style.display = 'none';
                
                fromSenderNameInput.value = '';
                fromEmailInput.value = '';
                fromTransferInput.value = '';
                
                if (userId.length < 3) return;

                clearTimeout(searchTimeout);
                
                fromLoadingMsg.style.display = 'block';
                updateDebugInfo('البحث عن المستخدم المرسل...', `searchuser?user_id=${userId}`, 'انتظار...');
                
                searchTimeout = setTimeout(() => {
                    searchFromUser(userId);
                }, 500);
            });

            function searchFromUser(userId) {
                fetch(`searchuser?user_id=${encodeURIComponent(userId)}`)
                    .then(response => {
                        console.log('From User search status:', response.status);
                        updateDebugInfo('استجابة البحث عن المستخدم المرسل', `searchuser?user_id=${userId}`, `Status: ${response.status}`);
                        
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('From User search response:', data);
                        updateDebugInfo('نتيجة البحث عن المستخدم المرسل', `searchuser?user_id=${userId}`, data);
                        
                        fromLoadingMsg.style.display = 'none';
                        
                        if (data.success) {
                            fromSenderNameInput.value = data.data.name || '';
                            fromEmailInput.value = data.data.email || '';
                            fromTransferInput.value = data.data.transfer_number || '';
                            fromSuccessMsg.style.display = 'block';
                            
                            fromSenderNameInput.removeAttribute('readonly');
                            fromEmailInput.removeAttribute('readonly');
                            fromTransferInput.removeAttribute('readonly');
                        } else {
                            fromErrorMsg.textContent = data.message || 'رقم المستخدم المرسل غير موجود';
                            fromErrorMsg.style.display = 'block';
                            
                            fromSenderNameInput.setAttribute('readonly', true);
                            fromEmailInput.setAttribute('readonly', true);
                            fromTransferInput.setAttribute('readonly', true);
                        }
                    })
                    .catch(error => {
                        console.error('خطأ في البحث عن المستخدم المرسل:', error);
                        updateDebugInfo('خطأ في البحث عن المستخدم المرسل', `searchuser?user_id=${userId}`, error.message);
                        
                        fromLoadingMsg.style.display = 'none';
                        fromErrorMsg.textContent = 'خطأ في الاتصال بالخادم';
                        fromErrorMsg.style.display = 'block';
                    });
            }
        }

        // وظيفة البحث عن المستخدم المستلم
        function initUserSearch() {
            const userIdInput = document.getElementById('userId');
            const receiverNameInput = document.getElementById('receiverName');
            const emailInput = document.getElementById('userEmail');
            const transferInput = document.getElementById('transferNumber');
            const loadingMsg = document.getElementById('userLoadingMsg');
            const errorMsg = document.getElementById('userErrorMsg');
            const successMsg = document.getElementById('userSuccessMsg');

            userIdInput.addEventListener('input', function() {
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
                updateDebugInfo('البحث عن مستخدم...', `searchuser?user_id=${userId}`, 'انتظار...');
                
                searchTimeout = setTimeout(() => {
                    searchUser(userId);
                }, 500);
            });

            function searchUser(userId) {
                fetch(`searchuser?user_id=${encodeURIComponent(userId)}`)
                    .then(response => {
                        console.log('User search status:', response.status);
                        updateDebugInfo('استجابة البحث عن مستخدم', `searchuser?user_id=${userId}`, `Status: ${response.status}`);
                        
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('User search response:', data);
                        updateDebugInfo('نتيجة البحث عن مستخدم', `searchuser?user_id=${userId}`, data);
                        
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
                            errorMsg.textContent = data.message || 'رقم المستخدم غير موجود';
                            errorMsg.style.display = 'block';
                            
                            receiverNameInput.setAttribute('readonly', true);
                            emailInput.setAttribute('readonly', true);
                            transferInput.setAttribute('readonly', true);
                        }
                    })
                    .catch(error => {
                        console.error('خطأ في البحث عن المستخدم:', error);
                        updateDebugInfo('خطأ في البحث عن مستخدم', `searchuser?user_id=${userId}`, error.message);
                        
                        loadingMsg.style.display = 'none';
                        errorMsg.textContent = 'خطأ في الاتصال بالخادم';
                        errorMsg.style.display = 'block';
                    });
            }
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
                loadSections(this.value);
            });

            function loadInitialData() {
                updateDebugInfo('تحميل البيانات الأولية...', 'getformdata', 'انتظار...');
                
                fetch(`getformdata`)
                    .then(response => {
                        console.log('Form data response status:', response.status);
                        updateDebugInfo('استجابة البيانات الأولية', 'getformdata', `Status: ${response.status}`);
                        
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Form data response:', data);
                        updateDebugInfo('نتيجة البيانات الأولية', 'getformdata', data);
                        
                        if (data.success) {
                            if (data.buildings && Array.isArray(data.buildings)) {
                                buildingSelect.innerHTML = '<option value="">اختر المبنى</option>';
                                data.buildings.forEach(building => {
                                    buildingSelect.innerHTML += `<option value="${building.id}">${building.code || building.name}</option>`;
                                });
                            }

                            if (data.departments && Array.isArray(data.departments)) {
                                departmentSelect.innerHTML = '<option value="">اختر القسم</option>';
                                data.departments.forEach(dept => {
                                    departmentSelect.innerHTML += `<option value="${dept}">${dept}</option>`;
                                });
                            }

                            if (data.custody_types && Array.isArray(data.custody_types)) {
                                const custodyTypeSelect = document.getElementById('custodyTypeSelect');
                                custodyTypeSelect.innerHTML = '<option value="">اختر نوع العهدة</option>';
                                data.custody_types.forEach(type => {
                                    custodyTypeSelect.innerHTML += `<option value="${type.id}">${type.name}</option>`;
                                });
                            }
                        }
                    })
                    .catch(error => {
                        console.error('خطأ في تحميل البيانات:', error);
                        updateDebugInfo('خطأ في تحميل البيانات', 'getformdata', error.message);
                    });
            }

            function loadFloors(buildingId) {
                floorSelect.innerHTML = '<option value="">اختر الطابق</option>';
                roomSelect.innerHTML = '<option value="">اختر الغرفة</option>';
                
                if (!buildingId) {
                    floorSelect.disabled = true;
                    roomSelect.disabled = true;
                    return;
                }

                fetch(`getfloorsbybuilding/${buildingId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Floors response:', data);
                        if (data.success && data.data && Array.isArray(data.data)) {
                            data.data.forEach(floor => {
                                floorSelect.innerHTML += `<option value="${floor.id}">${floor.code || floor.name}</option>`;
                            });
                            floorSelect.disabled = false;
                        } else {
                            floorSelect.disabled = true;
                        }
                    })
                    .catch(error => {
                        console.error('خطأ في تحميل الطوابق:', error);
                        floorSelect.disabled = true;
                    });
            }

            function loadSections(floorId) {
                roomSelect.innerHTML = '<option value="">اختر الغرفة</option>';
                
                if (!floorId) {
                    roomSelect.disabled = true;
                    return;
                }

                fetch(`getsectionsbyfloor/${floorId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Sections response:', data);
                        if (data.success && data.data && Array.isArray(data.data) && data.data.length > 0) {
                            loadRooms(data.data[0].id);
                        } else {
                            roomSelect.disabled = true;
                        }
                    })
                    .catch(error => {
                        console.error('خطأ في تحميل الأقسام:', error);
                        roomSelect.disabled = true;
                    });
            }

            function loadRooms(sectionId) {
                if (!sectionId) {
                    roomSelect.disabled = true;
                    return;
                }

                fetch(`getroomsbysection/${sectionId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Rooms response:', data);
                        if (data.success && data.data && Array.isArray(data.data)) {
                            roomSelect.innerHTML = '<option value="">اختر الغرفة</option>';
                            data.data.forEach(room => {
                                roomSelect.innerHTML += `<option value="${room.id}">${room.code || room.name}</option>`;
                            });
                            roomSelect.disabled = false;
                        } else {
                            roomSelect.disabled = true;
                        }
                    })
                    .catch(error => {
                        console.error('خطأ في تحميل الغرف:', error);
                        roomSelect.disabled = true;
                    });
            }
        }

        // وظائف النموذج
        function openAddForm() {
            const modal = document.getElementById('addForm');
            if (modal) {
                modal.style.display = 'flex';
                restoreFormData();
            }
        }

        function closeAddForm() {
            saveFormData();
            // إعادة التوجيه إلى صفحة المخزون
            window.location.href = '/inventory6/inventoryController/index';
        }

        function saveFormData() {
            const form = document.querySelector('#addForm form');
            if (form) {
                const formData = new FormData(form);
                savedFormData = {};
                
                for (let [key, value] of formData.entries()) {
                    savedFormData[key] = value;
                }
                
                // حفظ البيانات في localStorage كنسخ احتياطية
                try {
                    localStorage.setItem('orderFormData', JSON.stringify(savedFormData));
                    console.log('تم حفظ البيانات تلقائياً');
                } catch (error) {
                    console.log('تعذر حفظ البيانات في التخزين المحلي');
                }
            }
        }

        function restoreFormData() {
            const form = document.querySelector('#addForm form');
            
            // محاولة استرداد البيانات من localStorage أولاً
            try {
                const savedData = localStorage.getItem('orderFormData');
                if (savedData) {
                    savedFormData = JSON.parse(savedData);
                }
            } catch (error) {
                console.log('تعذر استرداد البيانات من التخزين المحلي');
            }
            
            if (form && Object.keys(savedFormData).length > 0) {
                for (let [key, value] of Object.entries(savedFormData)) {
                    const input = form.querySelector(`[name="${key}"]`);
                    if (input) {
                        input.value = value;
                        
                        // إذا كان حقل الكمية، قم بإنشاء الحقول الديناميكية
                        if (key === 'quantity' && parseInt(value) > 0) {
                            createAssetSerialFields(parseInt(value));
                        }
                    }
                }
                
                // استعادة التصنيفات إذا كانت موجودة
                const itemInput = form.querySelector('input[name="item"]');
                if (itemInput && itemInput.value.trim() && savedFormData.major_category) {
                    document.getElementById('majorCategory').textContent = savedFormData.major_category || '-';
                    document.getElementById('minorCategory').textContent = savedFormData.minor_category || '-';
                    document.getElementById('classificationDisplay').classList.add('show');
                }
            }
        }

        function clearForm() {
            if (confirm('هل أنت متأكد من محو جميع البيانات المدخلة؟ ستفقد جميع المعلومات المدخلة في النموذج.')) {
                const form = document.querySelector('#addForm form');
                if (form) {
                    form.reset();
                    
                    // إخفاء التصنيفات والحقول الديناميكية
                    document.getElementById('classificationDisplay').classList.remove('show');
                    document.getElementById('dynamicFields').classList.remove('show');
                    document.getElementById('assetSerialContainer').innerHTML = '';
                    
                    // مسح رسائل الحالة
                    const statusMessages = form.querySelectorAll('.status-message');
                    statusMessages.forEach(msg => msg.style.display = 'none');
                    
                    // إعادة تعيين الحقول المعطلة
                    const floorSelect = document.getElementById('floorSelect');
                    const roomSelect = document.getElementById('roomSelect');
                    floorSelect.disabled = true;
                    roomSelect.disabled = true;
                    floorSelect.innerHTML = '<option value="">اختر الطابق</option>';
                    roomSelect.innerHTML = '<option value="">اختر الغرفة</option>';
                    
                    // مسح البيانات المحفوظة
                    savedFormData = {};
                    try {
                        localStorage.removeItem('orderFormData');
                    } catch (error) {
                        console.log('تعذر مسح البيانات من التخزين المحلي');
                    }
                    
                    alert('تم محو جميع البيانات بنجاح');
                }
            }
        }

        function clearSavedData() {
            savedFormData = {};
            try {
                localStorage.removeItem('orderFormData');
            } catch (error) {
                console.log('تعذر مسح البيانات من التخزين المحلي');
            }
        }

        // وظيفة حفظ تلقائي للبيانات عند تغيير أي حقل
        function initAutoSave() {
            const form = document.querySelector('#addForm form');
            if (form) {
                const inputs = form.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    input.addEventListener('change', saveFormData);
                    input.addEventListener('input', saveFormData);
                });
                
                // حفظ تلقائي كل 30 ثانية
                setInterval(saveFormData, 30000);
            }
        }

        // وظيفة التحقق من التكرار في JavaScript
        function validateAssetSerialNumbers(quantity) {
            const assetNumbers = [];
            const serialNumbers = [];
            
            for (let i = 1; i <= quantity; i++) {
                const assetInput = document.querySelector(`input[name="asset_num_${i}"]`);
                const serialInput = document.querySelector(`input[name="serial_num_${i}"]`);
                
                if (!assetInput || !serialInput) continue;
                
                const assetNum = assetInput.value.trim();
                const serialNum = serialInput.value.trim();
                
                if (assetNumbers.includes(assetNum)) {
                    alert(`رقم الأصول ${assetNum} مكرر. يجب أن يكون كل رقم أصول فريد.`);
                    assetInput.focus();
                    return false;
                }
                
                if (serialNumbers.includes(serialNum)) {
                    alert(`الرقم التسلسلي ${serialNum} مكرر. يجب أن يكون كل رقم تسلسلي فريد.`);
                    serialInput.focus();
                    return false;
                }
                
                assetNumbers.push(assetNum);
                serialNumbers.push(serialNum);
            }
            
            return true;
        }

        // معالج إرسال النموذج
        document.getElementById('orderForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const quantity = parseInt(document.querySelector('input[name="quantity"]').value) || 0;
            
            // التحقق من وجود أخطاء في التحقق
            const errorMessages = document.querySelectorAll('.validation-message.error-msg');
            if (errorMessages.length > 0) {
                alert('يوجد أخطاء في أرقام الأصول أو الأرقام التسلسلية. يرجى تصحيحها قبل الإرسال.');
                return;
            }
            
            // التحقق من أرقام الأصول والأرقام التسلسلية
            for (let i = 1; i <= quantity; i++) {
                const assetInput = document.querySelector(`input[name="asset_num_${i}"]`);
                const serialInput = document.querySelector(`input[name="serial_num_${i}"]`);
                
                if (!assetInput || !assetInput.value.trim()) {
                    alert(`يجب إدخال رقم الأصول للعنصر رقم ${i}`);
                    if (assetInput) assetInput.focus();
                    return;
                }
                
                if (!serialInput || !serialInput.value.trim()) {
                    alert(`يجب إدخال الرقم التسلسلي للعنصر رقم ${i}`);
                    if (serialInput) serialInput.focus();
                    return;
                }
            }
            
            if (!validateAssetSerialNumbers(quantity)) {
                return;
            }

            // إنشاء رسالة التأكيد مع ملخص الطلب
            const itemName = document.querySelector('input[name="item"]').value;
            const fromUserName = document.getElementById('fromSenderName').value;
            const toUserName = document.getElementById('receiverName').value;
            const building = document.getElementById('buildingSelect').selectedOptions[0]?.text || '';
            const floor = document.getElementById('floorSelect').selectedOptions[0]?.text || '';
            const room = document.getElementById('roomSelect').selectedOptions[0]?.text || '';
            
            let confirmationMessage = `هل أنت متأكد من إنشاء هذا الطلب؟\n\n`;
            confirmationMessage += `📋 تفاصيل الطلب:\n`;
            confirmationMessage += `• الصنف: ${itemName}\n`;
            confirmationMessage += `• الكمية: ${quantity}\n`;
            confirmationMessage += `• المرسل: ${fromUserName}\n`;
            confirmationMessage += `• المستلم: ${toUserName}\n`;
            confirmationMessage += `• الموقع: ${building} - ${floor} - ${room}\n\n`;
            
            if (quantity > 0) {
                confirmationMessage += `🔢 أرقام الأصول والتسلسلية:\n`;
                for (let i = 1; i <= quantity; i++) {
                    const assetNum = document.querySelector(`input[name="asset_num_${i}"]`).value;
                    const serialNum = document.querySelector(`input[name="serial_num_${i}"]`).value;
                    confirmationMessage += `• العنصر ${i}: ${assetNum} | ${serialNum}\n`;
                }
            }
            
            confirmationMessage += `\nسيتم إرسال الطلب فور التأكيد.`;
            
            if (!confirm(confirmationMessage)) {
                return;
            }
            
            updateDebugInfo('إرسال الطلب...', 'store', 'انتظار...');
            
            const formData = new FormData(this);
            
            fetch(`store`, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                updateDebugInfo('استجابة الإرسال', 'store', `Status: ${response.status}`);
                return response.json();
            })
            .then(data => {
                updateDebugInfo('نتيجة الإرسال', 'store', data);
                
                if (data.success) {
                    alert('تم إرسال الطلب بنجاح! رقم الطلب: ' + data.order_id);
                    
                    // مسح جميع البيانات المحفوظة
                    clearSavedData();
                    
                    // مسح النموذج بالكامل
                    const form = document.querySelector('#addForm form');
                    if (form) {
                        form.reset();
                        
                        // إخفاء التصنيفات والحقول الديناميكية
                        document.getElementById('classificationDisplay').classList.remove('show');
                        document.getElementById('dynamicFields').classList.remove('show');
                        document.getElementById('assetSerialContainer').innerHTML = '';
                        
                        // مسح رسائل الحالة والتحقق
                        const statusMessages = form.querySelectorAll('.status-message, .validation-message');
                        statusMessages.forEach(msg => msg.style.display = 'none');
                        
                        // إعادة تعيين الحقول المعطلة
                        const floorSelect = document.getElementById('floorSelect');
                        const roomSelect = document.getElementById('roomSelect');
                        floorSelect.disabled = true;
                        roomSelect.disabled = true;
                        floorSelect.innerHTML = '<option value="">اختر الطابق</option>';
                        roomSelect.innerHTML = '<option value="">اختر الغرفة</option>';
                        
                        // إعادة تعيين حدود الحقول
                        const allInputs = form.querySelectorAll('input, select, textarea');
                        allInputs.forEach(input => {
                            input.style.borderColor = 'rgba(59, 130, 182, 0.3)';
                        });
                    }
                    
                    // إعادة التوجيه إلى صفحة المخزون بعد نجاح الطلب
                    window.location.href = '/inventory6/inventoryController/index';
                } else {
                    alert('خطأ: ' + data.message);
                }
            })
            .catch(error => {
                console.error('خطأ في إرسال الطلب:', error);
                updateDebugInfo('خطأ في الإرسال', 'store', error.message);
                alert('حدث خطأ في إرسال الطلب: ' + error.message);
            });
        });

        // تهيئة جميع الوظائف عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            console.log('تحميل الصفحة مكتمل');
            updateDebugInfo('تهيئة الصفحة', 'DOMContentLoaded', 'مكتمل');
            
            initItemSearch();
            initQuantityHandler();
            initFromUserSearch();
            initUserSearch();
            initLocationDropdowns();
            initAutoSave(); // تفعيل الحفظ التلقائي
            
            //  تعطيل إغلاق النافذة عند الضغط خارجها
            // document.getElementById('addForm').addEventListener('click', function(e) {
            //     if (e.target === this) {
            //         closeAddForm();
            //     }
            // });
        });

        // فتح النموذج تلقائياً للعرض
        openAddForm();
    </script>
</body>
</html>