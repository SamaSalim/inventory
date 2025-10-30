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

            <?php if (session()->getFlashdata('info')): ?>
                <div class="alert alert-warning show" role="alert">
                    <i class="fas fa-info-circle"></i>
                    <?= session()->getFlashdata('info') ?>
                </div>
            <?php endif; ?>

            <form method="get" action="<?= base_url('return/superWarehouse/returnrequests') ?>">
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
                            <a href="<?= base_url('return/superWarehouse/returnrequests') ?>" class="filter-btn reset-btn">
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
                            <th>حالة الطلب</th>
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
                                    <td>
                                        <?php 
                                        if ($order['usage_status_id'] == 2): 
                                        ?>
                                            <span class="status-badge order-status-pending">قيد الانتظار</span>
                                        <?php else: ?>
                                            <?php if (isset($order['order_status_id'])): ?>
                                                <?php if ($order['order_status_id'] == 1): ?>
                                                    <span class="status-badge order-status-pending">قيد الانتظار</span>
                                                <?php elseif ($order['order_status_id'] == 2): ?>
                                                    <span class="status-badge order-status-accepted">مقبول</span>
                                                <?php elseif ($order['order_status_id'] == 3): ?>
                                                    <span class="status-badge order-status-rejected">مرفوض</span>
                                                <?php else: ?>
                                                    <span class="status-badge">غير محدد</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="status-badge">غير محدد</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
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
                                            
                                            <?php 
                                            $isPending = ($order['usage_status_id'] == 2) || (isset($order['order_status_id']) && $order['order_status_id'] == 1);
                                            ?>
                                            
                                            <?php if ($isPending): ?>
                                                <button onclick="showAcceptConfirmation('<?= esc($order['item_order_id']) ?>')" 
                                                class="action-btn accept-btn">
                                                    <i class="fas fa-check btn-icon"></i> قبول
                                                </button>
                                                <button onclick="showRejectConfirmation('<?= esc($order['item_order_id']) ?>')" 
                                                class="action-btn reject-btn">
                                                    <i class="fas fa-times btn-icon"></i> رفض
                                                </button>
                                            <?php else: ?>
                                                <button class="action-btn accept-btn" disabled>
                                                    <i class="fas fa-check btn-icon"></i> قبول
                                                </button>
                                                <button class="action-btn reject-btn" disabled>
                                                    <i class="fas fa-times btn-icon"></i> رفض
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11">
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

    <!-- Accept Confirmation Modal -->
    <div id="acceptConfirmationModal" class="confirmation-modal">
        <div class="confirmation-modal-content">
            <div class="confirmation-modal-header accept-header">
                <i class="fas fa-check-circle"></i>
                <h3>تأكيد القبول</h3>
            </div>
            <div class="confirmation-modal-body">
                <p>هل أنت متأكد من قبول طلب الإرجاع؟</p>
            </div>
            <div class="confirmation-modal-footer">
                  <button class="confirm-btn accept-confirm-btn" onclick="confirmAccept()">
                    نعم
                </button>
                <button class="cancel-btn" onclick="closeAcceptConfirmation()">
                    إلغاء
                </button>
            </div>
        </div>
    </div>

    <!-- Reject Confirmation Modal -->
    <div id="rejectConfirmationModal" class="confirmation-modal">
        <div class="confirmation-modal-content">
            <div class="confirmation-modal-header reject-header">
                <i class="fas fa-times-circle"></i>
                <h3>تأكيد الرفض  </h3>
            </div>
            <div class="confirmation-modal-body">
                <p>هل أنت متأكد من رفض طلب الإرجاع؟</p></div>
            <div class="confirmation-modal-footer">
                  <button class="confirm-btn reject-confirm-btn" onclick="confirmReject()">
                    نعم
                </button>
                <button class="cancel-btn" onclick="closeRejectConfirmation()">
                    إلغاء
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

    <!-- Hidden iframe for printing -->
    <iframe id="hiddenPrintFrame"></iframe>

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
        let orderToAccept = null;
        let orderToReject = null;

        function showAcceptConfirmation(orderId) {
            orderToAccept = orderId;
            const modal = document.getElementById('acceptConfirmationModal');
            modal.style.display = 'block';
            setTimeout(() => modal.classList.add('show'), 10);
        }

        function closeAcceptConfirmation() {
            const modal = document.getElementById('acceptConfirmationModal');
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
                orderToAccept = null;
            }, 300);
        }

        function confirmAccept() {
            if (orderToAccept) {
                window.location.href = '<?= base_url('return/superwarehouse/returnrequests/acceptReturn/') ?>' + orderToAccept;
            }
        }

        function showRejectConfirmation(orderId) {
            orderToReject = orderId;
            const modal = document.getElementById('rejectConfirmationModal');
            modal.style.display = 'block';
            setTimeout(() => modal.classList.add('show'), 10);
        }

        function closeRejectConfirmation() {
            const modal = document.getElementById('rejectConfirmationModal');
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
                orderToReject = null;
            }, 300);
        }

        function confirmReject() {
            if (orderToReject) {
                window.location.href = '<?= base_url('return/superwarehouse/returnrequests/rejectReturn/') ?>' + orderToReject;
            }
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
            const attachmentUrl = '<?= base_url('return/superwarehouse/returnrequests/serveAttachment/') ?>' + assetNum;
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
                    alert('حدث خطأ أثناء محاولة الطباعة');
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
            const attachmentUrl = '<?= base_url('return/superwarehouse/returnrequests/serveAttachment/') ?>' + orderData.asset_num;
            
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
            const acceptModal = document.getElementById('acceptConfirmationModal');
            const rejectModal = document.getElementById('rejectConfirmationModal');
            
            if (event.target === attachmentModal) {
                closeAttachmentModal();
            }
            if (event.target === acceptModal) {
                closeAcceptConfirmation();
            }
            if (event.target === rejectModal) {
                closeRejectConfirmation();
            }
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const attachmentModal = document.getElementById('attachmentModal');
                const acceptModal = document.getElementById('acceptConfirmationModal');
                const rejectModal = document.getElementById('rejectConfirmationModal');
                
                if (attachmentModal.style.display === 'block') {
                    closeAttachmentModal();
                }
                if (acceptModal.style.display === 'block') {
                    closeAcceptConfirmation();
                }
                if (rejectModal.style.display === 'block') {
                    closeRejectConfirmation();
                }
            }
        });
    </script>
</body>

</html>