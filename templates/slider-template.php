<?php
$plugin_url = plugin_dir_url(dirname(__DIR__));

// 從 options 讀取 JSON
$items = get_option('bs_slider_items');


// 資料驗證
$slides = [];



if (is_array($items)) {
  foreach ($items as $i => $item) {
    if (!empty($item['image'])) {
      $slides[] = [
        'image' => $item['image'],
        'title' => $item['title'] ?? '',
        'link'  => $item['link'] ?? '',
        'alt'   => $item['alt'] ?? '',
        'id'    => "slide" . ($i + 1),
      ];
    }
  }
}

?>

<div class="bs-slider-wrapper"
     data-autoplay="<?php echo esc_attr(get_option('bs_slider_autoplay', 'no')); ?>"
     data-interval="<?php echo esc_attr(get_option('bs_slider_interval', 5000)); ?>">

  <div class="bs-fade-left"></div>
  <div class="bs-fade-right"></div>

  <button class="bs-arrow left" aria-label="上一張">
    <svg
      class="bs-arrow-icon"
      viewBox="0 0 128 128"
      width="48"
      height="48"
      xmlns="http://www.w3.org/2000/svg"
    >
      <path
        d="M84 108c-1.023 0-2.047-.391-2.828-1.172l-40-40c-1.563-1.563-1.563-4.094 0-5.656l40-40c1.563-1.563 4.094-1.563 5.656 0s1.563 4.094 0 5.656L49.656 64l37.172 37.172c1.563 1.563 1.563 4.094 0 5.656-.781.781-1.805 1.172-2.828 1.172z"
        fill="black"
      />
    </svg>
  </button>

  <form>
  <?php foreach ($slides as $index => $slide): ?>
    <input type="radio" name="fancy" id="<?php echo $slide['id']; ?>" <?php echo $index === 0 ? 'checked' : ''; ?> />
  <?php endforeach; ?>

  <?php foreach ($slides as $slide): ?>
    <label for="<?php echo $slide['id']; ?>">
      <?php if ($slide['link']): ?><a href="<?php echo esc_url($slide['link']); ?>" target="_blank"><?php endif; ?>
        <img
  src="<?php echo esc_url($slide['image']); ?>"
  alt="<?php echo esc_attr($slide['alt']); ?>"
  loading="lazy"
/>
      <?php if ($slide['title']): ?><p><?php echo esc_html($slide['title']); ?></p><?php endif; ?>
      <?php if ($slide['link']): ?></a><?php endif; ?>
    </label>
  <?php endforeach; ?>
</form>
  <button class="bs-arrow right" aria-label="下一張">
    <svg
      class="bs-arrow-icon"
      viewBox="0 0 128 128"
      width="48"
      height="48"
      xmlns="http://www.w3.org/2000/svg"
    >
      <path
        d="M44 108c-1.023 0-2.047-.391-2.828-1.172-1.563-1.563-1.563-4.094 0-5.656l37.172-37.172-37.172-37.172c-1.563-1.563-1.563-4.094 0-5.656s4.094-1.563 5.656 0l40 40c1.563 1.563 1.563 4.094 0 5.656l-40 40c-.781.781-1.805 1.172-2.828 1.172z"
        fill="black"
      />
    </svg>
  </button>
</div>
