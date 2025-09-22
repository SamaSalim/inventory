   <!-- النافذة المنبثقة للطلبات -->
    <div class="form-modal" id="orderModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">إنشاء طلب جديد</h3>
                <button class="close-btn" onclick="closeOrderForm()">&times;</button>
            </div>

            <form id="orderForm" action="<?= base_url('InventoryController/store') ?>">
                <input type="hidden" name="from_employee_id" value="<?= esc(session()->get('employee_id')) ?>">

                <!-- بيانات المسلم -->
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

                <!-- بيانات المستلم -->
                <div class="section-header">
                    <h4>بيانات المستلم</h4>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>الرقم الوظيفي <span class="required">*</span></label>
                        <input type="text" name="to_employee_id" id="toEmployeeId" placeholder="أدخل الرقم الوظيفي" required>
                        <div id="employeeLoadingMsg" class="status-message loading-msg">جاري البحث...</div>
                        <div id="employeeErrorMsg" class="status-message error-msg">الرقم الوظيفي غير موجود</div>
                        <div id="employeeSuccessMsg" class="status-message success-msg">تم العثور على الموظف</div>
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

                <!-- موقع المستلم -->
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

                <!-- قسم الطلب الواحد -->
                <div id="singleItemSection">
                    <div class="section-header">
                        <h4>تفاصيل الطلب</h4>
                    </div>

                    <!-- اختيار التصنيف الرئيسي والفرعي -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label>التصنيف الرئيسي <span class="required">*</span></label>
                            <select name="major_category" id="majorCategorySelect" required>
                                <option value="">اختر التصنيف الرئيسي</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>التصنيف الفرعي <span class="required">*</span></label>
                            <select name="minor_category" id="minorCategorySelect" required disabled>
                                <option value="">اختر التصنيف الفرعي</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>الصنف <span class="required">*</span></label>
                            <div class="search-dropdown">
                                <input type="text" name="item" class="search-input" placeholder="ابحث عن الصنف..." autocomplete="off" required>
                                <div class="dropdown-list" id="itemDropdown"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>الكمية <span class="required">*</span></label>
                            <input type="number" name="quantity" min="1" max="100" placeholder="أدخل الكمية" required>
                        </div>
                    </div>

                    <div class="dynamic-fields" id="dynamicFields">
                        <div id="assetSerialContainer"></div>
                    </div>
                </div>

                <!-- قسم الطلبات المتعددة -->
                <div class="multiple-items-section" id="multipleItemsSection">
                    <div class="section-header">
                        <h4>الأصناف المتعددة</h4>
                    </div>

                    <div id="multipleItemsContainer">
                        <!-- سيتم إضافة الأصناف هنا ديناميكياً -->
                    </div>

                    <button type="button" class="add-item-btn" onclick="addNewItemEntry()">
                        <i class="fas fa-plus"></i> إضافة صنف جديد
                    </button>
                </div>

                <!-- الملاحظات -->
                <div class="form-group full-width">
                    <label>ملاحظات إضافية</label>
                    <textarea name="notes" rows="3" placeholder="أدخل أي ملاحظات إضافية للطلب"></textarea>
                </div>

                <!-- أزرار العمل -->
                <div class="form-actions">
                    <button type="button" class="cancel-btn" onclick="closeOrderForm()">إلغاء</button>
                    <button type="submit" class="submit-btn">إنشاء الطلب</button>
                </div>
            </form>
        </div>
    </div>

