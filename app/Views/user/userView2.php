<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>العهد الخاصة بي</title>
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

    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }

    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        display: flex;
        align-items: center;
        gap: 15px;
        transition: transform 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .stat-icon.total {
        background: linear-gradient(135deg, #057590, #3AC0C3);
        color: white;
    }

    .stat-icon.direct {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
    }

    .stat-icon.transferred {
        background: linear-gradient(135deg, #007bff, #17a2b8);
        color: white;
    }

    .stat-info h3 {
        font-size: 28px;
        font-weight: 700;
        color: #057590;
        margin: 0;
    }

    .stat-info p {
        margin: 0;
        color: #666;
        font-size: 14px;
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
        font-size: 13px;
    }

    .custom-table thead th {
        background-color: #057590;
        color: white;
        font-weight: 600;
        padding: 15px 12px;
        border: none;
        text-align: center;
        white-space: nowrap;
    }

    .custom-table tbody td {
        padding: 14px 12px;
        border-bottom: 1px solid #f0f0f0;
        text-align: center;
        color: #555;
        vertical-align: middle;
        font-size: 12px;
    }

    .custom-table tbody tr:hover {
        background-color: rgba(5, 117, 144, 0.05);
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

    .transfer-btn {
        background: #3AC0C3;
        color: white;
        box-shadow: 0 2px 6px rgba(5, 117, 144, 0.25);
    }



    .return-btn {
        background: #2269a7ff;
        color: white;
        box-shadow: 0 2px 6px rgba(5, 117, 144, 0.25);
        text-decoration: none;
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

    .order-status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .order-status-accepted {
        background: #d4edda;
        color: #155724;
    }

  

    .source-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: bold;
    }

    .source-direct {
        background: linear-gradient(135deg, #d4edda, #c3e6cb);
        color: #155724;
        border: 1px solid #b1dfbb;
    }

    .source-transfer {
        background: linear-gradient(135deg, #cfe2ff, #b6d4fe);
        color: #084298;
        border: 1px solid #9ec5fe;
    }

    .filter-buttons {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
</style>
</head>

<body>
    <?= $this->include('layouts/header') ?>

    <div class="main-content">
        <div class="header">
            <h1 class="page-title"><i class="fas fa-handshake"></i> العهد الخاصة بي</h1>
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
            <?php
            $totalCount = count($orders ?? []);
            $directCount = count(array_filter($orders ?? [], fn($o) => ($o->source_table ?? '') === 'orders'));
            $transferCount = count(array_filter($orders ?? [], fn($o) => ($o->source_table ?? '') === 'transfer_items'));
            ?>

            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-icon total">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $totalCount ?></h3>
                        <p>إجمالي العهد</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon direct">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $directCount ?></h3>
                        <p>العهد المباشرة</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon transferred">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $transferCount ?></h3>
                        <p>العهد المحولة</p>
                    </div>
                </div>
            </div>

            <div class="filter-buttons">
                <button class="custom-btn active" onclick="filterBySource('all')">
                    <i class="fas fa-list"></i> الكل
                </button>
                <button class="custom-btn" onclick="filterBySource('orders')">
                    <i class="fas fa-plus-circle"></i> المباشرة فقط
                </button>
                <button class="custom-btn" onclick="filterBySource('transfer_items')">
                    <i class="fas fa-exchange-alt"></i> المحولة فقط
                </button>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" id="searchInput" class="form-control" placeholder="ابحث في كل الأعمدة...">
                </div>
                <div class="col-md-4">
                    <input type="text" id="startDate" class="form-control" placeholder="من تاريخ">
                </div>
                <div class="col-md-4">
                    <input type="text" id="endDate" class="form-control" placeholder="إلى تاريخ">
                </div>
            </div>

            <div class="table-container">
                <table class="custom-table" id="covenantsTable">
                    <thead>
                        <tr>
                            <th>رقم الأصل</th>
                            <th>الرقم التسلسلي</th>
                            <th>نوع المصدر</th>
                            <th>حالة الاستخدام</th>
                            <th>حالة الطلب</th>
                            <th>تاريخ الإنشاء</th>
                            <th>عمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($orders) && !empty($orders)): ?>
                            <?php foreach ($orders as $order): ?>
                                <tr data-source="<?= esc($order->source_table ?? '') ?>">
                                    <td><?= esc($order->asset_num ?? '-') ?></td>
                                    <td><?= esc($order->serial_num ?? '-') ?></td>
                                    <td>
                                        <span class="source-badge source-<?= ($order->source_table ?? '') === 'orders' ? 'direct' : 'transfer' ?>">
                                            <?= ($order->source_table ?? '') === 'orders' ? 'مباشر' : 'محول' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        $usageClass = 'status-new';
                                        $usageStatus = $order->usage_status_name ?? '';
                                        if ($usageStatus == 'تحويل') $usageClass = 'status-transfer';
                                        ?>
                                        <span class="status-badge <?= $usageClass ?>">
                                            <?= esc($usageStatus ?: '-') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        $orderClass = 'order-status-pending';
                                        $orderStatus = $order->order_status_name ?? '';
                                        if ($orderStatus == 'مقبول') $orderClass = 'order-status-accepted';
                                        if ($orderStatus == 'مرفوض') $orderClass = 'order-status-rejected';
                                        ?>
                                        <span class="status-badge <?= $orderClass ?>">
                                            <?= esc($orderStatus ?: '-') ?>
                                        </span>
                                    </td>
                                    <td><?= isset($order->created_at) ? date('d/m/Y', strtotime($order->created_at)) : '-' ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button onclick="transferCovenant(<?= $order->item_order_id ?? $order->id ?>)" class="action-btn transfer-btn">
                                                <i class="fas fa-exchange-alt"></i>
                                                تحويل
                                            </button>
                                            <a href="<?= site_url('AssetsController/orderDetails/' . ($order->id ?? 0)) ?>" class="action-btn return-btn">
                                                <svg class="btn-icon" viewBox="0 0 24 24">
                                                    <path d="M9 11H3v2h6v3l5-4-5-4v3zm12-8h-6c-1.1 0-2 .9-2 2v3h2V5h6v14h-6v-3h-2v3c0 1.1.9 2 2 2h6c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z" />
                                                </svg>
                                                إرجاع
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">لا توجد عهد مسجلة</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal للتحويل -->
    <div class="modal fade" id="transferModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; border: none;">
                <div class="modal-header" style="background: #3AC0C3; color: white; border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title"><i class="fas fa-exchange-alt"></i> تحويل العهدة</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding: 30px;">
                    <form id="transferForm">
                        <input type="hidden" id="transfer_item_order_id" name="item_order_id">
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #057590;">تحويل إلى</label>
                            <select class="form-select" id="transfer_to_user" name="to_user_id" required style="border-color: #3AC0C3; border-width: 2px;">
                                <option value="">اختر المستخدم...</option>
                                <!-- سيتم ملؤها ديناميكياً -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #057590;">ملاحظات (اختياري)</label>
                            <textarea class="form-control" id="transfer_note" name="note" rows="3" style="border-color: #3AC0C3; border-width: 2px;"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e0e0e0; padding: 20px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn" onclick="submitTransfer()" style="background: #3AC0C3; color: white; border: none;">
                        <i class="fas fa-paper-plane"></i> تحويل
                    </button>
                </div>
            </div>
        </div>
    </div>

<script>
    function transferCovenant(itemOrderId) {
        const modal = new bootstrap.Modal(document.getElementById('transferModal'));
        document.getElementById('transfer_item_order_id').value = itemOrderId;
        
        // جلب قائمة المستخدمين
        fetch('<?= base_url('UserController/getUsersList') ?>')
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('transfer_to_user');
                select.innerHTML = '<option value="">اختر المستخدم...</option>';
                if (data.success && data.users) {
                    data.users.forEach(user => {
                        select.innerHTML += `<option value="${user.user_id}">${user.name} - ${user.user_dept || ''}</option>`;
                    });
                }
            });
        
        modal.show();
    }

    function submitTransfer() {
        const formData = new FormData(document.getElementById('transferForm'));
        
        fetch('<?= base_url('UserController/submitTransfer') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('تم تحويل العهدة بنجاح');
                location.reload();
            } else {
                alert('حدث خطأ: ' + data.message);
            }
        })
        .catch(error => {
            alert('حدث خطأ: ' + error.message);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#startDate", {dateFormat: "d/m/Y", allowInput: true, onChange: filterTableFast});
        flatpickr("#endDate", {dateFormat: "d/m/Y", allowInput: true, onChange: filterTableFast});
        document.getElementById("searchInput").addEventListener("keyup", filterTableFast);

        const allRows = Array.from(document.querySelectorAll("#covenantsTable tbody tr"));
        window.fastTableData = allRows.map(row => {
            const cells = row.querySelectorAll("td");
            return {
                row: row,
                text: Array.from(cells).map(td => td.textContent.trim().toLowerCase()).join(" "),
                source: row.dataset.source || "",
                date: parseRowDate(cells[5]?.textContent.trim() || "")
            };
        });
        window.currentSourceFilter = 'all';
    });

    function parseDMY(d) {
        if (!d) return null;
        const p = d.split('/');
        return p.length === 3 ? new Date(p[2], p[1]-1, p[0]) : null;
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

        let start = startStr ? parseDMY(startStr) : null;
        let end = endStr ? parseDMY(endStr) : null;
        if (end) end.setHours(23,59,59,999);

        fastTableData.forEach(item => {
            let visible = true;
            
            if (search && !item.text.includes(search)) visible = false;
            
            if ((start || end) && item.date) {
                if (start && item.date < start) visible = false;
                if (end && item.date > end) visible = false;
            } else if ((start || end) && !item.date) visible = false;
            
            if (currentSourceFilter !== 'all' && item.source !== currentSourceFilter) visible = false;
            
            item.row.style.display = visible ? "" : "none";
        });
    }

    function filterBySource(source) {
        currentSourceFilter = source;
        document.querySelectorAll('.custom-btn').forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
        filterTableFast();
    }
</script>

</body>
</html>