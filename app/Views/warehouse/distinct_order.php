<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نموذج طلب متعدد الأصناف</title>
    <style>
        * {
            font-family: "Cairo", sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f4f4f4;
            direction: rtl;
            text-align: right;
            min-height: 100vh;
            color: #333;
            font-size: 14px;
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

        .content-area {
            padding: 25px;
            background-color: #eff8fa;
            min-height: calc(100vh - 70px);
        }

        .form-container {
            background: linear-gradient(135deg, #168aad, #1d3557);
            padding: 30px;
            border-radius: 20px;
            color: white;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: white;
            margin: 0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 6px;
            font-weight: 500;
            color: white;
            font-size: 13px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            background: rgba(42, 61, 85, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            padding: 10px 12px;
            color: white;
            font-size: 13px;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            background: rgba(52, 73, 94, 0.9);
            border-color: rgba(255, 255, 255, 0.4);
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.1);
        }

        .form-group select option {
            background: #2c3e50;
            color: white;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        .required {
            color: #ff6b6b;
        }

        .items-section {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
        }

        .item-entry {
            background: rgba(255, 255, 255, 0.05);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
        }

        .item-entry-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .item-number {
            background: #3ac0c3;
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

        .remove-item-btn:hover {
            background: rgba(220, 53, 69, 1);
            transform: scale(1.1);
        }

        .add-item-btn {
            background: rgba(40, 167, 69, 0.8);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 13px;
            width: 100%;
            margin-top: 15px;
        }

        .add-item-btn:hover {
            background: rgba(40, 167, 69, 1);
            transform: translateY(-1px);
        }

        .search-dropdown {
            position: relative;
        }

        .dropdown-list {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: rgba(42, 61, 85, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }

        .dropdown-item {
            padding: 10px 12px;
            cursor: pointer;
            color: white;
            font-size: 13px;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background: rgba(58, 192, 195, 0.3);
        }

        .dropdown-item.loading,
        .dropdown-item.no-results,
        .dropdown-item.error {
            cursor: default;
            color: rgba(255, 255, 255, 0.7);
            font-style: italic;
        }

        .classification-display {
            background: rgba(58, 192, 195, 0.1);
            padding: 10px;
            border-radius: 8px;
            margin-top: 10px;
            border: 1px solid rgba(58, 192, 195, 0.3);
            display: none;
        }

        .classification-display.show {
            display: block;
        }

        .classification-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .classification-label {
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
        }

        .classification-value {
            color: #3ac0c3;
            font-weight: 500;
        }

        .validation-message {
            font-size: 12px;
            padding: 5px 10px;
            margin-top: 5px;
            border-radius: 5px;
            display: none;
        }

        .status-message {
            font-size: 12px;
            padding: 5px 10px;
            margin-top: 5px;
            border-radius: 5px;
            display: none;
        }

        .loading-msg {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .error-msg {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }

        .success-msg {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        .submit-btn,
        .cancel-btn,
        .clear-btn {
            padding: 12px 25px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }

        .submit-btn {
            background: rgba(58, 192, 195, 0.9);
            color: white;
        }

        .submit-btn:hover {
            background: rgba(58, 192, 195, 1);
            transform: translateY(-1px);
        }

        .clear-btn {
            background: rgba(255, 152, 0, 0.8);
            color: white;
        }

        .clear-btn:hover {
            background: rgba(255, 152, 0, 1);
        }

        .cancel-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .cancel-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        @media (max-width: 768px) {
            .main-content {
                margin-right: 0;
            }

            .content-area {
                padding: 15px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-container {
                padding: 20px;
            }

            .page-title {
                font-size: 18px;
            }

            .form-actions {
                flex-direction: column;
            }

            .submit-btn,
            .cancel-btn,
            .clear-btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="header">
            <h1 class="page-title">إنشاء طلب متعدد الأصناف</h1>
        </div>

        <div class="content-area">
            <div class="form-container">
                <form id="orderForm">
                    
                    <!-- قسم الأصناف المتعددة -->
                    <div class="items-section">
                        <div class="section-header">
                            <h4 class="section-title">الأصناف المطلوبة</h4>
                        </div>

                        <div id="itemsContainer">
                            <!-- سيتم إضافة الأصناف هنا ديناميكياً -->
                        </div>

                        <button type="button" class="add-item-btn" onclick="addNewItem()">
                            إضافة صنف جديد
                        </button>
                    </div>

                    <!-- قسم بيانات المرسل -->
                    <div class="section-header">
                        <h4 class="section-title">بيانات المرسل</h4>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label>رقم المستخدم المرسل <span class="required">*</span></label>
                            <input type="text" name="from_user_id" id="fromUserId" placeholder="أدخل رقم المستخدم المرسل" required>
                            <div id="fromUserLoadingMsg" class="status-message loading-msg">جاري البحث...</div>
                            <div id="fromUserErrorMsg" class="status-message error-msg">رقم المستخدم غير موجود</div>
                            <div id="fromUserSuccessMsg" class="status-message success-msg">تم العثور على المستخدم</div>
                        </div>
                        <div class="form-group">
                            <label>اسم المرسل <span class="required">*</span></label>
                            <input type="text" id="fromSenderName" placeholder="اسم المرسل سيظهر هنا" readonly>
                        </div>
                        <div class="form-group">
                            <label>البريد الإلكتروني</label>
                            <input type="email" id="fromUserEmail" placeholder="البريد الإلكتروني للمرسل" readonly>
                        </div>
                        <div class="form-group">
                            <label>رقم التحويلة</label>
                            <input type="text" id="fromTransferNumber" placeholder="رقم التحويلة للمرسل" readonly>
                        </div>
                    </div>

                    <!-- قسم بيانات المستلم -->
                    <div class="section-header">
                        <h4 class="section-title">بيانات المستلم</h4>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label>رقم المستخدم <span class="required">*</span></label>
                            <input type="text" name="user_id" id="userId" placeholder="أدخل رقم المستخدم" required>
                            <div id="userLoadingMsg" class="status-message loading-msg">جاري البحث...</div>
                            <div id="userErrorMsg" class="status-message error-msg">رقم المستخدم غير موجود</div>
                            <div id="userSuccessMsg" class="status-message success-msg">تم العثور على المستخدم</div>
                        </div>
                        <div class="form-group">
                            <label>اسم المستلم <span class="required">*</span></label>
                            <input type="text" id="receiverName" placeholder="اسم المستلم سيظهر هنا" readonly>
                        </div>
                        <div class="form-group">
                            <label>البريد الإلكتروني</label>
                            <input type="email" id="userEmail" placeholder="البريد الإلكتروني للمستلم" readonly>
                        </div>
                        <div class="form-group">
                            <label>رقم التحويلة</label>
                            <input type="text" id="transferNumber" placeholder="رقم التحويلة للمستلم" readonly>
                        </div>
                    </div>

                    <!-- قسم موقع المستلم -->
                    <div class="section-header">
                        <h4 class="section-title">موقع المستلم</h4>
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
                        <button type="button" class="cancel-btn" onclick="closeForm()">إلغاء</button>
                        <button type="button" class="clear-btn" onclick="clearForm()">محو النموذج</button>
                        <button type="submit" class="submit-btn">إنشاء الطلب</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // متغيرات عامة
        let itemCounter = 0;
        let savedFormData = {};
        let searchTimeout;
        let debugMode = true;

        // وظيفة إظهار معلومات التصحيح
        function updateDebugInfo(status, request, response) {
            if (!debugMode) return;
            console.log(`Status: ${status}, Request: ${request}, Response:`, response);
        }

        // وظيفة إضافة صنف جديد
        function addNewItem() {
            itemCounter++;
            const container = document.getElementById('itemsContainer');

            const itemEntry = document.createElement('div');
            itemEntry.className = 'item-entry';
            itemEntry.setAttribute('data-item-id', itemCounter);

            itemEntry.innerHTML = `
                <div class="item-entry-header">
                    <span class="item-number">صنف ${itemCounter}</span>
                    <button type="button" class="remove-item-btn" onclick="removeItem(${itemCounter})">&times;</button>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>الصنف <span class="required">*</span></label>
                        <div class="search-dropdown">
                            <input type="text" name="item_name_${itemCounter}" id="itemInput_${itemCounter}" 
                                   class="search-input" placeholder="ابحث عن الصنف..." required autocomplete="off">
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
                        <label>نوع العهدة <span class="required">*</span></label>
                        <select name="custody_type_${itemCounter}" required>
                            <option value="">اختر نوع العهدة</option>
                            <option value="عهدة">عهدة</option>
                            <option value="عهدة عامة">عهدة عامة</option>
                            <option value="عهدة خاصة">عهدة خاصة</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>رقم الأصول <span class="required">*</span></label>
                        <input type="text" name="asset_num_${itemCounter}" placeholder="أدخل 12 رقم فقط" 
                               pattern="[0-9]{12}" maxlength="12" required 
                               onblur="validateAssetSerial(this, ${itemCounter}, 'asset')">
                        <div class="validation-message asset-validation-${itemCounter}"></div>
                    </div>
                    <div class="form-group">
                        <label>الرقم التسلسلي <span class="required">*</span></label>
                        <input type="text" name="serial_num_${itemCounter}" placeholder="أدخل الرقم التسلسلي" required
                               onblur="validateAssetSerial(this, ${itemCounter}, 'serial')">
                        <div class="validation-message serial-validation-${itemCounter}"></div>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>رقم المودل</label>
                        <input type="text" name="model_num_${itemCounter}" placeholder="أدخل رقم المودل">
                    </div>
                    <div class="form-group">
                        <label>رقم الأصول القديمة</label>
                        <input type="text" name="old_asset_num_${itemCounter}" placeholder="أدخل رقم الأصول القديمة">
                    </div>
                </div>

                <div class="form-group full-width">
                    <label>العلامة التجارية</label>
                    <input type="text" name="brand_${itemCounter}" placeholder="أدخل العلامة التجارية">
                </div>

                <div class="form-group full-width">
                    <label>ملاحظات الصنف</label>
                    <textarea name="item_note_${itemCounter}" rows="2" placeholder="أدخل ملاحظات خاصة بهذا الصنف"></textarea>
                </div>
            `;

            container.appendChild(itemEntry);
            setupItemSearch(itemCounter);
        }

        // وظيفة إزالة صنف
        function removeItem(itemId) {
            if (document.querySelectorAll('.item-entry').length === 1) {
                alert('لا يمكن حذف الصنف الوحيد. يجب أن يحتوي الطلب على صنف واحد على الأقل.');
                return;
            }

            const itemEntry = document.querySelector(`[data-item-id="${itemId}"]`);
            if (itemEntry && confirm('هل تريد حذف هذا الصنف؟')) {
                itemEntry.remove();
                updateItemNumbers();
            }
        }

        // وظيفة إعادة ترقيم الأصناف
        function updateItemNumbers() {
            const remainingItems = document.querySelectorAll('.item-entry[data-item-id]');
            remainingItems.forEach((item, index) => {
                const itemNumber = item.querySelector('.item-number');
                if (itemNumber) {
                    itemNumber.textContent = `صنف ${index + 1}`;
                }
            });
        }

        // وظيفة إعداد البحث للصنف
        function setupItemSearch(itemId) {
            const searchInput = document.getElementById(`itemInput_${itemId}`);
            const dropdown = document.getElementById(`itemDropdown_${itemId}`);
            const classificationDisplay = document.getElementById(`classificationDisplay_${itemId}`);

            if (!searchInput || !dropdown) return;

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.trim();
                
                if (searchTerm.length < 2) {
                    dropdown.style.display = 'none';
                    classificationDisplay.classList.remove('show');
                    return;
                }

                dropdown.innerHTML = '<div class="dropdown-item loading">جاري البحث...</div>';
                dropdown.style.display = 'block';

                fetch(`searchitems?term=${encodeURIComponent(searchTerm)}`)
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
                if (!e.target.closest('.search-dropdown')) {
                    dropdown.style.display = 'none';
                }
            });

            searchInput.addEventListener('input', function() {
                if (this.value.trim() === '') {
                    classificationDisplay.classList.remove('show');
                }
            });
        }

        // وظيفة التحقق من تكرار الأرقام
        function validateAssetSerial(input, index, type) {
            const value = input.value.trim();
            const messageElement = document.querySelector(`.${type}-validation-${index}`);
            
            // مسح الرسائل السابقة
            if (messageElement) {
                messageElement.style.display = 'none';
                messageElement.className = `validation-message ${type}-validation-${index}`;
            }
            
            if (!value) {
                return;
            }

            // التحقق من صحة تنسيق رقم الأصول محلياً قبل الإرسال
            if (type === 'asset') {
                if (!/^\d{12}$/.test(value)) {
                    if (messageElement) {
                        messageElement.textContent = '✗ رقم الأصول يجب أن يكون 12 رقم بالضبط';
                        messageElement.className = `validation-message ${type}-validation-${index} error-msg`;
                        messageElement.style.display = 'block';
                        input.style.borderColor = '#e74c3c';
                    }
                    return;
                }
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

            fetch('validateAssetSerial', {
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
                }
            })
            .catch(error => {
                console.error('خطأ في التحقق:', error);
                if (messageElement) {
                    messageElement.textContent = 'خطأ في التحقق';
                    messageElement.className = `validation-message ${type}-validation-${index} error-msg`;
                    messageElement.style.display = 'block';
                    input.style.borderColor = '#e74c3c';
                }
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
                
                // إخفاء جميع الرسائل
                [fromLoadingMsg, fromErrorMsg, fromSuccessMsg].forEach(msg => {
                    if (msg) msg.style.display = 'none';
                });
                
                // مسح الحقول
                [fromSenderNameInput, fromEmailInput, fromTransferInput].forEach(input => {
                    if (input) input.value = '';
                });
                
                if (userId.length < 3) return;

                clearTimeout(searchTimeout);
                
                if (fromLoadingMsg) fromLoadingMsg.style.display = 'block';
                updateDebugInfo('البحث عن المستخدم المرسل...', `searchuser?user_id=${userId}`, 'انتظار...');
                
                searchTimeout = setTimeout(() => {
                    searchFromUser(userId);
                }, 500);
            });

            function searchFromUser(userId) {
                fetch(`searchuser?user_id=${encodeURIComponent(userId)}`)
                    .then(response => {
                        updateDebugInfo('استجابة البحث عن المستخدم المرسل', `searchuser?user_id=${userId}`, `Status: ${response.status}`);
                        
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        updateDebugInfo('نتيجة البحث عن المستخدم المرسل', `searchuser?user_id=${userId}`, data);
                        
                        if (fromLoadingMsg) fromLoadingMsg.style.display = 'none';
                        
                        if (data.success) {
                            if (fromSenderNameInput) fromSenderNameInput.value = data.data.name || '';
                            if (fromEmailInput) fromEmailInput.value = data.data.email || '';
                            if (fromTransferInput) fromTransferInput.value = data.data.extension || '';
                            if (fromSuccessMsg) fromSuccessMsg.style.display = 'block';
                        } else {
                            if (fromErrorMsg) {
                                fromErrorMsg.textContent = data.message || 'رقم المستخدم المرسل غير موجود';
                                fromErrorMsg.style.display = 'block';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('خطأ في البحث عن المستخدم المرسل:', error);
                        updateDebugInfo('خطأ في البحث عن المستخدم المرسل', `searchuser?user_id=${userId}`, error.message);
                        
                        if (fromLoadingMsg) fromLoadingMsg.style.display = 'none';
                        if (fromErrorMsg) {
                            fromErrorMsg.textContent = 'خطأ في الاتصال بالخادم';
                            fromErrorMsg.style.display = 'block';
                        }
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
                
                // إخفاء جميع الرسائل
                [loadingMsg, errorMsg, successMsg].forEach(msg => {
                    if (msg) msg.style.display = 'none';
                });
                
                // مسح الحقول
                [receiverNameInput, emailInput, transferInput].forEach(input => {
                    if (input) input.value = '';
                });
                
                if (userId.length < 3) return;

                clearTimeout(searchTimeout);
                
                if (loadingMsg) loadingMsg.style.display = 'block';
                updateDebugInfo('البحث عن مستخدم...', `searchuser?user_id=${userId}`, 'انتظار...');
                
                searchTimeout = setTimeout(() => {
                    searchUser(userId);
                }, 500);
            });

            function searchUser(userId) {
                fetch(`searchuser?user_id=${encodeURIComponent(userId)}`)
                    .then(response => {
                        updateDebugInfo('استجابة البحث عن مستخدم', `searchuser?user_id=${userId}`, `Status: ${response.status}`);
                        
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        updateDebugInfo('نتيجة البحث عن مستخدم', `searchuser?user_id=${userId}`, data);
                        
                        if (loadingMsg) loadingMsg.style.display = 'none';
                        
                        if (data.success) {
                            if (receiverNameInput) receiverNameInput.value = data.data.name || '';
                            if (emailInput) emailInput.value = data.data.email || '';
                            if (transferInput) transferInput.value = data.data.extension || '';
                            if (successMsg) successMsg.style.display = 'block';
                        } else {
                            if (errorMsg) {
                                errorMsg.textContent = data.message || 'رقم المستخدم غير موجود';
                                errorMsg.style.display = 'block';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('خطأ في البحث عن المستخدم:', error);
                        updateDebugInfo('خطأ في البحث عن مستخدم', `searchuser?user_id=${userId}`, error.message);
                        
                        if (loadingMsg) loadingMsg.style.display = 'none';
                        if (errorMsg) {
                            errorMsg.textContent = 'خطأ في الاتصال بالخادم';
                            errorMsg.style.display = 'block';
                        }
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

            if (buildingSelect) {
                buildingSelect.addEventListener('change', function() {
                    loadFloors(this.value);
                });
            }

            if (floorSelect) {
                floorSelect.addEventListener('change', function() {
                    loadSections(this.value);
                });
            }

            function loadInitialData() {
                updateDebugInfo('تحميل البيانات الأولية...', 'getformdata', 'انتظار...');
                
                fetch(`getformdata`)
                    .then(response => {
                        updateDebugInfo('استجابة البيانات الأولية', 'getformdata', `Status: ${response.status}`);
                        
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        updateDebugInfo('نتيجة البيانات الأولية', 'getformdata', data);
                        
                        if (data.success) {
                            if (data.buildings && Array.isArray(data.buildings) && buildingSelect) {
                                buildingSelect.innerHTML = '<option value="">اختر المبنى</option>';
                                data.buildings.forEach(building => {
                                    buildingSelect.innerHTML += `<option value="${building.id}">${building.code || building.name}</option>`;
                                });
                            }

                            if (data.departments && Array.isArray(data.departments) && departmentSelect) {
                                departmentSelect.innerHTML = '<option value="">اختر القسم</option>';
                                data.departments.forEach(dept => {
                                    departmentSelect.innerHTML += `<option value="${dept}">${dept}</option>`;
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
                if (floorSelect) {
                    floorSelect.innerHTML = '<option value="">اختر الطابق</option>';
                    floorSelect.disabled = !buildingId;
                }
                if (roomSelect) {
                    roomSelect.innerHTML = '<option value="">اختر الغرفة</option>';
                    roomSelect.disabled = true;
                }
                
                if (!buildingId) return;

                fetch(`getfloorsbybuilding/${buildingId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success && data.data && Array.isArray(data.data) && floorSelect) {
                            data.data.forEach(floor => {
                                floorSelect.innerHTML += `<option value="${floor.id}">${floor.code || floor.name}</option>`;
                            });
                            floorSelect.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('خطأ في تحميل الطوابق:', error);
                    });
            }

            function loadSections(floorId) {
                if (roomSelect) {
                    roomSelect.innerHTML = '<option value="">اختر الغرفة</option>';
                    roomSelect.disabled = true;
                }
                
                if (!floorId) return;

                fetch(`getsectionsbyfloor/${floorId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success && data.data && Array.isArray(data.data) && data.data.length > 0) {
                            loadRooms(data.data[0].id);
                        }
                    })
                    .catch(error => {
                        console.error('خطأ في تحميل الأقسام:', error);
                    });
            }

            function loadRooms(sectionId) {
                if (!sectionId || !roomSelect) return;

                fetch(`getroomsbysection/${sectionId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success && data.data && Array.isArray(data.data)) {
                            roomSelect.innerHTML = '<option value="">اختر الغرفة</option>';
                            data.data.forEach(room => {
                                roomSelect.innerHTML += `<option value="${room.id}">${room.code || room.name}</option>`;
                            });
                            roomSelect.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('خطأ في تحميل الغرف:', error);
                    });
            }
        }

        // وظائف النموذج
        function closeForm() {
            if (confirm('هل تريد إلغاء الطلب؟ ستفقد جميع البيانات المدخلة.')) {
                window.location.href = '/inventory6/inventoryController/index';
            }
        }

        function clearForm() {
            if (confirm('هل أنت متأكد من محو جميع البيانات المدخلة؟ ستفقد جميع المعلومات المدخلة في النموذج.')) {
                const form = document.getElementById('orderForm');
                if (form) {
                    form.reset();
                    
                    // مسح جميع الأصناف
                    const itemsContainer = document.getElementById('itemsContainer');
                    if (itemsContainer) itemsContainer.innerHTML = '';
                    itemCounter = 0;
                    
                    // مسح رسائل الحالة
                    const statusMessages = form.querySelectorAll('.status-message, .validation-message');
                    statusMessages.forEach(msg => msg.style.display = 'none');
                    
                    // إعادة تعيين الحقول المعطلة
                    const floorSelect = document.getElementById('floorSelect');
                    const roomSelect = document.getElementById('roomSelect');
                    if (floorSelect) {
                        floorSelect.disabled = true;
                        floorSelect.innerHTML = '<option value="">اختر الطابق</option>';
                    }
                    if (roomSelect) {
                        roomSelect.disabled = true;
                        roomSelect.innerHTML = '<option value="">اختر الغرفة</option>';
                    }
                    
                    // مسح البيانات المحفوظة
                    savedFormData = {};
                    
                    // إضافة صنف واحد افتراضي
                    addNewItem();
                    
                    alert('تم محو جميع البيانات بنجاح');
                }
            }
        }

        // التحقق من صحة النموذج قبل الإرسال
        function validateForm() {
            // التحقق من المرسل
            const fromUserId = document.getElementById('fromUserId');
            const fromUserName = document.getElementById('fromSenderName');
            
            if (!fromUserId || !fromUserId.value || !fromUserName || !fromUserName.value) {
                alert('يجب تحديد المرسل والبحث عنه بشكل صحيح');
                if (fromUserId) fromUserId.focus();
                return false;
            }
            
            // التحقق من المستلم
            const userId = document.getElementById('userId');
            const toUserName = document.getElementById('receiverName');
            
            if (!userId || !userId.value || !toUserName || !toUserName.value) {
                alert('يجب تحديد المستلم والبحث عنه بشكل صحيح');
                if (userId) userId.focus();
                return false;
            }
            
            // التحقق من الموقع
            const roomSelect = document.getElementById('roomSelect');
            if (!roomSelect || !roomSelect.value) {
                alert('يجب تحديد الغرفة');
                if (roomSelect) roomSelect.focus();
                return false;
            }
            
            // التحقق من الأصناف
            const items = document.querySelectorAll('.item-entry');
            if (items.length === 0) {
                alert('يجب إضافة صنف واحد على الأقل');
                return false;
            }
            
            // التحقق من بيانات كل صنف
            for (let i = 0; i < items.length; i++) {
                const item = items[i];
                const itemName = item.querySelector('[name^="item_name_"]');
                const assetNum = item.querySelector('[name^="asset_num_"]');
                const serialNum = item.querySelector('[name^="serial_num_"]');
                const custodyType = item.querySelector('[name^="custody_type_"]');
                
                if (!itemName || !itemName.value.trim()) {
                    alert(`يجب تحديد اسم الصنف للعنصر رقم ${i + 1}`);
                    if (itemName) itemName.focus();
                    return false;
                }
                
                if (!custodyType || !custodyType.value) {
                    alert(`يجب تحديد نوع العهدة للعنصر رقم ${i + 1}`);
                    if (custodyType) custodyType.focus();
                    return false;
                }
                
                if (!assetNum || !assetNum.value.trim()) {
                    alert(`يجب إدخال رقم الأصول للعنصر رقم ${i + 1}`);
                    if (assetNum) assetNum.focus();
                    return false;
                }
                
                if (!serialNum || !serialNum.value.trim()) {
                    alert(`يجب إدخال الرقم التسلسلي للعنصر رقم ${i + 1}`);
                    if (serialNum) serialNum.focus();
                    return false;
                }
                
                // التحقق من تنسيق رقم الأصول (12 رقم)
                if (!/^\d{12}$/.test(assetNum.value.trim())) {
                    alert(`رقم الأصول للعنصر رقم ${i + 1} يجب أن يكون 12 رقم بالضبط`);
                    if (assetNum) assetNum.focus();
                    return false;
                }
            }

            // التحقق من وجود أخطاء في التحقق
            const errorMessages = document.querySelectorAll('.validation-message.error-msg');
            const visibleErrors = Array.from(errorMessages).filter(msg => msg.style.display !== 'none');
            if (visibleErrors.length > 0) {
                alert('يوجد أخطاء في أرقام الأصول أو الأرقام التسلسلية. يرجى تصحيحها قبل الإرسال.');
                return false;
            }

            // التحقق من تكرار الأرقام داخلياً
            const assetNumbers = [];
            const serialNumbers = [];
            
            for (let i = 0; i < items.length; i++) {
                const item = items[i];
                const assetInput = item.querySelector('[name^="asset_num_"]');
                const serialInput = item.querySelector('[name^="serial_num_"]');
                
                if (assetInput && serialInput) {
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
            }
            
            return true;
        }

        // معالج إرسال النموذج
        document.addEventListener('DOMContentLoaded', function() {
            const orderForm = document.getElementById('orderForm');
            if (orderForm) {
                orderForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    if (!validateForm()) {
                        return;
                    }

                    // إنشاء رسالة التأكيد
                    const itemsCount = document.querySelectorAll('.item-entry').length;
                    const fromUserName = document.getElementById('fromSenderName')?.value || '';
                    const toUserName = document.getElementById('receiverName')?.value || '';
                    const building = document.getElementById('buildingSelect')?.selectedOptions[0]?.text || '';
                    const floor = document.getElementById('floorSelect')?.selectedOptions[0]?.text || '';
                    const room = document.getElementById('roomSelect')?.selectedOptions[0]?.text || '';
                    
                    let confirmationMessage = `هل أنت متأكد من إنشاء هذا الطلب؟\n\n`;
                    confirmationMessage += `📋 تفاصيل الطلب:\n`;
                    confirmationMessage += `• عدد الأصناف: ${itemsCount}\n`;
                    confirmationMessage += `• المرسل: ${fromUserName}\n`;
                    confirmationMessage += `• المستلم: ${toUserName}\n`;
                    confirmationMessage += `• الموقع: ${building} - ${floor} - ${room}\n\n`;
                    confirmationMessage += `سيتم إرسال الطلب فور التأكيد.`;
                    
                    if (!confirm(confirmationMessage)) {
                        return;
                    }
                    
                    // عرض رسالة التحميل
                    const submitBtn = document.querySelector('button[type="submit"]');
                    const originalText = submitBtn?.textContent || 'إنشاء الطلب';
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.textContent = 'جاري الإرسال...';
                    }
                    
                    updateDebugInfo('إرسال الطلب...', 'storeMultipleItems', 'انتظار...');
                    
                    const formData = new FormData(this);
                    
                    fetch('storeMultipleItems', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        updateDebugInfo('استجابة الإرسال', 'storeMultipleItems', `Status: ${response.status}`);
                        return response.json();
                    })
                    .then(data => {
                        updateDebugInfo('نتيجة الإرسال', 'storeMultipleItems', data);
                        
                        // إعادة تفعيل الزر
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.textContent = originalText;
                        }
                        
                        if (data.success) {
                            // عرض رسالة نجاح مفصلة
                            let successMessage = `✅ تم إنشاء الطلب بنجاح!\n\n`;
                            successMessage += `📋 تفاصيل الطلب:\n`;
                            successMessage += `• رقم الطلب: ${data.order_id}\n`;
                            successMessage += `• عدد الأصناف: ${data.items_count || itemsCount}\n`;
                            successMessage += `• المرسل: ${fromUserName}\n`;
                            successMessage += `• المستلم: ${toUserName}\n\n`;
                            successMessage += `سيتم توجيهك إلى الصفحة الرئيسية...`;
                            
                            alert(successMessage);
                            
                            // توجيه إلى الصفحة الرئيسية أو صفحة عرض الطلبات
                            setTimeout(() => {
                                window.location.href = '/inventory6/inventoryController/index';
                            }, 1000);
                            
                        } else {
                            // عرض رسالة الخطأ
                            alert('❌ خطأ في إنشاء الطلب:\n' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('خطأ في إرسال الطلب:', error);
                        updateDebugInfo('خطأ في الإرسال', 'storeMultipleItems', error.message);
                        
                        // إعادة تفعيل الزر
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.textContent = originalText;
                        }
                        
                        alert('❌ حدث خطأ في الاتصال بالخادم:\n' + error.message);
                    });
                });
            }

            // تهيئة جميع الوظائف
            console.log('تحميل الصفحة مكتمل');
            updateDebugInfo('تهيئة الصفحة', 'DOMContentLoaded', 'مكتمل');
            
            initFromUserSearch();
            initUserSearch();
            initLocationDropdowns();
            
            // إضافة صنف واحد افتراضي
            addNewItem();
        });
    </script>
</body>
</html>