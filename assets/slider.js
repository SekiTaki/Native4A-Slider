document.addEventListener("DOMContentLoaded", () => {
  console.log("🚀 Slider script loaded!");

  const radios = [...document.querySelectorAll('input[name="fancy"]')];
  const leftArrow = document.querySelector(".bs-arrow.left");
  const rightArrow = document.querySelector(".bs-arrow.right");
  const wrapper = document.querySelector(".bs-slider-wrapper");

  if (!leftArrow || !rightArrow || radios.length === 0) {
    console.warn("⚠️ Slider not initialized: missing elements.");
    return;
  }

  function getCheckedIndex() {
    return radios.findIndex((r) => r.checked);
  }

  leftArrow.addEventListener("click", () => {
    let i = getCheckedIndex();
    radios[(i - 1 + radios.length) % radios.length].checked = true;
  });

  rightArrow.addEventListener("click", () => {
    let i = getCheckedIndex();
    radios[(i + 1) % radios.length].checked = true;
  });

  // ✅ 自動輪播功能
  if (wrapper) {
    const interval = parseInt(wrapper.dataset.interval) || 5000;
    const autoPlay = wrapper.dataset.autoplay === "yes";

    if (autoPlay) {
      setInterval(() => {
        let i = getCheckedIndex();
        radios[(i + 1) % radios.length].checked = true;
      }, interval);
    }
  }

  // 自動預載所有輪播圖片
  const preloadImages = () => {
    const imgElements = document.querySelectorAll(".bs-slider-wrapper img");
    imgElements.forEach((img) => {
      const url = img.getAttribute("src");
      if (url) {
        const preload = new Image();
        preload.src = url;
      }
    });
  };
  preloadImages();
});
