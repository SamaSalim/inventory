<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Bootstrap -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.rtl.min.css" rel="stylesheet">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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
        transition: width 0.3s ease;
        overflow: hidden;
    }

    .sidebar:hover {
        width: 200px;
    }

    .sidebar .logo {
        margin-bottom: 30px;
    }

    .sidebar-logo {
        width: 70px;
        height: 70px;
        object-fit: contain;
    }

    /* ====================================
       CSS الإشعارات - مُصحح وكامل
       ==================================== */

    /* جرس الإشعارات */
    .notification-bell {
        position: relative;
        width: 45px;
        height: 45px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }

    .notification-bell:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: scale(1.05);
    }

    .notification-bell:active {
        transform: scale(0.95);
    }

    .notification-bell svg {
        width: 24px;
        height: 24px;
        fill: white;
    }

    /* عداد الإشعارات */
    .notification-count {
        position: absolute;
        top: -6px;
        right: -6px;
        background: #ff4444;
        color: white;
        border-radius: 50%;
        min-width: 22px;
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        padding: 0 6px;
        box-shadow: 0 2px 8px rgba(255, 68, 68, 0.4);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
            box-shadow: 0 2px 8px rgba(255, 68, 68, 0.4);
        }

        50% {
            transform: scale(1.15);
            box-shadow: 0 2px 12px rgba(255, 68, 68, 0.6);
        }
    }

    /* الخلفية الداكنة */
    .notification-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 1099;
    }

    .notification-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    /* لوحة الإشعارات */
    .notification-panel {
        position: fixed;
        top: 0;
        left: 0;
        width: 420px;
        height: 100vh;
        background: white;
        box-shadow: 4px 0 20px rgba(0, 0, 0, 0.15);
        transform: translateX(-100%);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1100;
        display: flex;
        flex-direction: column;
    }

    .notification-panel.show {
        transform: translateX(0);
    }

    /* هيدر اللوحة */
    .notification-header {
        padding: 20px 24px;
        background: linear-gradient(135deg, #057590 0%, #045d75 100%);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .notification-header h3 {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
    }

    .notification-count-text {
        display: block;
        font-size: 12px;
        opacity: 0.9;
        margin-top: 4px;
    }

    .close-panel {
        background: rgba(255, 255, 255, 0.1);
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: all 0.2s;
    }

    .close-panel:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: rotate(90deg);
    }

    /* التبويبات */
    .notification-tabs {
        display: flex;
        gap: 8px;
        padding: 16px 24px;
        background: #f8f9fa;
        border-bottom: 1px solid #e0e0e0;
    }

    .tab-btn {
        flex: 1;
        padding: 10px 16px;
        border: 1px solid #ddd;
        background: white;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        color: #666;
    }

    .tab-btn:hover {
        border-color: #057590;
        color: #057590;
    }

    .tab-btn.active {
        background: #057590;
        color: white;
        border-color: #057590;
    }

    /* قائمة الإشعارات */
    .notification-list {
        flex: 1;
        overflow-y: auto;
        padding: 12px;
        background: #fafbfc;
    }

    .notification-list::-webkit-scrollbar {
        width: 6px;
    }

    .notification-list::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .notification-list::-webkit-scrollbar-thumb {
        background: #057590;
        border-radius: 3px;
    }

    /* عنصر الإشعار */
    .notification-item {
        display: flex;
        gap: 14px;
        padding: 16px;
        border-radius: 10px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid #e5e7eb;
        background: white;
        position: relative;
    }

    .notification-item:hover {
        background: #f8f9fa;
        transform: translateX(-4px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .notification-item.unread {
        background: #e3f2fd;
        border-color: #057590;
        box-shadow: 0 0 0 1px rgba(5, 117, 144, 0.1);
    }

    .notification-item.unread::before {
        content: '';
        position: absolute;
        right: 8px;
        top: 8px;
        width: 8px;
        height: 8px;
        background: #057590;
        border-radius: 50%;
    }

    /* أيقونة الإشعار */
    .notification-icon {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 18px;
    }

    .notification-icon.transfer {
        background: #e3f2fd;
        color: #1976d2;
    }

    .notification-icon.return {
        background: #fff3e0;
        color: #f57c00;
    }

    .notification-icon.order {
        background: #e8f5e9;
        color: #388e3c;
    }

    .notification-icon.order_status {
        background: #f3e5f5;
        color: #7b1fa2;
    }

    .notification-icon.new_order {
        background: #fce4ec;
        color: #c2185b;
    }

    .notification-icon.admin_transfer {
        background: linear-gradient(135deg, #ff9800, #f57c00);
        color: white;
    }

    .notification-icon.admin_return {
        background: linear-gradient(135deg, #4caf50, #388e3c);
        color: white;
    }

    /* محتوى الإشعار */
    .notification-content {
        flex: 1;
        min-width: 0;
    }

    .notification-title {
        font-weight: 600;
        font-size: 14px;
        color: #1f2937;
        margin-bottom: 4px;
        line-height: 1.4;
    }

    .notification-message {
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 6px;
        line-height: 1.5;
    }

    .notification-time {
        font-size: 11px;
        color: #9ca3af;
        font-weight: 500;
    }

    /* تمييز إشعارات مدير العهد */
    .notification-item.admin_transfer {
        border-right: 4px solid #ff9800;
        background: #fff8e1;
    }

    .notification-item.admin_return {
        border-right: 4px solid #4caf50;
        background: #e8f5e9;
    }

    /* حالة فارغة */
    .notification-empty,
    .no-notifications {
        text-align: center;
        padding: 80px 20px;
        color: #9ca3af;
    }

    .notification-empty svg,
    .no-notifications svg {
        width: 80px;
        height: 80px;
        fill: #d1d5db;
        margin-bottom: 16px;
        opacity: 0.6;
    }

    .notification-empty p,
    .no-notifications p {
        font-size: 15px;
        color: #6b7280;
        margin: 0;
    }

    /* فوتر اللوحة */
    .notification-footer {
        padding: 16px 24px;
        background: #f8f9fa;
        border-top: 1px solid #e0e0e0;
        display: flex;
        gap: 10px;
    }

    .mark-all-read-btn,
    .clear-all-btn {
        flex: 1;
        padding: 10px 16px;
        border: 1px solid #ddd;
        background: white;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        color: #374151;
    }

    .mark-all-read-btn:hover {
        background: #057590;
        color: white;
        border-color: #057590;
    }

    .clear-all-btn:hover {
        background: #ef4444;
        color: white;
        border-color: #ef4444;
    }

    /* ====================================
       نهاية CSS الإشعارات
       ==================================== */

    .sidebar a {
        color: white;
        text-decoration: none;
        font-size: 14px;
        padding: 10px 15px;
        margin-bottom: 10px;
        border-radius: 8px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        white-space: nowrap;
        overflow: hidden;
    }

    .sidebar a i {
        font-size: 18px;
        transition: opacity 0.3s ease;
    }

    .sidebar span {
        display: none;
        font-size: 14px;
        text-align: center;
        width: 100%;
    }

    .sidebar:hover a i {
        display: none;
    }

    .sidebar:hover a span {
        display: block;
    }

    .sidebar a:hover {
        background-color: rgba(255, 255, 255, 0.2);
    }

    .sidebar a.active {
        background-color: white;
        color: #057590;
        font-weight: bold;
    }

    /* المحتوى الرئيسي */
    .main-content {
        margin-right: 80px;
        transition: margin-right 0.3s ease;
    }

    .sidebar:hover~.main-content {
        margin-right: 200px;
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

    .content-area {
        padding: 25px;
        background-color: #EFF8FA;
        min-height: calc(100vh - 70px);
    }
</style>

<!-- الشريط الجانبي -->
<div class="sidebar">
    <div class="logo">
        <img src="<?= base_url('public/assets/images/kamc1.png') ?>" alt="Logo" class="sidebar-logo">
    </div>

    <?php $role = service('session')->get('role'); ?>

    <?php if (session()->has('user_id') || $role == 'super_assets'): ?>
        <!-- جرس الإشعارات - فقط لمستخدمي العهد ومدير العهد -->
        <div class="notification-bell" id="notificationBell">
            <svg viewBox="0 0 24 24">
                <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z" />
            </svg>
            <span class="notification-count" id="notificationCount" style="display: none;">0</span>
        </div>
    <?php endif; ?>


    <?php if ($role === 'admin' || $role === 'warehouse' ): ?>
        <a href="<?= base_url('inventoryController/index') ?>"
            class="<?= (service('uri')->getSegment(1) == 'inventoryController') ? 'active' : '' ?>">
            <i class="fa-solid fa-warehouse"></i> <span>إدارة المستودعات</span>
        </a>
    <?php endif; ?>

    <?php if ($role === 'super_warehouse'): ?>
    <a href="<?= base_url('Return/SuperWarehouse/ReturnRequests') ?>"
        class="<?= (service('uri')->getSegment(1) == 'Return/SuperWarehouse/ReturnRequests') ? 'active' : '' ?>">
        <i class="fa-solid fa-rotate-left"></i> <span>عمليات الإرجاع </span>
    </a>
<?php endif; ?>

<?php if ($role === 'super_warehouse'): ?>
    <?php 
    $currentUri = service('uri');
    $isReissueActive = ($currentUri->getSegment(1) == 'Return' && 
                        $currentUri->getSegment(2) == 'SuperWarehouse' && 
                        $currentUri->getSegment(3) == 'ReissueItems' );
    ?>
    <a href="<?= base_url('Return/SuperWarehouse/ReissueItems') ?>"
        class="<?= $isReissueActive ? 'active' : '' ?>">
        <i class="fa-solid fa-box-archive"></i> <span>اعادة صرف العهد</span>
    </a>
    <?php endif; ?>

    <?php if ($role === 'IT_specialist'): ?>
    <a href="<?= base_url('Return/IT/ReturnRequests') ?>"
        class="<?= (service('uri')->getSegment(1) == 'Return/SuperWarehouse/ReturnRequests') ? 'active' : '' ?>">
        <i class="fa-solid fa-rotate-left"></i> <span>عمليات الإرجاع </span>
    </a>
<?php endif; ?>

    <?php if ($role === 'admin' || $role === 'assets' || $role === 'super_assets'): ?>
        <a href="<?= base_url('AssetsController/index') ?>"
            class="<?= (service('uri')->getSegment(1) == 'AssetsController') ? 'active' : '' ?>">
            <i class="fa-solid fa-boxes-stacked"></i> <span>إدارة العهد</span>
        </a>
    <?php endif; ?>

    <?php if ($role === 'super_assets'): ?>
        <a href="<?= base_url('AssetsHistory/superAssets') ?>"
            class="<?= (service('uri')->getSegment(1) == 'AssetsHistory' && service('uri')->getSegment(2) == 'superAssets') ? 'active' : '' ?>">
            <i class="fa-solid fa-diagram-project"></i> <span>تتبع العهد</span>
        </a>
    <?php endif; ?>

    <?php if ($role === 'admin' || $role === 'assets' || $role === 'user' || $role === 'super_warehouse'  || $role === 'super_assets' || $role === 'warehouse' || $role === 'IT_specialist'): ?>
        <a href="<?= base_url('UserController/userView2') ?>"
            class="<?= (service('uri')->getSegment(1) == 'UserController' && service('uri')->getSegment(2) == 'userView2') ? 'active' : '' ?>">
            <i class="fa-solid fa-file-circle-plus"></i> <span> العهد الخاصة بي</span>
        </a>

        <a href="<?= base_url('UserController/dashboard') ?>"
            class="<?= (service('uri')->getSegment(1) == 'UserController' && service('uri')->getSegment(2) == 'dashboard') ? 'active' : '' ?>">
            <i class="fa-solid fa-id-card"></i> <span> طلبات العهد</span>
        </a>
    <?php endif; ?>
    <?php if ($role === 'assets' || $role === 'user' || $role === 'super_assets' || $role === 'super_warehouse'  || $role === 'IT_specialist'): ?>
        <a href="<?= base_url('AssetsHistory/assetsHistory') ?>"
            class="<?= (service('uri')->getSegment(1) == 'AssetsHistory' && service('uri')->getSegment(2) == 'assetsHistory') ? 'active' : '' ?>">
            <i class="fa-solid fa-file-lines"></i> <span>سجلات العهد</span>
        </a>
    <?php endif; ?>

    <?php if ($role === 'admin'): ?>
        <a href="<?= base_url('AdminController/dashboard') ?>"
            class="<?= (service('uri')->getSegment(1) == 'AdminController') ? 'active' : '' ?>">
            <i class="fa-solid fa-user-shield"></i> <span>الصلاحيات</span>
        </a>
    <?php endif; ?>



    <a href="<?= base_url('login/logout') ?>">
        <i class="fa-solid fa-right-from-bracket"></i> <span>تسجيل الخروج</span>
    </a>
</div>

<!-- لوحة الإشعارات - فقط لمستخدمي العهد ومدير العهد -->
<?php if (session()->has('user_id') || $role == 'super_assets'): ?>
    <div class="notification-panel" id="notificationPanel">
        <div class="notification-header">
            <div>
                <h3>الإشعارات</h3>
                <span class="notification-count-text" id="unreadCountText">0 غير مقروء</span>
            </div>
            <button class="close-panel" id="closeNotificationPanel">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
                </svg>
            </button>
        </div>

        <div class="notification-tabs">
            <button class="tab-btn active" data-tab="all">الكل</button>
            <button class="tab-btn" data-tab="unread">غير المقروءة</button>
        </div>

        <div class="notification-list" id="notificationList">
            <div class="no-notifications">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="#ccc">
                    <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z" />
                </svg>
                <p>لا توجد إشعارات</p>
            </div>
        </div>

        <div class="notification-footer">
            <button class="mark-all-read-btn" id="markAllRead">تعليم الكل كمقروء</button>
            <button class="clear-all-btn" id="clearAll">مسح الكل</button>
        </div>
    </div>

    <!-- Overlay -->
    <div class="notification-overlay" id="notificationOverlay"></div>
<?php endif; ?>

<script>
    // نظام الإشعارات - فقط لمستخدمي العهد ومدير العهد
    <?php if (session()->has('user_id') || $role == 'super_assets'): ?>

        // العناصر
        const notificationBell = document.querySelector('.notification-bell');
        const notificationPanel = document.getElementById('notificationPanel');
        const notificationOverlay = document.getElementById('notificationOverlay');
        const closePanel = document.getElementById('closeNotificationPanel');
        const notificationList = document.getElementById('notificationList');
        const notificationCount = document.getElementById('notificationCount');
        const unreadCountText = document.getElementById('unreadCountText');
        const markAllReadBtn = document.getElementById('markAllRead');
        const clearAllBtn = document.getElementById('clearAll');
        const tabBtns = document.querySelectorAll('.tab-btn');

        // دالة فتح/إغلاق لوحة الإشعارات
        function toggleNotifications() {
            if (notificationPanel) {
                const isShowing = notificationPanel.classList.contains('show');

                if (isShowing) {
                    notificationPanel.classList.remove('show');
                    if (notificationOverlay) notificationOverlay.classList.remove('show');
                } else {
                    notificationPanel.classList.add('show');
                    if (notificationOverlay) notificationOverlay.classList.add('show');
                    loadNotifications();
                }
            }
        }

        // فتح/إغلاق لوحة الإشعارات عند الضغط على الجرس
        if (notificationBell) {
            notificationBell.addEventListener('click', toggleNotifications);
        }

        // إغلاق اللوحة عند الضغط على زر الإغلاق
        if (closePanel) {
            closePanel.addEventListener('click', function() {
                notificationPanel.classList.remove('show');
                if (notificationOverlay) notificationOverlay.classList.remove('show');
            });
        }

        // إغلاق اللوحة عند الضغط على الخلفية
        if (notificationOverlay) {
            notificationOverlay.addEventListener('click', function() {
                notificationPanel.classList.remove('show');
                notificationOverlay.classList.remove('show');
            });
        }

        // التبويبات
        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                tabBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const tab = this.getAttribute('data-tab');
                loadNotifications(tab === 'unread');
            });
        });

        // تحميل الإشعارات
        function loadNotifications(unreadOnly = false) {
            const url = '<?= base_url('notifications/get') ?>' + (unreadOnly ? '?unread=true' : '');

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayNotifications(data.notifications);
                    }
                })
                .catch(error => console.error('خطأ في تحميل الإشعارات:', error));
        }

        // عرض الإشعارات
        function displayNotifications(notifications) {
            if (!notificationList) return;

            if (!notifications || notifications.length === 0) {
                notificationList.innerHTML = `
            <div class="notification-empty">
                <svg viewBox="0 0 24 24">
                    <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                </svg>
                <p>لا توجد إشعارات</p>
            </div>
        `;
                return;
            }

            let html = '';
            notifications.forEach(notification => {
                const unreadClass = notification.is_read ? '' : 'unread';
                const iconClass = getIconClass(notification.type);
                const icon = getIcon(notification.type);

                html += `
            <div class="notification-item ${unreadClass} ${notification.type}" data-id="${notification.id}" onclick="markAsRead('${notification.id}')">
                <div class="notification-icon ${iconClass}">
                    <i class="${icon}"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-title">${notification.title}</div>
                    <div class="notification-message">${notification.message}</div>
                    <div class="notification-time">${notification.time_ago || 'الآن'}</div>
                </div>
            </div>
        `;
            });

            notificationList.innerHTML = html;
        }

        // الحصول على أيقونة حسب النوع
        function getIcon(type) {
            const icons = {
                'transfer': 'fa-solid fa-exchange-alt',
                'return': 'fa-solid fa-undo',
                'order': 'fa-solid fa-shopping-cart',
                'order_status': 'fa-solid fa-info-circle',
                'new_order': 'fa-solid fa-bell',
                'admin_transfer': 'fa-solid fa-people-arrows',
                'admin_return': 'fa-solid fa-box-open'
            };
            return icons[type] || 'fa-solid fa-bell';
        }

        // الحصول على class الأيقونة
        function getIconClass(type) {
            return type || 'order';
        }

        // تحديث عداد الإشعارات
        function updateNotificationCount() {
            fetch('<?= base_url('notifications/get') ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const unreadCount = data.notifications.filter(n => !n.is_read).length;

                        // تحديث العداد في الجرس
                        if (notificationCount) {
                            if (unreadCount > 0) {
                                notificationCount.textContent = unreadCount > 99 ? '99+' : unreadCount;
                                notificationCount.style.display = 'flex';
                            } else {
                                notificationCount.style.display = 'none';
                            }
                        }

                        // تحديث النص في الهيدر
                        if (unreadCountText) {
                            unreadCountText.textContent = unreadCount + ' غير مقروء';
                        }
                    }
                })
                .catch(error => console.error('خطأ في تحديث العداد:', error));
        }

        // تعليم إشعار كمقروء
        function markAsRead(notificationId) {
            // هنا يمكن إضافة request للسيرفر لتحديث حالة القراءة
            updateNotificationCount();

            // تحديث الإشعار الحالي
            const activeTab = document.querySelector('.tab-btn.active');
            const isUnreadTab = activeTab && activeTab.getAttribute('data-tab') === 'unread';
            loadNotifications(isUnreadTab);
        }

        // تعليم الكل كمقروء
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', function() {
                fetch('<?= base_url('notifications/mark-all-read') ?>', {
                        method: 'POST'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            updateNotificationCount();
                            loadNotifications();
                        }
                    })
                    .catch(error => console.error('خطأ:', error));
            });
        }

        // مسح الكل
        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', function() {
                if (confirm('هل أنت متأكد من مسح جميع الإشعارات؟')) {
                    fetch('<?= base_url('notifications/clear') ?>', {
                            method: 'POST'
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                updateNotificationCount();
                                loadNotifications();
                            }
                        })
                        .catch(error => console.error('خطأ:', error));
                }
            });
        }

        // تحديث العداد عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            updateNotificationCount();
            // فحص الإشعارات كل دقيقة
            setInterval(updateNotificationCount, 60000);
        });

    <?php endif; ?>
</script>