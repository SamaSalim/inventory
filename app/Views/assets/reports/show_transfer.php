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

        .header-field {
            display: flex;
            align-items: center;
            gap: 5px;
            justify-content: flex-end;
        }

        .field-label {
            white-space: nowrap;
        }

        .field-value-text {
            border-bottom: 1px solid #000;
            padding: 2px 5px;
            min-width: 200px;
            text-align: center;
        }

        .signature-fields {
            text-align: right;
        }

        .signature-line {
            display: inline-block;
            min-width: 150px;
            border-bottom: 1px solid #333;
            padding: 2px 5px;
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
                                    alt="KAMC Logo"
                                    style="max-width: 350px; height: auto;">
                            </div>
                            <div class="form-title">
                                <h1 style="font-size: 25px; margin-top:-5px">نموذج نقل عهدة</h1>
                            </div>
                        </div>
                        <div style="width:60px;"></div>
                    </div>

                    <!-- Header fields -->
                    <div class="header-fields">
                        <div class="header-field">
                            <span class="field-label">المحول من:</span>
                            <span class="field-value"><?= esc($from_user_name) ?></span>
                        </div>
                        <div class="header-field">
                            <span class="field-label">المحول إلى:</span>
                            <span class="field-value"><?= esc($to_user_name) ?></span>
                        </div>
                    </div>

                    <div class="header-fields">
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

                <!-- Table -->
                <table class="main-table">
                    <thead>
                        <tr>
                            <th class="col-serial">#</th>
                            <th class="col-description">رقم الأصل KAMC</th>
                            <th class="col-description">رقم الأصل MOH</th>
                            <th class="col-description">الرقم التسلسلي</th>
                            <th class="col-description">اسم الصنف وتصنيفه</th>
                            <th class="col-model">الموديل</th>
                            <th class="col-model">الماركة</th>
                            <th class="col-unit-price">نوع الصنف</th>
                            <th class="col-unit-price">الموقع</th>
                            <th class="col-unit-price">الحالة</th>
                            <th class="col-notes">ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $index => $item): ?>
                        <tr>
                            <td class="col-serial"><?= $index + 1 ?></td>
                            <td><?= esc($item['asset_num']) ?></td>
                            <td><?= esc($item['old_asset_num']) ?></td>
                            <td><?= esc($item['serial_num']) ?></td>
                            <td class="item-description-cell">
                                <div class="item-main-name"><?= esc($item['item_name']) ?></div>
                                <div class="item-sub-details"><?= esc($item['minor_category']) ?> / <?= esc($item['major_category']) ?></div>
                            </td>
                            <td><?= esc($item['model']) ?></td>
                            <td><?= esc($item['brand']) ?></td>
                            <td><?= esc($item['asset_type']) ?></td>
                            <td><?= esc($item['location_code']) ?></td>
                            <td><?= esc($item['usage_status']) ?></td>
                            <td class="notes-cell"><?= esc($item['notes']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Signature Section -->
                <div class="signature-section no-page-break">
                    <table class="signature-table" style="table-layout: fixed; width: 100%;">
                        <tr>
                            <td style="width: 20%; vertical-align: top; padding: 10px;">
                                <div style="font-weight: bold; text-align: center; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 8px;">المسلم</div>
                                <div style="line-height: 2;">
                                    الاسم: <span class="signature-line"><?= esc($from_user_name) ?></span><br>
                                    التوقيع: <span class="signature-line"></span><br>
                                    الرقم الوظيفي: <span class="signature-line"><?= esc($from_user_id) ?></span>
                                </div>
                            </td>
                            <td style="width: 20%; vertical-align: top; padding: 10px;">
                                <div style="font-weight: bold; text-align: center; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 8px;">المستلم</div>
                                <div style="line-height: 2;">
                                    الاسم: <span class="signature-line"><?= esc($to_user_name) ?></span><br>
                                    التوقيع: <span class="signature-line"></span><br>
                                    الرقم الوظيفي: <span class="signature-line"><?= esc($to_user_id) ?></span>
                                </div>
                            </td>
                            <td style="width: 20%; vertical-align: top; padding: 10px;">
                                <div style="font-weight: bold; text-align: center; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 8px;">رئيس الجهة المسلمة</div>
                                <div style="line-height: 2;">
                                    الاسم: <span class="signature-line"></span><br>
                                    التوقيع: <span class="signature-line"></span><br>
                                    الرقم الوظيفي: <span class="signature-line"></span>
                                </div>
                            </td>
                            <td style="width: 20%; vertical-align: top; padding: 10px;">
                                <div style="font-weight: bold; text-align: center; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 8px;">رئيس الجهة المستلمة</div>
                                <div style="line-height: 2;">
                                    الاسم: <span class="signature-line"></span><br>
                                    التوقيع: <span class="signature-line"></span><br>
                                    الرقم الوظيفي: <span class="signature-line"></span>
                                </div>
                            </td>
                            <td style="width: 20%; vertical-align: top; padding: 10px;">
                                <div style="font-weight: bold; text-align: center; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 8px;">إدارة مراقبة المخزون</div>
                                <div style="line-height: 2;">
                                    الاسم: <span class="signature-line"></span><br>
                                    التوقيع: <span class="signature-line"></span><br>
                                    الرقم الوظيفي: <span class="signature-line"></span>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Footer -->
                <div class="form-footer">
                    <div class="footer-note">
                        تمت معاينة (الصنف/الأصناف) الموضحة أعلاه من قبل المسلم والمستلم وبالتوقيع على هذا المحضر أخلى الطرف الأول مسؤوليته من تلك الصنف/الأصناف، وأصبح الطرف الثاني مسئولاً عنها وعلى هذا جرى التوقيع والمصادقة
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</body>
</html>