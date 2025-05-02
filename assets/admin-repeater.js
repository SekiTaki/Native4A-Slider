jQuery(document).ready(function ($) {
  let slideIndex = $("#bs-repeater-table .bs-repeater-item").length;

  function updateNames() {
    $("#bs-repeater-table .bs-repeater-item").each(function (i) {
      $(this)
        .find("input, select")
        .each(function () {
          let name = $(this).attr("name");
          if (name) {
            const newName = name.replace(
              /bs_slider_items\[[^\]]+\]/,
              `bs_slider_items[${i}]`
            );
            $(this).attr("name", newName);
          }
        });
    });
  }

  function refreshPreview($row) {
    const url = $row.find(".bs-img-url").val();
    let $preview = $row.find(".bs-preview");
    if (!$preview.length) {
      $preview = $(
        '<img class="bs-preview" style="max-width:120px; max-height:90px; display:block; margin-top:5px;">'
      );
      $row.find(".bs-img-url").after($preview);
    }
    $preview.attr("src", url);
  }

  // ➕ 滑動支援：觸控與滑鼠拖動
  const wrapper = document.querySelector(".bs-slider-wrapper");
  if (wrapper) {
    const radios = [...document.querySelectorAll('input[name="fancy"]')];
    const getCheckedIndex = () => radios.findIndex((r) => r.checked);
    let startX = 0,
      endX = 0,
      isDown = false;

    // 手機滑動
    wrapper.addEventListener("touchstart", (e) => {
      startX = e.touches[0].clientX;
    });
    wrapper.addEventListener("touchend", (e) => {
      endX = e.changedTouches[0].clientX;
      const diff = startX - endX;
      if (Math.abs(diff) > 50) {
        const i = getCheckedIndex();
        if (diff > 0) {
          radios[(i + 1) % radios.length].checked = true;
        } else {
          radios[(i - 1 + radios.length) % radios.length].checked = true;
        }
      }
    });

    // 桌面滑鼠拖動
    wrapper.addEventListener("mousedown", (e) => {
      isDown = true;
      startX = e.clientX;
    });
    wrapper.addEventListener("mouseup", (e) => {
      if (!isDown) return;
      endX = e.clientX;
      const diff = startX - endX;
      if (Math.abs(diff) > 50) {
        const i = getCheckedIndex();
        if (diff > 0) {
          radios[(i + 1) % radios.length].checked = true;
        } else {
          radios[(i - 1 + radios.length) % radios.length].checked = true;
        }
      }
      isDown = false;
    });
  }

  // 匯出設定
  $("#bs-export-json").on("click", function (e) {
    e.preventDefault();
    const data = [];
    $("#bs-repeater-table .bs-repeater-item").each(function () {
      const item = {
        image: $(this).find("input[name*='[image]']").val(),
        title: $(this).find("input[name*='[title]']").val(),
        link: $(this).find("input[name*='[link]']").val(),
        alt: $(this).find("input[name*='[alt]']").val(),
      };
      data.push(item);
    });
    const blob = new Blob([JSON.stringify(data, null, 2)], {
      type: "application/json",
    });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "banner-slider-settings.json";
    a.click();
    URL.revokeObjectURL(url);
  });

  // 匯入設定
  $("#bs-import-json").on("click", function (e) {
    e.preventDefault();
    const input = $(
      '<input type="file" accept="application/json" style="display:none;">'
    );
    input.on("change", function () {
      const file = this.files[0];
      const reader = new FileReader();
      reader.onload = function (event) {
        try {
          const importedData = JSON.parse(event.target.result);
          if (Array.isArray(importedData)) {
            $("#bs-repeater-table tbody").empty();
            importedData.forEach((item, index) => {
              const template = $("#bs-repeater-template").html();
              const $clone = $(
                template.replace(/__name__/g, `bs_slider_items[${index}]`)
              );
              $clone.find("input[name*='[image]']").val(item.image || "");
              $clone.find("input[name*='[title]']").val(item.title || "");
              $clone.find("input[name*='[link]']").val(item.link || "");
              $clone.find("input[name*='[alt]']").val(item.alt || "");
              $clone
                .find("td")
                .prepend(
                  '<span class="bs-drag-handle" title="拖拉排序">☰</span> '
                );
              $("#bs-repeater-table tbody").append($clone);
              refreshPreview($clone);
            });
            slideIndex = importedData.length;
            updateNames();
          } else {
            alert("JSON 格式錯誤");
          }
        } catch (e) {
          alert("JSON 解析失敗");
        }
      };
      reader.readAsText(file);
    });
    input.trigger("click");
  });

  // 新增欄位
  $("#bs-add-slide").on("click", function (e) {
    e.preventDefault();
    const template = $("#bs-repeater-template").html();
    const $clone = $(
      template.replace(/__name__/g, `bs_slider_items[${slideIndex}]`)
    );
    $clone
      .find("td")
      .prepend('<span class="bs-drag-handle" title="拖拉排序">☰</span> ');
    $("#bs-repeater-table tbody").append($clone);
    slideIndex++;
    updateNames();
  });

  // 刪除欄位
  $(document).on("click", ".bs-remove-btn", function (e) {
    e.preventDefault();
    $(this).closest(".bs-repeater-item").remove();
    updateNames();
  });

  // 圖片上傳 + 更新預覽
  $(document).on("click", ".bs-upload-btn", function (e) {
    e.preventDefault();
    const $input = $(this).siblings(".bs-img-url");
    const $row = $(this).closest(".bs-repeater-item");
    const customUploader = wp.media({
      title: "選擇圖片",
      button: { text: "使用這張圖片" },
      multiple: false,
    });
    customUploader.on("select", function () {
      const attachment = customUploader
        .state()
        .get("selection")
        .first()
        .toJSON();
      $input.val(attachment.url);
      refreshPreview($row);
    });
    customUploader.open();
  });

  // 即時更新縮圖預覽
  $(document).on("input", ".bs-img-url", function () {
    const $row = $(this).closest(".bs-repeater-item");
    refreshPreview($row);
  });

  // 拖拉排序
  $("#bs-repeater-table tbody").sortable({
    handle: ".bs-drag-handle",
    update: function () {
      updateNames();
    },
  });

  // 初始欄位加圖示與預覽
  $("#bs-repeater-table .bs-repeater-item").each(function () {
    $(this)
      .find("td")
      .prepend('<span class="bs-drag-handle" title="拖拉排序">☰</span> ');
    refreshPreview($(this));
  });
});
