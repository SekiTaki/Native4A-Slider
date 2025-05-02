<?php
/**
 * 這份檔案是後台設定頁，改為可視化動態新增圖片欄位（repeater UI）
 */

defined('ABSPATH') || exit;

// Enqueue jQuery 和我們自己的 admin JS
add_action('admin_enqueue_scripts', function($hook) {
  if ($hook === 'toplevel_page_bs-banner-slider') {
    wp_enqueue_media();
    wp_enqueue_script('bs-admin-repeater', plugin_dir_url(__DIR__) . 'assets/admin-repeater.js', ['jquery'], false, true);
  }
});

// 建立選單
add_action('admin_menu', function() {
  add_menu_page(
    'Banner Slider 設定',
    'Banner Slider',
    'manage_options',
    'bs-banner-slider',
    'bs_render_settings_page',
    'dashicons-images-alt2',
    20
  );
});

// 註冊欄位
add_action('admin_init', function() {
  register_setting('bs_slider_settings', 'bs_slider_items', [
    'sanitize_callback' => 'bs_sanitize_slider_items'
  ]);
  register_setting('bs_slider_settings', 'bs_slider_autoplay', [
    'sanitize_callback' => function($value) {
      return in_array($value, ['yes', 'no'], true) ? $value : 'no';
    }
  ]);
  
  register_setting('bs_slider_settings', 'bs_slider_interval', [
    'sanitize_callback' => function($value) {
      return max(1000, intval($value));
    }
  ]);
  });

// 設定頁內容
function bs_render_settings_page() {
  $items = get_option('bs_slider_items');
  if (!is_array($items)) {
    $items = [];
  }
  ?>
  <div class="wrap">
    <h1>Banner Slider 設定</h1>
    <form method="post" action="options.php">
      <?php settings_fields('bs_slider_settings'); ?>
      <table class="form-table" id="bs-repeater-table">
        <tbody>
          <?php foreach ($items as $index => $item): ?>
            <tr class="bs-repeater-item">
              <td>
                <input type="text" name="bs_slider_items[<?php echo $index; ?>][image]" value="<?php echo esc_url($item['image']); ?>" placeholder="圖片網址" style="width: 90%;" class="bs-img-url" />
                <button class="button bs-upload-btn">上傳</button>
                <br><br>
                <input type="text" name="bs_slider_items[<?php echo $index; ?>][title]" value="<?php echo esc_attr($item['title']); ?>" placeholder="標題" style="width: 90%;" />
                <br><br>
                <input type="text" name="bs_slider_items[<?php echo $index; ?>][link]" value="<?php echo esc_url($item['link']); ?>" placeholder="連結網址" style="width: 90%;" />
                <br><br>
                <input type="text" name="bs_slider_items[<?php echo $index; ?>][alt]" value="<?php echo esc_attr($item['alt'] ?? ''); ?>" placeholder="圖片說明（Alt）" style="width: 90%;" />
                <br><br>
                <button class="button-link-delete bs-remove-btn">❌ 移除這張圖</button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <p><button class="button" id="bs-add-slide">➕ 新增圖片</button></p>
      <p>
  <button class="button" id="bs-export-json">📤 匯出 JSON</button>
  <button class="button" id="bs-import-json">📥 匯入 JSON</button>
</p>
      <?php submit_button(); ?>
    </form>

    <!-- 模板（JS 用） -->
    <template id="bs-repeater-template">
      <tr class="bs-repeater-item">
        <td>
          <input type="text" name="__name__[image]" value="" placeholder="圖片網址" style="width: 90%;" class="bs-img-url" />
          <button class="button bs-upload-btn">上傳</button>
          <br><br>
          <input type="text" name="__name__[title]" value="" placeholder="標題" style="width: 90%;" />
          <br><br>
          <input type="text" name="__name__[alt]" value="" placeholder="圖片說明（Alt）" style="width: 90%;" />
          <br><br>
          <button class="button-link-delete bs-remove-btn">❌ 移除這張圖</button>
        </td>
      </tr>
    </template>
  </div>
  <h2>輪播選項</h2>
<label>
  啟用自動輪播：
  <select name="bs_slider_autoplay">
    <option value="no" <?php selected(get_option('bs_slider_autoplay'), 'no'); ?>>否</option>
    <option value="yes" <?php selected(get_option('bs_slider_autoplay'), 'yes'); ?>>是</option>
  </select>
</label>
<br><br>
<label>
  輪播間隔（毫秒）：
  <input type="number" name="bs_slider_interval" value="<?php echo esc_attr(get_option('bs_slider_interval', 5000)); ?>" min="1000" step="500" />
</label>
  <?php
}
function bs_sanitize_slider_items($input) {
  if (!is_array($input)) return [];

  foreach ($input as &$item) {
    $item['image'] = esc_url_raw($item['image'] ?? '');
    $item['title'] = sanitize_text_field($item['title'] ?? '');
    $item['link']  = esc_url_raw($item['link'] ?? '');
    $item['alt']   = sanitize_text_field($item['alt'] ?? '');
  }

  return $input;
}