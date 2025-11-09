<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلبات الإرجاع</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('public/assets/css/warehouse-style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/assets/css/super_warehouse_style.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <?= $this->include('layouts/header') ?>

    <div class="main-content">
        <div class="header">
            <h1 class="page-title">طلبات الإرجاع</h1>
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
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success show" role="alert">
                    <i class="fas fa-check-circle"></i>
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger show" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form method="get" action="<?= base_url('return/it/returnrequests') ?>">
                <div class="filters-section">
                    <div class="main-search-container">
                        <h3 class="search-section-title">
                            <i class="fas fa-search"></i>
                            البحث العام
                        </h3>
                        <div class="search-bar-wrapper">
                            <input type="text" 
                                class="main-search-input" 
                                name="general_search"
                                value="<?= esc($filters['general_search'] ?? '') ?>"
                                placeholder="ابحث في جميع الحقول...">
                            <i class="fas fa-search search-icon"></i>
                        </div>
                    </div>

                    <div class="filters-divider">
                        <span>أو استخدم الفلاتر التفصيلية</span>
                    </div>

                    <div class="detailed-filters">
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-hashtag"></i>
                                رقم الطلب
                            </label>
                            <input type="text" 
                                class="filter-input" 
                                name="order_id"
                                value="<?= esc($filters['order_id'] ?? '') ?>" 
                                placeholder="أدخل رقم الطلب">
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-id-badge"></i>
                                الرقم الوظيفي
                            </label>
                            <input type="text" 
                                class="filter-input" 
                                name="emp_id"
                                value="<?= esc($filters['emp_id'] ?? '') ?>" 
                                placeholder="أدخل الرقم الوظيفي">
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-box"></i>
                                اسم الصنف
                            </label>
                            <input type="text" 
                                class="filter-input" 
                                name="item_name"
                                value="<?= esc($filters['item_name'] ?? '') ?>" 
                                placeholder="أدخل اسم الصنف">
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-barcode"></i>
                                رقم الأصل
                            </label>
                            <input type="text" 
                                class="filter-input" 
                                name="asset_num"
                                value="<?= esc($filters['asset_num'] ?? '') ?>" 
                                placeholder="أدخل رقم الأصل">
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-hashtag"></i>
                                الرقم التسلسلي
                            </label>
                            <input type="text" 
                                class="filter-input" 
                                name="serial_num"
                                value="<?= esc($filters['serial_num'] ?? '') ?>" 
                                placeholder="أدخل الرقم التسلسلي">
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-calendar-alt"></i>
                                من تاريخ
                            </label>
                            <input type="date" 
                                class="filter-input" 
                                name="date_from"
                                value="<?= esc($filters['date_from'] ?? '') ?>">
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-calendar-check"></i>
                                إلى تاريخ
                            </label>
                            <input type="date" 
                                class="filter-input" 
                                name="date_to"
                                value="<?= esc($filters['date_to'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="filter-actions">
                        <div style="display: flex; gap: 15px;">
                            <button type="submit" class="filter-btn search-btn">
                                <i class="fas fa-search"></i>
                                بحث
                            </button>
                            <a href="<?= base_url('return/it/returnrequests') ?>" class="filter-btn reset-btn">
                                <i class="fas fa-undo"></i>
                                إعادة تعيين
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>رقم الطلب</th>
                            <th>الرقم الوظيفي</th>
                            <th>اسم المرجع</th>
                            <th>اسم الصنف</th>
                            <th>رقم الأصل</th>
                            <th>الرقم التسلسلي</th>
                            <th>حالة الاستخدام</th>
                            <th>تاريخ الطلب</th>
                            <th>عمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($returnOrders)): ?>
                            <?php foreach ($returnOrders as $order): ?>
                                <tr>
                                    <td><?= esc($order['item_order_id']) ?></td>
                                    <td><?= esc($order['created_by']) ?></td>
                                    <td><?= esc($order['employee_name'] ?? 'غير محدد') ?></td>
                                    <td><?= esc($order['item_name'] ?? 'غير محدد') ?></td>
                                    <td><?= esc($order['asset_num'] ?? '-') ?></td>
                                    <td><?= esc($order['serial_num'] ?? '-') ?></td>
                                    <td>
                                        <span class="status-badge status-return">
                                            <?= esc($order['usage_status'] ?? 'رجيع') ?>
                                        </span>
                                    </td>
                                    <td><?= date('d-m-Y', strtotime($order['created_at'])) ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <?php if (!empty($order['attachment']) && $order['attachment'] !== 'NULL'): ?>
                                                <button onclick='handleAttachmentView(<?= json_encode($order, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' 
                                                class="action-btn view-btn">
                                                    <i class="fas fa-eye btn-icon"></i> عرض المرفق
                                                </button>
                                            <?php else: ?>
                                                <button class="action-btn view-btn" disabled>
                                                    <i class="fas fa-eye btn-icon"></i> لا يوجد مرفق
                                                </button>
                                            <?php endif; ?>
                                            
                                            <?php if ($order['usage_status_id'] == 7): ?>
                                                <button type="button" 
                                                        class="action-btn accept-btn" 
                                                        data-asset-num="<?= esc($order['asset_num']) ?>"
                                                        data-serial-num="<?= esc($order['serial_num']) ?>"
                                                        data-item-name="<?= esc($order['item_name']) ?>"
                                                        data-item-order-id="<?= esc($order['item_order_id']) ?>"
                                                        onclick="showTechnicalReportModal(this)">
                                                    <i class="fas fa-file-medical btn-icon"></i> إنشاء تقرير فني
                                                </button>
                                            <?php elseif ($order['usage_status_id'] == 2): ?>
                                                <button type="button" 
                                                        class="action-btn info-btn" 
                                                        onclick="viewTechnicalReport('<?= esc($order['asset_num']) ?>')">
                                                    <i class="fas fa-file-alt btn-icon"></i> عرض التقرير الفني
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <p>لا توجد طلبات إرجاع</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Technical Report Modal -->
    <div id="technicalReportModal" class="selected-items-popup">
        <div class="selected-items-popup-content" style="max-width: 700px;">
            <div style="padding: 20px; background: linear-gradient(135deg, #057590, #3ac0c3); color: white; display: flex; justify-content: space-between; align-items: center; border-radius: 15px 15px 0 0;">
                <h3 style="margin: 0; font-size: 20px;">
                    <i class="fas fa-file-medical" style="margin-left: 8px;"></i>
                    إنشاء تقرير فني
                </h3>
                <button onclick="closeTechnicalReportModal()" style="background: rgba(255,255,255,0.2); color: white; border: none; border-radius: 50%; width: 35px; height: 35px; cursor: pointer; font-size: 20px; display: flex; align-items: center; justify-content: center; transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">✕</button>
            </div>

            <div style="padding: 25px;">
                <div style="background: #f8fdff; border-right: 4px solid #3ac0c3; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <div style="display: flex; align-items: start; gap: 12px;">
                        <i class="fas fa-info-circle" style="color: #3ac0c3; font-size: 24px; margin-top: 2px;"></i>
                        <div>
                            <p style="margin: 0; color: #057590; font-weight: 600; font-size: 15px; margin-bottom: 8px;">
                                معلومات التقرير
                            </p>
                            <p style="margin: 0; color: #555; font-size: 14px; line-height: 1.6;">
                                املأ التفاصيل أدناه لإنشاء التقرير الفني للأصل <strong id="techReportItemName"></strong> (رقم الأصل: <strong id="techReportAssetNum"></strong>)
                            </p>
                        </div>
                    </div>
                </div>

                <form id="technicalReportForm">
                    <input type="hidden" id="techReportAssetNumHidden" name="asset_num">

                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col-md-6">
                            <label style="display: block; font-size: 14px; font-weight: 600; color: #057590; margin-bottom: 8px;">
                                <i class="fas fa-city" style="margin-left: 5px;"></i>
                                الرقم التسلسلي للمدينة
                            </label>
                            <input type="text" 
                                   id="citySerialNum" 
                                   name="city_serial_num" 
                                   class="form-control" 
                                   placeholder="أدخل الرقم التسلسلي للمدينة (اختياري)"
                                   style="border: 2px solid #e8f4f8;">
                        </div>
                        <div class="col-md-6">
                            <label style="display: block; font-size: 14px; font-weight: 600; color: #057590; margin-bottom: 8px;">
                                <i class="fas fa-building" style="margin-left: 5px;"></i>
                                الرقم التسلسلي للوزارة
                            </label>
                            <input type="text" 
                                   id="ministrySerialNum" 
                                   name="ministry_serial_num" 
                                   class="form-control" 
                                   placeholder="أدخل الرقم التسلسلي للوزارة (اختياري)"
                                   style="border: 2px solid #e8f4f8;">
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #057590; margin-bottom: 8px;">
                            <i class="fas fa-comment-dots" style="margin-left: 5px;"></i>
                            وصف الحالة <span style="color: #e74c3c; font-size: 16px;">*</span>
                        </label>
                        <textarea 
                            id="techReportNotes"
                            name="notes"
                            placeholder="اكتب وصفاً تفصيلياً لحالة الأصل... (هذا الحقل إلزامي)"
                            style="width: 100%; min-height: 120px; padding: 12px; border: 2px solid #e8f4f8; border-radius: 8px; font-size: 14px; font-family: 'Cairo', sans-serif; resize: vertical; transition: border-color 0.2s; box-sizing: border-box;"
                            onfocus="this.style.borderColor='#3ac0c3'"
                            onblur="this.style.borderColor='#e8f4f8'"
                            required
                        ></textarea>
                        <small style="font-size: 12px; color: #e74c3c; margin-top: 5px; display: block; font-weight: 600;">
                            <i class="fas fa-exclamation-triangle" style="margin-left: 3px;"></i>
                            هذا الحقل إلزامي - يجب ملؤه قبل إنشاء التقرير
                        </small>
                    </div>
                </form>
            </div>

            <div style="padding: 15px 25px; background: #f8f9fa; border-top: 2px solid #e0e6ed; display: flex; justify-content: space-between; gap: 10px; border-radius: 0 0 15px 15px;">
                <button onclick="closeTechnicalReportModal()" style="flex: 1; padding: 12px 20px; background: #95a5a6; color: white; border: none; border-radius: 20px; cursor: pointer; font-weight: bold; font-size: 14px; transition: all 0.2s;" onmouseover="this.style.background='#7f8c8d'" onmouseout="this.style.background='#95a5a6'">
                    <i class="fas fa-times" style="margin-left: 5px;"></i>
                    إلغاء
                </button>
                <button onclick="submitTechnicalReport()" id="submitTechReportBtn" style="flex: 1; padding: 12px 20px; background: linear-gradient(135deg, #3ac0c3, #2aa8ab); color: white; border: none; border-radius: 20px; cursor: pointer; font-weight: bold; font-size: 14px; transition: all 0.2s; box-shadow: 0 2px 8px rgba(58, 192, 195, 0.3);" onmouseover="this.style.background='linear-gradient(135deg, #2aa8ab, #259a9d)'" onmouseout="this.style.background='linear-gradient(135deg, #3ac0c3, #2aa8ab)'">
                    <i class="fas fa-save" style="margin-left: 5px;"></i>
                    إنشاء التقرير
                </button>
            </div>
        </div>
    </div>

    <!-- Attachment Modal -->
    <div id="attachmentModal" class="attachment-modal">
        <div class="attachment-modal-content">
            <div class="modal-header-custom">
                <h3>
                    <i class="fas fa-file-alt"></i>
                    تفاصيل الطلب والمرفق
                </h3>
                <button class="modal-close" onclick="closeAttachmentModal()">✕</button>
            </div>

            <div class="modal-body-custom">
                <div class="item-info-section">
                    <div class="info-grid" id="itemInfoGrid"></div>
                </div>

                <div id="attachmentDisplayContainer"></div>
            </div>
        </div>
    </div>

    <iframe id="hiddenPrintFrame" style="display: none;"></iframe>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });
        });

        let currentOrderData = null;

        function showTechnicalReportModal(button) {
            const assetNum = button.dataset.assetNum;
            const serialNum = button.dataset.serialNum;
            const itemName = button.dataset.itemName;
            
            document.getElementById('techReportAssetNumHidden').value = assetNum;
            document.getElementById('techReportItemName').textContent = itemName;
            document.getElementById('techReportAssetNum').textContent = assetNum;
            
            document.getElementById('techReportNotes').value = '';
            
            const modal = document.getElementById('technicalReportModal');
            modal.style.display = 'block';
            setTimeout(() => modal.classList.add('show'), 10);
        }

        function closeTechnicalReportModal() {
            const modal = document.getElementById('technicalReportModal');
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
                document.getElementById('technicalReportForm').reset();
            }, 300);
        }

        function submitTechnicalReport() {
            const assetNum = document.getElementById('techReportAssetNumHidden').value;
            const citySerialNum = document.getElementById('citySerialNum').value.trim();
            const ministrySerialNum = document.getElementById('ministrySerialNum').value.trim();
            const notes = document.getElementById('techReportNotes').value.trim();
            
            // MANDATORY VALIDATION - Notes must be filled
            if (!notes) {
                showAlert('error', 'يجب ملء حقل وصف الحالة - هذا الحقل إلزامي ولا يمكن تركه فارغاً');
                document.getElementById('techReportNotes').focus();
                document.getElementById('techReportNotes').style.borderColor = '#e74c3c';
                setTimeout(() => {
                    document.getElementById('techReportNotes').style.borderColor = '#e8f4f8';
                }, 2000);
                return;
            }

            const submitBtn = document.getElementById('submitTechReportBtn');
            const originalContent = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإنشاء...';

            const formData = new FormData();
            formData.append('asset_num', assetNum);
            formData.append('city_serial_num', citySerialNum);
            formData.append('ministry_serial_num', ministrySerialNum);
            formData.append('notes', notes);

            fetch('<?= base_url('return/it/returnrequests/generateTechnicalReport') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    closeTechnicalReportModal();
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showAlert('error', data.message || 'حدث خطأ أثناء إنشاء التقرير');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalContent;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'حدث خطأ في الاتصال بالخادم');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalContent;
            });
        }

        function handleAttachmentView(orderData) {
            const attachmentExt = orderData.attachment ? orderData.attachment.split('.').pop().toLowerCase() : '';
            const isHTML = ['html', 'htm', 'php'].includes(attachmentExt);
            
            if (isHTML) {
                printHTMLDirectly(orderData.asset_num);
            } else {
                openAttachmentModal(orderData);
            }
        }

        function printHTMLDirectly(assetNum) {
            const attachmentUrl = '<?= base_url('return/it/returnrequests/serveAttachment/') ?>' + assetNum;
            const printFrame = document.getElementById('hiddenPrintFrame');
            
            printFrame.onload = null;
            printFrame.onload = function() {
                try {
                    setTimeout(() => {
                        printFrame.contentWindow.focus();
                        printFrame.contentWindow.print();
                    }, 800);
                } catch (e) {
                    console.error('Print error:', e);
                    showAlert('error', 'حدث خطأ أثناء محاولة الطباعة');
                }
            };
            
            printFrame.src = attachmentUrl;
        }

        function openAttachmentModal(orderData) {
            currentOrderData = orderData;
            
            const modal = document.getElementById('attachmentModal');
            const modalBody = modal.querySelector('.modal-body-custom');
            const infoGrid = document.getElementById('itemInfoGrid');
            const displayContainer = document.getElementById('attachmentDisplayContainer');
            
            infoGrid.innerHTML = `
                <div class="info-item">
                    <span class="info-label">رقم الطلب</span>
                    <span class="info-value">${orderData.item_order_id || '-'}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">الرقم الوظيفي</span>
                    <span class="info-value">${orderData.created_by || '-'}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">اسم الصنف</span>
                    <span class="info-value">${orderData.item_name || 'غير محدد'}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">رقم الأصل</span>
                    <span class="info-value">${orderData.asset_num || '-'}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">الرقم التسلسلي</span>
                    <span class="info-value">${orderData.serial_num || '-'}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">تاريخ الطلب</span>
                    <span class="info-value">${new Date(orderData.created_at).toLocaleDateString('en-GB')}</span>
                </div>
            `;
            
            const attachmentExt = orderData.attachment ? orderData.attachment.split('.').pop().toLowerCase() : '';
            const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
            const attachmentUrl = '<?= base_url('return/it/returnrequests/serveAttachment/') ?>' + orderData.asset_num;
            
            if (imageExtensions.includes(attachmentExt)) {
                modalBody.classList.remove('file-view');
                displayContainer.innerHTML = `
                    <div class="attachment-display-image">
                        <div class="attachment-container-image">
                            <img src="${attachmentUrl}" alt="Attachment" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>❌</text></svg>'">
                        </div>
                    </div>
                `;
            } else {
                modalBody.classList.add('file-view');
                displayContainer.innerHTML = `
                    <div class="attachment-display-file">
                        <div class="attachment-container-file">
                            <div class="loading-spinner">
                                <i class="fas fa-circle-notch"></i>
                                <span>جاري تحميل الملف...</span>
                            </div>
                        </div>
                    </div>
                `;
                
                setTimeout(() => {
                    displayContainer.querySelector('.attachment-container-file').innerHTML = `
                        <iframe src="${attachmentUrl}" 
                                frameborder="0" 
                                onload="this.style.display='block';"
                                onerror="this.parentElement.innerHTML='<div style=\'padding:40px;text-align:center;color:#999;\'>خطأ في تحميل الملف</div>'">
                        </iframe>
                    `;
                }, 300);
            }
            
            modal.style.display = 'block';
            setTimeout(() => modal.classList.add('show'), 10);
        }

        function closeAttachmentModal() {
            const modal = document.getElementById('attachmentModal');
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
                document.getElementById('attachmentDisplayContainer').innerHTML = '';
                currentOrderData = null;
            }, 300);
        }

        window.onclick = function(event) {
            const attachmentModal = document.getElementById('attachmentModal');
            const technicalReportModal = document.getElementById('technicalReportModal');
            
            if (event.target === attachmentModal) {
                closeAttachmentModal();
            }
            if (event.target === technicalReportModal) {
                closeTechnicalReportModal();
            }
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const attachmentModal = document.getElementById('attachmentModal');
                const technicalReportModal = document.getElementById('technicalReportModal');
                
                if (attachmentModal.style.display === 'block') {
                    closeAttachmentModal();
                }
                if (technicalReportModal.style.display === 'block') {
                    closeTechnicalReportModal();
                }
            }
        });

        function showAlert(type, message) {
            let alertContainer = document.getElementById('alertContainer');
            
            if (!alertContainer) {
                alertContainer = document.createElement('div');
                alertContainer.id = 'alertContainer';
                alertContainer.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 99999;
                    display: flex;
                    flex-direction: column;
                    gap: 10px;
                `;
                document.body.appendChild(alertContainer);
            }

            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type}`;
            
            const bgColor = type === 'success' ? '#2ecc71' : type === 'warning' ? '#f39c12' : '#e74c3c';
            
            alertDiv.style.cssText = `
                background: ${bgColor};
                color: white;
                padding: 15px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                font-size: 14px;
                font-weight: 500;
                min-width: 300px;
                max-width: 400px;
                animation: slideInRight 0.3s ease;
                opacity: 1;
                transform: translateX(0);
                transition: all 0.3s ease;
            `;
            
            const icon = type === 'success' ? '✓' : type === 'warning' ? '⚠' : '✕';
            alertDiv.innerHTML = `<strong style="margin-left: 8px; font-size: 16px;">${icon}</strong> ${message}`;
            
            alertContainer.appendChild(alertDiv);
            
            setTimeout(() => {
                alertDiv.style.opacity = '0';
                alertDiv.style.transform = 'translateX(20px)';
                setTimeout(() => alertDiv.remove(), 300);
            }, 5000);
        }

        function viewTechnicalReport(assetNum) {
            const reportUrl = '<?= base_url('return/it/returnrequests/serveEvaluationReport/') ?>' + assetNum;
            window.open(reportUrl, '_blank');
        }
    </script>

    <style>
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .selected-items-popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .selected-items-popup.show {
            opacity: 1;
        }

        .selected-items-popup-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            max-width: 90%;
            width: 100%;

        }


        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            font-family: 'Cairo', sans-serif;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: #27ae60;
        }
    </style>
</body>

</html>