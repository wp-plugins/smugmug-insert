<?php
require_once(SMUGINS_PLUGIN_DIR.'/functions/function.inc.php');
require_once(SMUGINS_PLUGIN_DIR.'/classes/class.admin.php');
$admin = new SMUGINS_Admin();
if ( isset($_POST['stage']) AND 'process' == $_POST['stage'] ) {
	$admin->update_options($_POST);
	$message ="Settings saved.";
}
$settings = $admin->option_array;
?>
<div class="wrap">
	<div class="icon32 admin_settings_icon"><br></div>
	<h2><?php _e('Smugmug Insert', 'smugins') ?></h2>
	<?php
	if (isset($message) and strlen($message)) {
		echo '<div id="message" class="updated fade"><p><strong>'.$message.'</strong></p></div>';
		unset($message);
	}
	?>
<form method="post" action="">
	<input type="hidden" name="stage" value="process" />
	<table width="100%" cellspacing="2" cellpadding="5" class="form-table">
                    <tr valign="baseline">
                        <th scope="row" colspan="2">
                            <h3><?php _e('User properties', 'smugins') ?></h3>
                        </th>
                    </tr>
                    <?php
		for ($i=1; $i<=5; $i++){
		?>
		<tr valign="baseline">
			<th scope="row"><?php _e('Smugmug user name', 'smugins') ?> <?php print $i; ?>:</th>
			<td>
			<input type='text' class='form_element' name="user_id_<?php print $i?>" value="<?php print $settings['user_id_'.$i]?>" />
			</td>
		</tr>
		<?php
		}
		?>
		<tr valign="baseline">
			<th scope="row"><?php _e('Default photo thumbnail size', 'smugins') ?>:</th>
			<td>
			<select name='thumbnail_size' class='form_element'>
			<?php
			foreach($admin->image_sizes as $key => $val){
				$selected = '';
				if ($key == $settings['thumbnail_size']){
					$selected = "selected='selected'";
				}
				echo "<option value='$key' $selected>";
				echo $val;
				echo "</option>\n";
			}
			?>
			</select>
                <span class="description">
                    Default photo thumnail size.
                </span>
			</td>
		</tr>
		<tr valign="baseline">
			<th scope="row"><?php _e('Default photo full size', 'smugins') ?>:</th>
			<td>
			<select name='full_size' class='form_element'>
			<?php
			foreach($admin->image_sizes as $key => $val){
				$selected = '';
				if ($key == $settings['full_size']){
					$selected = "selected='selected'";
				}
				echo "<option value='$key' $selected>";
				echo $val;
				echo "</option>\n";
			}
			?>
			</select>
                <span class="description">
                    Default photo full size.
                </span>
			</td>
		</tr>
		<tr valign="baseline">
			<th scope="row"><?php _e('CSS Class', 'smugins') ?>:</th>
			<td>
				<input type='text' class='form_element' name="css_class" value="<?php print $settings['css_class']?>" />
					<span class="description">
						You can define default class for images from theme's style.css
					</span>
			</td>
		</tr>
	</table>
	<p class="submit">
		<input type="submit" name="Submit" value="<?php _e('Save Changes', 'smugins') ?>" class="button-primary"/>
	</p>
</form>
</div>