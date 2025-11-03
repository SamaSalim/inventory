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
    <link rel="stylesheet" href="<?= base_url('public/assets/css/transfer-style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/assets/css/my_assets_style.css') ?>">

    <!-- BASE URL CONFIGURATION -->
    <script>
        window.appConfig = {
            baseUrl: '<?= base_url() ?>'
        };
        console.log('Base URL configured:', window.appConfig.baseUrl);
    </script>
</head>

<body>
<?= $this->include('layouts/header') ?>

    <div id="alertContainer"></div>

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
                            <th>اسم الأصل</th>
                            <th>التصنيف</th>
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
                                <?php 
                                $classification = [];
                                if (!empty($order->major_category_name)) {
                                    $classification[] = $order->major_category_name;
                                }
                                if (!empty($order->minor_category_name)) {
                                    $classification[] = $order->minor_category_name;
                                }
                                $classificationText = implode(' / ', $classification) ?: '-';
                                
                                // Check if item is reissued from controller flag
                                $isReissued = isset($order->is_reissued) && $order->is_reissued === true;
                                
                                // Get order status for styling
                                $orderStatusName = $order->order_status_name ?? '';
                                ?>
                                <tr data-source="<?= esc($order->source_table ?? '') ?>" 
                                    data-reissued="<?= $isReissued ? 'true' : 'false' ?>"
                                    data-order-status="<?= esc($orderStatusName) ?>"
                                    data-item-order-id="<?= esc($order->item_order_id ?? $order->id) ?>"
                                    data-asset-num="<?= esc($order->asset_num ?? '-') ?>"
                                    data-item-name="<?= esc($order->item_name ?? '-') ?>"
                                    data-classification="<?= esc($classificationText) ?>"
                                    data-minor-category="<?= esc($order->minor_category_name ?? '') ?>"
                                    data-model="<?= esc($order->model ?? '') ?>"
                                    data-serial-num="<?= esc($order->serial_num ?? '') ?>"
                                    data-brand="<?= esc($order->brand ?? '') ?>"
                                    data-old-asset-num="<?= esc($order->old_asset_num ?? '') ?>"
                                    data-assets-type="<?= esc($order->assets_type ?? '') ?>">
                                    <td><?= esc($order->asset_num ?? '-') ?></td>
                                    <td><?= esc($order->item_name ?? '-') ?></td>
                                    <td><?= esc($classificationText) ?></td>
                                    <td>
                                        <?php 
                                        $sourceDisplay = ($order->source_table ?? '') === 'orders' ? 'مباشر' : 'محول';
                                        $sourceClass = ($order->source_table ?? '') === 'orders' ? 'source-direct' : 'source-transfer';
                                        ?>
                                        <span class="source-badge <?= $sourceClass ?>">
                                            <?= $sourceDisplay ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        $usageClass = 'status-new';
                                        $usageStatus = '';
                                        
                                        // If item is reissued, show "معاد صرفه" with green badge
                                        if ($isReissued) {
                                            $usageStatus = 'معاد صرفه';
                                            $usageClass = 'status-new'; // Green badge
                                        } else {
                                            // Normal status display
                                            $usageStatus = $order->usage_status_name ?? '';
                                            if ($usageStatus == 'جديد') $usageClass = 'status-new';
                                            if ($usageStatus == 'تحويل') $usageClass = 'status-transfer';
                                        }
                                        ?>
                                        <span class="status-badge <?= $usageClass ?>">
                                            <?= esc($usageStatus ?: '-') ?>
                                        </span>
                                    </td>
                            <td>
                                <?php 

                                $displayOrderStatus = $order->order_status_name ?? '';
                                
                                $orderClass = 'order-status-pending';
                                $inlineStyle = '';
                                
                                if ($displayOrderStatus == 'مقبول') {
                                    $orderClass = 'order-status-accepted';
                                }
                                if ($displayOrderStatus == 'مرفوض') {
                                    $orderClass = 'order-status-rejected';
                                    // Add inline style for rejected status
                                    $inlineStyle = 'background: rgba(220, 53, 69, 0.15); color: #3c3939ff; padding: 5px 12px; border-radius: 20px; font-size: 10px; font-weight: bold; text-transform: uppercase; display: inline-block;';
                                }
                                ?>
                                <span class="status-badge <?= $orderClass ?>" <?= !empty($inlineStyle) ? 'style="' . $inlineStyle . '"' : '' ?>>
                                    <?= esc($displayOrderStatus ?: '-') ?>
                                </span>
                            </td>
                                    <td><?= isset($order->created_at) ? date('d/m/Y', strtotime($order->created_at)) : '-' ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button onclick="openReturnPopup(<?= $order->item_order_id ?? $order->id ?>)" class="action-btn return-btn">
                                                    <svg class="btn-icon" viewBox="0 0 24 24">
                                                        <path d="M9 11H3v2h6v3l5-4-5-4v3zm12-8h-6c-1.1 0-2 .9-2 2v3h2V5h6v14h-6v-3h-2v3c0 1.1.9 2 2 2h6c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z" />
                                                    </svg>
                                                إرجاع
                                            </button>
                                            <button onclick="openTransferPopup(<?= $order->item_order_id ?? $order->id ?>)" class="action-btn transfer-btn">
                                                <i class="fas fa-exchange-alt"></i>
                                                تحويل
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
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

    <!-- Transfer Modal -->
    <div class="transfer-modal" id="transferModal">
        <div class="transfer-modal-content">
            <div class="transfer-modal-title">
                <i class="fas fa-exchange-alt"></i>
                تحويل العهدة
            </div>
            
            <div id="selectedTransferItem" style="max-height: 150px; overflow-y: auto; margin-bottom: 20px; padding: 12px; background: #f8f9fa; border-radius: 8px; border: 2px solid #e0e6ed;"></div>
            
            <div class="form-group">
                <label for="toUserInput">تحويل إلى:</label>
                <div class="search-select-container">
                    <input 
                        type="text" 
                        id="toUserInput" 
                        class="search-select-input"
                        placeholder="ابحث بالاسم أو القسم..."
                        autocomplete="off"
                        oninput="filterTransferUsers()"
                        onfocus="showTransferDropdown()"
                    >
                    <span class="search-icon">🔍</span>
                    <div id="toUserDropdown" class="search-dropdown"></div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="transferNote">ملاحظات التحويل (اختياري):</label>
                <textarea id="transferNote" placeholder="أضف ملاحظات حول سبب التحويل أو حالة العهدة..."></textarea>
            </div>
            
            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                <button onclick="closeTransferModal()" style="padding: 10px 25px; background: #6c757d; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">إلغاء</button>
                <button onclick="submitTransferSingle()" style="padding: 10px 25px; background: #3AC0C3; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">تأكيد التحويل</button>
            </div>
        </div>
    </div>

    <!-- Return Confirmation Modal -->
    <div class="delete-modal" id="deleteModal">
        <div class="delete-modal-content">
            <div class="delete-modal-title">
                <i class="fas fa-undo-alt"></i>
                تأكيد الترجيع
            </div>
            <div class="delete-modal-message" id="deleteMessage"></div>
            <div class="delete-modal-actions">
                <button class="confirm-btn confirm-cancel-btn" onclick="closeDeleteModal()">
                    إلغاء
                </button>
                <button class="confirm-btn confirm-delete-btn" onclick="confirmBulkReturnWithFiles()">
                    <i class="fas fa-undo"></i>
                    تأكيد الترجيع
                </button>
            </div>
        </div>
    </div>

    <!-- Load External Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <!-- Enhanced JavaScript with reissued filter -->
    <script>
        // Update existing filterBySource to work with all filters
        function filterBySource(source) {
            const rows = document.querySelectorAll('#covenantsTable tbody tr');
            const buttons = document.querySelectorAll('.filter-buttons .custom-btn');
            
            buttons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            rows.forEach(row => {
                const rowSource = row.getAttribute('data-source');
                if (source === 'all') {
                    row.style.display = '';
                } else {
                    row.style.display = rowSource === source ? '' : 'none';
                }
            });
        }
    </script>
    
    <!-- Load the separated JavaScript file -->
    <script src="<?= base_url('public/assets/JS/user_return.js') ?>"></script>
</body>
</html>