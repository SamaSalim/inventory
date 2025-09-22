<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الصفحة الرئيسية</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Cairo', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #F4F4F4;
            direction: rtl;
            text-align: right;
            min-height: 100vh;
            color: #333;
            font-size: 14px;
        }

        /* الشريط الجانبي */
        .sidebar {
            position: fixed;
            right: 0;
            top: 0;
            height: 100vh;
            width: 80px;
            background-color: #057590;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px 0;
            z-index: 1000;
        }

        .sidebar .logo {
            color: white;
            font-size: 20px;
            margin-bottom: 30px;
        }

        .sidebar-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .sidebar-icon:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

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

      
 /* بطاقات الإحصائيات */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }

        .stat-card.blue {
            background: linear-gradient(135deg, #d6eaff, #bfdcff);
        }

        .stat-card.pink {
            background: linear-gradient(135deg, #ffe0e5, #ffc9d1);
        }

        .stat-card.green {
            background: linear-gradient(135deg, #e2f0eb, #c9ede0);
        }

        .stat-card.yellow {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
        }

        .stat-number {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
            line-height: 1;
        }

        .stat-label {
            color: #666;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .stat-icon {
            width: 18px;
            height: 18px;
            fill: currentColor;
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

        /* شريط العمليات الجماعية */
        .bulk-actions {
            display: none;
            background: linear-gradient(135deg, #057590, #3AC0C3);
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            color: white;
            box-shadow: 0 4px 15px rgba(5, 117, 144, 0.3);
        }

        .bulk-actions.show {
            display: flex;
            justify-content: space-between;
            align-items: center;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .bulk-info {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
        }

        .selected-count {
            background: rgba(255, 255, 255, 0.2);
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: bold;
        }

        .bulk-buttons {
            display: flex;
            gap: 10px;
        }

        .bulk-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .bulk-edit-btn {
            background: rgba(255, 255, 255, 0.9);
            color: #057590;
        }

        .bulk-edit-btn:hover {
            background: white;
            transform: translateY(-1px);
        }

        .bulk-delete-btn {
            background: rgba(220, 53, 69, 0.9);
            color: white;
        }

        .bulk-delete-btn:hover {
            background: #dc3545;
            transform: translateY(-1px);
        }

        .bulk-cancel-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .bulk-cancel-btn:hover {
            background: rgba(255, 255, 255, 0.3);
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

        /* Edit Button - Dark blue gradient */
        .edit-btn {
            background: linear-gradient(135deg, #057590, #046073);
            color: white;
            box-shadow: 0 2px 6px rgba(5, 117, 144, 0.25);
        }

        .edit-btn:hover {
            background: linear-gradient(135deg, #046073, #035a6b);
            color: white;
            box-shadow: 0 4px 10px rgba(5, 117, 144, 0.35);
            transform: translateY(-1px);
        }

        /* Delete Button */
        .delete-btn {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            box-shadow: 0 2px 6px rgba(231, 76, 60, 0.25);
        }

        .delete-btn:hover {
            background: linear-gradient(135deg, #c0392b, #a93226);
            color: white;
            box-shadow: 0 4px 10px rgba(231, 76, 60, 0.35);
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

        /* Modal styles */
        .form-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
            backdrop-filter: blur(3px);
            padding: 20px;
        }

        .modal-content {
            background: linear-gradient(135deg, #168aad, #1d3557);
            padding: 30px;
            border-radius: 20px;
            width: 95%;
            max-width: 700px;
            max-height: 85vh;
            overflow-y: auto;
            color: white;
            position: relative;
        }

        /* رسائل التنبيه */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: none;
            font-weight: 500;
            display: none;
        }

        .alert.show {
            display: block;
            animation: slideDown 0.3s ease;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
        }

        .alert-warning {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            color: #856404;
        }

        /* التصميم المتجاوب */
        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .filter-row {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 60px;
            }

            .main-content {
                margin-right: 60px;
            }

            .content-area {
                padding: 15px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .filter-row {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .section-header {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }

            .buttons-group {
                flex-direction: column;
                gap: 10px;
            }

            .custom-table {
                font-size: 10px;
            }

            .custom-table thead th {
                padding: 10px 6px;
                font-size: 10px;
            }

            .custom-table tbody td {
                padding: 8px 4px;
                font-size: 9px;
                max-width: 80px;
            }

            .page-title {
                font-size: 18px;
            }

            .header {
                padding: 12px 15px;
            }

            .action-buttons {
                flex-direction: column;
                gap: 6px;
            }

            .action-btn {
                min-width: 60px;
                padding: 6px 12px;
                font-size: 10px;
            }

            .btn-icon {
                width: 10px;
                height: 10px;
            }

            /* تحسينات للعمليات الجماعية في الموبايل */
            .bulk-actions {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .bulk-buttons {
                justify-content: center;
                flex-wrap: wrap;
            }
        }

        @media (max-width: 480px) {
            .action-btn {
                padding: 5px 10px;
                font-size: 9px;
                min-width: 50px;
            }
        }
    </style>
</head>

<body>
   <!-- الشريط الجانبي للتنقل -->
    <?= $this->include('layouts/header') ?>

 <div class="main-content">
        <div class="header">
            <h1 class="page-title">الصفحة الرئيسية</h1>
            <div class="user-info" onclick="location.href='<?= base_url('UserInfo/getUserInfo') ?>'">
                <div class="user-avatar">
                    <?= strtoupper(substr(esc(session()->get('name')), 0, 1)) ?>
                </div>
                <span><?= esc(session()->get('name')) ?></span>
            </div>
        </div>


   
        <div class="content-area">
            <!-- رسائل التنبيه -->
            <div id="alertContainer"></div>

            <!-- قسم العنوان -->
            <div class="section-header">
                <h2 class="section-title"> *البيانات ستحذف بعد 24 ساعة*</h2>
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
                            <!-- <th>الرقم الوظيفي</th> -->
                            <th>التحويلة</th>
                            <th>تاريخ الطلب </th>
                            <th>رمز الموقع </th>
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
                                    <!-- <td><?= esc($order->employee_id ?? '-') ?></td> -->
                                    <td><?= esc($order->extension ?? 'na') ?></td>
                                    <td><?= esc($order->created_at ?? '-') ?></td>
                                    <td><?= esc($order->room_code ?? '---') ?></td>
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
       
  
 
    </body>