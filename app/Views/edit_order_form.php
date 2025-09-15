<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تحديث الطلب</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body { background: #f9f9f9; }
        .section-header { margin: 20px 0 10px; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; }
        .table td, .table th { vertical-align: middle; }
        .note { resize: vertical; }
    </style>
</head>
<body>

<div class="container py-4">

    <h3 class="mb-4">تحديث الطلب رقم #<?= esc($order->id) ?></h3>

    <!-- بيانات المسلم -->
    <div class="section-header">
        <h5>بيانات المسلم (منشئ الطلب)</h5>
    </div>
    <div class="form-grid mb-3">
        <div><strong>الاسم:</strong> <?= esc($fromEmployee->name ?? '-') ?></div>
        <div><strong>القسم:</strong> <?= esc($fromEmployee->section ?? '-') ?></div>
    </div>

    <!-- بيانات المستلم -->
    <div class="section-header">
        <h5>بيانات المستلم</h5>
    </div>
    <form id="editOrderForm" action="<?= base_url('InventoryController/updateOrder/' . $order->id) ?>" method="post">
        <div class="form-grid mb-3">
            <div>
                <label for="to_employee_id" class="form-label">المستلم</label>
                <select class="form-select" name="to_employee_id" id="to_employee_id" required>
                    <option value="">-- اختر موظفاً --</option>
                    <?php foreach ($buildings as $building): ?>
                        <optgroup label="مبنى <?= esc($building->code) ?>">
                            <?php // هنا ممكن تجيب الموظفين حسب المبنى ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="notes" class="form-label">ملاحظات</label>
                <textarea name="notes" id="notes" class="form-control note"><?= esc($order->note ?? '') ?></textarea>
            </div>
        </div>

        <!-- جدول الأصناف -->
        <div class="section-header">
            <h5>الأصناف في الطلب</h5>
        </div>
        <table class="table table-bordered bg-white">
            <thead class="table-light">
                <tr>
                    <th>الصنف</th>
                    <th>التصنيف الرئيسي</th>
                    <th>التصنيف الفرعي</th>
                    <th>الموديل</th>
                    <th>رقم الأصل</th>
                    <th>الرقم التسلسلي</th>
                    <th>المبنى</th>
                    <th>الطابق</th>
                    <th>القسم</th>
                    <th>الغرفة</th>
                    <th>إجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderItems as $item): ?>
                    <tr>
                        <td><?= esc($item['item_name']) ?></td>
                        <td><?= esc($item['major_category_name']) ?></td>
                        <td><?= esc($item['minor_category_name']) ?></td>
                        <td><?= esc($item['model_num']) ?></td>
                        <td><?= esc($item['asset_num']) ?></td>
                        <td><?= esc($item['serial_num']) ?></td>
                        <td><?= esc($item['building_code']) ?></td>
                        <td><?= esc($item['floor_code']) ?></td>
                        <td><?= esc($item['section_id']) ?></td>
                        <td><?= esc($item['room_code']) ?></td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteItem(<?= $item['id'] ?>)">حذف</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- نوع التحديث -->
        <input type="hidden" name="update_type" id="update_type" value="single">

        <div class="mt-4">
            <button type="submit" class="btn btn-success">حفظ التعديلات</button>
            <a href="<?= base_url('InventoryController') ?>" class="btn btn-secondary">رجوع</a>
        </div>
    </form>
</div>

<script>
    function deleteItem(itemOrderId) {
        if (confirm("هل أنت متأكد من حذف هذا الصنف؟")) {
            fetch("<?= base_url('InventoryController/deleteOrderItem') ?>/" + itemOrderId, {
                method: "DELETE"
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.success) location.reload();
            })
            .catch(err => alert("خطأ أثناء الحذف"));
        }
    }
</script>

</body>
</html>
