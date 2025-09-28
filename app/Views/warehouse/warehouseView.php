<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المستودعات</title>
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

        .add-btn.multiple-items {
            background-color: #057590;
            box-shadow: 0 2px 8px rgba(5, 117, 144, 0.3);
        }

        .add-btn.multiple-items:hover {
            background-color: #046073;
            box-shadow: 0 4px 12px rgba(5, 117, 144, 0.4);
        }

        /* شريط العمليات الجماعية - جديد */
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

        /* قسم الفلاتر المحدث - تصميم أكثر احترافية */
        .filters-section {
            background-color: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(5, 117, 144, 0.1);
        }

        /* شريط البحث الرئيسي */
        .main-search-container {
            margin-bottom: 25px;
            position: relative;
        }

        .search-section-title {
            color: #057590;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .search-bar-wrapper {
            position: relative;
            width: 50%;
            max-width: 400px;
        }

        .main-search-input {
            width: 100%;
            padding: 12px 45px 12px 16px;
            border: 2px solid #e8f4f8;
            border-radius: 20px;
            font-size: 14px;
            background: linear-gradient(135deg, #ffffff, #f8fdff);
            outline: none;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 2px 8px rgba(5, 117, 144, 0.08);
        }

        .main-search-input:focus {
            border-color: #3AC0C3;
            box-shadow: 0 0 0 4px rgba(58, 192, 195, 0.15),
                        0 4px 20px rgba(58, 192, 195, 0.2);
            background: white;
            transform: translateY(-1px);
        }

        .main-search-input::placeholder {
            color: #8aa8b5;
            font-weight: 400;
        }

        .search-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #8aa8b5;
            font-size: 18px;
            transition: all 0.3s ease;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .search-icon:hover {
            color: #3AC0C3;
            background-color: rgba(58, 192, 195, 0.1);
            transform: translateY(-50%) scale(1.1);
        }

        .main-search-input:focus + .search-icon {
            color: #3AC0C3;
        }

        /* مقسم الفلاتر */
        .filters-divider {
            display: flex;
            align-items: center;
            margin: 20px 0;
            text-align: center;
            color: #666;
        }

        .filters-divider::before,
        .filters-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, #ddd, transparent);
        }

        .filters-divider span {
            padding: 0 20px;
            background: white;
            font-size: 13px;
            font-weight: 500;
            color: #666;
        }

        /* الفلاتر التفصيلية */
        .detailed-filters {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            align-items: end;
            /* Add more bottom margin to prevent upward dropdown */
            margin-bottom: 40px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            position: relative;
        }

        .filter-label {
            font-size: 13px;
            color: #057590;
            margin-bottom: 5px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .filter-label i {
            font-size: 12px;
            color: #3AC0C3;
        }

        .filter-input,
        .filter-select {
            padding: 12px 16px;
            border: 1.5px solid #e8f4f8;
            border-radius: 10px;
            font-size: 14px;
            background: linear-gradient(135deg, #ffffff, #f8fdff);
            outline: none;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            font-weight: 400;
        }

        .filter-input:focus,
        .filter-select:focus {
            border-color: #3AC0C3;
            box-shadow: 0 0 0 3px rgba(58, 192, 195, 0.1);
            background: white;
            transform: translateY(-1px);
        }

        .filter-input::placeholder {
            color: #a0b8c4;
            font-weight: 300;
        }

        .filter-select {
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%238aa8b5' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: left 12px center;
            background-size: 16px;
            padding-left: 40px;
        }

        .filter-select option {
            background: white;
            color: #333;
            padding: 8px;
        }

        /* أزرار العمليات */
        .filter-actions {
            margin-top: 20px;
            display: flex;
            gap: 15px;
            justify-content: flex-start;
        }

        .filter-btn {
            padding: 12px 24px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            border: none;
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 120px;
            justify-content: center;
        }

        .search-btn {
            background: linear-gradient(135deg, #3AC0C3, #2aa8ab);
            color: white;
            box-shadow: 0 4px 15px rgba(58, 192, 195, 0.3);
        }

        .search-btn:hover {
            background: linear-gradient(135deg, #2aa8ab, #259a9d);
            box-shadow: 0 6px 20px rgba(58, 192, 195, 0.4);
            transform: translateY(-2px);
        }

        .reset-btn {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            color: #057590;
            border: 1.5px solid #e8f4f8;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            text-decoration: none;
        }

        .reset-btn:hover {
            background: linear-gradient(135deg, #e9ecef, #dee2e6);
            border-color: #3AC0C3;
            color: #046073;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-decoration: none;
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

        /* تحسينات checkbox - جديد */
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

        /* أزرار العمليات - محدثة ومدمجة */
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

        /* النافذة المنبثقة */
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

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .modal-title {
            font-size: 20px;
            font-weight: 600;
            color: white;
        }

        .close-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .close-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* شبكة النموذج */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 6px;
            font-weight: 500;
            color: white;
            font-size: 13px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            background: rgba(42, 61, 85, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            padding: 10px 12px;
            color: white;
            font-size: 13px;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            background: rgba(52, 73, 94, 0.9);
            border-color: rgba(255, 255, 255, 0.4);
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.1);
        }

        .form-group select option {
            background: #2c3e50;
            color: white;
        }

        /* أزرار العمليات */
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        .submit-btn,
        .cancel-btn {
            padding: 12px 25px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }

        .submit-btn {
            background: rgba(58, 192, 195, 0.9);
            color: white;
        }

        .submit-btn:hover {
            background: rgba(58, 192, 195, 1);
            transform: translateY(-1px);
        }

        .cancel-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .cancel-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .full-width {
            grid-column: 1 / -1;
        }

        /* قسم الأصناف المتعددة */
        .multiple-items-section {
            display: none;
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
        }

        .multiple-items-section.show {
            display: block;
        }

        .item-entry {
            background: rgba(255, 255, 255, 0.05);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .item-entry-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .item-number {
            background: #3AC0C3;
            color: white;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }

        .remove-item-btn {
            background: rgba(220, 53, 69, 0.8);
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .remove-item-btn:hover {
            background: rgba(220, 53, 69, 1);
            transform: scale(1.1);
        }

        .add-item-btn {
            background: rgba(40, 167, 69, 0.8);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 13px;
            width: 100%;
            margin-top: 15px;
        }

        .add-item-btn:hover {
            background: rgba(40, 167, 69, 1);
            transform: translateY(-1px);
        }

        /* dropdown للبحث في الأصناف */
        .search-dropdown {
            position: relative;
        }

        .dropdown-list {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: rgba(42, 61, 85, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }

        .dropdown-item {
            padding: 10px 12px;
            cursor: pointer;
            color: white;
            font-size: 13px;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background: rgba(58, 192, 195, 0.3);
        }

        .dropdown-item.loading,
        .dropdown-item.no-results,
        .dropdown-item.error {
            cursor: default;
            color: rgba(255, 255, 255, 0.7);
            font-style: italic;
        }

        /* رسائل الحالة */
        .status-message {
            font-size: 12px;
            padding: 5px 10px;
            margin-top: 5px;
            border-radius: 5px;
            display: none;
        }

        .loading-msg {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .error-msg {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }

        .success-msg {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .required {
            color: #ff6b6b;
        }

        /* مودال تأكيد الحذف */
        .delete-confirm-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2500;
            backdrop-filter: blur(3px);
        }

        .delete-confirm-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            max-width: 450px;
            width: 90%;
            text-align: center;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
            transform: scale(0.7);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .delete-confirm-modal.show .delete-confirm-content {
            transform: scale(1);
            opacity: 1;
        }

        .delete-confirm-title {
            color: #e74c3c;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .delete-confirm-message {
            color: #333;
            margin-bottom: 25px;
            line-height: 1.6;
            font-size: 14px;
        }

        .delete-confirm-actions {
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
            font-size: 13px;
        }

        .confirm-delete-btn {
            background: #e74c3c;
            color: white;
        }

        .confirm-delete-btn:hover {
            background: #c0392b;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
        }

        .confirm-cancel-btn {
            background: #95a5a6;
            color: white;
        }

        .confirm-cancel-btn:hover {
            background: #7f8c8d;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(149, 165, 166, 0.3);
        }

        /* مودال الحذف الجماعي - جديد */
        .bulk-delete-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2600;
            backdrop-filter: blur(3px);
        }

        .bulk-delete-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            max-width: 500px;
            width: 90%;
            text-align: center;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
            transform: scale(0.7);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .bulk-delete-modal.show .bulk-delete-content {
            transform: scale(1);
            opacity: 1;
        }

        /* رسائل التنبيه - جديد */
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

            .detailed-filters {
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

            .form-grid {
                grid-template-columns: 1fr;
            }

            .detailed-filters {
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

            .modal-content {
                padding: 20px;
                margin: 10px;
                width: calc(100% - 20px);
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

            /* تحسينات شريط البحث للموبايل */
            .main-search-input {
                padding: 14px 45px 14px 18px;
                font-size: 14px;
            }

            .search-icon {
                left: 15px;
                font-size: 16px;
            }

            .filter-actions {
                flex-direction: column;
                gap: 10px;
            }

            .filter-btn {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .modal-content {
                max-height: 90vh;
            }

            .form-actions {
                flex-direction: column;
            }

            .submit-btn,
            .cancel-btn {
                width: 100%;
            }

            .action-btn {
                padding: 5px 10px;
                font-size: 9px;
                min-width: 50px;
            }

            .filters-section {
                padding: 20px 15px;
            }

            .main-search-input {
                padding: 12px 40px 12px 15px;
                font-size: 13px;
            }
        }
    </style>
</head>

<body>
    <!-- الشريط الجانبي للتنقل -->
    <?= $this->include('layouts/header') ?>

    <div class="main-content">
        <div class="header">
            <h1 class="page-title">إدارة المستودعات</h1>
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

            <div class="stats-grid">
                <div class="stat-card blue">
                    <div class="stat-number"><?= number_format($stats['total_receipts'] ?? 0) ?></div>
                    <div class="stat-label">
                        <svg class="stat-icon" viewBox="0 0 24 24">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z" />
                        </svg>
                        إجمالي الكميات
                    </div>
                </div>

                <div class="stat-card pink">
                    <div class="stat-number"><?= number_format($stats['available_items'] ?? 0) ?></div>
                    <div class="stat-label">
                        <svg class="stat-icon" viewBox="0 0 24 24">
                            <path d="M20 6h-2.18c.11-.31.18-.65.18-1a2.996 2.996 0 0 0-5.5-1.65l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2z" />
                        </svg>
                        أصناف متوفرة
                    </div>
                </div>

                <div class="stat-card green">
                    <div class="stat-number"><?= number_format($stats['total_entries'] ?? 0) ?></div>
                    <div class="stat-label">
                        <svg class="stat-icon" viewBox="0 0 24 24">
                            <path d="M9 11H7v6h2v-6zm4 0h-2v6h2v-6zm4 0h-2v6h2v-6zm2-7h-3V2h-2v2H8V2H6v2H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2z" />
                        </svg>
                        عدد الإدخالات
                    </div>
                </div>

                <div class="stat-card" style="background: linear-gradient(135deg, #fff3cd, #ffeaa7);">
                    <div class="stat-number"><?= number_format($stats['low_stock'] ?? 0) ?></div>
                    <div class="stat-label">
                        <svg class="stat-icon" viewBox="0 0 24 24">
                            <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" />
                        </svg>
                        مخزون منخفض
                    </div>
                </div>
            </div>

            <div class="section-header">
                <h2 class="section-title">قائمة المخزون</h2>
                <div class="buttons-group">
                   <a href="<?= site_url('OrderController/create') ?>" class="add-btn">
                    <i class="fas fa-plus"></i> إنشاء طلب لنفس التصنيف
                    </a>
                    <!-- هنا زر لإنشاء طلب لتصنيفات مختلفة -->
                    <a href="<?= site_url('OrderController/index') ?>" class="add-btn multiple-items">
                        <i class="fas fa-layer-group"></i> إنشاء طلب لتصنيفات مختلفة
                    </a>
                </div>
            </div>

            <!-- شريط العمليات الجماعية -->
            <div class="bulk-actions" id="bulkActions">
                <div class="bulk-info">
                    <i class="fas fa-check-circle"></i>
                    <span>تم اختيار</span>
                    <span class="selected-count" id="selectedCount">0</span>
                    <span>طلب</span>
                </div>
                <div class="bulk-buttons">
                    <button class="bulk-btn bulk-delete-btn" onclick="bulkDelete()">
                        <i class="fas fa-trash"></i>
                        حذف جماعي
                    </button>
                    <button class="bulk-btn bulk-cancel-btn" onclick="clearSelection()">
                        <i class="fas fa-times"></i>
                        إلغاء الاختيار
                    </button>
                </div>
            </div>

            <!-- قسم البحث والفلاتر المحدث -->
            <form method="get" action="<?= base_url('InventoryController/index') ?>">
                <div class="filters-section">
                    <!-- شريط البحث الرئيسي -->
                    <div class="main-search-container">
                        <h3 class="search-section-title">
                            <i class="fas fa-search"></i>
                            البحث العام
                        </h3>
                        <div class="search-bar-wrapper">
                            <input type="text" class="main-search-input" name="search" id="mainSearchInput"
                                   value="<?= esc($filters['search'] ?? '') ?>" 
                                   placeholder="ابحث في جميع الحقول...">
                            <i class="fas fa-search search-icon" onclick="document.querySelector('form').submit();" title="بحث"></i>
                        </div>
                    </div>

                    <!-- مقسم الفلاتر -->
                    <div class="filters-divider">
                        <span><i class="fas fa-filter"></i> الفلاتر التفصيلية</span>
                    </div>

                    <!-- الفلاتر التفصيلية -->
                    <div class="detailed-filters">
                        <!-- الصنف -->
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-tag"></i>
                                الصنف
                            </label>
                            <input type="text" class="filter-input" name="item_type"
                                   value="<?= esc($filters['item_type'] ?? '') ?>" 
                                   placeholder="اكتب نوع الصنف">
                        </div>

                        <!-- التصنيف -->
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-layer-group"></i>
                                التصنيف
                            </label>
                            <select class="filter-select" name="category">
                                <option value="">اختر التصنيف</option>
                                <?php if (isset($categories)): ?>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= esc($cat->id) ?>"
                                            <?= ($filters['category'] ?? '') == $cat->id ? 'selected' : '' ?>>
                                            <?= esc($cat->name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- الرقم التسلسلي -->
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-barcode"></i>
                                الرقم التسلسلي
                            </label>
                            <input type="text" class="filter-input" name="serial_number"
                                   value="<?= esc($filters['serial_number'] ?? '') ?>" 
                                   placeholder="رقم تسلسلي محدد">
                        </div>

                        <!-- الرقم الوظيفي -->
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-id-badge"></i>
                                الرقم الوظيفي
                            </label>
                            <input type="text" class="filter-input" name="employee_id"
                                   value="<?= esc($filters['employee_id'] ?? '') ?>" 
                                   placeholder="رقم الموظف">
                        </div>

                        <!-- الموقع -->
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-map-marker-alt"></i>
                                الموقع
                            </label>
                            <input type="text" class="filter-input" name="location"
                                   value="<?= esc($filters['location'] ?? '') ?>" 
                                   placeholder="اكتب اسم الموقع">
                        </div>
                    </div>

                    <!-- أزرار العمليات -->
                    <div class="filter-actions">
                        <button type="submit" class="filter-btn search-btn">
                            <i class="fas fa-search"></i>
                            بحث
                        </button>
                        <a href="<?= base_url('InventoryController/index') ?>" class="filter-btn reset-btn">
                            <i class="fas fa-undo"></i>
                            إعادة تعيين
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-container">
                <table class="custom-table" id="datatable-orders">
                    <thead>
                        <tr class="text-center">
                            <th class="checkbox-cell">
                                <input type="checkbox" class="master-checkbox" id="masterCheckbox" onchange="toggleAllSelection()">
                            </th>
                            <th>رقم الطلب</th>
                            <th>الرقم الوظيفي</th>
                            <th>التحويلة</th>
                            <th>تاريخ الطلب</th>
                            <th>رمز الموقع</th>
                            <th>مدخل البيانات</th>
                            <th>عمليات</th>
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
                                    <td><?= esc($order->extension ?? 'na') ?></td>
                                    <td><?= esc($order->created_at ?? '-') ?></td>
                                    <td><?= $order->location_code ?></td>
                                    <td><?= esc($order->created_by_name ?? '-') ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="<?= site_url('InventoryController/showOrder/' . $order->order_id) ?>" class="action-btn view-btn">
                                                <svg class="btn-icon" viewBox="0 0 24 24">
                                                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" />
                                                </svg>
                                                عرض
                                            </a>
                                            <a href="<?= site_url('InventoryController/editOrder/' . $order->order_id) ?>" class="action-btn edit-btn">
                                                <svg class="btn-icon" viewBox="0 0 24 24">
                                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                                                </svg>
                                                تعديل
                                            </a>
                                            <button class="action-btn delete-btn" onclick="deleteOrderConfirm(<?= $order->order_id ?>)" title="حذف الطلب كاملاً">
                                                <i class="fas fa-trash"></i>
                                                حذف
                                            </button>
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
        </div>
    </div>

    <!-- مودال تأكيد الحذف الفردي -->
    <div class="delete-confirm-modal" id="deleteConfirmModal">
        <div class="delete-confirm-content">
            <div class="delete-confirm-title">
                <i class="fas fa-exclamation-triangle"></i>
                تأكيد حذف الطلب
            </div>
            <div class="delete-confirm-message" id="deleteConfirmMessage">
                هل أنت متأكد من حذف هذا الطلب؟<br>
                <strong>سيتم حذف جميع العناصر المرتبطة بالطلب ولا يمكن التراجع عن هذا الإجراء.</strong>
            </div>
            <div class="delete-confirm-actions">
                <button class="confirm-btn confirm-cancel-btn" onclick="cancelDeleteOrder()">
                    إلغاء
                </button>
                <button class="confirm-btn confirm-delete-btn" onclick="confirmDeleteOrder()">
                    <i class="fas fa-trash"></i>
                    تأكيد الحذف
                </button>
            </div>
        </div>
    </div>

    <!-- مودال تأكيد الحذف الجماعي -->
    <div class="bulk-delete-modal" id="bulkDeleteModal">
        <div class="bulk-delete-content">
            <div class="delete-confirm-title">
                <i class="fas fa-exclamation-triangle"></i>
                تأكيد الحذف الجماعي
            </div>
            <div class="delete-confirm-message" id="bulkDeleteMessage">
                هل أنت متأكد من حذف الطلبات المختارة؟<br>
                <strong style="color: #e74c3c;">سيتم حذف جميع العناصر المرتبطة بهذه الطلبات ولا يمكن التراجع عن هذا الإجراء.</strong>
            </div>
            <div class="delete-confirm-actions">
                <button class="confirm-btn confirm-cancel-btn" onclick="closeBulkDeleteModal()">
                    إلغاء
                </button>
                <button class="confirm-btn confirm-delete-btn" onclick="confirmBulkDelete()">
                    <i class="fas fa-trash"></i>
                    تأكيد الحذف
                </button>
            </div>
        </div>
    </div>



    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // متغيرات عامة
        let currentOrderType = 'single';
        let itemCounter = 0;
        let savedFormData = {};
        let searchTimeout;
        let debugMode = true;
        let orderToDelete = null;
        let selectedOrders = new Set(); // متغير جديد للطلبات المختارة

        // =================== وظائف الاختيار المتعدد - جديد ===================

        // وظائف الاختيار
        function toggleAllSelection() {
            const masterCheckbox = document.getElementById('masterCheckbox');
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');

            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = masterCheckbox.checked;
                const row = checkbox.closest('tr');
                if (masterCheckbox.checked) {
                    row.classList.add('selected');
                    selectedOrders.add(row.dataset.orderId);
                } else {
                    row.classList.remove('selected');
                    selectedOrders.delete(row.dataset.orderId);
                }
            });

            updateBulkActions();
        }

        function updateSelection() {
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');
            const masterCheckbox = document.getElementById('masterCheckbox');

            selectedOrders.clear();

            let checkedCount = 0;
            rowCheckboxes.forEach(checkbox => {
                const row = checkbox.closest('tr');
                if (checkbox.checked) {
                    checkedCount++;
                    row.classList.add('selected');
                    selectedOrders.add(row.dataset.orderId);
                } else {
                    row.classList.remove('selected');
                    selectedOrders.delete(row.dataset.orderId);
                }
            });

            // تحديث الـ master checkbox
            if (checkedCount === 0) {
                masterCheckbox.indeterminate = false;
                masterCheckbox.checked = false;
            } else if (checkedCount === rowCheckboxes.length) {
                masterCheckbox.indeterminate = false;
                masterCheckbox.checked = true;
            } else {
                masterCheckbox.indeterminate = true;
                masterCheckbox.checked = false;
            }

            updateBulkActions();
        }

        function updateBulkActions() {
            const bulkActions = document.getElementById('bulkActions');
            const selectedCount = document.getElementById('selectedCount');

            if (selectedOrders.size > 0) {
                bulkActions.classList.add('show');
                selectedCount.textContent = selectedOrders.size;
            } else {
                bulkActions.classList.remove('show');
            }
        }

        function clearSelection() {
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');
            const masterCheckbox = document.getElementById('masterCheckbox');

            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
                checkbox.closest('tr').classList.remove('selected');
            });

            masterCheckbox.checked = false;
            masterCheckbox.indeterminate = false;
            selectedOrders.clear();
            updateBulkActions();
        }

        // وظائف العمليات الجماعية
        function bulkDelete() {
            if (selectedOrders.size === 0) {
                showAlert('يرجى اختيار طلب واحد على الأقل للحذف', 'warning');
                return;
            }

            const modal = document.getElementById('bulkDeleteModal');
            const message = document.getElementById('bulkDeleteMessage');
            const orderIds = Array.from(selectedOrders);

            message.innerHTML = `
                هل أنت متأكد من حذف <strong>${orderIds.length}</strong> طلب؟<br>
                الطلبات المختارة: <strong>${orderIds.join(', ')}</strong><br>
                <strong style="color: #e74c3c;">سيتم حذف جميع العناصر المرتبطة بهذه الطلبات ولا يمكن التراجع عن هذا الإجراء.</strong>
            `;

            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('show'), 10);
        }

        function closeBulkDeleteModal() {
            const modal = document.getElementById('bulkDeleteModal');
            modal.classList.remove('show');
            setTimeout(() => modal.style.display = 'none', 300);
        }

        function confirmBulkDelete() {
            const orderIds = Array.from(selectedOrders);

            if (orderIds.length === 0) {
                showAlert('لم يتم اختيار أي طلبات للحذف', 'warning');
                closeBulkDeleteModal();
                return;
            }

            // تعطيل الزر أثناء العملية
            const confirmBtn = document.querySelector('.confirm-delete-btn');
            const originalBtnText = confirmBtn.innerHTML;
            confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحذف...';
            confirmBtn.disabled = true;

            console.log('إرسال طلب حذف جماعي للطلبات:', orderIds);

            // طلب AJAX للحذف الجماعي
            fetch('<?= base_url('InventoryController/bulkDeleteOrders') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        order_ids: orderIds
                    })
                })
                .then(response => {
                    console.log('استجابة الخادم:', response);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('بيانات الاستجابة:', data);

                    if (data.success) {
                        // إزالة الصفوف من الجدول مع تأثير بصري
                        orderIds.forEach(orderId => {
                            const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
                            if (row) {
                                row.style.transition = 'all 0.5s ease';
                                row.style.opacity = '0';
                                row.style.transform = 'translateX(-100%)';
                                setTimeout(() => {
                                    if (row.parentNode) {
                                        row.remove();
                                    }
                                }, 500);
                            }
                        });

                        showAlert(data.message || `تم حذف ${orderIds.length} طلب بنجاح`, 'success');
                        clearSelection();
                    } else {
                        showAlert('خطأ في حذف الطلبات: ' + (data.message || 'خطأ غير معروف'), 'danger');
                    }

                    closeBulkDeleteModal();
                })
                .catch(error => {
                    console.error('خطأ في حذف الطلبات:', error);
                    showAlert('حدث خطأ أثناء حذف الطلبات: ' + error.message, 'danger');
                    closeBulkDeleteModal();
                })
                .finally(() => {
                    // استعادة الزر
                    confirmBtn.innerHTML = originalBtnText;
                    confirmBtn.disabled = false;
                });
        }

        // وظيفة عرض الرسائل - محدثة
        function showAlert(message, type = 'success') {
            const alertContainer = document.getElementById('alertContainer');
            const alert = document.createElement('div');
            alert.className = `alert alert-${type} show`;
            alert.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : 'exclamation-triangle'}"></i>
                ${message}
            `;

            alertContainer.appendChild(alert);

            // إزالة الرسالة بعد 4 ثوان
            setTimeout(() => {
                alert.style.transition = 'all 0.3s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 300);
            }, 4000);
        }

        // =================== وظائف النموذج المنبثق الأصلية ===================

        function openOrderForm(type = 'single') {
            currentOrderType = type;
            const modal = document.getElementById('orderModal');
            const modalTitle = document.getElementById('modalTitle');

            if (type === 'single') {
                modalTitle.textContent = 'إنشاء طلب لنفس التصنيف';
                showSingleItemSection();
            } else {
                modalTitle.textContent = 'إنشاء طلب لتصنيفات مختلفة';
                showMultipleItemsSection();
                document.getElementById('multipleItemsContainer').innerHTML = '';
                itemCounter = 0;
                addNewItemEntry();
            }

            modal.style.display = 'flex';
            restoreFormData();
        }

        function closeOrderForm() {
            saveFormData();
            const modal = document.getElementById('orderModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        function showSingleItemSection() {
            const singleSection = document.getElementById('singleItemSection');
            const multipleSection = document.getElementById('multipleItemsSection');

            singleSection.style.display = 'block';
            multipleSection.classList.remove('show');

            enableRequiredFields(singleSection, true);
            enableRequiredFields(multipleSection, false);
        }

        function showMultipleItemsSection() {
            const singleSection = document.getElementById('singleItemSection');
            const multipleSection = document.getElementById('multipleItemsSection');

            singleSection.style.display = 'none';
            multipleSection.classList.add('show');

            enableRequiredFields(singleSection, false);
        }

        function enableRequiredFields(section, enable) {
            const requiredFields = section.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (enable) {
                    field.setAttribute('required', 'required');
                } else {
                    field.removeAttribute('required');
                }
            });
        }

        function saveFormData() {
            const form = document.querySelector('#orderModal form');
            if (form) {
                const formData = new FormData(form);
                savedFormData = {};
                for (let [key, value] of formData.entries()) {
                    savedFormData[key] = value;
                }
            }
        }

        function restoreFormData() {
            const form = document.querySelector('#orderModal form');
            if (form && Object.keys(savedFormData).length > 0) {
                for (let [key, value] of Object.entries(savedFormData)) {
                    const input = form.querySelector(`[name="${key}"]`);
                    if (input) {
                        input.value = value;
                    }
                }
                const quantity = parseInt(savedFormData.quantity) || 0;
                if (quantity > 0) {
                    createAssetSerialFields(quantity);
                }
            }
        }

        function clearSavedData() {
            savedFormData = {};
        }

        // وظائف إدارة الأصناف المتعددة
        function addNewItemEntry() {
            itemCounter++;
            const container = document.getElementById('multipleItemsContainer');

            const itemEntry = document.createElement('div');
            itemEntry.className = 'item-entry';
            itemEntry.setAttribute('data-item-id', itemCounter);

            itemEntry.innerHTML = `
                <div class="item-entry-header">
                    <span class="item-number">صنف ${itemCounter}</span>
                    <button type="button" class="remove-item-btn" onclick="removeItemEntry(${itemCounter})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>التصنيف الرئيسي <span class="required">*</span></label>
                        <select name="major_category_${itemCounter}" id="majorCategory_${itemCounter}" required>
                            <option value="">جاري التحميل...</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>التصنيف الفرعي <span class="required">*</span></label>
                        <select name="minor_category_${itemCounter}" id="minorCategory_${itemCounter}" required disabled>
                            <option value="">اختر التصنيف الفرعي</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>الصنف <span class="required">*</span></label>
                        <div class="search-dropdown">
                            <input type="text" name="item_${itemCounter}" id="itemInput_${itemCounter}" class="search-input" placeholder="ابحث عن الصنف..." autocomplete="off" required>
                            <div class="dropdown-list" id="itemDropdown_${itemCounter}"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>رقم الموديل</label>
                        <input type="text" name="model_num_${itemCounter}" placeholder="أدخل رقم الموديل">
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>رقم الأصول <span class="required">*</span></label>
                        <input type="text" name="asset_num_${itemCounter}" placeholder="أدخل رقم الأصول" required>
                    </div>
                    <div class="form-group">
                        <label>الرقم التسلسلي <span class="required">*</span></label>
                        <input type="text" name="serial_num_${itemCounter}" placeholder="أدخل الرقم التسلسلي" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>ملاحظات خاصة بهذا الصنف</label>
                    <textarea name="note_${itemCounter}" rows="2" placeholder="ملاحظات إضافية"></textarea>
                </div>
            `;

            container.appendChild(itemEntry);

            setupItemEvents(itemCounter);
            loadMajorCategoriesForItem(itemCounter);
        }

        function setupItemEvents(itemId) {
            const majorSelect = document.getElementById(`majorCategory_${itemId}`);
            if (majorSelect) {
                majorSelect.addEventListener('change', function() {
                    updateMinorCategoriesForItem(itemId, this.value);
                });
            }

            initItemSearchForMultiple(itemId);
        }

        function loadMajorCategoriesForItem(itemId) {
            const majorCategorySelect = document.getElementById(`majorCategory_${itemId}`);
            if (!majorCategorySelect) return;

            fetch('<?= base_url('InventoryController/getformdata') ?>')
                .then(response => response.json())
                .then(data => {
                    majorCategorySelect.innerHTML = '<option value="">اختر التصنيف الرئيسي</option>';
                    if (data.success && data.categories) {
                        data.categories.forEach(category => {
                            majorCategorySelect.innerHTML += `<option value="${category.id}">${category.name}</option>`;
                        });
                    }
                })
                .catch(error => {
                    console.error('خطأ في تحميل التصنيفات:', error);
                    majorCategorySelect.innerHTML = '<option value="">خطأ في التحميل</option>';
                });
        }

        function updateMinorCategoriesForItem(itemId, majorCategoryId) {
            const minorSelect = document.getElementById(`minorCategory_${itemId}`);
            if (!minorSelect) return;

            minorSelect.innerHTML = '<option value="">اختر التصنيف الفرعي</option>';
            minorSelect.disabled = !majorCategoryId;

            if (majorCategoryId) {
                fetch(`<?= base_url('InventoryController/getminorcategoriesbymajor') ?>/${majorCategoryId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data) {
                            data.data.forEach(minorCategory => {
                                minorSelect.innerHTML += `<option value="${minorCategory.id}">${minorCategory.name}</option>`;
                            });
                            minorSelect.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('خطأ في تحميل التصنيفات الفرعية:', error);
                    });
            }
        }

        function initItemSearchForMultiple(itemId) {
            const searchInput = document.getElementById(`itemInput_${itemId}`);
            const dropdown = document.getElementById(`itemDropdown_${itemId}`);

            if (!searchInput || !dropdown) return;

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.trim();

                if (searchTerm.length < 2) {
                    dropdown.style.display = 'none';
                    return;
                }

                dropdown.innerHTML = '<div class="dropdown-item loading">جاري البحث...</div>';
                dropdown.style.display = 'block';

                fetch(`<?= base_url('InventoryController/searchitems') ?>?term=${encodeURIComponent(searchTerm)}`)
                    .then(response => response.json())
                    .then(data => {
                        dropdown.innerHTML = '';
                        if (data.success && data.data && data.data.length > 0) {
                            dropdown.innerHTML = data.data.map(item => `<div class="dropdown-item">${item}</div>`).join('');
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
        }

        function removeItemEntry(itemId) {
            const itemEntry = document.querySelector(`[data-item-id="${itemId}"]`);
            if (itemEntry) {
                itemEntry.remove();
            }

            const remainingItems = document.querySelectorAll('.item-entry[data-item-id]');
            remainingItems.forEach((item, index) => {
                const itemNumber = item.querySelector('.item-number');
                if (itemNumber) {
                    itemNumber.textContent = `صنف ${index + 1}`;
                }
            });
        }

        // وظائف البحث والتصفية
        function performSearch() {
            const generalSearch = document.getElementById('generalSearch').value.toLowerCase();
            const searchId = document.getElementById('searchId').value.toLowerCase();
            const searchCategory = document.getElementById('searchCategory').value.toLowerCase();
            const searchSerial = document.getElementById('searchSerial').value.toLowerCase();
            const searchEmployeeId = document.getElementById('searchEmployeeId').value.toLowerCase();
            const searchLocation = document.getElementById('searchLocation').value.toLowerCase();

            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                if (row.cells.length < 8) return;

                const orderIdCell = row.cells[1]?.textContent.toLowerCase() || '';
                const employeeIdCell = row.cells[2]?.textContent.toLowerCase() || '';
                const extensionCell = row.cells[3]?.textContent.toLowerCase() || '';
                const createdAtCell = row.cells[4]?.textContent.toLowerCase() || '';
                const roomCodeCell = row.cells[5]?.textContent.toLowerCase() || '';
                const createdByCell = row.cells[6]?.textContent.toLowerCase() || '';

                const allText = row.textContent.toLowerCase();

                const matchGeneral = !generalSearch || allText.includes(generalSearch);
                const matchId = !searchId || orderIdCell.includes(searchId);
                const matchCategory = !searchCategory || allText.includes(searchCategory);
                const matchSerial = !searchSerial || allText.includes(searchSerial);
                const matchEmployeeId = !searchEmployeeId || employeeIdCell.includes(searchEmployeeId);
                const matchLocation = !searchLocation || roomCodeCell.includes(searchLocation);

                if (matchGeneral && matchId && matchCategory && matchSerial && matchEmployeeId && matchLocation) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function initItemSearch() {
            const searchInput = document.querySelector('input[name="item"]');
            const dropdown = document.getElementById('itemDropdown');

            if (!searchInput || !dropdown) return;

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.trim();
                if (searchTerm.length < 2) {
                    dropdown.style.display = 'none';
                    return;
                }

                dropdown.innerHTML = '<div class="dropdown-item loading">جاري البحث...</div>';
                dropdown.style.display = 'block';

                fetch(`<?= base_url('InventoryController/searchitems') ?>?term=${encodeURIComponent(searchTerm)}`)
                    .then(response => response.json())
                    .then(data => {
                        dropdown.innerHTML = '';
                        if (data.success && data.data && data.data.length > 0) {
                            dropdown.innerHTML = data.data.map(item => `<div class="dropdown-item">${item}</div>`).join('');
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
                if (!e.target.closest('.search-dropdown')) {
                    dropdown.style.display = 'none';
                }
            });
        }

        function createAssetSerialFields(quantity) {
            const container = document.getElementById('assetSerialContainer');
            const dynamicSection = document.getElementById('dynamicFields');
            container.innerHTML = '';
            if (quantity > 0) {
                for (let i = 1; i <= quantity; i++) {
                    const fieldDiv = document.createElement('div');
                    fieldDiv.className = 'item-entry';
                    fieldDiv.innerHTML = `
                        <div class="item-entry-header">
                            <span class="item-number">العنصر رقم ${i}</span>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>رقم الأصول <span class="required">*</span></label>
                                <input type="text" name="asset_num_${i}" placeholder="أدخل رقم الأصول" required>
                            </div>
                            <div class="form-group">
                                <label>الرقم التسلسلي <span class="required">*</span></label>
                                <input type="text" name="serial_num_${i}" placeholder="أدخل الرقم التسلسلي" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>رقم الموديل</label>
                            <input type="text" name="model_num_${i}" placeholder="أدخل رقم الموديل">
                        </div>
                    `;
                    container.appendChild(fieldDiv);
                }
                dynamicSection.style.display = 'block';
            } else {
                dynamicSection.style.display = 'none';
            }
        }

        function initQuantityHandler() {
            const quantityInput = document.querySelector('input[name="quantity"]');
            if (quantityInput) {
                quantityInput.addEventListener('input', function() {
                    const quantity = parseInt(this.value) || 0;
                    createAssetSerialFields(quantity);
                });
            }
        }

        function initEmployeeSearch() {
            const employeeIdInput = document.getElementById('toEmployeeId');
            const receiverNameInput = document.getElementById('receiverName');
            const emailInput = document.getElementById('employeeEmail');
            const transferInput = document.getElementById('transferNumber');
            const loadingMsg = document.getElementById('employeeLoadingMsg');
            const errorMsg = document.getElementById('employeeErrorMsg');
            const successMsg = document.getElementById('employeeSuccessMsg');

            employeeIdInput.addEventListener('input', function() {
                const employeeId = this.value.trim();
                loadingMsg.style.display = 'none';
                errorMsg.style.display = 'none';
                successMsg.style.display = 'none';
                receiverNameInput.value = '';
                emailInput.value = '';
                transferInput.value = '';
                if (employeeId.length < 3) return;
                clearTimeout(searchTimeout);
                loadingMsg.style.display = 'block';

                fetch(`<?= base_url('InventoryController/searchemployee') ?>?emp_id=${encodeURIComponent(employeeId)}`)
                    .then(response => response.json())
                    .then(data => {
                        loadingMsg.style.display = 'none';
                        if (data.success) {
                            receiverNameInput.value = data.data.name || '';
                            emailInput.value = data.data.email || '';
                            transferInput.value = data.data.transfer_number || '';
                            successMsg.style.display = 'block';
                            receiverNameInput.removeAttribute('readonly');
                            emailInput.removeAttribute('readonly');
                            transferInput.removeAttribute('readonly');
                        } else {
                            errorMsg.textContent = data.message || 'الرقم الوظيفي غير موجود';
                            errorMsg.style.display = 'block';
                            receiverNameInput.setAttribute('readonly', true);
                            emailInput.setAttribute('readonly', true);
                            transferInput.setAttribute('readonly', true);
                        }
                    })
                    .catch(error => {
                        console.error('خطأ في البحث عن الموظف:', error);
                        loadingMsg.style.display = 'none';
                        errorMsg.textContent = 'خطأ في الاتصال بالخادم';
                        errorMsg.style.display = 'block';
                    });
            });
        }

        function initLocationDropdowns() {
            const buildingSelect = document.getElementById('buildingSelect');
            const floorSelect = document.getElementById('floorSelect');
            const roomSelect = document.getElementById('roomSelect');
            const departmentSelect = document.getElementById('departmentSelect');
            const majorCategorySelect = document.getElementById('majorCategorySelect');
            const minorCategorySelect = document.getElementById('minorCategorySelect');

            loadInitialData();

            buildingSelect.addEventListener('change', function() {
                loadFloors(this.value);
            });

            floorSelect.addEventListener('change', function() {
                loadRoomsBySection(this.value);
            });

            majorCategorySelect.addEventListener('change', function() {
                loadMinorCategories(this.value);
            });

            function loadInitialData() {
                fetch(`<?= base_url('InventoryController/getformdata') ?>`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (data.buildings && Array.isArray(data.buildings)) {
                                buildingSelect.innerHTML = '<option value="">اختر المبنى</option>';
                                data.buildings.forEach(building => {
                                    buildingSelect.innerHTML += `<option value="${building.id}">${building.code || building.name}</option>`;
                                });
                            }

                            if (data.categories && Array.isArray(data.categories)) {
                                majorCategorySelect.innerHTML = '<option value="">اختر التصنيف الرئيسي</option>';
                                data.categories.forEach(category => {
                                    majorCategorySelect.innerHTML += `<option value="${category.id}">${category.name}</option>`;
                                });
                            }

                            if (data.departments && Array.isArray(data.departments)) {
                                departmentSelect.innerHTML = '<option value="">اختر القسم</option>';
                                data.departments.forEach(dept => {
                                    departmentSelect.innerHTML += `<option value="${dept}">${dept}</option>`;
                                });
                            }
                        }
                    });
            }

            function loadFloors(buildingId) {
                floorSelect.innerHTML = '<option value="">اختر الطابق</option>';
                roomSelect.innerHTML = '<option value="">اختر الغرفة</option>';
                floorSelect.disabled = !buildingId;
                roomSelect.disabled = true;
                if (!buildingId) return;
                fetch(`<?= base_url('InventoryController/getfloorsbybuilding') ?>/${buildingId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data && Array.isArray(data.data)) {
                            data.data.forEach(floor => {
                                floorSelect.innerHTML += `<option value="${floor.id}">${floor.code || floor.name}</option>`;
                            });
                            floorSelect.disabled = false;
                        }
                    });
            }

            function loadRoomsBySection(floorId) {
                roomSelect.innerHTML = '<option value="">اختر الغرفة</option>';
                roomSelect.disabled = true;
                if (!floorId) return;
                fetch(`<?= base_url('InventoryController/getsectionsbyfloor') ?>/${floorId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data && Array.isArray(data.data) && data.data.length > 0) {
                            const sectionId = data.data[0].id;
                            fetch(`<?= base_url('InventoryController/getroomsbysection') ?>/${sectionId}`)
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success && data.data && Array.isArray(data.data)) {
                                        data.data.forEach(room => {
                                            roomSelect.innerHTML += `<option value="${room.id}">${room.code || room.name}</option>`;
                                        });
                                        roomSelect.disabled = false;
                                    }
                                });
                        }
                    });
            }

            function loadMinorCategories(majorCategoryId) {
                minorCategorySelect.innerHTML = '<option value="">اختر التصنيف الفرعي</option>';
                minorCategorySelect.disabled = !majorCategoryId;
                if (!majorCategoryId) return;
                fetch(`<?= base_url('InventoryController/getminorcategoriesbymajor') ?>/${majorCategoryId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data && Array.isArray(data.data)) {
                            data.data.forEach(minorCategory => {
                                minorCategorySelect.innerHTML += `<option value="${minorCategory.id}">${minorCategory.name}</option>`;
                            });
                            minorCategorySelect.disabled = false;
                        }
                    });
            }
        }

        // وظائف إدارة حذف الطلبات الفردية
        function editOrder(orderId) {
            window.location.href = `<?= base_url('InventoryController/editOrder') ?>/${orderId}`;
        }

        function viewOrder(orderId) {
            console.log('عرض تفاصيل الطلب:', orderId);
        }

        function deleteOrderConfirm(orderId) {
            orderToDelete = orderId;
            const modal = document.getElementById('deleteConfirmModal');
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('show'), 10);

            document.getElementById('deleteConfirmMessage').innerHTML = `
                هل أنت متأكد من حذف الطلب رقم <strong>${orderId}</strong>؟<br>
                <strong style="color: #e74c3c;">سيتم حذف جميع العناصر المرتبطة بالطلب ولا يمكن التراجع عن هذا الإجراء.</strong>
            `;
        }

        function cancelDeleteOrder() {
            orderToDelete = null;
            const modal = document.getElementById('deleteConfirmModal');
            modal.classList.remove('show');
            setTimeout(() => modal.style.display = 'none', 300);
        }

        function confirmDeleteOrder() {
            if (!orderToDelete) {
                cancelDeleteOrder();
                return;
            }

            const deleteBtn = document.querySelector(`tr[data-order-id="${orderToDelete}"] .delete-btn`);
            const originalHtml = deleteBtn.innerHTML;

            deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            deleteBtn.disabled = true;

            fetch(`<?= base_url('InventoryController/deleteOrder') ?>/${orderToDelete}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const orderRow = document.querySelector(`tr[data-order-id="${orderToDelete}"]`);
                        if (orderRow) {
                            orderRow.style.transition = 'all 0.5s ease';
                            orderRow.style.opacity = '0';
                            orderRow.style.transform = 'translateX(-100%)';

                            setTimeout(() => {
                                orderRow.remove();
                            }, 500);
                        }

                        showAlert(data.message, 'success');
                    } else {
                        showAlert('خطأ: ' + data.message, 'danger');
                        deleteBtn.innerHTML = originalHtml;
                        deleteBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('خطأ في حذف الطلب:', error);
                    showAlert('حدث خطأ أثناء حذف الطلب', 'danger');
                    deleteBtn.innerHTML = originalHtml;
                    deleteBtn.disabled = false;
                })
                .finally(() => {
                    cancelDeleteOrder();
                });
        }

        // معالج إرسال النموذج
        document.getElementById('orderForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;

            if (currentOrderType === 'single') {
                const formData = new FormData(form);

                fetch(form.action, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAlert(data.message, 'success');
                            window.location.reload();
                        } else {
                            showAlert('خطأ: ' + data.message, 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('خطأ في إرسال الطلب:', error);
                        showAlert('حدث خطأ في إرسال الطلب: ' + error.message, 'danger');
                    });
            } else {
                const formData = new FormData(form);
                const itemsData = [];
                const itemEntries = document.querySelectorAll('.item-entry[data-item-id]');

                if (itemEntries.length === 0) {
                    showAlert('يجب إضافة صنف واحد على الأقل', 'warning');
                    return;
                }

                let hasError = false;

                itemEntries.forEach((entry, index) => {
                    const itemId = entry.getAttribute('data-item-id');

                    const majorCategory = document.getElementById(`majorCategory_${itemId}`)?.value || '';
                    const minorCategory = document.getElementById(`minorCategory_${itemId}`)?.value || '';
                    const item = document.getElementById(`itemInput_${itemId}`)?.value || '';
                    const assetNum = form.querySelector(`input[name="asset_num_${itemId}"]`)?.value || '';
                    const serialNum = form.querySelector(`input[name="serial_num_${itemId}"]`)?.value || '';
                    const modelNum = form.querySelector(`input[name="model_num_${itemId}"]`)?.value || '';
                    const note = form.querySelector(`textarea[name="note_${itemId}"]`)?.value || '';

                    if (!majorCategory) {
                        showAlert(`يجب اختيار التصنيف الرئيسي للصنف رقم ${index + 1}`, 'warning');
                        hasError = true;
                        return;
                    }

                    if (!minorCategory) {
                        showAlert(`يجب اختيار التصنيف الفرعي للصنف رقم ${index + 1}`, 'warning');
                        hasError = true;
                        return;
                    }

                    if (!item.trim()) {
                        showAlert(`يجب اختيار الصنف للعنصر رقم ${index + 1}`, 'warning');
                        hasError = true;
                        return;
                    }

                    if (!assetNum.trim()) {
                        showAlert(`يجب إدخال رقم الأصول للعنصر رقم ${index + 1}`, 'warning');
                        hasError = true;
                        return;
                    }

                    if (!serialNum.trim()) {
                        showAlert(`يجب إدخال الرقم التسلسلي للعنصر رقم ${index + 1}`, 'warning');
                        hasError = true;
                        return;
                    }

                    itemsData.push({
                        major_category_id: majorCategory,
                        minor_category_id: minorCategory,
                        item: item.trim(),
                        brand: 'غير محدد',
                        asset_num: assetNum.trim(),
                        serial_num: serialNum.trim(),
                        model_num: modelNum.trim(),
                        note: note.trim()
                    });
                });

                if (hasError) {
                    return;
                }

                formData.append('multiple_items', JSON.stringify(itemsData));

                fetch('<?= base_url('InventoryController/storeMultipleItems') ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAlert(data.message, 'success');
                            window.location.reload();
                        } else {
                            showAlert('خطأ: ' + data.message, 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('خطأ في إرسال الطلب:', error);
                        showAlert('حدث خطأ في إرسال الطلب. تحقق من وحدة التحكم للمزيد من التفاصيل.', 'danger');
                    });
            }
        });

        // تهيئة جميع الوظائف عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            initItemSearch();
            initQuantityHandler();
            initEmployeeSearch();
            initLocationDropdowns();

            // تهيئة مستمعات البحث
            document.getElementById('generalSearch')?.addEventListener('input', performSearch);
            document.getElementById('searchId')?.addEventListener('input', performSearch);
            document.getElementById('searchCategory')?.addEventListener('change', performSearch);
            document.getElementById('searchSerial')?.addEventListener('input', performSearch);
            document.getElementById('searchEmployeeId')?.addEventListener('input', performSearch);
            document.getElementById('searchLocation')?.addEventListener('input', performSearch);

            // تهيئة اختيار الصفوف العادي (بدون checkbox)
            const rows = document.querySelectorAll('.custom-table tbody tr');
            rows.forEach(row => {
                row.addEventListener('click', function(e) {
                    // تجاهل النقر إذا كان على checkbox أو أزرار
                    if (e.target.closest('.checkbox-cell') || e.target.closest('.action-buttons')) {
                        return;
                    }

                    rows.forEach(r => r.classList.remove('selected'));
                    this.classList.add('selected');
                });
            });

            // إغلاق المودالات عند الضغط خارجها
            document.getElementById('orderModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeOrderForm();
                }
            });

            document.getElementById('deleteConfirmModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    cancelDeleteOrder();
                }
            });

            document.getElementById('bulkDeleteModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeBulkDeleteModal();
                }
            });

            // إغلاق المودالات بمفتاح Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const orderModal = document.getElementById('orderModal');
                    const deleteModal = document.getElementById('deleteConfirmModal');
                    const bulkDeleteModal = document.getElementById('bulkDeleteModal');

                    if (orderModal.style.display === 'flex') {
                        closeOrderForm();
                    }
                    if (deleteModal.style.display === 'flex') {
                        cancelDeleteOrder();
                    }
                    if (bulkDeleteModal.style.display === 'flex') {
                        closeBulkDeleteModal();
                    }
                }
            });

            // إغلاق قوائم البحث المنسدلة
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.search-dropdown')) {
                    document.querySelectorAll('.dropdown-list').forEach(dropdown => {
                        dropdown.style.display = 'none';
                    });
                }
            });


        });
    </script>

</body>

</html>