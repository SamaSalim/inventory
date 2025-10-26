<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> طلبات العهد</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <style>
        * {
            font-family: 'Cairo', sans-serif;
        }

        body {
            background-color: #EFF8FA;
            margin: 0;
            padding: 0;
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

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #3AC0C3;
            font-size: 14px;
            cursor: pointer;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            background-color: #3AC0C3;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            font-weight: bold;
        }

        .content-area {
            padding: 25px;
            background-color: #EFF8FA;
            min-height: calc(100vh - 70px);
        }

        .custom-btn {
            background-color: #057590;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
            font-weight: 500;
        }

        .custom-btn:hover {
            background-color: #045d6e;
            transform: translateY(-1px);
        }

        .custom-btn.active {
            background-color: #3AC0C3;
        }

        .table-container {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            margin-top: 20px;
        }

        .custom-table {
            width: 100%;
            margin: 0;
            border-collapse: collapse;
            font-size: 12px;
        }

        .custom-table thead th {
            background-color: #057590;
            color: white;
            font-weight: 600;
            padding: 15px 10px;
            border: none;
            text-align: center;
            white-space: nowrap;
        }

        .custom-table tbody td {
            padding: 12px 10px;
            border-bottom: 1px solid #f0f0f0;
            text-align: center;
            color: #555;
            vertical-align: middle;
            font-size: 11px;
        }

        .custom-table tbody tr:hover {
            background-color: rgba(5, 117, 144, 0.05);
        }

        .checkbox-cell {
            width: 40px;
        }

        .custom-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #3AC0C3;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .action-btn {
            padding: 8px 16px;
            border-radius: 16px;
            border: none;
            font-size: 11px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .view-btn {
            background: linear-gradient(135deg, #3AC0C3, #2aa8ab);
            color: white;
            box-shadow: 0 2px 6px rgba(58, 192, 195, 0.25);
        }

        .view-btn:hover {
            background: linear-gradient(135deg, #2aa8ab, #259a9d);
            color: white;
            transform: translateY(-1px);
        }

        .btn-icon {
            width: 12px;
            height: 12px;
            fill: currentColor;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-new {
            background: #d4edda;
            color: #155724;
        }

        .status-transfer {
            background: #cfe2ff;
            color: #084298;
        }

        .status-return {
            background: #fff3cd;
            color: #856404;
        }

        .order-status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .order-status-accepted {
            background: #d4edda;
            color: #155724;
        }

        .order-status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        tr.opened-row {
            background-color: rgba(255, 247, 200, 0.5) !important;
        }

        /* تنسيق جدول الأصناف في المودال */
        .items-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .items-table thead {
            background: linear-gradient(135deg, #057590, #3AC0C3);
        }

        .items-table thead th {
            color: white;
            padding: 12px 10px;
            font-size: 13px;
            font-weight: 600;
            text-align: center;
            border: none;
        }

        .items-table tbody td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #e8f4f8;
            font-size: 12px;
            color: #555;
        }

        .items-table tbody tr:nth-child(even) {
            background-color: #f8fcfd;
        }

        .items-table tbody tr:hover {
            background-color: #e8f4f8;
            transition: background-color 0.2s ease;
        }

        .items-section {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
        }

        .section-title {
            color: #057590;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-title i {
            color: #3AC0C3;
        }
    </style>
</head>

<body>
    <?= $this->include('layouts/header') ?>

    <div class="main-content">
        <div class="header">
            <h1 class="page-title">طلبات العهد </h1>
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
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="text" id="searchInput" class="form-control" placeholder="ابحث في كل الأعمدة...">
                </div>
                <div class="col-md-3">
                    <input type="text" id="startDate" class="form-control" placeholder="من تاريخ">
                </div>
                <div class="col-md-3">
                    <input type="text" id="endDate" class="form-control" placeholder="إلى تاريخ">
                </div>
                <div class="col-md-3">
                    <input type="text" id="employeeFilter" class="form-control" placeholder="ابحث بالرقم الوظيفي">
                </div>
            </div>

            <div class="table-container">
                <table class="custom-table" id="usersTable">
                    <thead>
                        <tr>
                            <th>رقم الطلب</th>
                            <th>محول من</th>
                            <th>القسم</th>
                            <th>حالة الاستخدام</th>
                            <th>حالة الطلب</th>
                            <th>تاريخ التحويل</th>
                            <th>عمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($orders) && !empty($orders)): ?>
                            <?php foreach ($orders as $order): ?>
                                <tr data-transfer-id="<?= $order->transfer_item_id ?>"
                                    data-usage="<?= esc($order->usage_status_name ?? '') ?>"
                                    class="<?= ($order->is_opened == 1) ? 'opened-row' : '' ?>">

                                    <td><?= esc($order->transfer_item_id ?? '-') ?></td>
                                    <td><?= esc($order->from_user_name ?? '-') ?></td>
                                    <td><?= esc($order->from_user_dept ?? '-') ?></td>
                                    <td>
                                        <?php
                                        $usageClass = 'status-new';
                                        if ($order->usage_status_name == 'تحويل') $usageClass = 'status-transfer';
                                        if ($order->usage_status_name == 'رجيع') $usageClass = 'status-return';
                                        ?>
                                        <span class="status-badge <?= $usageClass ?>">
                                            <?= esc($order->usage_status_name ?? '-') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $orderClass = 'order-status-pending';
                                        if ($order->order_status_name == 'مقبول') $orderClass = 'order-status-accepted';
                                        if ($order->order_status_name == 'مرفوض') $orderClass = 'order-status-rejected';
                                        ?>
                                        <span class="status-badge <?= $orderClass ?>">
                                            <?= esc($order->order_status_name ?? '-') ?>
                                        </span>
                                    </td>
                                    <td><?= isset($order->created_at) ? date('d/m/Y', strtotime($order->created_at)) : '-' ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button
                                                onclick="viewTransferDetails(<?= $order->transfer_item_id ?>, this)"
                                                class="action-btn view-btn">
                                                <svg class="btn-icon" viewBox="0 0 24 24">
                                                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" />
                                                </svg>
                                                عرض
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">لا توجد طلبات محولة إليك</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="transferDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; border: none;">
                <div class="modal-header" style="background: linear-gradient(135deg, #057590, #3AC0C3); color: white; border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title">
                        <i class="fas fa-exchange-alt"></i> تفاصيل طلب التحويل
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding: 30px;" id="modalBody">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">جاري التحميل...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e0e0e0; padding: 20px;" id="modalFooter">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function viewTransferDetails(transferId, button) {
            const row = button.closest('tr');
            row.classList.add('opened-row');

            fetch('<?= base_url('UserController/markAsOpened') ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        transfer_id: transferId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('تم التحديث بنجاح');
                    } else {
                        console.error('فشل التحديث:', data.message);
                    }
                })
                .catch(err => console.error('خطأ:', err));

            const modal = new bootstrap.Modal(document.getElementById('transferDetailsModal'));
            const modalBody = document.getElementById('modalBody');
            const modalFooter = document.getElementById('modalFooter');

            modalBody.innerHTML = '<div class="text-center"><div class="spinner-border text-primary"></div></div>';
            modal.show();

            fetch('<?= base_url('UserController/getTransferDetails/') ?>' + transferId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const t = data.data;
                        const items = data.items || [];
                        const isPending = t.order_status_id == 1;

                        let itemsTableHtml = '';
                        if (items.length > 0) {
                            itemsTableHtml = `
                                <div class="items-section">
                                    <h6 class="section-title">
                                        <i class="fas fa-boxes"></i>
                                        الأصناف المرتبطة بهذا الطلب
                                    </h6>
                                    <table class="items-table">
                                        <thead>
                                            <tr>
                                                <th>اسم الصنف</th>
                                                <th>رقم الأصل</th>
                                                <th>الرقم التسلسلي</th>
                                                <th>حالة الاستخدام</th>
                                                <th>نوع العهدة</th>
                                            </tr>
                                        </thead>
                                        <tbody>`;

                            items.forEach(item => {
                                itemsTableHtml += `
                                    <tr>
                                        <td><strong>${item.item_name || '-'}</strong></td>
                                        <td>${item.asset_num || '-'}</td>
                                        <td>${item.serial_num || '-'}</td>
                                        <td>
                                            <span class="status-badge ${getUsageStatusClass(item.usage_status_name)}">
                                                ${item.usage_status_name || '-'}
                                            </span>
                                        </td>
                                        <td>${item.assets_type || '-'}</td>
                                    </tr>`;
                            });

                            itemsTableHtml += `
                                        </tbody>
                                    </table>
                                </div>`;
                        }

                        modalBody.innerHTML = `
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>ملاحظة:</strong> 
                                ${isPending ? 'إذا قبلت هذا الطلب، ستصبح العهدة في عهدتك. إذا رفضته، ستبقى مع المُرسل.' : 'تم معالجة هذا الطلب مسبقاً'}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted">رقم الطلب</label>
                            <p class="form-control-plaintext">${t.item_order_id || '-'}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted">حالة الطلب</label>
                            <p class="form-control-plaintext">
                                <span class="status-badge ${getOrderStatusClass(t.order_status_id)}">${t.order_status_name}</span>
                            </p>
                        </div>
                        <div class="col-12"><hr></div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted">محول من</label>
                            <p class="form-control-plaintext">${t.from_user_name || '-'}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted">القسم</label>
                            <p class="form-control-plaintext">${t.from_user_dept || '-'}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted">التحويلة</label>
                            <p class="form-control-plaintext">${t.from_user_ext || '-'}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted">البريد الإلكتروني</label>
                            <p class="form-control-plaintext">${t.from_user_email || '-'}</p>
                        </div>
                     
                    </div>
                    ${itemsTableHtml}
                `;

                        if (isPending) {
                            modalFooter.innerHTML = `
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="button" class="btn btn-danger" onclick="respondToTransfer(${transferId}, 'reject')">
                            <i class="fas fa-times"></i> رفض الطلب
                        </button>
                        <button type="button" class="btn btn-success" onclick="respondToTransfer(${transferId}, 'accept')">
                            <i class="fas fa-check"></i> قبول الطلب
                        </button>
                    `;
                        } else {
                            modalFooter.innerHTML = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>';
                        }
                    } else {
                        modalBody.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ${data.message}</div>`;
                    }
                })
                .catch(error => {
                    modalBody.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> حدث خطأ: ${error.message}</div>`;
                });
        }

        function respondToTransfer(transferId, action) {
            if (!confirm(`هل أنت متأكد من ${action === 'accept' ? 'قبول' : 'رفض'} هذا الطلب؟`)) return;

            document.querySelectorAll('#modalFooter button').forEach(btn => btn.disabled = true);

            fetch('<?= base_url('UserController/respondToTransfer') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        transfer_id: transferId,
                        action: action
                    })
                })
                .then(response => {
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('الاستجابة ليست JSON. تحقق من الـ Controller');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const modalElement = document.getElementById('transferDetailsModal');
                        const modalInstance = bootstrap.Modal.getInstance(modalElement);
                        if (modalInstance) modalInstance.hide();

                        const row = document.querySelector(`tr[data-transfer-id="${transferId}"]`);
                        if (row) {
                            row.style.transition = 'opacity 0.3s';
                            row.style.opacity = '0';
                            setTimeout(() => {
                                row.remove();
                                if (window.fastTableData) {
                                    window.fastTableData = window.fastTableData.filter(item => item.row !== row);
                                }
                                const tbody = document.querySelector('#usersTable tbody');
                                if (tbody.querySelectorAll('tr').length === 0) {
                                    tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4">لا توجد طلبات محولة إليك</td></tr>';
                                }
                            }, 300);
                        }
                        alert(data.message);
                    } else {
                        alert('خطأ: ' + data.message);
                        document.querySelectorAll('#modalFooter button').forEach(btn => btn.disabled = false);
                    }
                })
                .catch(error => {
                    alert('حدث خطأ: ' + error.message);
                    document.querySelectorAll('#modalFooter button').forEach(btn => btn.disabled = false);
                });
        }

        function getOrderStatusClass(id) {
            return id == 1 ? 'order-status-pending' : id == 2 ? 'order-status-accepted' : 'order-status-rejected';
        }

        function getUsageStatusClass(name) {
            return name == 'جديد' ? 'status-new' : name == 'تحويل' ? 'status-transfer' : 'status-return';
        }

        // استبدل دالة formatDate في userView.php بهذا الكود:

        function formatDate(d) {
            if (!d) return '-';
            try {
                const date = new Date(d);
                if (isNaN(date.getTime())) return '-';
                return date.toLocaleDateString('ar-SA', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } catch (e) {
                return '-';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#startDate", {
                dateFormat: "d/m/Y",
                allowInput: true,
                onChange: filterTableFast
            });
            flatpickr("#endDate", {
                dateFormat: "d/m/Y",
                allowInput: true,
                onChange: filterTableFast
            });
            document.getElementById("searchInput").addEventListener("keyup", filterTableFast);
            document.getElementById("employeeFilter").addEventListener("keyup", filterTableFast);

            const allRows = Array.from(document.querySelectorAll("#usersTable tbody tr"));
            window.fastTableData = allRows.map(row => {
                const cells = row.querySelectorAll("td");
                return {
                    row: row,
                    text: Array.from(cells).map(td => td.textContent.trim().toLowerCase()).join(" "),
                    employee: cells[2]?.textContent.trim().toLowerCase() || "",
                    usage: row.dataset.usage?.toLowerCase() || "",
                    date: parseRowDate(cells[5]?.textContent.trim() || "")
                };
            });
            window.currentFilterType = 'all';
        });

        function parseDMY(d) {
            if (!d) return null;
            const p = d.split('/');
            return p.length === 3 ? new Date(p[2], p[1] - 1, p[0]) : null;
        }

        function parseRowDate(t) {
            if (!t) return null;
            const m = t.match(/(\d{1,2}\/\d{1,2}\/\d{4})/);
            if (m) return parseDMY(m[1]);
            const dt = new Date(t);
            return isNaN(dt.getTime()) ? null : dt;
        }

        function filterTableFast() {
            const search = document.getElementById("searchInput").value.trim().toLowerCase();
            const startStr = document.getElementById("startDate").value.trim();
            const endStr = document.getElementById("endDate").value.trim();
            const empVal = document.getElementById("employeeFilter").value.trim().toLowerCase();

            let start = startStr ? parseDMY(startStr) : null;
            let end = endStr ? parseDMY(endStr) : null;
            if (end) end.setHours(23, 59, 59, 999);

            fastTableData.forEach(item => {
                let visible = true;
                if (search && !item.text.includes(search)) visible = false;
                if (empVal && !item.employee.includes(empVal)) visible = false;
                if ((start || end) && item.date) {
                    if (start && item.date < start) visible = false;
                    if (end && item.date > end) visible = false;
                } else if ((start || end) && !item.date) visible = false;
                if (currentFilterType !== 'all' && item.usage !== currentFilterType) visible = false;
                item.row.style.display = visible ? "" : "none";
            });
        }

        function filterTable(type) {
            currentFilterType = type;
            document.querySelectorAll('.custom-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            filterTableFast();
        }

        function toggleAllSelection() {
            const master = document.getElementById('masterCheckbox');
            document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = master.checked);
        }
    </script>
</body>

</html>