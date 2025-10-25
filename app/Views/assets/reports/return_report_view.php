<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مستند الإرجاع </title>
    <link rel="stylesheet" href="<?= base_url('public/assets/css/components/print_form_style.css') ?>">
    <style>
        @media screen {
            body { 
                font-family: 'Arial', sans-serif; 
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
        
        .field-value {
            display: inline-block;
            min-width: 200px;
            border-bottom: 1px solid #333;
            padding: 0 10px;
            font-weight: bold;
        }
        
        .signature-value {
            display: inline-block;
            min-width: 150px;
            font-weight: bold;
            border-bottom: 1px solid #333;
            padding: 2px 5px;
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
            <h3>لا توجد عناصر مرجعة</h3>
            <p>لم يتم العثور على أي عناصر مرجعة بناءً على الفلاتر المحددة</p>
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
                                    alt="KAMC Logo"
                                    style="max-width: 350px; height: auto;">
                            </div>
                            <div class="form-title">
                                <h1 style="font-size: 25px; margin-top:-5px">مستند الإرجاع </h1>
                            </div>
                        </div>
                        
                        <div class="header-fields">
                            <div class="header-field">
                                <span class="field-label">الجهة المرجعة:</span>
                                <span class="field-value">إدارة العهد</span>
                            </div>
                            <div class="header-field">
                                <span class="field-label">التاريخ:</span>
                                <span class="field-value"><?= esc($current_date) ?></span>
                            </div>
                            <div class="header-field">
                                <span class="field-label">عدد العناصر:</span>
                                <span class="field-value"><?= esc($total_count) ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <table class="main-table">
                    <thead>
                        <tr>
                            <th class="col-serial">#</th>
                            <th class="col-description">رقم الصنف</th>
                            <th class="col-description">اسم الصنف وتصنيفه</th>
                            <th class="col-model">نوع الصنف</th>
                            <th class="col-quantity-available">الوحدة</th>
                            <th class="col-quantity-requested">الكمية</th>
                            <th class="col-unit-price">انتهاء الغرض</th>
                            <th class="col-unit-price">فائض</th>
                            <th class="col-unit-price">عدم الصلاحية</th>
                            <th class="col-unit-price">تالف</th>
                            <th class="col-notes">ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $index => $item): ?>
                        <tr>
                            <td class="col-serial"><?= $index + 1 ?></td>
                            <td><?= esc($item['asset_num']) ?></td>
                            <td class="item-description-cell">
                                <div class="item-main-name"><?= esc($item['item_name']) ?></div>
                                <div class="item-sub-details"><?= esc($item['category']) ?></div>
                            </td>
                            <td><?= esc($item['asset_type']) ?></td>
                            <td>قطعة</td>
                            <td>1</td>
                            <td class="check-mark"><?= $item['reasons']['purpose_end'] ? '✓' : '' ?></td>
                            <td class="check-mark"><?= $item['reasons']['excess'] ? '✓' : '' ?></td>
                            <td class="check-mark"><?= $item['reasons']['unfit'] ? '✓' : '' ?></td>
                            <td class="check-mark"><?= $item['reasons']['damaged'] ? '✓' : '' ?></td>
                            <td class="notes-cell"><?= esc($item['notes']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="signature-section no-page-break">
                    <table class="signature-table">
                        <tr>
                            <td>
                                <div class="signature-title-cell">المسئول في الجهة المرجعة</div>
                                <div class="signature-fields">
                                    الاسم: <span class="signature-value"><?= esc($creator_name) ?></span><br>
                                    التاريخ: <span class="signature-value"><?= esc($current_date) ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="signature-title-cell">المستلم / أمين / مأمور المستودع</div>
                                <div class="signature-fields">
                                    الاسم: <span class="signature-line"></span><br>
                                    التاريخ: <span class="signature-line"></span>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="form-footer">
                    <div class="footer-warning">
                        تنبيه مهم: يرجى مراجعة جميع البنود بدقة والتأكد من صحة البيانات قبل التوقيع والاستلام
                    </div>
                    <div class="footer-note">
                        هذا المستند رسمي ويجب الاحتفاظ به للمراجعة والتدقيق
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</body>
</html>