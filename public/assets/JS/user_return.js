// ==================== GLOBAL VARIABLES ====================
let selectedItems = [];
let uploadedFiles = {};
let itemReasons = {};
let selectedToUser = null;
let currentTransferItemId = null;

// ==================== RETURN POPUP FUNCTIONS ====================

function openReturnPopup(itemOrderId) {
  const row = document.querySelector(`tr[data-item-order-id="${itemOrderId}"]`);
  if (!row) {
    showAlert("error", "لم يتم العثور على العنصر");
    return;
  }

  // Get ALL data from row's data attributes
  const assetNum = row.dataset.assetNum || "-";
  const itemName = row.dataset.itemName || "-";
  const classification = row.dataset.classification || "-";
  const minorCategory = row.dataset.minorCategory || "";
  const source = row.dataset.source || "";
  const model = row.dataset.model || "غير محدد";
  const serialNum = row.dataset.serialNum || "غير محدد";
  const brand = row.dataset.brand || "غير محدد";
  const oldAssetNum = row.dataset.oldAssetNum || "غير محدد";
  const assetsType = row.dataset.assetsType || "غير محدد";

  // Clear previous selections
  selectedItems = [];
  uploadedFiles = {};
  itemReasons = {};

  // Add the single item with ALL required fields
  selectedItems = [
    {
      id: itemOrderId,
      name: itemName,
      assetNum: assetNum,
      category: classification,
      minorCategory: minorCategory,
      source: source === "orders" ? "مباشر" : "محول",
      model: model,
      serialNum: serialNum,
      brand: brand,
      oldAssetNum: oldAssetNum,
      assetType: assetsType,
    },
  ];

  console.log("Selected item for return:", selectedItems[0]);

  showSelectedItemsPopup();
}

function showSelectedItemsPopup() {
  if (selectedItems.length === 0) {
    showAlert("warning", "لم يتم تحديد أي عناصر للترجيع");
    return;
  }

  const existingPopup = document.getElementById("selectedItemsPopup");
  if (existingPopup) existingPopup.remove();

  const popup = document.createElement("div");
  popup.id = "selectedItemsPopup";
  popup.className = "selected-items-popup show";
  popup.style.cssText =
    "position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); max-width: 800px; width: 90%; max-height: 85vh; overflow: hidden; z-index: 1001; animation: popupSlideIn 0.3s ease; display: flex; flex-direction: column;";

  let itemsHTML = "";
  selectedItems.forEach((item, index) => {
    const categoryBadgeColor =
      item.minorCategory === "IT" ? "#3a61c3ff" : "#ff6b6b";
    const categoryBadgeText = item.minorCategory || "غير محدد";

    itemsHTML += `
            <div class="popup-item" style="padding: 15px; border-bottom: 1px solid #e0e6ed; transition: background 0.2s;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                            <div style="font-weight: bold; color: #057590; font-size: 16px;">
                                ${index + 1}. ${item.name}
                            </div>
                            <span style="background: ${categoryBadgeColor}; color: white; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600;">${categoryBadgeText}</span>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; font-size: 14px; color: #555;">
                            <div><span style="color: #888;">التصنيف:</span> ${
                              item.category
                            }</div>
                            <div><span style="color: #888;">رقم الأصل:</span> ${
                              item.assetNum
                            }</div>
                            <div><span style="color: #888;">نوع المصدر:</span> ${
                              item.source
                            }</div>
                            <div><span style="color: #888;">الموديل:</span> ${
                              item.model
                            }</div>
                        </div>
                    </div>
                </div>
                
                ${getFileUploadSection(item)}
                
                <div style="margin-top: 15px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #057590; margin-bottom: 6px;">
                        <i class="fas fa-comment-dots" style="margin-left: 5px;"></i>
                        ملاحظات الترجيع:
                    </label>
                    <textarea 
                        id="comment_${item.assetNum}"
                        placeholder="أضف ملاحظة حول حالة الصنف أو سبب الترجيع..."
                        style="width: 100%; min-height: 70px; padding: 10px; border: 2px solid #e8f4f8; border-radius: 8px; font-size: 14px; font-family: inherit; resize: vertical; transition: border-color 0.2s; box-sizing: border-box; background: linear-gradient(135deg, #ffffff, #f8fdff);"
                        onfocus="this.style.borderColor='#3ac0c3'"
                        onblur="this.style.borderColor='#e8f4f8'"
                    ></textarea>
                </div>
            </div>
        `;
  });

  const hasITItems = selectedItems.some((item) => item.minorCategory === "IT");

  const printButtonSection = hasITItems
    ? `
        <div style="padding: 15px 20px; background: #fffbf0; border-top: 2px solid #f4d03f; border-bottom: 1px solid #e0e6ed;">
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 15px;">
                <div style="flex: 1;">
                    <div style="font-size: 13px; color: #7d6608; line-height: 1.5;">
                        <i class="fas fa-info-circle" style="margin-left: 5px; color: #f39c12;"></i>
                        <strong>معاينة التقرير المُنشأ تلقائياً قبل إرساله لموظفي مستودع الإرجاع</strong>
                    </div>
                </div>
                <button onclick="printAllForms()" style="padding: 10px 20px; background: linear-gradient(135deg, #5f97d6, #3a7bc8); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s; box-shadow: 0 3px 8px rgba(58, 123, 200, 0.3); white-space: nowrap; flex-shrink: 0;">
                    <i class="fas fa-eye" style="margin-left: 5px;"></i>
                    عرض التقرير
                </button>
            </div>
        </div>
    `
    : "";

  popup.innerHTML = `
        <div style="padding: 20px; background: linear-gradient(135deg, #057590, #3ac0c3); color: white; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 20px;">
                <i class="fas fa-undo-alt" style="margin-left: 8px;"></i>
                العناصر المحددة للترجيع (${selectedItems.length})
            </h3>
            <button onclick="closeSelectedItemsPopup()" style="background: rgba(255,255,255,0.2); color: white; border: none; border-radius: 50%; width: 35px; height: 35px; cursor: pointer; font-size: 20px; display: flex; align-items: center; justify-content: center; transition: background 0.2s;">✕</button>
        </div>
        
        <div style="flex: 1; overflow-y: auto; min-height: 0;">
            ${itemsHTML}
        </div>
        
        ${printButtonSection}
        
        <div style="padding: 15px 20px; background: #f8f9fa; border-top: 2px solid #e0e6ed; display: flex; justify-content: space-between; gap: 10px; flex-shrink: 0;">
            <button onclick="handleCancelSelection()" style="flex: 1; padding: 12px 20px; background: #95a5a6; color: white; border: none; border-radius: 20px; cursor: pointer; font-weight: bold; font-size: 14px; transition: all 0.2s;">
                <i class="fas fa-times" style="margin-left: 5px;"></i>
                إلغاء التحديد
            </button>
            <button onclick="submitReturn()" style="flex: 1; padding: 12px 20px; background: linear-gradient(135deg, #3ac0c3, #2aa8ab); color: white; border: none; border-radius: 20px; cursor: pointer; font-weight: bold; font-size: 14px; transition: all 0.2s; box-shadow: 0 2px 8px rgba(58, 192, 195, 0.3);">
                <i class="fas fa-undo" style="margin-left: 5px;"></i>
                تأكيد الترجيع
            </button>
        </div>
    `;

  const backdrop = document.createElement("div");
  backdrop.id = "popupBackdrop";
  backdrop.style.cssText =
    "position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;";
  backdrop.onclick = closeSelectedItemsPopup;

  document.body.appendChild(backdrop);
  document.body.appendChild(popup);
}

function getFileUploadSection(item) {
  const isIT = item.minorCategory === "IT";

  if (isIT) {
    return `
            <div style="margin-top: 15px; margin-bottom: 15px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #057590; margin-bottom: 8px;">
                    <i class="fas fa-file-alt" style="margin-left: 5px;"></i>
                    نموذج الترجيع (سيتم إنشاؤه تلقائياً):
                </label>
                <div style="border: 2px dashed #e0e6ed; border-radius: 8px; padding: 15px; background: #f8fdff;">
                    <div style="margin-bottom: 15px;">
                        <strong style="color: #057590; font-size: 14px; display: block; margin-bottom: 10px;">
                            <i class="fas fa-clipboard-list" style="margin-left: 5px;"></i>
                            حدد سبب الإرجاع:
                        </strong>
                        <div class="action-checkbox-group">
                            <label class="action-checkbox-item" onclick="toggleReason('${item.assetNum}', 'purpose_end', event)">
                                <input type="radio" name="reason_${item.assetNum}" id="reason_purpose_end_${item.assetNum}">
                                <div class="label">انتهاء الغرض</div>
                            </label>
                            <label class="action-checkbox-item" onclick="toggleReason('${item.assetNum}', 'excess', event)">
                                <input type="radio" name="reason_${item.assetNum}" id="reason_excess_${item.assetNum}">
                                <div class="label">فائض</div>
                            </label>
                            <label class="action-checkbox-item" onclick="toggleReason('${item.assetNum}', 'unfit', event)">
                                <input type="radio" name="reason_${item.assetNum}" id="reason_unfit_${item.assetNum}">
                                <div class="label">عدم الصلاحية</div>
                            </label>
                            <label class="action-checkbox-item" onclick="toggleReason('${item.assetNum}', 'damaged', event)">
                                <input type="radio" name="reason_${item.assetNum}" id="reason_damaged_${item.assetNum}">
                                <div class="label">تالف</div>
                            </label>
                        </div>
                    </div>
                    <div class="form-preview-container">
                        <div class="form-preview-title">
                            <i class="fas fa-check-circle"></i>
                            السبب المحدد:
                        </div>
                        <div class="form-preview-content" id="formPreview_${item.assetNum}">
                            <div style="color: #999; font-style: italic;">لم يتم تحديد سبب الإرجاع</div>
                        </div>
                    </div>
                    <div style="font-size: 11px; color: #3ac0c3; text-align: center; margin-top: 12px;">
                        <i class="fas fa-magic"></i>
                        سيتم إنشاء النموذج تلقائياً بجميع بيانات الصنف عند الترجيع
                    </div>
                </div>
            </div>
        `;
  } else {
    return `
            <div style="margin-top: 15px; margin-bottom: 15px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #057590; margin-bottom: 8px;">
                    <i class="fas fa-image" style="margin-left: 5px;"></i>
                    الصور المرفقة:
                </label>
                <div style="border: 2px dashed #e0e6ed; border-radius: 8px; padding: 15px; background: #f8fdff;">
                    <input 
                        type="file" 
                        id="fileInput_${item.assetNum}"
                        data-asset-num="${item.assetNum}"
                        data-minor-category="${item.minorCategory}"
                        multiple
                        accept="image/*"
                        onchange="handleFileUpload(this.dataset.assetNum, this.files, this.dataset.minorCategory)"
                        style="display: none;"
                    />
                    <button 
                        onclick="document.getElementById('fileInput_${item.assetNum}').click()"
                        style="background: linear-gradient(135deg, #3ac0c3, #2aa8ab); color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 500; width: 100%; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 8px;"
                    >
                        <i class="fas fa-camera"></i>
                        اختر الصور
                    </button>
                    <div style="font-size: 11px; color: #999; text-align: center; margin-top: 8px;">
                        الحد الأقصى: 5 ميجابايت لكل صورة | صور فقط (JPG, PNG)
                    </div>
                    <div id="fileList_${item.assetNum}" style="margin-top: 10px;">
                        <div style="color: #999; font-size: 13px; padding: 10px; text-align: center;">لم يتم رفع أي صور</div>
                    </div>
                </div>
            </div>
        `;
  }
}

function handleCancelSelection() {
  selectedItems = [];
  uploadedFiles = {};
  itemReasons = {};
  closeSelectedItemsPopup();
}

function closeSelectedItemsPopup() {
  const popup = document.getElementById("selectedItemsPopup");
  const backdrop = document.getElementById("popupBackdrop");

  if (popup) popup.remove();
  if (backdrop) backdrop.remove();
}

// ==================== FILE UPLOAD FUNCTIONS ====================

function handleFileUpload(assetNum, files, minorCategory) {
  if (!uploadedFiles[assetNum]) {
    uploadedFiles[assetNum] = [];
  }

  const isIT = minorCategory === "IT";

  Array.from(files).forEach((file) => {
    if (file.size > 5 * 1024 * 1024) {
      showAlert("warning", `الملف ${file.name} أكبر من 5 ميجابايت`);
      return;
    }

    if (!isIT) {
      const allowedImageTypes = [
        "image/jpeg",
        "image/jpg",
        "image/png",
        "image/gif",
      ];
      if (!allowedImageTypes.includes(file.type)) {
        showAlert("warning", `الملف ${file.name} ليس صورة. يرجى رفع صور فقط`);
        return;
      }
    }

    uploadedFiles[assetNum].push(file);
  });

  updateFileList(assetNum, minorCategory);
}

function updateFileList(assetNum, minorCategory) {
  const fileList = document.getElementById(`fileList_${assetNum}`);
  if (!fileList) return;

  const files = uploadedFiles[assetNum] || [];
  const isIT = minorCategory === "IT";

  if (files.length === 0) {
    const emptyMessage = isIT ? "لم يتم رفع أي ملفات" : "لم يتم رفع أي صور";
    fileList.innerHTML = `<div style="color: #999; font-size: 13px; padding: 10px; text-align: center;">${emptyMessage}</div>`;
    return;
  }

  fileList.innerHTML = files
    .map((file, index) => {
      const icon = file.type.startsWith("image/") ? "fa-image" : "fa-file";
      const iconColor = isIT ? "#3ac0c3" : "#ff6b6b";
      return `
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; background: #f8f9fa; border-radius: 6px; margin-bottom: 6px; border: 1px solid #e0e6ed;">
                <div style="display: flex; align-items: center; gap: 8px; flex: 1; overflow: hidden;">
                    <i class="fas ${icon}" style="color: ${iconColor};"></i>
                    <span style="font-size: 13px; color: #333; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${
                      file.name
                    }</span>
                    <span style="font-size: 11px; color: #999;">(${(
                      file.size / 1024
                    ).toFixed(1)} KB)</span>
                </div>
                <button onclick="removeFile('${assetNum}', ${index})" style="background: #e74c3c; color: white; border: none; border-radius: 50%; width: 24px; height: 24px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">✕</button>
            </div>
        `;
    })
    .join("");
}

function removeFile(assetNum, fileIndex) {
  if (uploadedFiles[assetNum]) {
    uploadedFiles[assetNum].splice(fileIndex, 1);
    const item = selectedItems.find((i) => i.assetNum === assetNum);
    updateFileList(assetNum, item?.minorCategory || "");
  }
}

// ==================== REASON SELECTION FUNCTIONS ====================

function toggleReason(assetNum, reason, event) {
  if (event) {
    event.preventDefault();
    event.stopPropagation();
  }

  if (!itemReasons[assetNum]) {
    itemReasons[assetNum] = {};
  }

  ["purpose_end", "excess", "unfit", "damaged"].forEach((r) => {
    itemReasons[assetNum][r] = false;
    const otherCheckbox = document.getElementById(`reason_${r}_${assetNum}`);
    const otherLabel = otherCheckbox?.closest(".action-checkbox-item");
    if (otherCheckbox) otherCheckbox.checked = false;
    if (otherLabel) otherLabel.classList.remove("selected");
  });

  itemReasons[assetNum][reason] = true;

  const checkbox = document.getElementById(`reason_${reason}_${assetNum}`);
  const label = checkbox?.closest(".action-checkbox-item");

  if (label) label.classList.add("selected");
  if (checkbox) checkbox.checked = true;

  updateFormPreview(assetNum);
}

function updateFormPreview(assetNum) {
  const preview = document.getElementById(`formPreview_${assetNum}`);
  if (!preview) return;

  const item = selectedItems.find((i) => i.assetNum === assetNum);
  if (!item) return;

  const reasons = itemReasons[assetNum] || {};
  const selectedReasons = Object.keys(reasons).filter((key) => reasons[key]);

  if (selectedReasons.length === 0) {
    preview.innerHTML =
      '<div style="color: #999; font-style: italic;">لم يتم تحديد سبب الإرجاع</div>';
    return;
  }

  const reasonLabels = {
    purpose_end: "انتهاء الغرض",
    excess: "فائض",
    unfit: "عدم الصلاحية",
    damaged: "تالف",
  };

  preview.innerHTML = `
        <div style="background: white; padding: 12px; border-radius: 6px; border: 1px solid #3ac0c3;">
            <strong style="color: #057590;">سبب الإرجاع المحدد:</strong>
            <div style="margin-top: 8px; display: flex; gap: 8px; flex-wrap: wrap;">
                ${selectedReasons
                  .map(
                    (reason) => `
                    <span style="background: #3ac0c3; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px;">
                        ${reasonLabels[reason]}
                    </span>
                `
                  )
                  .join("")}
            </div>
            <div style="margin-top: 12px; font-size: 12px; color: #666;">
                <i class="fas fa-info-circle" style="color: #3ac0c3;"></i>
                سيتم إنشاء النموذج تلقائياً مع بيانات الصنف
            </div>
        </div>
    `;
}

// ==================== SUBMIT RETURN FUNCTION ====================

function submitReturn() {
  if (selectedItems.length === 0) {
    showAlert("warning", "لم يتم تحديد أي عناصر للترجيع");
    return;
  }

  const returnData = selectedItems.map((item) => {
    const commentElement = document.getElementById(`comment_${item.assetNum}`);
    const isIT = item.minorCategory === "IT";

    return {
      id: item.id,
      name: item.name,
      assetNum: item.assetNum,
      serialNum: item.serialNum,
      model: item.model,
      brand: item.brand,
      oldAssetNum: item.oldAssetNum,
      assetType: item.assetType,
      category: item.category,
      minorCategory: item.minorCategory,
      comment: commentElement ? commentElement.value.trim() : "",
      files: uploadedFiles[item.assetNum] || [],
      reasons: isIT ? itemReasons[item.assetNum] || {} : null,
      generateForm: isIT,
    };
  });

  const itItemsWithoutReasons = returnData.filter(
    (item) =>
      item.generateForm &&
      (!item.reasons ||
        Object.keys(item.reasons).filter((k) => item.reasons[k]).length === 0)
  );

  if (itItemsWithoutReasons.length > 0) {
    showAlert("warning", "يجب تحديد سبب الإرجاع لعناصر IT قبل الترجيع");
    return;
  }

  showReturnConfirmation(returnData);
}

function showReturnConfirmation(returnData) {
  const modal = document.getElementById("deleteModal");
  const message = document.getElementById("deleteMessage");

  if (!modal || !message) {
    showAlert("error", "عنصر النافذة المنبثقة غير موجود");
    return;
  }

  let itemsList = returnData
    .map((item, index) => {
      let attachmentInfo = "";

      if (item.generateForm) {
        const reasons = item.reasons || {};
        const selectedReasons = Object.keys(reasons).filter((k) => reasons[k]);
        const reasonLabels = {
          purpose_end: "انتهاء الغرض",
          excess: "فائض",
          unfit: "عدم الصلاحية",
          damaged: "تالف",
        };
        const reasonsText = selectedReasons
          .map((r) => reasonLabels[r])
          .join(", ");
        attachmentInfo = `<br><small style="color: #3ac0c3;">📄 نموذج تلقائي: ${reasonsText}</small>`;
      } else if (item.files.length > 0) {
        attachmentInfo = `<br><small style="color: #ff6b6b;">📷 ${item.files.length} صورة</small>`;
      }

      return `<div style="margin: 8px 0; padding: 8px; background: #f8f9fa; border-radius: 6px;">
            <strong>${index + 1}. ${item.name}</strong>
            <br><small style="color: #888;">رقم الأصل: ${item.assetNum}</small>
            ${
              item.comment
                ? `<br><small style="color: #666;">📝 ${item.comment}</small>`
                : '<br><small style="color: #999;">بدون ملاحظات</small>'
            }
            ${attachmentInfo}
        </div>`;
    })
    .join("");

  message.innerHTML = `
        <div style="max-height: 300px; overflow-y: auto; margin: 10px 0;">
            ${itemsList}
        </div>
        <strong style="color: #057590; margin-top: 15px; display: block;">
            هل أنت متأكد من ترجيع هذه العناصر؟
        </strong>
    `;

  modal.style.display = "flex";
  setTimeout(() => modal.classList.add("show"), 10);

  window.tempReturnData = returnData;

  closeSelectedItemsPopup();
}

function closeDeleteModal() {
  const modal = document.getElementById("deleteModal");
  if (modal) {
    modal.classList.remove("show");
    setTimeout(() => (modal.style.display = "none"), 300);
  }
}

function confirmBulkReturnWithFiles() {
  const returnData = window.tempReturnData;

  if (!returnData) {
    showAlert("warning", "حدث خطأ في البيانات");
    return;
  }

  const confirmBtn = document.querySelector(".confirm-delete-btn");
  if (!confirmBtn) return;

  const originalText = confirmBtn.innerHTML;
  confirmBtn.disabled = true;
  confirmBtn.innerHTML =
    '<i class="fas fa-spinner fa-spin"></i> جاري الترجيع...';

  const formData = new FormData();

  returnData.forEach((item, index) => {
    formData.append(`asset_nums[${index}]`, item.assetNum);
    formData.append(`comments[${item.assetNum}]`, item.comment || "");

    if (item.generateForm) {
      formData.append(`generate_form[${item.assetNum}]`, "1");
      formData.append(`item_data[${item.assetNum}][name]`, item.name);
      formData.append(
        `item_data[${item.assetNum}][serial_num]`,
        item.serialNum
      );
      formData.append(`item_data[${item.assetNum}][model]`, item.model);
      formData.append(`item_data[${item.assetNum}][brand]`, item.brand);
      formData.append(
        `item_data[${item.assetNum}][old_asset_num]`,
        item.oldAssetNum
      );
      formData.append(
        `item_data[${item.assetNum}][asset_type]`,
        item.assetType
      );
      formData.append(`item_data[${item.assetNum}][category]`, item.category);

      const reasons = item.reasons || {};
      formData.append(
        `reasons[${item.assetNum}][purpose_end]`,
        reasons.purpose_end ? "1" : "0"
      );
      formData.append(
        `reasons[${item.assetNum}][excess]`,
        reasons.excess ? "1" : "0"
      );
      formData.append(
        `reasons[${item.assetNum}][unfit]`,
        reasons.unfit ? "1" : "0"
      );
      formData.append(
        `reasons[${item.assetNum}][damaged]`,
        reasons.damaged ? "1" : "0"
      );
    }

    if (item.files && item.files.length > 0) {
      item.files.forEach((file) => {
        const fileKey = `attachments[${item.assetNum}][]`;
        formData.append(fileKey, file, file.name);
      });
    }
  });

  const baseUrl =
    typeof window.appConfig !== "undefined" ? window.appConfig.baseUrl : "";

  fetch(baseUrl + "return/attachment/upload", {
    method: "POST",
    headers: {
      "X-Requested-With": "XMLHttpRequest",
    },
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showAlert("success", data.message);

        selectedItems = [];
        uploadedFiles = {};
        itemReasons = {};
        closeDeleteModal();
        delete window.tempReturnData;

        setTimeout(() => window.location.reload(), 1500);
      } else {
        showAlert("error", data.message || "حدث خطأ أثناء الترجيع");
        confirmBtn.disabled = false;
        confirmBtn.innerHTML = originalText;
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showAlert("error", "حدث خطأ في الاتصال بالخادم");
      confirmBtn.disabled = false;
      confirmBtn.innerHTML = originalText;
    });
}

// ==================== PRINT FORM FUNCTION ====================

function printAllForms() {
  const itItems = selectedItems.filter((item) => item.minorCategory === "IT");

  if (itItems.length === 0) {
    showAlert("warning", "لا توجد عناصر IT لمعاينة نماذج");
    return;
  }

  const itemsWithoutReasons = itItems.filter((item) => {
    const reasons = itemReasons[item.assetNum] || {};
    return Object.keys(reasons).filter((k) => reasons[k]).length === 0;
  });

  if (itemsWithoutReasons.length > 0) {
    showAlert("warning", "يجب تحديد سبب الإرجاع قبل عرض التقرير");
    return;
  }

  const printBtn = event.target;
  const originalBtnContent = printBtn.innerHTML;
  printBtn.disabled = true;
  printBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحميل...';

  const itItemsData = itItems.map((item) => {
    const itemReasonsData = itemReasons[item.assetNum] || {};
    const commentElement = document.getElementById(`comment_${item.assetNum}`);
    const itemNotes = commentElement ? commentElement.value.trim() : "";

    return {
      assetNum: item.assetNum,
      name: item.name,
      category: item.category,
      assetType: item.assetType || "غير محدد",
      notes: itemNotes,
      reasons: {
        purpose_end: itemReasonsData.purpose_end ? "1" : "0",
        excess: itemReasonsData.excess ? "1" : "0",
        unfit: itemReasonsData.unfit ? "1" : "0",
        damaged: itemReasonsData.damaged ? "1" : "0",
      },
    };
  });

  const formData = new FormData();
  formData.append("asset_num", itItems[0].assetNum);
  formData.append("item_data[name]", itItems[0].name);
  formData.append("item_data[serial_num]", itItems[0].serialNum || "غير محدد");
  formData.append("item_data[model]", itItems[0].model || "غير محدد");
  formData.append("item_data[brand]", itItems[0].brand || "غير محدد");
  formData.append(
    "item_data[old_asset_num]",
    itItems[0].oldAssetNum || "غير محدد"
  );
  formData.append("item_data[asset_type]", itItems[0].assetType || "غير محدد");
  formData.append("item_data[category]", itItems[0].category);

  itItemsData.forEach((item, index) => {
    formData.append(`all_items[${index}][assetNum]`, item.assetNum);
    formData.append(`all_items[${index}][name]`, item.name);
    formData.append(`all_items[${index}][category]`, item.category);
    formData.append(`all_items[${index}][assetType]`, item.assetType);
    formData.append(`all_items[${index}][notes]`, item.notes);
    formData.append(
      `all_items[${index}][reasons][purpose_end]`,
      item.reasons.purpose_end
    );
    formData.append(
      `all_items[${index}][reasons][excess]`,
      item.reasons.excess
    );
    formData.append(`all_items[${index}][reasons][unfit]`, item.reasons.unfit);
    formData.append(
      `all_items[${index}][reasons][damaged]`,
      item.reasons.damaged
    );
  });

  const printFrame = document.createElement("iframe");
  printFrame.style.cssText =
    "position: fixed; right: 0; bottom: 0; width: 0; height: 0; border: none; visibility: hidden;";
  document.body.appendChild(printFrame);

  const baseUrl =
    typeof window.appConfig !== "undefined" ? window.appConfig.baseUrl : "";

  fetch(baseUrl + "return/attachment/printForm", {
    method: "POST",
    headers: {
      "X-Requested-With": "XMLHttpRequest",
      Accept: "application/json",
    },
    body: formData,
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const contentType = response.headers.get("content-type");
      if (contentType && contentType.includes("application/json")) {
        return response.json();
      } else {
        return response.text().then((html) => ({ success: true, html: html }));
      }
    })
    .then((data) => {
      if (data.success && data.html) {
        const iframeDoc = printFrame.contentWindow.document;
        iframeDoc.open();
        iframeDoc.write(data.html);
        iframeDoc.close();

        printFrame.onload = function () {
          try {
            printFrame.contentWindow.focus();
            printFrame.contentWindow.print();

            setTimeout(() => {
              if (printFrame && printFrame.parentNode) {
                document.body.removeChild(printFrame);
              }
            }, 1000);
          } catch (e) {
            console.error("Print error:", e);
            showAlert("error", "حدث خطأ أثناء الطباعة");
            if (printFrame && printFrame.parentNode) {
              document.body.removeChild(printFrame);
            }
          }
        };
      } else {
        showAlert("error", data.message || "فشل إنشاء النموذج");
        if (printFrame && printFrame.parentNode) {
          document.body.removeChild(printFrame);
        }
      }
    })
    .catch((error) => {
      console.error("Error details:", error);
      showAlert("error", "حدث خطأ في الاتصال بالخادم: " + error.message);
      if (printFrame && printFrame.parentNode) {
        document.body.removeChild(printFrame);
      }
    })
    .finally(() => {
      printBtn.disabled = false;
      printBtn.innerHTML = originalBtnContent;
    });
}

// ==================== ALERT FUNCTION ====================

function showAlert(type, message) {
  const popup = document.getElementById("selectedItemsPopup");

  if (popup) {
    const icon = type === "warning" ? "⚠️" : type === "error" ? "❌" : "✅";
    alert(`${icon} ${message}`);
    return;
  }

  let alertContainer = document.getElementById("alertContainer");

  if (!alertContainer) {
    alertContainer = document.createElement("div");
    alertContainer.id = "alertContainer";
    alertContainer.style.cssText =
      "position: fixed; top: 20px; right: 20px; z-index: 99999; display: flex; flex-direction: column; gap: 10px; pointer-events: none;";
    document.body.appendChild(alertContainer);
  }

  const existingAlerts = alertContainer.querySelectorAll(".alert");
  for (let alert of existingAlerts) {
    if (alert.textContent.includes(message)) {
      return;
    }
  }

  const alertDiv = document.createElement("div");
  alertDiv.className = `alert alert-${type}`;

  const bgColor =
    type === "success" ? "#2ecc71" : type === "warning" ? "#f39c12" : "#e74c3c";

  alertDiv.style.cssText = `
        background: ${bgColor};
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        font-size: 14px;
        font-weight: 500;
        min-width: 300px;
        max-width: 400px;
        pointer-events: auto;
        animation: slideInRight 0.3s ease;
        opacity: 1;
        transform: translateX(0);
        transition: all 0.3s ease;
    `;

  const icon = type === "success" ? "✓" : type === "warning" ? "⚠" : "✕";
  alertDiv.innerHTML = `<strong style="margin-left: 8px; font-size: 16px;">${icon}</strong> ${message}`;

  alertContainer.appendChild(alertDiv);

  setTimeout(() => {
    alertDiv.style.opacity = "0";
    alertDiv.style.transform = "translateX(20px)";
    setTimeout(() => alertDiv.remove(), 300);
  }, 4000);

  if (!document.getElementById("alertAnimationStyles")) {
    const style = document.createElement("style");
    style.id = "alertAnimationStyles";
    style.textContent = `
            @keyframes slideInRight {
                from { opacity: 0; transform: translateX(100px); }
                to { opacity: 1; transform: translateX(0); }
            }
        `;
    document.head.appendChild(style);
  }
}

// ==================== FILTER FUNCTIONS ====================

function filterBySource(source) {
  const buttons = document.querySelectorAll(".filter-buttons .custom-btn");
  buttons.forEach((btn) => btn.classList.remove("active"));
  event.target.classList.add("active");

  const rows = document.querySelectorAll("#covenantsTable tbody tr");
  rows.forEach((row) => {
    const rowSource = row.dataset.source;
    if (source === "all" || rowSource === source) {
      row.style.display = "";
    } else {
      row.style.display = "none";
    }
  });
}

// ==================== TRANSFER FUNCTIONS (PLACEHOLDERS) ====================
// These functions are referenced in the HTML but not implemented in the original code
// Add them if you have transfer functionality

function openTransferPopup(itemOrderId) {
  console.log("Transfer function called for item:", itemOrderId);
  showAlert("info", "وظيفة التحويل قيد التطوير");
}

function closeTransferModal() {
  const modal = document.getElementById("transferModal");
  if (modal) {
    modal.style.display = "none";
  }
}

function filterTransferUsers() {
  console.log("Filter transfer users called");
}

function showTransferDropdown() {
  console.log("Show transfer dropdown called");
}

function submitTransferSingle() {
  console.log("Submit transfer single called");
}

// ==================== INITIALIZATION ====================

console.log("Return system initialized - Ready for returns from userView2");
