<?php                                                                                                                                                                               
/*                                                                                                                                                                                  
Plugin Name: Lazy Content Slider                                                                                                                                                    
Plugin URI: http://mysqlhow2.com/                                                                                                                                                   
Description: This is a content slider that shows 5 slides from a "Featured Category"                                                                                                
Author: Lee Thompson                                                                                                                                                                
Version: 1.2
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

add_action('admin_menu', 'lzcs_add_admin_menu');
add_action('wp_enqueue_scripts', 'add_jscss');
register_activation_hook(__FILE__, 'lzcs_init');
register_deactivation_hook(__FILE__, 'lzcs_deactivate');

//set up lzcs plugin
function add_jscss() {
    wp_deregister_script( 'jquery-min' );
    wp_register_script( 'jquery-min', 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js');
    wp_enqueue_script( 'jquery-min' );
    wp_deregister_script( 'jquery-ui' );
    wp_register_script( 'jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/jquery-ui.min.js');
    wp_enqueue_script( 'jquery-ui' );
    wp_deregister_style( 'lazyslider');
    wp_register_style( 'lazyslider', plugins_url('/css/style.css', __FILE__) );
    wp_enqueue_style( 'lazyslider' );
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
}

function lzcs_deactivate() {
    delete_option('lzcs_cat');
}

function lzcs_admin_menu() {
    include('lzcs_admin.php');
}

function drawslider() {
    echo "<div id=\"featured\" >";
    echo "<ul class=\"ui-tabs-nav\">";
    $posts = get_option('lzcs_cat');
    $args = array('category' => $posts );
    $recent_posts = wp_get_recent_posts( $args );
    
    $limit = 4;
    $displayedPosts = 0;
    
    foreach( $recent_posts as $recent ){
        $postid = $recent["ID"];
        if (!has_post_thumbnail($postid)) {
                continue;
        }
        
        if (++$displayedPosts > $limit) break;
        
        $thumbnail =  get_the_post_thumbnail( $postid, array(50, 50) );
        
?>
        <li class="ui-tabs-nav-item ui-tabs-selected" id="nav-fragment-<?php echo $recent["ID"] ?> ">
            <a href="#fragment-<?php echo $recent["ID"]; ?>">
                    <?php echo $thumbnail ?>
                    <span><?php echo esc_attr($recent["post_title"]); ?></span>
            </a>
        </li>
<?php
    }

    echo '</ul>';   // Closing the div.ui-tabs-nav
    
    $displayedPosts = 0;
            
    foreach( $recent_posts as $recent ){
        $postid = $recent["ID"];
        if (!has_post_thumbnail($postid)) {
                continue;
        }
        
        if (++$displayedPosts > $limit) break;
        
        $largeimage = get_the_post_thumbnail( $postid, array(400, 250));
?>                      
        <div id="fragment-<?php echo $recent["ID"] ?>" class="ui-tabs-panel" >
            <?php echo $largeimage ?>
            <div class="info" >
                    <h2><a href="<?php echo get_permalink($recent["ID"]) ?>" ><?php echo esc_attr($recent["post_title"]); ?></a></h2>
                    <p><a href="<?php echo get_permalink($recent["ID"]) ?>" >read more</a></p>
            </div>
        </div>
<?php
    }

    echo '</div>'; // Closing the div#featured
}
add_shortcode('lazyslider', 'drawslider');

