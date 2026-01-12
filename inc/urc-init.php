<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class urc_init {
    public function __construct() {
        add_action('admin_menu', [$this, 'create_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_filter('the_content', [$this, 'display_reading_time']);
        add_action('wp_footer', [$this, 'add_frontend_elements']);
    }

    public function create_menu() {
        add_menu_page('Reading Tool', 'Reading Tool', 'manage_options', 'ultra-reading-companion', [$this, 'settings_html'], 'dashicons-book-alt', 9);
    }

    public function register_settings() {
        // Sanitization callback added here
        register_setting('urc_group', 'urc_options', [$this, 'sanitize_urc_options']);
    }

    // New Sanitization Function for Database Security
    public function sanitize_urc_options($input) {
        $new_input = [];
        if(isset($input['scroll_icon'])) $new_input['scroll_icon'] = sanitize_text_field($input['scroll_icon']);
        if(isset($input['color']))       $new_input['color']       = sanitize_hex_color($input['color']);
        if(isset($input['bar_height']))  $new_input['bar_height']  = absint($input['bar_height']);
        if(isset($input['wpm']))         $new_input['wpm']         = absint($input['wpm']);
        if(isset($input['rt_bg']))       $new_input['rt_bg']       = sanitize_hex_color($input['rt_bg']);
        if(isset($input['rt_text']))     $new_input['rt_text']     = sanitize_hex_color($input['rt_text']);
        if(isset($input['rt_padding']))  $new_input['rt_padding']  = absint($input['rt_padding']);
        if(isset($input['rt_margin']))   $new_input['rt_margin']   = absint($input['rt_margin']);
        
        return $new_input;
    }

    public function settings_html() {
        $options = get_option('urc_options');
        ?>
        <div class="wrap">
            <h1>Ultra Reading Companion Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields('urc_group'); ?>
                
                <h2>1. Progress Bar & Scroll Settings</h2>
                <table class="form-table">
                    <tr>
                        <th>Scroll to Top Icon</th>
                        <td>
                            <select name="urc_options[scroll_icon]">
                                <option value="dashicons-arrow-up-alt2" <?php selected($options['scroll_icon'] ?? '', 'dashicons-arrow-up-alt2'); ?>>Arrow (↑)</option>
                                <option value="dashicons-arrow-up-alt" <?php selected($options['scroll_icon'] ?? '', 'dashicons-arrow-up-alt'); ?>>Bold Arrow</option>
                                <option value="dashicons-upload" <?php selected($options['scroll_icon'] ?? '', 'dashicons-upload'); ?>>Upload Style</option>
                                <option value="dashicons-caret-up-alt" <?php selected($options['scroll_icon'] ?? '', 'dashicons-caret-up-alt'); ?>>Caret Up</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Bar Color</th>
                        <td><input type="color" name="urc_options[color]" value="<?php echo esc_attr($options['color'] ?? '#3498db'); ?>"></td>
                    </tr>
                    <tr>
                        <th>Bar Height (px)</th>
                        <td><input type="number" name="urc_options[bar_height]" value="<?php echo esc_attr($options['bar_height'] ?? '6'); ?>" min="1" max="20"> px</td>
                    </tr>
                </table>

                <h2>2. Reading Time Settings</h2>
                <table class="form-table">
                    <tr>
                        <th>Words Per Minute</th>
                        <td><input type="number" name="urc_options[wpm]" value="<?php echo esc_attr($options['wpm'] ?? '200'); ?>"></td>
                    </tr>
                    <tr>
                        <th>Background Color</th>
                        <td><input type="color" name="urc_options[rt_bg]" value="<?php echo esc_attr($options['rt_bg'] ?? '#f4f4f4'); ?>"></td>
                    </tr>
                    <tr>
                        <th>Text Color</th>
                        <td><input type="color" name="urc_options[rt_text]" value="<?php echo esc_attr($options['rt_text'] ?? '#333333'); ?>"></td>
                    </tr>
                    <tr>
                        <th>Padding (px)</th>
                        <td><input type="number" name="urc_options[rt_padding]" value="<?php echo esc_attr($options['rt_padding'] ?? '10'); ?>"> px</td>
                    </tr>
                    <tr>
                        <th>Margin Bottom (px)</th>
                        <td><input type="number" name="urc_options[rt_margin]" value="<?php echo esc_attr($options['rt_margin'] ?? '20'); ?>"> px</td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function enqueue_assets() {
        wp_enqueue_style('dashicons');
        $plugin_url = defined('URC_URL') ? URC_URL : plugin_dir_url( dirname(__FILE__) );
        wp_enqueue_style('urc-style', $plugin_url . 'assets/css/style.css');
        wp_enqueue_script('urc-script', $plugin_url . 'assets/js/script.js', [], '1.1', true);

        $options = get_option('urc_options');
        
        // Data sanitization for Inline CSS
        $p_color  = sanitize_hex_color($options['color'] ?? '#3498db');
        $p_height = absint($options['bar_height'] ?? '6') . 'px';
        $rt_bg    = sanitize_hex_color($options['rt_bg'] ?? '#f4f4f4');
        $rt_text  = sanitize_hex_color($options['rt_text'] ?? '#333333');
        $rt_pad   = absint($options['rt_padding'] ?? '10') . 'px';
        $rt_mar   = absint($options['rt_margin'] ?? '20') . 'px';

        $custom_css = "
            #urc-progress-container { height: $p_height; }
            #urc-progress { background: $p_color !important; }
            #urc-scroll-top { background: $p_color !important; }
            .urc-reading-time { 
                background-color: $rt_bg; 
                color: $rt_text; 
                padding: $rt_pad; 
                margin-bottom: $rt_mar;
                border-radius: 5px;
                display: flex;
                align-items: center;
                justify-content: center; 
                width: 100%;             
                box-sizing: border-box;  
                gap: 10px;
            }";
        wp_add_inline_style('urc-style', $custom_css);
    }

    public function display_reading_time($content) {
        if (is_singular('post')) {
            $options = get_option('urc_options');
            $wpm = (int)($options['wpm'] ?? 200);
            $word_count = str_word_count(strip_tags($content));
            $minutes = ceil($word_count / $wpm);
            
            // Output Escaping and Translation Support
            $html = '<div class="urc-reading-time">' . 
                    esc_html__('⏱ Reading Time:', 'ultra-reading-companion') . ' ' . 
                    absint($minutes) . ' ' . 
                    esc_html__('min', 'ultra-reading-companion') . 
                    ($minutes > 1 ? 's' : '') . 
                    '</div>';
            return $html . $content;
        }
        return $content;
    }

    public function add_frontend_elements() {
        if (!is_singular('post')) return;
        $options = get_option('urc_options');
        $icon_class = sanitize_text_field($options['scroll_icon'] ?? 'dashicons-arrow-up-alt2');
        ?>
        <div id="urc-progress-container">
            <div id="urc-progress"></div>
        </div>
        <button id="urc-scroll-top" title="<?php esc_attr_e('Go to top', 'ultra-reading-companion'); ?>">
            <span class="dashicons <?php echo esc_attr($icon_class); ?>"></span>
        </button>
        <?php
    }
}