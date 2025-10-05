<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>العهد الخاصة بي </title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- استدعاء مكتبة Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

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
<!-- الشريط الجانبي للتنقل -->
<?= $this->include('layouts/header') ?>

<div class="main-content">
<div class="header">
<h1 class="page-title">العهد الخاصة بي </h1>
<!-- <div class="user-info" onclick="location.href='<?= base_url('UserInfo/getUserInfo') ?>'">
<div class="user-avatar">
<?= strtoupper(substr(esc(session()->get('name')), 0, 1)) ?>
</div>
<span><?= esc(session()->get('name')) ?></span>
</div> -->
</div>


<br>
<br>


<!-- أزرار العمليات -->
<div class="mb-3 d-flex justify-content-center gap-2">
    <button type="button" class="custom-btn" onclick="filterTable('transfer')">عمليات التحويل</button>
    <button type="button" class="custom-btn" onclick="filterTable('return')">عمليات الإرجاع</button>
    <button type="button" class="custom-btn" onclick="filterTable('all')">عرض الكل</button>
</div>




<!-- 🔎 البحث والفلترة -->
<div class="row mb-3" dir="rtl">
    <!-- بحث عام -->
    <div class="col-md-3">
        <input type="text" id="searchInput" class="form-control" placeholder="ابحث في كل الأعمدة...">
    </div>

    <!-- من تاريخ -->
    <div class="col-md-3">
        <input type="text" id="startDate" class="form-control" placeholder="من تاريخ">
    </div>

    <!-- إلى تاريخ -->
    <div class="col-md-3">
        <input type="text" id="endDate" class="form-control" placeholder="إلى تاريخ">
    </div>

    <!-- بحث بالرقم الوظيفي -->
    <div class="col-md-3">
        <input type="text" id="employeeFilter" class="form-control" placeholder="ابحث بالرقم الوظيفي">
    </div>
</div>



<!-- الجدول -->
<div class="table-container">
<table class="custom-table" id="usersTable">
<thead>
<tr class="text-center">
<th class="checkbox-cell">
<input type="checkbox" class="master-checkbox" id="masterCheckbox" onchange="toggleAllSelection()">
</th>
<th>رقم الطلب</th>
<th>الرقم الوظيفي</th>
<!-- <th>التحويلة</th> -->
<th>حالة الاستخدام</th> 
<th>تاريخ الطلب </th>
<!-- <th>رمز الموقع </th> -->
<!-- <th>مدخل البيانات</th> -->
<th>عمليات </th>

</tr>
</thead>
<tbody>
<?php if (isset($orders) && !empty($orders)): ?>
<?php foreach ($orders as $order): ?>
<tr class="text-center align-middle" data-order-id="<?= $order->order_id ?>">
<td class="checkbox-cell">
<input type="checkbox" class="custom-checkbox row-checkbox" onchange="updateSelection()">
</td>
<td><?= esc($order->order_id ?? '-') ?></td>
<td><?= esc($order->employee_id ?? '-') ?></td>
<!-- <td><?= esc($order->extension ?? 'na') ?></td> -->
<td><?= esc($order->usage_status_name ?? '-') ?></td>
<td><?= isset($order->created_at) ? esc(date('Y-m-d', strtotime($order->created_at))) : '-' ?></td>
<!-- <td><?= esc($order->location_code ?? '---') ?></td> -->
<!-- <td><?= esc($order->created_by_name ?? '-') ?></td> -->
<td>
<div class="action-buttons">
<a href="<?= site_url('InventoryController/showOrder/' . $order->order_id) ?>" class="action-btn view-btn">
<svg class="btn-icon" viewBox="0 0 24 24">
<path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" />
</svg>
عرض
</a>

</div>
</td>
</tr>
<?php endforeach; ?>
<?php else: ?>
<tr>
<td colspan="8" class="text-center">لا توجد بيانات متاحة</td>
</tr>
<?php endif; ?>
</tbody>
</table>
</div>



<script>
document.addEventListener('DOMContentLoaded', function () {
    // Flatpickr datepicker
    flatpickr("#startDate", { dateFormat: "d/m/Y", allowInput: true, onChange: filterTableFast });
    flatpickr("#endDate",   { dateFormat: "d/m/Y", allowInput: true, onChange: filterTableFast });

    // البحث بالكتابة
    document.getElementById("searchInput").addEventListener("keyup", filterTableFast);
    document.getElementById("employeeFilter").addEventListener("keyup", filterTableFast);

    // حفظ كل الصفوف في مصفوفة عند البداية
    const tableBody = document.querySelector("#usersTable tbody");
    const allRows = Array.from(tableBody.querySelectorAll("tr"));

    window.fastTableData = allRows.map(row => {
        const cells = row.querySelectorAll("td");
        return {
            row: row,
            text: Array.from(cells).map(td => td.textContent.trim().toLowerCase()).join(" "),
            employee: (cells[2] && cells[2].textContent) ? cells[2].textContent.trim().toLowerCase() : "",
            status: (cells[3] && cells[3].textContent) ? cells[3].textContent.trim().toLowerCase() : "",
            date: parseRowDate((cells[4] && cells[4].textContent) ? cells[4].textContent.trim() : "")
        };
    });

    // النوع الحالي (الكل / تحويل / رجيع)
    window.currentFilterType = 'all';

    filterTableFast(); // فلترة أولية
});

// دالة تحويل نص التاريخ
function parseDMY(d) {
    if (!d) return null;
    const parts = d.split('/');
    if (parts.length !== 3) return null;
    return new Date(parts[2], parts[1]-1, parts[0]);
}

function parseRowDate(dateText) {
    if (!dateText) return null;
    dateText = dateText.trim();
    const dmy = dateText.match(/(\d{1,2}\/\d{1,2}\/\d{4})/);
    if (dmy) return parseDMY(dmy[1]);
    const dt = new Date(dateText);
    return isNaN(dt.getTime()) ? null : dt;
}

// فلترة سريعة لجميع الشروط
function filterTableFast() {
    const searchValue = document.getElementById("searchInput").value.trim().toLowerCase();
    const startDateStr = document.getElementById("startDate").value.trim();
    const endDateStr = document.getElementById("endDate").value.trim();
    const employeeValue = document.getElementById("employeeFilter").value.trim().toLowerCase();

    let start = startDateStr ? parseDMY(startDateStr) : null;
    let end = endDateStr ? parseDMY(endDateStr) : null;
    if (end) end.setHours(23,59,59,999);

    fastTableData.forEach(item => {
        let visible = true;

        // البحث العام
        if (searchValue && !item.text.includes(searchValue)) visible = false;

        // البحث بالرقم الوظيفي
        if (employeeValue && !item.employee.includes(employeeValue)) visible = false;

        // البحث بالتاريخ
        if ((start || end) && item.date) {
            if (start && item.date < start) visible = false;
            if (end && item.date > end) visible = false;
        } else if ((start || end) && !item.date) visible = false;

        // فلترة النوع
        if (currentFilterType === 'transfer' && item.status !== 'تحويل') visible = false;
        if (currentFilterType === 'return' && item.status !== 'رجيع') visible = false;

        item.row.style.display = visible ? "" : "none";
    });
}

// تغيير نوع الفلترة عند الضغط على الأزرار
function filterTable(type) {
    currentFilterType = type;
    filterTableFast();
}
</script>



</body>
</html>