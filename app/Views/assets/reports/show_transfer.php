<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نموذج نقل عهدة</title>
    <link rel="stylesheet" href="<?= base_url('public/assets/css/components/print_form_style.css') ?>">
    <style>
        @media screen {
            body { 
                font-family: 'Cairo', 'Arial', sans-serif; 
                direction: rtl; 
                padding: 20px;
                background: #f5f5f5;
            }
            .print-container { 
                max-width: 210mm; 
                margin: 0 auto; 
                background: white;
                padding: 20px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
        }
        
        @media print {
            body { 
                margin: 0; 
                padding: 0; 
                background: white;
            }
            .print-container { 
                box-shadow: none;
                max-width: 100%;
                padding: 10mm;
            }
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            color: #ddd;
        }

        .empty-state h3 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #333;
        }
    </style>
</head>
<body>
    <?php if (empty($items)): ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h3>لا توجد عناصر</h3>
            <p>لم يتم العثور على أي عناصر تحويل بناءً على الفلاتر المحددة</p>
        </div>
    <?php else: ?>
        <div class="print-container">
            <div class="print-only">
                <!-- Header Section -->
                <div class="print-header">
                    <div class="ministry-details">
                        <div class="logo-section">
                            <div class="kamc-emblem">
                                <img src="<?= base_url('public/assets/images/Kamc Logo Guideline-04.png') ?>" 
                                    alt="KAMC Logo">
                            </div>
                            <div class="form-title">نموذج نقل عهدة</div>
                        </div>
                        <div style="width:60px;"></div>
                    </div>
                    
                    <!-- Header fields -->
                    <div class="header-fields">
                        <div class="header-field">
                            <span class="field-label">المحول من:</span>
                            <div class="field-line" style="border-top: none; border-bottom: 1px solid #000;"><?= esc($from_user_name) ?></div>
                        </div>
                        <div class="header-field">
                            <span class="field-label">التاريخ:</span>
                            <div class="field-line" style="border-top: none; border-bottom: 1px solid #000;"><?= esc($current_date) ?></div>
                        </div>
                    </div>
                    
                    <div class="header-fields">
                        <div class="header-field" style="border-left:none;">
                            <span class="field-label">المحول إلى:</span>
                            <div class="field-line" style="border-top: none; border-bottom: 1px solid #000;"><?= esc($to_user_name) ?></div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <table class="main-table">
                    <thead>
                        <tr>
                            <th>م</th>
                            <th colspan="2">رقم الصنف</th>
                            <th rowspan="2">اسم الصنف</th>
                            <th rowspan="2">الوحدة</th>
                            <th rowspan="2">الكمية</th>
                            <th colspan="2">حالة الصنف</th>
                            <th rowspan="2">ملاحظات</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th>KAMC</th>
                            <th>MOH</th>
                            <th>جديد</th>
                            <th>مستعمل</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($items)): ?>
                            <?php $counter = 1; ?>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?= $counter++ ?></td>
                                    <td><?= esc($item['asset_num']) ?: '' ?></td>
                                    <td><?= esc($item['old_asset_num']) ?: '' ?></td>
                                    <td>
                                        <?= esc($item['item_name']) ?>
                                        <?php if (!empty($item['model'])): ?>
                                            <br><small>موديل: <?= esc($item['model']) ?></small>
                                        <?php endif; ?>
                                        <?php if (!empty($item['brand'])): ?>
                                            <br><small>ماركة: <?= esc($item['brand']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td style="text-align: center;">
                                        <?php if (strtolower($item['usage_status']) == 'جديد'): ?>
                                            ✓
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <?php if (strtolower($item['usage_status']) == 'مستعمل' || strtolower($item['usage_status']) == 'معاد الصرف'): ?>
                                            ✓
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($item['notes']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?php for ($i=1; $i<=10; $i++): ?>
                                <tr class="empty-row">
                                    <td><?= $i ?></td>
                                    <td></td><td></td><td></td>
                                    <td></td><td></td><td></td>
                                    <td></td><td></td>
                                </tr>
                            <?php endfor; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Signature Section -->
                <div class="signature-section">
                    <table class="signature-table" style="table-layout: fixed; width: 100%;">
                        <tr>
                            <td style="width: 50%; vertical-align: top; padding: 10px;">
                                <div style="font-weight: bold; text-align: center; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 8px;">المسلم</div>
                                <div style="line-height: 2;">
                                    الاسم: <?= esc($from_user_name) ?><br>
                                    التوقيع: ..................................<br>
                                    الرقم الوظيفي: ..................................
                                </div>
                            </td>
                            <td style="width: 50%; vertical-align: top; padding: 10px;">
                                <div style="font-weight: bold; text-align: center; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 8px;">المستلم</div>
                                <div style="line-height: 2;">
                                    الاسم: <?= esc($to_user_name) ?><br>
                                    التوقيع: ..................................<br>
                                    الرقم الوظيفي: ..................................
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Footer -->
                <div class="form-footer">
                    <div class="footer-note">
                       تمت معاينة (الصنف/الأصناف ) الموضحة أعلاه من قبل المسلم و المستلم و بالتوقيع على هذا المحضر أخلى الطرف الأول مسؤوليته من تلك الصنف / الأصناف ، و أصبح الطرف الثاني مسئولا عنها و على هذا جرى التوقيع و المصادقة
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</body>
</html>