<?php                                                                                                                                                                               
/*                                                                                                                                                                                  
Plugin Name: Lazy Content Slider                                                                                                                                                    
Plugin URI: http://mysqlhow2.com/                                                                                                                                                   
Description: This is a content slider that shows 5 slides from a "Featured Category"                                                                                                
Author: Lee Thompson                                                                                                                                                                
Version: 3.4
Author URI: http://mysqlhow2.com                                                                                                                                                    
                                                                                                                                                                                    
Copyright 2012  Lee Thompson (email : sr.mysql.dba@gmail.com)                                                                                                                       
                                                                                                                                                                                    
    This program is free software; you can redistribute it and/or modify                                                                                                            
    it under the terms of the GNU General Public License, version 2, as                                                                                                             
    published by the Free Software Foundation.                                                                                                                                      
                                                                                                                                                                                    
    This program is distributed in the hope that it will be useful,                                                                                                                 
    but WITHOUT ANY WARRANTY; without even the implied warranty of                                                                                                                  
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                                                                                                                   
    GNU General Public License for more details.                                                                                                                                    

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
//add_action( 'admin_enqueue_scripts', 'add_jscss' );
add_action('admin_menu', 'lzcs_add_admin_menu');
add_action('wp_enqueue_scripts', 'add_jscss');
register_activation_hook(__FILE__, 'lzcs_init');
register_deactivation_hook(__FILE__, 'lzcs_deactivate');

//set up lzcs plugin
function add_jscss() {
    wp_deregister_script( 'jquery-min' );
    wp_register_script( 'jquery-min', 'http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
    wp_enqueue_script( 'jquery-min' );
    wp_deregister_script( 'jquery-ui' );
    wp_register_script( 'jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/jquery-ui.min.js');
    wp_enqueue_script( 'jquery-ui' );
    $lzcscolor = get_option('lzcs_color');
    if ($lzcscolor == "light" ) {
    wp_deregister_style( 'lazyslider');
    wp_register_style( 'lazyslider', plugins_url('/css/style.css', __FILE__) );
    wp_enqueue_style( 'lazyslider' );
    } else {
    wp_deregister_style( 'lazyslider');
    wp_register_style( 'lazyslider', plugins_url('/css/style-dark.css', __FILE__) );
    wp_enqueue_style( 'lazyslider' );
    }
    wp_deregister_script( 'jquery-tab' );
    wp_register_script( 'jquery-tab', plugins_url('/js/tabs.js', __FILE__) );
    wp_enqueue_script( 'jquery-tab' );
    wp_deregister_script( 'lazyslider');
    wp_register_script( 'lazyslider', plugins_url('/js/slider.js', __FILE__) );
    wp_enqueue_script( 'lazyslider' );
}

function lzcs_add_admin_menu() {
    add_submenu_page('options-general.php', ' Lazy Content Slider', 'Lazy Content Slider', 8, __FILE__, 'lzcs_admin_menu');
}

function lzcs_Plugin_Links($links, $file) {
    $plugin = plugin_basename(__FILE__);
    if ($file == $plugin) {
        $links[] = '<a href="options-general.php?page='.$plugin.'">' . __('Settings') . '</a>';
        $links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=25LDNVSUTHKAJ" target="_blank">' . __('Donate to this plugin') . '</a>';
    }
        return $links;
}

function lzcs_init() {
    if(!get_option('lzcs_cat')) {
        add_option('lzcs_cat');
        update_option('lzcs_cat','lzcs');
    }
        add_filter('plugin_row_meta', 'lzcs_Plugin_Links',10,2);
	add_option('lzcs_count');
}
function lzcs_deactivate() {
    delete_option('lzcs_cat');
    delete_option('lzcs_color');
    delete_option('lzcs_count');
}

function lzcs_admin_menu() {
    include('lzcs_admin.php');
}

function string_limit_words($string, $word_limit)
{
  $words = explode(' ', $string, ($word_limit + 1));
  if(count($words) > $word_limit)
  array_pop($words);
  return implode(' ', $words);
}


function getDisplayPosts()
{
	$limit = get_option('lzcs_count')-1;
    $posts = get_option('lzcs_cat');
    $args = array('category' => $posts );
    $recent_posts = wp_get_recent_posts( $args );

    $output = array();
    foreach ($recent_posts as $post) {
        if (has_post_thumbnail($post['ID'])) {
            $output[] = $post;
        }

        if (count($output) > $limit) break;
    }

    return $output;
}

function drawslider() {
    global $post;
    $recent_posts = getDisplayPosts();

    if (count($recent_posts) == 0) {
        return;
    }

    echo "<div id=\"featured\" >";
    echo "<ul class=\"ui-tabs-nav\">";
    foreach( $recent_posts as $recent ){
        $postid = $recent["ID"];
        $thumbnail =  get_the_post_thumbnail($postid, array(50,50) );
?>
        <li class="ui-tabs-nav-item " id="nav-fragment-<?php echo $recent["ID"] ?> ">
            <a href="#fragment-<?php echo $recent["ID"]; ?>"><?php echo $thumbnail ?><span><?php echo esc_attr($recent["post_title"]); ?></span></a>
        </li>
<?php
    }

    echo '</ul>';   // Closing the div.ui-tabs-nav
    

    foreach( $recent_posts as $recent ){
        $postid = $recent["ID"];
        $postexcerpt = $recent["post_content"];
        $postexcerpt = preg_replace ( "'<[^>]+>'U", "", $postexcerpt);
        $postexcerpt = string_limit_words($postexcerpt,15);

        $largeimage = get_the_post_thumbnail($postid, array(400,250));
?>                      
        <div id="fragment-<?php echo $recent["ID"] ?>" class="ui-tabs-panel" >
            <?php echo $largeimage ?>
            <div class="info" >
                    <h2><a href="<?php echo get_permalink($recent["ID"]) ?>" ><?php echo esc_attr($recent["post_title"]); ?></a></h2>
                    <p><?php echo $postexcerpt; ?><a href="<?php echo get_permalink($recent["ID"]) ?>" ><strong> read more</strong></a></p>
            </div>
        </div>
<?php
    }

    echo '</div>'; // Closing the div#featured
}
add_shortcode('lazyslider', 'drawslider');
