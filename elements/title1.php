<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Element_Custom_Title1 extends \Bricks\Element {
  /** 
   * How to create custom elements in Bricks
   * 
   * https://academy.bricksbuilder.io/article/create-your-own-elements
   */
  public $category     = 'custom';
  public $name         = 'custom-title1';
  public $icon         = 'fas fa-anchor'; // FontAwesome 5 icon in builder (https://fontawesome.com/icons)
  public $css_selector = '.custom-title-wrapper'; // Default CSS selector for all controls with 'css' properties
  // public $scripts      = []; // Enqueue registered scripts by their handle

  public function get_form_settings() {
    return [
      'autoplay' => [
        'type' => 'select',
        'label' => '自動輪播',
        'options' => [
          'yes' => '是',
          'no' => '否'
        ],
        'default' => 'no',
      ],
      'interval' => [
        'type' => 'number',
        'label' => '輪播間隔（毫秒）',
        'default' => 5000,
        'placeholder' => '例如：5000'
      ]
    ];
  }

  public function render() {
    $autoplay = $this->settings['autoplay'] ?? 'no';
    $interval = $this->settings['interval'] ?? 5000;

    echo '<div class="bricks-banner-slider-preview">';
    echo do_shortcode('[banner_slider autoplay="' . esc_attr($autoplay) . '" interval="' . intval($interval) . '"]');
    echo '</div>';
  }
}
