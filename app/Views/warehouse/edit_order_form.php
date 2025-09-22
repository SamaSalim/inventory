<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-header h4 {
            color: white;
            margin: 0;
            font-size: 1.1rem;
        }

        .items-count {
            color: #ccc;
            font-size: 12px;
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            background: rgba(59, 130, 182, 0.1);
            border-radius: 5px;
        }

        .item-header-content {
            flex: 1;
        }

        .item-name {
            color: #3b82b6;
            font-weight: 600;
            font-size: 1rem;
        }

        /* زر حذف العنصر */
        .delete-item-btn {
            background: rgba(231, 76, 60, 0.1);
            border: 2px solid rgba(231, 76, 60, 0.3);
            color: #e74c3c;
            padding: 8px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
        }

        .delete-item-btn:hover {
            background: rgba(231, 76, 60, 0.2);
            border-color: rgba(231, 76, 60, 0.5);
            transform: translateY(-1px);
        }

        .delete-item-btn:active {
            transform: translateY(0);
        }

        /* مودال حذف العنصر */
        .delete-item-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 3000;
        }

        .delete-item-content {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            padding: 30px;
            border-radius: 15px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(59, 130, 182, 0.3);
        }

        .delete-item-title {
            color: #e74c3c;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .delete-item-title i {
            margin-left: 8px;
            font-size: 20px;
        }

        .delete-item-message {
            color: #ecf0f1;
            margin-bottom: 25px;
            line-height: 1.6;
            font-size: 14px;
        }

        .delete-item-actions {
            display: flex;
            justify-content: space-between;
            gap: 15px;
        }

        .confirm-btn {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .confirm-delete-btn {
            background: linear-gradient(45deg, #e74c3c, #c0392b);
            color: white;
        }

        .confirm-delete-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);
        }

        .confirm-cancel-btn {
            background: linear-gradient(45deg, #95a5a6, #7f8c8d);
            color: white;
        }

        .confirm-cancel-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(149, 165, 166, 0.3);
        }

        /* أنيميشن إزالة العنصر */
        .existing-item.removing {
            transition: all 0.5s ease;
            opacity: 0;
            transform: translateX(-100%) scale(0.8);
            max-height: 0;
            overflow: hidden;
            margin-bottom: 0;
            padding: 0;
        }

        /* رسالة عدم وجود عناصر */
        #noItemsMessage {
            background: rgba(231, 76, 60, 0.1);
            border: 2px dashed rgba(231, 76, 60, 0.3);
            border-radius: 10px;
            padding: 30px;
            color: #e74c3c;
            font-size: 16px;
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

                <!-- القسم المحسن للعناصر الحالية -->
                <div class="section-header">
                    <div>
                        <h4>العناصر الحالية في الطلب</h4>
                    </div>
                    <div class="items-count">
                        عدد العناصر: <span id="itemsCount"><?= count($orderItems) ?></span>
                    </div>
                </div>

                <div id="existingItemsContainer">
                    <?php if (count($orderItems) > 0): ?>
                        <?php foreach ($orderItems as $index => $item): ?>
                            <div class="asset-serial-grid existing-item" data-item-id="<?= esc($item->item_order_id) ?>">
                                <div class="asset-serial-header">
                                    <div class="item-header-content">
                                        <span class="item-name">الصنف: <?= esc($item->item_name) ?></span>
                                        <small style="color: #ccc; font-size: 11px;">
                                            (ID: <?= esc($item->item_order_id) ?>)
                                        </small>
                                    </div>
                                    
                                    <!-- زر حذف العنصر -->
                                    <button type="button" class="delete-item-btn" onclick="deleteItemConfirm(<?= esc($item->item_order_id) ?>, '<?= esc($item->item_name) ?>')" title="حذف هذا العنصر">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                
                                <!-- استخدام item_order_id الصحيح -->
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
                    <?php else: ?>
                        <div style="text-align: center; color: #888; padding: 20px;" id="noItemsMessage">
                            لا توجد عناصر في هذا الطلب
                        </div>
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

    <!-- مودال تأكيد حذف العنصر -->
    <div class="delete-item-modal" id="deleteItemModal">
        <div class="delete-item-content">
            <div class="delete-item-title">
                <i class="fas fa-exclamation-triangle"></i>
                تأكيد حذف العنصر
            </div>
            <div class="delete-item-message" id="deleteItemMessage">
                هل أنت متأكد من حذف هذا العنصر؟
            </div>
            <div class="delete-item-actions">
                <button class="confirm-btn confirm-cancel-btn" onclick="cancelDeleteItem()">
                    إلغاء
                </button>
                <button class="confirm-btn confirm-delete-btn" onclick="confirmDeleteItem()">
                    <i class="fas fa-trash"></i>
                    تأكيد الحذف
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
     let itemToDelete = null;
let itemNameToDelete = '';

document.addEventListener('DOMContentLoaded', function() {
    initEmployeeSearch();
    initLocationDropdowns();
    initItemSearchSimple('newItemSearch', 'newItemDropdown');

    document.getElementById('editOrderForm').addEventListener('submit', function(e) {
        e.preventDefault();
        updateOrder();
    });

    // منع إرسال الفورم عند الضغط على زر الحذف
    document.querySelectorAll('.delete-item-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
        });
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

// وظائف حذف العناصر
function deleteItemConfirm(itemId, itemName) {
    // فحص عدد العناصر المتبقية
    const itemsCount = document.querySelectorAll('.existing-item').length;
    
    if (itemsCount <= 1) {
        alert('لا يمكن حذف العنصر الأخير من الطلب.\nاحذف الطلب كاملاً بدلاً من ذلك.');
        return;
    }
    
    itemToDelete = itemId;
    itemNameToDelete = itemName;
    
    document.getElementById('deleteItemModal').style.display = 'flex';
    document.getElementById('deleteItemMessage').innerHTML = `
        هل أنت متأكد من حذف العنصر:<br>
        <strong style="color: #3b82b6;">${itemName}</strong><br>
        <small style="color: #e74c3c;">لا يمكن التراجع عن هذا الإجراء.</small>
    `;
}

function cancelDeleteItem() {
    itemToDelete = null;
    itemNameToDelete = '';
    document.getElementById('deleteItemModal').style.display = 'none';
}

function confirmDeleteItem() {
    if (!itemToDelete) {
        cancelDeleteItem();
        return;
    }

    const itemContainer = document.querySelector(`[data-item-id="${itemToDelete}"]`);
    const deleteBtn = itemContainer.querySelector('.delete-item-btn');
    const originalHtml = deleteBtn.innerHTML;
    
    // إظهار حالة التحميل
    deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    deleteBtn.disabled = true;

    fetch(`<?= base_url('InventoryController/deleteOrderItem') ?>/${itemToDelete}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // إزالة العنصر من الواجهة
            itemContainer.classList.add('removing');
            
            setTimeout(() => {
                itemContainer.remove();
                updateItemsCount();
                
                // إذا لم تعد هناك عناصر، أظهر رسالة
                const remainingItems = document.querySelectorAll('.existing-item');
                if (remainingItems.length === 0) {
                    document.getElementById('existingItemsContainer').innerHTML = `
                        <div style="text-align: center; color: #888; padding: 20px;" id="noItemsMessage">
                            لا توجد عناصر في هذا الطلب
                        </div>
                    `;
                }
            }, 500);
            
            alert(data.message);
        } else {
            alert('خطأ: ' + data.message);
            
            // إعادة حالة الزر
            deleteBtn.innerHTML = originalHtml;
            deleteBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('خطأ في حذف العنصر:', error);
        alert('حدث خطأ أثناء حذف العنصر');
        
        // إعادة حالة الزر
        deleteBtn.innerHTML = originalHtml;
        deleteBtn.disabled = false;
    })
    .finally(() => {
        cancelDeleteItem();
    });
}

function updateItemsCount() {
    const itemsCount = document.querySelectorAll('.existing-item').length;
    document.getElementById('itemsCount').textContent = itemsCount;
}

// إغلاق المودال عند الضغط خارجه
document.getElementById('deleteItemModal').addEventListener('click', function(e) {
    if (e.target === this) {
        cancelDeleteItem();
    }
});

// ✅ دالة البحث عن الموظف المحسنة
function initEmployeeSearch() {
    const employeeIdInput = document.getElementById('toEmployeeId');
    const receiverNameInput = document.getElementById('receiverName');
    const emailInput = document.getElementById('employeeEmail');
    const transferInput = document.getElementById('transferNumber');
    const loadingMsg = document.getElementById('employeeLoadingMsg');
    const errorMsg = document.getElementById('employeeErrorMsg');
    const successMsg = document.getElementById('employeeSuccessMsg');
    let searchTimeout;

    if (!employeeIdInput) return;

    // ✅ حفظ القيم الأصلية عند التحميل
    const initialEmployeeId = employeeIdInput.value.trim();
    let currentEmployeeId = initialEmployeeId;
    let lastValidData = {
        name: receiverNameInput.value,
        email: emailInput.value,
        transfer_number: transferInput.value
    };

    // ✅ إذا كانت هناك بيانات موجودة، أظهر رسالة النجاح
    if (initialEmployeeId && receiverNameInput.value) {
        successMsg.style.display = 'block';
    }

    employeeIdInput.addEventListener('input', function() {
        const newEmployeeId = this.value.trim();
        
        // ✅ إخفاء جميع الرسائل أولاً
        loadingMsg.style.display = 'none';
        errorMsg.style.display = 'none';
        successMsg.style.display = 'none';

        // ✅ إذا كان الرقم الوظيفي لم يتغير، لا تفعل شيئاً
        if (newEmployeeId === currentEmployeeId) {
            // إعادة إظهار رسالة النجاح إذا كانت البيانات موجودة
            if (receiverNameInput.value) {
                successMsg.style.display = 'block';
            }
            return;
        }

        // ✅ إذا كان الحقل فارغ، امسح البيانات
        if (newEmployeeId === '') {
            receiverNameInput.value = '';
            emailInput.value = '';
            transferInput.value = '';
            currentEmployeeId = '';
            lastValidData = { name: '', email: '', transfer_number: '' };
            return;
        }

        // ✅ إذا كان الرقم الوظيفي قصير جداً، لا تبحث لكن امسح البيانات
        if (newEmployeeId.length < 3) {
            receiverNameInput.value = '';
            emailInput.value = '';
            transferInput.value = '';
            currentEmployeeId = newEmployeeId;
            return;
        }

        // ✅ إذا تغير الرقم الوظيفي فعلاً، ابدأ البحث
        clearTimeout(searchTimeout);
        loadingMsg.style.display = 'block';
        
        // مسح البيانات مؤقتاً أثناء البحث
        receiverNameInput.value = '';
        emailInput.value = '';
        transferInput.value = '';

        searchTimeout = setTimeout(() => {
            searchEmployee(newEmployeeId);
        }, 500);
    });

    function searchEmployee(employeeId) {
        fetch(`<?= base_url('InventoryController/searchemployee') ?>?emp_id=${encodeURIComponent(employeeId)}`)
            .then(response => response.json())
            .then(data => {
                loadingMsg.style.display = 'none';
                
                if (data.success) {
                    // ✅ حفظ البيانات الجديدة
                    const newData = {
                        name: data.data.name || '',
                        email: data.data.email || '',
                        transfer_number: data.data.transfer_number || ''
                    };
                    
                    receiverNameInput.value = newData.name;
                    emailInput.value = newData.email;
                    transferInput.value = newData.transfer_number;
                    
                    // ✅ تحديث المتغيرات
                    currentEmployeeId = employeeId;
                    lastValidData = newData;
                    
                    successMsg.style.display = 'block';
                } else {
                    // ✅ في حالة الفشل، استعادة البيانات السابقة إذا كان الرقم الوظيفي عاد للقيمة الأصلية
                    if (employeeId === initialEmployeeId && lastValidData.name) {
                        receiverNameInput.value = lastValidData.name;
                        emailInput.value = lastValidData.email;
                        transferInput.value = lastValidData.transfer_number;
                        currentEmployeeId = employeeId;
                        successMsg.style.display = 'block';
                    } else {
                        errorMsg.textContent = data.message || 'الرقم الوظيفي غير موجود';
                        errorMsg.style.display = 'block';
                        currentEmployeeId = employeeId;
                    }
                }
            })
            .catch(error => {
                console.error('خطأ في البحث عن الموظف:', error);
                loadingMsg.style.display = 'none';
                
                // ✅ في حالة خطأ الشبكة، استعادة البيانات السابقة إذا أمكن
                if (employeeId === initialEmployeeId && lastValidData.name) {
                    receiverNameInput.value = lastValidData.name;
                    emailInput.value = lastValidData.email;
                    transferInput.value = lastValidData.transfer_number;
                    currentEmployeeId = employeeId;
                    successMsg.style.display = 'block';
                } else {
                    errorMsg.textContent = 'خطأ في الاتصال بالخادم';
                    errorMsg.style.display = 'block';
                    currentEmployeeId = employeeId;
                }
            });
    }
}

// باقي الدوال كما هي...
function initLocationDropdowns() {
    const buildingSelect = document.getElementById('buildingSelect');
    const floorSelect = document.getElementById('floorSelect');
    const sectionSelect = document.getElementById('sectionSelect');
    const roomSelect = document.getElementById('roomSelect');

    if (!buildingSelect) return;

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
    if (!buildingsSelect) return;

    const floorId = "<?= esc($locationInfo['floor']->id ?? '') ?>";
    const sectionId = "<?= esc($locationInfo['section']->id ?? '') ?>";
    const roomId = "<?= esc($locationInfo['room']->id ?? '') ?>";

    const buildingId = buildingsSelect.value;
    if (buildingId) {
        try {
            const floorsData = await fetch(`<?= base_url('InventoryController/getfloorsbybuilding') ?>/${buildingId}`).then(res => res.json());
            if (floorsData.success) {
                const floorSelect = document.getElementById('floorSelect');
                floorSelect.innerHTML = '<option value="">اختر الطابق</option>';
                floorsData.data.forEach(floor => {
                    const isSelected = floor.id == floorId ? 'selected' : '';
                    floorSelect.innerHTML += `<option value="${floor.id}" ${isSelected}>${floor.code}</option>`;
                });
            }
        } catch (error) {
            console.error('خطأ في تحميل الطوابق:', error);
        }
    }

    if (floorId) {
        try {
            const sectionsData = await fetch(`<?= base_url('InventoryController/getsectionsbyfloor') ?>/${floorId}`).then(res => res.json());
            if (sectionsData.success) {
                const sectionSelect = document.getElementById('sectionSelect');
                sectionSelect.innerHTML = '<option value="">اختر القسم</option>';
                sectionsData.data.forEach(section => {
                    const isSelected = section.id == sectionId ? 'selected' : '';
                    sectionSelect.innerHTML += `<option value="${section.id}" ${isSelected}>${section.code}</option>`;
                });
            }
        } catch (error) {
            console.error('خطأ في تحميل الأقسام:', error);
        }
    }

    if (sectionId) {
        try {
            const roomsData = await fetch(`<?= base_url('InventoryController/getroomsbysection') ?>/${sectionId}`).then(res => res.json());
            if (roomsData.success) {
                const roomSelect = document.getElementById('roomSelect');
                roomSelect.innerHTML = '<option value="">اختر الغرفة</option>';
                roomsData.data.forEach(room => {
                    const isSelected = room.id == roomId ? 'selected' : '';
                    roomSelect.innerHTML += `<option value="${room.id}" ${isSelected}>${room.code}</option>`;
                });
            }
        } catch (error) {
            console.error('خطأ في تحميل الغرف:', error);
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

    if (!buildingId) return;

    try {
        const response = await fetch(`<?= base_url('InventoryController/getfloorsbybuilding') ?>/${buildingId}`);
        const data = await response.json();
        if (data.success && Array.isArray(data.data)) {
            data.data.forEach(floor => {
                floorSelect.innerHTML += `<option value="${floor.id}">${floor.code}</option>`;
            });
        }
    } catch (error) {
        console.error('خطأ في تحميل الطوابق:', error);
    }
}

async function loadSections(floorId) {
    const sectionSelect = document.getElementById('sectionSelect');
    const roomSelect = document.getElementById('roomSelect');

    sectionSelect.innerHTML = '<option value="">اختر القسم</option>';
    roomSelect.innerHTML = '<option value="">اختر الغرفة</option>';

    if (!floorId) return;

    try {
        const response = await fetch(`<?= base_url('InventoryController/getsectionsbyfloor') ?>/${floorId}`);
        const data = await response.json();
        if (data.success && Array.isArray(data.data)) {
            data.data.forEach(section => {
                sectionSelect.innerHTML += `<option value="${section.id}">${section.code}</option>`;
            });
        }
    } catch (error) {
        console.error('خطأ في تحميل الأقسام:', error);
    }
}

async function loadRooms(sectionId) {
    const roomSelect = document.getElementById('roomSelect');
    roomSelect.innerHTML = '<option value="">اختر الغرفة</option>';

    if (!sectionId) return;

    try {
        const response = await fetch(`<?= base_url('InventoryController/getroomsbysection') ?>/${sectionId}`);
        const data = await response.json();
        if (data.success && Array.isArray(data.data)) {
            data.data.forEach(room => {
                roomSelect.innerHTML += `<option value="${room.id}">${room.code}</option>`;
            });
        }
    } catch (error) {
        console.error('خطأ في تحميل الغرف:', error);
    }
}

function initItemSearchSimple(inputId, dropdownId) {
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
                        dropdown.innerHTML = data.data.map(item => 
                            `<div class="dropdown-item" data-item-name="${item}">${item}</div>`
                        ).join('');
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
        if (e.target.classList.contains('dropdown-item') && 
            !e.target.classList.contains('loading') && 
            !e.target.classList.contains('no-results') && 
            !e.target.classList.contains('error')) {
            
            searchInput.value = e.target.textContent;
            dropdown.style.display = 'none';
        }
    });

    document.addEventListener('click', function(e) {
        if (!e.target.matches(`#${inputId}`) && 
            !e.target.matches(`#${dropdownId}`) && 
            !e.target.closest(`#${dropdownId}`)) {
            dropdown.style.display = 'none';
        }
    });
}

function updateOrder() {
    const form = document.getElementById('editOrderForm');
    const formData = new FormData();
    
    // إضافة البيانات الأساسية
    formData.append('to_employee_id', document.querySelector('input[name="to_employee_id"]').value);
    formData.append('room', document.querySelector('select[name="room"]').value);
    formData.append('notes', document.querySelector('textarea[name="notes"]').value || '');

    // تجميع بيانات العناصر الحالية
    const existingItems = [];
    const itemOrderIds = Array.from(document.querySelectorAll('input[name="existing_item_order_id[]"]'));
    const assetNums = Array.from(document.querySelectorAll('input[name="existing_asset_num[]"]'));
    const serialNums = Array.from(document.querySelectorAll('input[name="existing_serial_num[]"]'));
    const brands = Array.from(document.querySelectorAll('input[name="existing_brand[]"]'));
    const modelNums = Array.from(document.querySelectorAll('input[name="existing_model_num[]"]'));
    const notes = Array.from(document.querySelectorAll('textarea[name="existing_notes[]"]'));

    console.log('عدد العناصر الحالية:', itemOrderIds.length);
    console.log('قيم الـ IDs:', itemOrderIds.map(input => input.value));

    // فحص شامل لجميع IDs قبل المعالجة
    let hasEmptyIds = false;
    itemOrderIds.forEach((idInput, index) => {
        const idValue = idInput.value;
        console.log(`العنصر ${index + 1} - ID:`, idValue, 'نوعه:', typeof idValue);
        
        if (!idValue || idValue === '' || idValue === 'null' || idValue === 'undefined') {
            hasEmptyIds = true;
            console.error(`العنصر ${index + 1} - ID فارغ!`);
        }
    });

    if (hasEmptyIds) {
        alert('تم العثور على عناصر بدون ID صحيح. يرجى فحص console للتفاصيل.');
        return;
    }

    for (let i = 0; i < itemOrderIds.length; i++) {
        const itemId = itemOrderIds[i].value;
        
        const item = {
            item_order_id: itemId,
            asset_num: assetNums[i].value.trim(),
            serial_num: serialNums[i].value.trim(),
            brand: brands[i].value.trim(),
            model_num: modelNums[i].value.trim(),
            note: notes[i].value.trim()
        };
        
        console.log(`العنصر ${i + 1} البيانات:`, item);
        
        // التحقق من الحقول المطلوبة
        if (!item.asset_num || !item.serial_num) {
            alert(`يجب ملء حقول رقم الأصول والرقم التسلسلي للعنصر رقم ${i + 1}`);
            return;
        }
        
        if (!item.item_order_id) {
            alert(`العنصر رقم ${i + 1} لا يحتوي على ID صحيح`);
            return;
        }
        
        existingItems.push(item);
    }

    console.log('بيانات العناصر الحالية:', existingItems);

    // إضافة بيانات العناصر الحالية
    if (existingItems.length > 0) {
        formData.append('existing_items_data', JSON.stringify(existingItems));
    }

    // التحقق من العنصر الجديد
    const newItemName = document.getElementById('newItemSearch').value.trim();
    const newAssetNum = document.getElementById('newAssetNum').value.trim();
    const newSerialNum = document.getElementById('newSerialNum').value.trim();

    if (newItemName || newAssetNum || newSerialNum) {
        if (!newItemName || !newAssetNum || !newSerialNum) {
            alert('يجب ملء جميع الحقول الأساسية للعنصر الجديد (الصنف، رقم الأصول، الرقم التسلسلي) أو تركها فارغة تماماً.');
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
        
        console.log('بيانات العنصر الجديد:', newItemData);
        formData.append('new_item_data', JSON.stringify(newItemData));
    }

    // إظهار رسالة التحميل
    const submitBtn = document.querySelector('.submit-btn');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'جاري الحفظ...';
    submitBtn.disabled = true;

    console.log('إرسال البيانات إلى:', form.action);

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('استجابة الخادم:', response.status);
        return response.text().then(text => {
            console.log('نص الاستجابة:', text);
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('خطأ في تحليل JSON:', e);
                throw new Error('استجابة غير صالحة من الخادم: ' + text);
            }
        });
    })
    .then(data => {
        console.log('بيانات الاستجابة:', data);
        
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
        
        if (data.success) {
            alert('تم تحديث الطلب بنجاح');
            window.location.href = '<?= base_url('InventoryController') ?>';
        } else {
            alert('خطأ: ' + data.message);
        }
    })
    .catch(error => {
        console.error('خطأ في تحديث الطلب:', error);
        
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
        
        alert('حدث خطأ أثناء تحديث الطلب: ' + error.message);
    });
}
    </script>
</body>

</html>