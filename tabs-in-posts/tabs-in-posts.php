<?php
/*
Plugin Name: Images & Video Tabs in Posts
Description: Simple output images or video tabs in posts.
Plugin URI:  https://github.com/vankovski/WP-I-V-Tabs-in-Posts
Author URI:  https://kwork.ru/user/vankovski
Version: 0.1
Author: vankovski
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/


// регистрируем стили
add_action( 'wp_enqueue_scripts', 'register_plugin_styles' );
function register_plugin_styles() {
    wp_register_style( 'tabs-in-posts', plugins_url( 'tabs-in-posts/css/style.css' ) );
    wp_enqueue_style( 'tabs-in-posts' );
    wp_register_style( 'slick-gallery', plugins_url( 'tabs-in-posts/css/slick.css' ) );
    wp_enqueue_style( 'slick-gallery' );
    wp_register_style( 'slick-theme', plugins_url( 'tabs-in-posts/css/slick-theme.css' ) );
    wp_enqueue_style( 'slick-theme' );
}

// регистрируем скрипт
add_action( 'wp_enqueue_scripts', 'my_scripts_method' );
function my_scripts_method(){
    wp_enqueue_script( 'slick-gallery', plugins_url('tabs-in-posts/js/slick.min.js'), array('jquery'), null, true );
    wp_enqueue_script( 'tabs-in-posts', plugins_url('tabs-in-posts/js/tabs-in-posts.js'), array('jquery'), null, true );
}

add_filter( 'the_content', 'add_tabs_to_post_content' );
function add_tabs_to_post_content( $content ) {
    if(!is_single()) return $content; // Обработчик работает только в таксономии posts
    
    // Получаем значения полей
    $post_id = get_the_ID();
    $enable_tabs = get_post_meta( $post_id, 'enable_tabs', true ); 
    $videourl_tabs = get_post_meta( $post_id, 'videourl_tabs', true );

    function getYoutubeEmbedUrl($url){  // Функция конвертации адреса YouTube
        $shortUrlRegex = '/youtu.be\/([a-zA-Z0-9_]+)\??/i';
        $longUrlRegex = '/youtube.com\/((?:embed)|(?:watch))((?:\?v\=)|(?:\/))(\w+)/i';
        if (preg_match($longUrlRegex, $url, $matches)) {
            $youtube_id = $matches[count($matches) - 1];
        }
        if (preg_match($shortUrlRegex, $url, $matches)) {
            $youtube_id = $matches[count($matches) - 1];
        }
    return 'https://www.youtube.com/embed/' . $youtube_id ;
    }

    $videourl_tabs = getYoutubeEmbedUrl($videourl_tabs); // Получаем oembed url видео с YouTube
    $images_tabs = get_post_meta( $post_id, 'images_tabs', false ); // Получаем массив картинок
    $paragraph_tabs = get_post_meta( $post_id, 'paragraph_tabs', true ); 
    

    if ($enable_tabs) {
        $paragraphAfter = $paragraph_tabs; // После какого параграфа вставляем таб
        $content = explode ( "</p>", $content );
        $new_content = '';
        for ( $i = 0; $i < count ( $content ); $i ++ ) {
            if ( $i == $paragraphAfter ) {
                $new_content .= '
    <div class="tabs">
  
  <ul class="tabs__caption">
    <li class="active">Скриншоты</li>
    <li>Видео</li>
  </ul>
  
  <div class="tabs__content active">
    <div class="tabs__gallery">';
        if ( $images_tabs ) {
            foreach ( $images_tabs as $image ) {
            $class = "post-attachment mime-" . sanitize_title( $image->post_mime_type );
            $thumburl = wp_get_attachment_image_url( $image['ID'], 'full');
            $size = getimagesize($thumburl);                                            
            if ($size[0]>$size[1]) {
                $thumbimg = wp_get_attachment_image( $image['ID'], 'large');
                $imgclass='horisontal';
            }
            else { 
                $thumbimg = wp_get_attachment_image( $image['ID'], 'medium');
                $imgclass = 'vertical'; 
            }
            $new_content .= '<div style="padding:5px;" class="' . $class . ' data-design-thumbnail"><a class="'.$imgclass.'" href="'.$thumburl.'">'. $thumbimg.'</a></div>';
    }
}
    $new_content .= '</div>
  </div>
  
  <div class="tabs__content">
    <div class="youtube">
<iframe width="560" height="315" src="'.$videourl_tabs.'" frameborder="0" allowfullscreen></iframe>
</div>
  </div>
  
</div><!-- .tabs-->';    
            }
            $new_content .= $content[$i] . "</p>";
        }
        return $new_content;
    }

    else return $content;
}
?>