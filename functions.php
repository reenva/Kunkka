<?php
/**
 * @name Functions
 * @description Wordpress theme default functions file
 * @version     1.0.0
 * @author      mufeng (http://mufeng.me)
 * @url https://mufeng.me/wordpress-mobile-theme-kunkka.html
 * @package     Kunkka
 **/

/**
 * Define constants
 */
define( 'MUTHEME_NAME', 'Xunlu' );
define( 'MUTHEME_VERSION', '2.0.1' );
define( 'MUTHEME_PATH', dirname( __FILE__ ) );
define( "MUTHEME_THEME_URL", get_bloginfo( 'template_directory' ) );
//WordPress SSL at 2016/12/29 update

/**
 * Import core function files
 */
get_template_part( 'functions/mutheme-basic' );
get_template_part( 'functions/mutheme-function' );
get_template_part( 'functions/mutheme-widget' );
get_template_part( 'functions/mutheme-main' );

/**
 * Add rss feed
 */
add_theme_support( 'automatic-feed-links' );

/**
 * Enable link manager
 */
add_filter( 'pre_option_link_manager_enabled', '__return_true' );

/**
 * Add post thumbnail
 */
add_theme_support( 'post-thumbnails' );

/**
 * Disable symbol automatically converted to full ban
 */
remove_filter( 'the_content', 'wptexturize' );

/**
 * Remove invalid information display at head tag
 */
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'start_post_rel_link' );
remove_action( 'wp_head', 'index_rel_link' );
remove_action( 'wp_head', 'adjacent_posts_rel_link' );

/**
 * Remove default wordpress widgets
 */
if( !mutheme_settings('register_widget') ){
    add_action( 'widgets_init', 'mutheme_unregister_default_widgets', 1 );
}
function mutheme_unregister_default_widgets() {
    unregister_widget( 'WP_Widget_Pages' );
    unregister_widget( 'WP_Widget_Calendar' );
    unregister_widget( 'WP_Widget_Archives' );
    unregister_widget( 'WP_Widget_Links' );
    unregister_widget( 'WP_Widget_Meta' );
    unregister_widget( 'WP_Widget_Search' );
    unregister_widget( 'WP_Widget_Text' );
    unregister_widget( 'WP_Widget_Categories' );
    unregister_widget( 'WP_Widget_Recent_Posts' );
    unregister_widget( 'WP_Widget_Recent_Comments' );
    unregister_widget( 'WP_Widget_RSS' );
    unregister_widget( 'WP_Nav_Menu' );
    unregister_widget( 'WP_Widget_Tag_Cloud' );
}

/**
 * Post thumbnail custom sizes
 */
if ( function_exists( 'add_image_size' ) ) {
    add_image_size( 'index-thumbnail', 250, 250, true );
}

/**
 * Register wordpress menu
 */
if ( function_exists( 'register_nav_menus' ) ) {
    register_nav_menus( array(
        'top-menu'    => __( 'Top menu', MUTHEME_NAME ),
        'global-menu' => __( 'Dropdown menu', MUTHEME_NAME )
    ) );
}

/**
 * Register sidebar
 */
if ( function_exists( 'register_sidebar' ) ) {
    register_sidebar( array(
        'name'          => 'sidebar',
        'id'            => 'sidebar-page',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>'
    ) );
}

/**
 * Register theme languages files
 */
load_theme_textdomain( MUTHEME_NAME, mutheme_path( 'languages' ) );

//替换Gavatar头像地址
function get_ssl_avatar($avatar) {
    if (preg_match_all(
        '/(src|srcset)=["\']https?.*?\/avatar\/([^?]*)\?s=([\d]+)&([^"\']*)?["\']/i',
        $avatar,
        $matches
    ) > 0) {
        $url = 'https://secure.gravatar.com';
        $size = $matches[3][0];
        $vargs = array_pad(array(), count($matches[0]), array());
        for ($i = 1; $i < count($matches); $i++) {
            for ($j = 0; $j < count($matches[$i]); $j++) {
                $tmp = strtolower($matches[$i][$j]);
                $vargs[$j][] = $tmp;
                if ($tmp == 'src') {
                    $size = $matches[3][$j];
                }
            }
        }
        $buffers = array();
        foreach ($vargs as $varg) {
            $buffers[] = vsprintf(
            '%s="%s/avatar/%s?s=%s&%s"',
            array($varg[0], $url, $varg[1], $varg[2], $varg[3])
           );
        }
        return sprintf(
                '<img alt="avatar" %s class="avatar avatar-%s" height="%s" width="%s" />',
                implode(' ', $buffers), $size, $size, $size
            );
    } else {
        return false;
    }
}
	add_filter('get_avatar', 'get_ssl_avatar');
	
//禁止站内pingback
function no_self_ping( &$links ) {
    $home = get_option( 'home' );
    foreach ( $links as $l => $link )
        if ( 0 === strpos( $link, $home ) ) unset($links[$l]);
}
add_action( 'pre_ping', 'no_self_ping' );
//移除预获取DNS
	function remove_dns_prefetch( $hints, $relation_type ) {
		if ( 'dns-prefetch' === $relation_type ) { 
		return array_diff( wp_dependencies_unique_hosts(), $hints ); } return $hints; }
		add_filter( 'wp_resource_hints', 'remove_dns_prefetch', 10, 2 );
		
	remove_action('admin_print_scripts','print_emoji_detection_script');
	remove_action('admin_print_styles','print_emoji_styles');
	remove_action('wp_head','print_emoji_detection_script',	7);
	remove_action('wp_print_styles','print_emoji_styles');
	remove_action('embed_head','print_emoji_detection_script');
	remove_filter('the_content_feed','wp_staticize_emoji');
	remove_filter('comment_text_rss','wp_staticize_emoji');
	remove_filter('wp_mail','wp_staticize_emoji_for_email');
	remove_action( 'wp_head', 'feed_links', 2 ); //移除feed
	remove_action( 'wp_head', 'feed_links_extra', 3 ); //移除feed
	remove_action( 'wp_head', 'wp_generator' ); //移除WordPress版本
	add_filter( 'emoji_svg_url', '__return_false' ); //DNS EMOJI
	remove_action('rest_api_init', 'wp_oembed_register_route'); //EMbed
	remove_filter('rest_pre_serve_request', '_oembed_rest_pre_serve_request', 10, 4);
	remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10 );
	remove_filter('oembed_response_data',   'get_oembed_response_data_rich',  10, 4);
	remove_action('wp_head', 'wp_oembed_add_discovery_links');
	remove_action('wp_head', 'wp_oembed_add_host_js');//EMbed结束
	add_filter('xmlrpc_enabled', '__return_false');
	// add_filter('use_block_editor_for_post', '__return_false');//禁用古腾堡
	// remove_action( 'wp_enqueue_scripts', 'wp_common_block_scripts_and_styles' );//禁用古腾堡前端

	add_action('admin_menu', function (){
	global $menu, $submenu;
	// 移除设置菜单下的隐私子菜单。
	unset($submenu['options-general.php'][45]);
	// 移除工具菜单下的相关页面
	remove_action( 'admin_menu', '_wp_privacy_hook_requests_page' );
	remove_filter( 'wp_privacy_personal_data_erasure_page', 'wp_privacy_process_personal_data_erasure_page', 10, 5 );
	remove_filter( 'wp_privacy_personal_data_export_page', 'wp_privacy_process_personal_data_export_page', 10, 7 );
	remove_filter( 'wp_privacy_personal_data_export_file', 'wp_privacy_generate_personal_data_export_file', 10 );
	remove_filter( 'wp_privacy_personal_data_erased', '_wp_privacy_send_erasure_fulfillment_notification', 10 );
 
	// Privacy policy text changes check.
	remove_action( 'admin_init', array( 'WP_Privacy_Policy_Content', 'text_change_check' ), 100 );
 
	// Show a "postbox" with the text suggestions for a privacy policy.
	remove_action( 'edit_form_after_title', array( 'WP_Privacy_Policy_Content', 'notice' ) );
 
	// Add the suggested policy text from WordPress.
	remove_action( 'admin_init', array( 'WP_Privacy_Policy_Content', 'add_suggested_content' ), 1 );
 
	// Update the cached policy info when the policy page is updated.
	remove_action( 'post_updated', array( 'WP_Privacy_Policy_Content', '_policy_page_updated' ) );
},9);

//复制出提示
function zm_copyright_tips() {
	echo '<script>document.body.oncopy=function(){alert("复制成功！转载请务必保留原文链接，申明来源，谢谢合作！");}</script>';
}
add_action( 'wp_footer', 'zm_copyright_tips', 100 );
//评论添加验证码
function spam_protection_math(){
	$num1=rand(0,9);
	$num2=rand(0,9);
	echo "<label for=\"math\">人机验证:<i>$num1 + $num2 = ?</i>  </label>\n ";
	echo "<input type=\"text\" name=\"sum\" class=\"text\" value=\"\" size=\"25\" tabindex=\"4\">\n";
	echo "<input type=\"hidden\" name=\"num1\" value=\"$num1\">\n";
	echo "<input type=\"hidden\" name=\"num2\" value=\"$num2\">";
}
function spam_protection_pre($commentdata){
	$sum=$_POST['sum'];
	switch($sum){
		case $_POST['num1']+$_POST['num2']:
		break;
		case null:
		wp_die('对不起: 请输入验证码。<a href="javascript:history.back(-1)">返回上一页</a>','评论失败');
		break;
		default:
		wp_die('对不起: 验证码错误，请<a href="javascript:history.back(-1)">返回</a>重试。','评论失败');
	}
	return $commentdata;
}
if($comment_data['comment_type']==''){
	add_filter('preprocess_comment','spam_protection_pre');
}
/**
* 禁用emoji表情
*/
function disable_emojis() {
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
}
add_action( 'init', 'disable_emojis' );
/**
* Filter function used to remove the tinymce emoji plugin.
*/
function disable_emojis_tinymce( $plugins ) {
if ( is_array( $plugins ) ) {
return array_diff( $plugins, array( 'wpemoji' ) );
} else {
return array();
}}



//给外部链接加上跳转
    add_filter('the_content','the_content_nofollow',999);
    function the_content_nofollow($content)
    {
        preg_match_all('/<a(.*?)href="(.*?)"(.*?)>/',$content,$matches);
        if($matches){
            foreach($matches[2] as $val){
                if(strpos($val,'://')!==false && strpos($val,home_url())===false && !preg_match('/\.(jpg|jepg|png|ico|bmp|gif|tiff)/i',$val)){
                    $content=str_replace("href=\"$val\"", "href=\"".home_url()."/go/?url=$val\" target=\"_blank\"",$content);
                }
            }
        }
        return $content;
    }


// 上传图片重命名,日期+随机数字的方式
function rename_upload_img($file) {
    $time=date("Ymd H:i:s");
    $file['name'] = $time."".mt_rand(100,999).".".pathinfo($file['name'] , PATHINFO_EXTENSION);
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'rename_upload_img');



?>