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
<h1 class="page-title"> عمليات الإرجاع </h1>
</div>

<br><br>

        <!-- 🔎 البحث والفلترة -->
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

<!-- الجدول -->
<div class="table-container">
<table class="custom-table" id="usersTable">
<thead>
<tr class="text-center">
<th>رقم الطلب</th>
<th>الرقم الوظيفي</th>
<th>التحويلة</th>
<th>حالة الاستخدام</th>
<th>تاريخ الطلب</th>
<th>عمليات</th>
</tr>
</thead>
<tbody>
<?php if (isset($orders) && !empty($orders)): ?>
<?php foreach ($orders as $order): ?>
<tr class="text-center align-middle" data-order-id="<?= $order->order_id ?>">
<td><?= esc($order->order_id ?? '-') ?></td>
<td><?= esc($order->employee_id ?? '-') ?></td>
<td><?= esc($order->extension ?? 'na') ?></td>
<td><?= esc($order->usage_status_name ?? '-') ?></td>
<td><?= isset($order->created_at) ? esc(date('Y-m-d', strtotime($order->created_at))) : '-' ?></td>
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
<td colspan="6" class="text-center">لا توجد بيانات متاحة</td>
</tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
        const table = document.getElementById("usersTable");
        const tbody = table.querySelector("tbody");
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
        date: cells[4].textContent.trim(),
        text: row.innerText.toLowerCase()
        });
        });

        // تهيئة Flatpickr للتاريخ
        flatpickr("#startDate", { dateFormat: "d/m/Y", allowInput: true, onChange: filterTable });
        flatpickr("#endDate", { dateFormat: "d/m/Y", allowInput: true, onChange: filterTable });

        document.getElementById("searchInput").addEventListener("keyup", filterTable);
        document.getElementById("employeeFilter").addEventListener("keyup", filterTable);

        filterTable(); // عرض الرجيع فقط عند التحميل

        function parseDMY(d) {
        if (!d) return null;
        const parts = d.split('/');
        return new Date(parts[2], parts[1] - 1, parts[0]);
        }

        function parseRowDate(dateText) {
        if (!dateText) return null;
        const dmy = dateText.match(/(\d{1,2}\/\d{1,2}\/\d{4})/);
        if (dmy) return parseDMY(dmy[1]);
        const ymd = dateText.match(/(\d{4}-\d{2}-\d{2})/);
        if (ymd) return new Date(ymd[1]);
        return new Date(dateText);
        }

        function filterTable() {
        const searchValue = document.getElementById("searchInput").value.trim().toLowerCase();
        const employeeValue = document.getElementById("employeeFilter").value.trim().toLowerCase();
        const startDate = document.getElementById("startDate").value ? parseDMY(document.getElementById("startDate").value) : null;
        const endDate = document.getElementById("endDate").value ? parseDMY(document.getElementById("endDate").value) : null;
        if (endDate) endDate.setHours(23, 59, 59, 999);

        rowsData.forEach(item => {
        const rowDate = parseRowDate(item.date);
        const matchSearch = !searchValue || item.text.includes(searchValue);
        const matchEmployee = !employeeValue || item.employeeId.includes(employeeValue);
        const matchDate = (!startDate && !endDate) || (rowDate && (!startDate || rowDate >= startDate) && (!endDate || rowDate <= endDate));
        const matchStatus = item.status === 'رجيع';

        item.rowElement.style.display = (matchSearch && matchEmployee && matchDate && matchStatus) ? '' : 'none';
        });
        }
        });
    </script>
</body>
</html>
