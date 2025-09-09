<form method="post" action="<?= site_url('warehouse/store') ?>">
    <label>الصنف</label>
    <input type="text" name="item" value="<?= isset($item) ? esc($item->item) : '' ?>">

    <label>التصنيف</label>
    <select name="category_id">
        <option value="">اختر التصنيف</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?= $category->id ?>" <?= isset($item) && $item->category_id == $category->id ? 'selected' : '' ?>>
                <?= $category->category ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>الكمية</label>
    <input type="number" name="quantity" value="<?= isset($item) ? esc($item->quantity) : '' ?>">

    <label>رقم الموديل</label>
    <input type="text" name="model_num" value="<?= isset($item) ? esc($item->model_num) : '' ?>">

    <label>الحجم</label>
    <input type="text" name="size" value="<?= isset($item) ? esc($item->size) : '' ?>">

    <button type="submit">إضافة</button>
</form>