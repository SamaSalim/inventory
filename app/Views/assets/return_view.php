<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عمليات الإرجاع</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- استدعاء مكتبة Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>

       
       

        /* المحتوى الرئيسي */
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


        /* العناوين والأزرار */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-title {
            color: #057590;
            font-size: 18px;
            font-weight: 600;
            margin: 0;
        }

        .buttons-group {
            display: flex;
            gap: 15px;
        }

        .add-btn {
            background-color: #3AC0C3;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(58, 192, 195, 0.3);
        }

        .add-btn:hover {
            background-color: #2aa8ab;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(58, 192, 195, 0.4);
        }

        .add-btn.import {
            background-color: #057590;
            box-shadow: 0 2px 8px rgba(5, 117, 144, 0.3);
        }

        .add-btn.import:hover {
            background-color: #046073;
            box-shadow: 0 4px 12px rgba(5, 117, 144, 0.4);
        }

        

        /* قسم الفلاتر */
        .filters-section {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .filter-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .filter-select,
        .search-box {
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 13px;
            background-color: white;
            outline: none;
            transition: all 0.3s ease;
        }

        .filter-select:focus,
        .search-box:focus {
            border-color: #057590;
            box-shadow: 0 0 0 2px rgba(5, 117, 144, 0.1);
        }

        /* الجدول */
        .table-container {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
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
            font-size: 12px;
            text-align: center;
            white-space: nowrap;
        }

        .custom-table tbody td {
            padding: 12px 8px;
            border-bottom: 1px solid #f0f0f0;
            text-align: center;
            font-size: 11px;
            color: #555;
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            vertical-align: middle;
        }

        .custom-table tbody tr:hover {
            background-color: rgba(5, 117, 144, 0.05);
        }

        .custom-table tbody tr.selected {
            background-color: rgba(58, 192, 195, 0.1) !important;
            border-left: 3px solid #3AC0C3;
        }

        .custom-table tbody tr {
            cursor: pointer;
        }
        

        /* تحسينات checkbox */
        .checkbox-cell {
            width: 40px;
            padding: 8px !important;
        }

        .custom-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #3AC0C3;
        }

        .master-checkbox {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #3AC0C3;
        }

        /* أزرار العمليات */
        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
            align-items: center;
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
            min-width: 70px;
            justify-content: center;
        }

        /* View Button - Teal gradient */
        .view-btn {
            background: linear-gradient(135deg, #3AC0C3, #2aa8ab);
            color: white;
            box-shadow: 0 2px 6px rgba(58, 192, 195, 0.25);
        }

        .view-btn:hover {
            background: linear-gradient(135deg, #2aa8ab, #259a9d);
            color: white;
            box-shadow: 0 4px 10px rgba(58, 192, 195, 0.35);
            transform: translateY(-1px);
        }


     
        /* Icons for buttons */
        .btn-icon {
            width: 12px;
            height: 12px;
            fill: currentColor;
        }

        /* Status badges */
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

      
        /* الازرار */

            .custom-btn {
        background-color: #057590;
        color: #fff;
        border: none;
        padding: 8px 18px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
        }
        .custom-btn:hover {
        background-color: #045d6e;
        }
            
    </style>
</head>

<body>

<?= $this->include('layouts/header') ?>

<div class="main-content">
    <div class="header">
        <h1 class="page-title"> عمليات الإرجاع </h1>
    </div>

    <br><br>

    <div class="row mb-3" dir="rtl">
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
                <tr class="text-center">
                    <th>رقم الطلب</th>
                    <th>الرقم الوظيفي</th>
                    <th>التحويلة</th>
                    <th>حالة الطلب</th>
                    <th>حالة الاستخدام</th>
                    <th>تاريخ الطلب</th>
                    <th>عمليات</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                        <tr class="text-center align-middle" data-order-id="<?= $order->order_id ?>" data-status="<?= strtolower($order->order_status_name) ?>">
                            <td><?= esc($order->order_id ?? '-') ?></td>
                            <td><?= esc($order->employee_id ?? '-') ?></td>
                            <td><?= esc($order->extension ?? '-') ?></td>
                            <td><?= esc($order->order_status_name ?? '-') ?></td>
                            <td><?= esc($order->usage_status_name ?? '-') ?></td>
                            <td><?= isset($order->created_at) ? esc(date('Y-m-d', strtotime($order->created_at))) : '-' ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="<?= site_url('InventoryController/showOrder/' . $order->order_id) ?>" class="action-btn view-btn">
                                        <svg class="btn-icon" viewBox="0 0 24 24">
                                            <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                                        </svg>
                                        عرض
                                    </a>
                                    <button class="action-btn reissue-btn" 
                                            data-id="<?= $order->order_id ?>"
                                            style="background: linear-gradient(135deg, #28a745, #218838); color: white; box-shadow: 0 2px 6px rgba(40,167,69,0.25);">
                                        <i class="fa-solid fa-rotate-right"></i>
                                        إعادة صرف
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center">لا توجد بيانات متاحة</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- مودال تفاصيل الطلب -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#057590; color:white;">
                <h5 class="modal-title" id="orderModalLabel">تفاصيل الطلب</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>

            <div class="modal-body" id="orderDetails" style="font-size:14px; line-height:1.8;">
                <div class="text-center text-muted">جاري تحميل التفاصيل...</div>
            </div>

            <div class="modal-footer d-flex justify-content-between">
                <div>
                    <button type="button" class="btn btn-success" id="approveBtn">قبول</button>
                    <button type="button" class="btn btn-danger" id="rejectBtn">رفض</button>
                </div>
            </div>
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const tbody = document.querySelector("#usersTable tbody");
    const rowsData = [];

    tbody.querySelectorAll("tr").forEach(row => {
        const cells = row.querySelectorAll("td");
        if (cells.length < 5) return;
        rowsData.push({
            rowElement: row,
            orderId: cells[0].textContent.trim(),
            employeeId: cells[1].textContent.trim().toLowerCase(),
            extension: cells[2].textContent.trim(),
            status: cells[3].textContent.trim().toLowerCase(),
            date: cells[5].textContent.trim(),
            text: row.innerText.toLowerCase()
        });
    });

    flatpickr("#startDate", { dateFormat: "d/m/Y", allowInput: true, onChange: filterTable });
    flatpickr("#endDate", { dateFormat: "d/m/Y", allowInput: true, onChange: filterTable });
    document.getElementById("searchInput").addEventListener("keyup", filterTable);
    document.getElementById("employeeFilter").addEventListener("keyup", filterTable);
    filterTable();

    function parseDMY(d) { if (!d) return null; const p=d.split('/'); return new Date(p[2],p[1]-1,p[0]); }
    function parseRowDate(dateText) { if (!dateText) return null; const dmy=dateText.match(/(\d{1,2}\/\d{1,2}\/\d{4})/); if(dmy) return parseDMY(dmy[1]); const ymd=dateText.match(/(\d{4}-\d{2}-\d{2})/); if(ymd) return new Date(ymd[1]); return new Date(dateText); }

    function filterTable() {
        const searchValue = document.getElementById("searchInput").value.trim().toLowerCase();
        const employeeValue = document.getElementById("employeeFilter").value.trim().toLowerCase();
        const startDate = document.getElementById("startDate").value ? parseDMY(document.getElementById("startDate").value) : null;
        const endDate = document.getElementById("endDate").value ? parseDMY(document.getElementById("endDate").value) : null;
        if (endDate) endDate.setHours(23,59,59,999);

        rowsData.forEach(item => {
            const rowDate = parseRowDate(item.date);
            const matchSearch = !searchValue || item.text.includes(searchValue);
            const matchEmployee = !employeeValue || item.employeeId.includes(employeeValue);
            const matchDate = (!startDate && !endDate) || (rowDate && (!startDate || rowDate >= startDate) && (!endDate || rowDate <= endDate));
            item.rowElement.style.display = (matchSearch && matchEmployee && matchDate) ? '' : 'none';
        });
    }

    const viewButtons = document.querySelectorAll('.view-btn');
    viewButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            const modal = new bootstrap.Modal(document.getElementById('orderModal'));
            const detailsDiv = document.getElementById('orderDetails');
            const approveBtn = document.getElementById('approveBtn');
            const rejectBtn = document.getElementById('rejectBtn');

            detailsDiv.innerHTML = '<div class="text-center text-muted">جاري تحميل التفاصيل...</div>';
            modal.show();

            fetch(url)
                .then(resp => resp.text())
                .then(data => {
                    detailsDiv.innerHTML = data;
                    const orderId = url.split('/').pop();
                    approveBtn.setAttribute('data-id', orderId);
                    rejectBtn.setAttribute('data-id', orderId);

                    // إظهار أو إخفاء أزرار القبول والرفض حسب حالة الطلب
                    const row = document.querySelector(`tr[data-order-id='${orderId}']`);
                    if(row && row.dataset.status !== 'مقبول' && row.dataset.status !== 'مرفوض'){
                        approveBtn.style.display = '';
                        rejectBtn.style.display = '';
                    } else {
                        approveBtn.style.display = 'none';
                        rejectBtn.style.display = 'none';
                    }
                })
                .catch(err => { 
                    detailsDiv.innerHTML = '<div class="text-danger text-center">حدث خطأ أثناء التحميل.</div>'; 
                    console.error(err); 
                });
        });
    });

    function updateRowStatus(orderId, newStatus) {
        const row = rowsData.find(r => r.orderId === orderId);
        if (!row) return;
        const statusCell = row.rowElement.querySelectorAll('td')[3];
        statusCell.textContent = newStatus;
        row.rowElement.dataset.status = newStatus.toLowerCase();

        if(newStatus==='مقبول') statusCell.className = 'text-success fw-bold';
        else if(newStatus==='مرفوض') statusCell.className = 'text-danger fw-bold';
        else statusCell.className = 'text-secondary fw-bold';

        row.status = newStatus.toLowerCase();
        row.text = row.rowElement.innerText.toLowerCase();
    }

    function updateStatus(orderId, statusId, statusText) {
        fetch(`<?= base_url('InventoryController/updateOrderStatus/') ?>${orderId}`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ status_id: statusId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({icon:'success',title:data.message || 'تم بنجاح ✅',timer:1500,showConfirmButton:false});
                updateRowStatus(orderId, statusText);

                // إخفاء أزرار العمليات في صف الطلب
                const row = document.querySelector(`tr[data-order-id='${orderId}']`);
                if(row){
                    const actionButtons = row.querySelector('.action-buttons');
                    if(actionButtons){
                        actionButtons.style.display = 'none';
                    }
                }

                bootstrap.Modal.getInstance(document.getElementById('orderModal')).hide();
            } else {
                Swal.fire({icon:'error',title:data.message || 'حدث خطأ أثناء تحديث الحالة'});
            }
        })
        .catch(() => Swal.fire({icon:'error',title:'فشل الاتصال بالخادم'}));
    }

    document.getElementById('approveBtn').addEventListener('click', function () {
        const orderId = this.getAttribute('data-id');
        Swal.fire({
            title: 'تأكيد القبول', text: 'هل تريد قبول هذا الطلب؟', icon: 'question',
            showCancelButton: true, confirmButtonColor:'#198754', cancelButtonColor:'#6c757d',
            confirmButtonText:'نعم، قبول', cancelButtonText:'إلغاء'
        }).then(result=>{ if(result.isConfirmed){ updateStatus(orderId, 2, 'مقبول'); } });
    });

    document.getElementById('rejectBtn').addEventListener('click', function () {
        const orderId = this.getAttribute('data-id');
        Swal.fire({
            title: 'تأكيد الرفض', text: 'هل تريد رفض هذا الطلب؟', icon: 'warning',
            showCancelButton:true, confirmButtonColor:'#dc3545', cancelButtonColor:'#6c757d',
            confirmButtonText:'نعم، رفض', cancelButtonText:'إلغاء'
        }).then(result=>{ if(result.isConfirmed){ updateStatus(orderId, 3, 'مرفوض'); } });
    });

});
</script>

</body>
</html>
