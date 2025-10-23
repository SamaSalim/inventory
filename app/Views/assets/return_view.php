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

        /* مودال إعادة الصرف */
#reissueModal .modal-content {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    font-family: 'Cairo', sans-serif;
}

#reissueModal .modal-header {
    background: linear-gradient(135deg, #3AC0C3, #057590);
    color: #fff;
    font-weight: 600;
    font-size: 16px;
}

#reissueModal .modal-body {
    background-color: #EFF8FA;
    padding: 25px;
}

#reissueModal .form-label {
    font-size: 13px;
    font-weight: 500;
    color: #057590;
}

#reissueModal .form-control {
    border-radius: 10px;
    border: 1px solid #ddd;
    padding: 10px 12px;
    font-size: 13px;
    transition: all 0.3s ease;
}

#reissueModal .form-control:focus {
    border-color: #057590;
    box-shadow: 0 0 0 2px rgba(5, 117, 144, 0.1);
}

#reissueModal table {
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

#reissueModal table th {
    background-color: #057590;
    color: white;
    font-size: 12px;
    text-align: center;
    padding: 10px;
}

#reissueModal table td {
    font-size: 12px;
    text-align: center;
    padding: 8px;
    vertical-align: middle;
}

#reissueModal .btn-success {
    background: linear-gradient(135deg, #28a745, #218838);
    border-radius: 25px;
    padding: 8px 20px;
    font-weight: 500;
}

#reissueModal .btn-success:hover {
    background: linear-gradient(135deg, #218838, #1e7e34);
}

#reissueModal .btn-secondary {
    border-radius: 25px;
    padding: 8px 20px;
}

/* تحسين مظهر الـcheckbox داخل المودال */
#reissueModal .item-check,
#reissueModal #selectAll {
    width: 18px;
    height: 18px;
    accent-color: #3AC0C3;
    cursor: pointer;
}
            
    </style>
</head>

<body>

<?= $this->include('layouts/header') ?>

    <div class="main-content">
        <div class="header">
            <h1 class="page-title">  عمليات الإرجاع</h1>
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
                                <a href="<?= site_url('return/requesthandler/showReturnAssets/' . $order->order_id) ?>" class="action-btn view-btn">
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

<!-- ✅ مودال إعادة الصرف -->
<div class="modal fade" id="reissueModal" tabindex="-1" aria-labelledby="reissueModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">إعادة صرف العهدة</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="reissueForm">
          <input type="hidden" id="reissueOrderId">

          <!-- التحويل من -->
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">التحويل من (الرقم الوظيفي)</label>
              <input type="text" class="form-control" id="fromUser" value="1006" readonly>
              <div id="fromUserInfo" class="small text-muted mt-1">ثابت: سيتم إعادة الصرف من ليالي</div>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">التحويل إلى (الرقم الوظيفي)</label>
              <input type="text" class="form-control" id="toUser" value="1002" readonly>
              <div id="toUserInfo" class="small text-muted mt-1">ثابت: سيتم إعادة الصرف إلى حميدة</div>
            </div>
          </div>

          <!-- جدول اختيار العهد -->
          <div class="mb-3">
            <label class="form-label">العُهد المرتبطة بالطلب</label>
            <table class="table table-bordered table-sm text-center" id="itemsTable">
              <thead class="table-light">
                <tr>
                  <th><input type="checkbox" id="selectAll"></th>
                  <th>رقم الأصل</th>
                  <th>النوع</th>
                  <th>الموديل</th>
                  <th>الكمية</th>
                </tr>
              </thead>
              <tbody><tr><td colspan="5" class="text-muted">جاري التحميل...</td></tr></tbody>
            </table>
          </div>

          <div class="mb-3">
            <label class="form-label">ملاحظات</label>
            <textarea class="form-control" id="notes" rows="3"></textarea>
          </div>

          <div class="text-end">
            <button type="submit" class="btn btn-success">تأكيد إعادة الصرف</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
          </div>
        </form>
      </div>
    </div>
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
            status: cells[3].textContent.trim().toLowerCase(),
            date: cells[5].textContent.trim(),
            text: row.innerText.toLowerCase()
        });
    });

    function parseDMY(d) { if (!d) return null; const p=d.split('/'); return new Date(p[2], p[1]-1, p[0]); }
    function parseRowDate(dateText) { if (!dateText) return null; const dmy=dateText.match(/(\d{1,2}\/\d{1,2}\/\d{4})/); if(dmy) return parseDMY(dmy[1]); return new Date(dateText); }

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

    flatpickr("#startDate", { dateFormat: "d/m/Y", allowInput: true, onChange: filterTable });
    flatpickr("#endDate", { dateFormat: "d/m/Y", allowInput: true, onChange: filterTable });
    document.getElementById("searchInput").addEventListener("keyup", filterTable);
    document.getElementById("employeeFilter").addEventListener("keyup", filterTable);
    filterTable();

    document.querySelectorAll('.view-btn').forEach(button => {
        button.addEventListener('click', function(e){
            e.preventDefault();
            const url = this.href;
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
                    approveBtn.dataset.id = orderId;
                    rejectBtn.dataset.id = orderId;

                    const row = document.querySelector(`tr[data-order-id='${orderId}']`);
                    approveBtn.style.display = '';
                    rejectBtn.style.display = '';
                })
                .catch(() => detailsDiv.innerHTML = '<div class="text-danger text-center">حدث خطأ أثناء التحميل.</div>');
        });
    });

    function updateRowStatus(orderId, newStatus) {
        const row = rowsData.find(r => r.orderId === orderId);
        if (!row) return;
        const statusCell = row.rowElement.querySelectorAll('td')[3];
        statusCell.textContent = newStatus;
        row.rowElement.dataset.status = newStatus.toLowerCase();
        row.status = newStatus.toLowerCase();
        row.text = row.rowElement.innerText.toLowerCase();
        if(newStatus==='مقبول') statusCell.className = 'text-success fw-bold';
        else if(newStatus==='مرفوض') statusCell.className = 'text-danger fw-bold';
        else statusCell.className = 'text-secondary fw-bold';
    }

    function updateStatus(orderId, statusId, statusText) {
        fetch(`<?= base_url('AssetsHistory/updateOrderStatus/') ?>${orderId}`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ status_id: statusId })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                Swal.fire({icon:'success',title:data.message || 'تم بنجاح ✅',timer:1500,showConfirmButton:false});
                updateRowStatus(orderId, statusText);
                bootstrap.Modal.getInstance(document.getElementById('orderModal')).hide();
            } else Swal.fire({icon:'error',title:data.message || 'حدث خطأ أثناء تحديث الحالة'});
        })
        .catch(()=> Swal.fire({icon:'error',title:'فشل الاتصال بالخادم'}));
    }

    document.getElementById('approveBtn').addEventListener('click', function(){
        const orderId = this.dataset.id;
        Swal.fire({
            title: 'تأكيد القبول', text: 'هل تريد قبول هذا الطلب؟', icon: 'question',
            showCancelButton:true, confirmButtonColor:'#198754', cancelButtonColor:'#6c757d',
            confirmButtonText:'نعم، قبول', cancelButtonText:'إلغاء'
        }).then(result=>{ if(result.isConfirmed) updateStatus(orderId, 2, 'مقبول'); });
    });

    document.getElementById('rejectBtn').addEventListener('click', function(){
        const orderId = this.dataset.id;
        Swal.fire({
            title: 'تأكيد الرفض', text: 'هل تريد رفض هذا الطلب؟', icon: 'warning',
            showCancelButton:true, confirmButtonColor:'#dc3545', cancelButtonColor:'#6c757d',
            confirmButtonText:'نعم، رفض', cancelButtonText:'إلغاء'
        }).then(result=>{ if(result.isConfirmed) updateStatus(orderId, 3, 'مرفوض'); });
    });

    document.querySelectorAll('.reissue-btn').forEach(btn => {
        btn.addEventListener('click', function(){
            const orderId = btn.dataset.id;
            document.getElementById('reissueOrderId').value = orderId;
            fetchItems(orderId);
            new bootstrap.Modal(document.getElementById('reissueModal')).show();
        });
    });

    function fetchItems(orderId){
        fetch(`<?= base_url('AssetsHistory/getItemsByOrder/') ?>${orderId}`)
        .then(r=>r.json())
        .then(data=>{
            const tbody = document.querySelector('#itemsTable tbody');
            if(data.success && data.items.length){
                tbody.innerHTML = data.items.map(item=>`
                    <tr>
                        <td><input type="checkbox" class="item-check" value="${item.item_order_id}"></td>
                        <td>${item.asset_num||'-'}</td>
                        <td>${item.assets_type||'-'}</td>
                        <td>${item.model_num||'-'}</td>
                        <td>${item.quantity||'-'}</td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = `<tr><td colspan="5" class="text-danger">${data.message || 'لا توجد بيانات'}</td></tr>`;
            }
        });
    }

    document.getElementById('selectAll').addEventListener('change', e=>{
        document.querySelectorAll('.item-check').forEach(cb=>cb.checked=e.target.checked);
    });

    document.getElementById('reissueForm').addEventListener('submit', e=>{
        e.preventDefault();
        const orderId = document.getElementById('reissueOrderId').value;
        const fromUser = document.getElementById('fromUser').value.trim();
        const toUser = document.getElementById('toUser').value.trim();
        const notes = document.getElementById('notes').value.trim();
        const items = Array.from(document.querySelectorAll('.item-check:checked')).map(i=>i.value);

        if(!fromUser || !toUser || !items.length){
            Swal.fire({icon:'warning',title:'يرجى تعبئة جميع الحقول واختيار العهد'});
            return;
        }

        fetch(`<?= base_url('AssetsHistory/reDistributeItems') ?>`, {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({order_id:orderId, items, from_user_id:fromUser, to_user_id:toUser, note:notes})
        })
        .then(r=>r.json())
        .then(data=>{
            if(data.success){
                Swal.fire({icon:'success',title:data.message||'تمت العملية بنجاح',timer:1500,showConfirmButton:false});
                bootstrap.Modal.getInstance(document.getElementById('reissueModal')).hide();
            } else Swal.fire({icon:'error',title:data.message});
        })
        .catch(()=>Swal.fire({icon:'error',title:'حدث خطأ أثناء الاتصال بالخادم'}));
    });

});
</script>

</body>
</html>
