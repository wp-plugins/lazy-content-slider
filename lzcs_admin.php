<?php
/*
Copyright 2010  Lee Thompson (email : sr.mysql.dba@gmail.com) 

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

function register_lzcssettings() {
        register_setting( 'lzcs_cat', 'lzcs' );
	register_setting( 'lzcs_color', 'lzcs' );
	register_setting( 'lzcs_count', 'lzcs' );
}

function draw_form(){
	$myvariable = get_option('lzcs_cat');
	$lzcscolor = get_option('lzcs_color');
	$lzcscount = get_option('lzcs_count');
	if ($lzcscolor == "dark"){
	$selected = "checked";
	}else{
	$selected_default = "checked";
	}
?>
<div class="wrap">
<h2>Lazy Content Slider Options</h2>
What category do you want to use.<br>
<form method="post" action="<?php echo $PHP_SELF; ?>">
<?php settings_fields( 'lzcs_cat' ); ?>    
    <table class="form-table">
        <tr valign="top">
<?php $curcat = get_option('lzcs_cat'); ?>
	<tr><td>Select new category</td>
        <td><?php wp_dropdown_categories(array('hide_empty' => 0, 'name' => 'lzcs', 'hierarchical' => true, 'echo' => 1, 'selected' => $curcat)); ?>
	</td>
        </tr>
	<tr>
	  <td>Select Color</td>
	  <td>
		<Input type = 'Radio' Name ='lzcs_color' value= 'light' <?php echo $selected_default ?>> Light

		<Input type = 'Radio' Name ='lzcs_color' value= 'dark' <?php echo $selected ?>> Dark

	</tr>
	<tr>
	  <td>How many articles to display</td>
		<td>
		<select name ="lzcs_count">
  			<option value="1" <?php if (get_option('lzcs_count') == 1) { echo "selected = selected"; } ?>>One</option>
			<option value="2" <?php if (get_option('lzcs_count') == 2) { echo "selected = selected"; } ?>>Two</option>
			<option value="3" <?php if (get_option('lzcs_count') == 3) { echo "selected = selected"; } ?>>Three</option>
			<option value="4" <?php if (get_option('lzcs_count') == 4) { echo "selected = selected"; } ?>>Four</option>
			<option value="5" <?php if (get_option('lzcs_count') == 5) { echo "selected = selected"; } ?>>Five</option>
		</select> 
		</td>
    </table>

    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
</div>
<br><br>
<div class="wrap">
<strong>How to use</strong><br>

This is easy to do you have 2 choices.</br>
1. Add the shortcut code <strong>[lazyslider]</strong> to any page or post.</br>
2. Add this <strong>&lt;&#63;php if (function_exists("drawslider")){ drawslider(); }; &#63;&gt;</strong>  to index.php or any page where you want to use the slider.</br>
</div><br>

<div class="wrap">
Donations are accepted for continued development of Lazy content Slider. Thank you.<br>
<script type="text/javascript">
        <!--
        document.write(unescape("%3Ca%20href%20%3D%20%22https%3A//www.paypal.com/cgi-bin/webscr%3Fcmd%3D_s-xclick%26hosted_button_id%3D25LDNVSUTHKAJ%22%20target%20%3D%20%22_blank%22%3E%3Cimg%20src%3D%22https%3A//www.paypal.com/en_US/i/btn/btn_donate_SM.gif%22%20border%3D%220%22%20name%3D%22submit%22%20alt%3D%22PayPal%20-%20The%20safer%2C%20easier%20way%20to%20pay%20online%21%22%3E%3C/a%3E%0A"));
        //-->
        </script>
</div>

<?php
}

if(isset($_POST['lzcs']))
{
        echo "<div class=\"updated\">Settings have been updated.</div>";
        $myvariable=$_POST["lzcs"];
	$lzcscolor=$_POST["lzcs_color"];
	$lzcscount=$_POST["lzcs_count"];	
	update_option('lzcs_cat', $myvariable);
	update_option('lzcs_color', $lzcscolor);
	update_option('lzcs_count', $lzcscount);
	draw_form();
}else{
	draw_form();
}
?>
