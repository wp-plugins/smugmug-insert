<?php

echo '<div id="navigation" style="display: none;"></div>'."\r\n";
echo '<div id="images"></div>'."\r\n";
echo '<div id="accounts">'."\r\n";

$db_options = get_option('smugins_option');
for ($i=1; $i<=5; $i++){
	if (isset($db_options['user_id_'.$i]) AND !empty($db_options['user_id_'.$i])){
		echo "<a class='user_link' href='javascript:void(0);' onClick=\"getAlbumsByUser('".$db_options['user_id_'.$i]."')\">".$db_options['user_id_'.$i]."</a>"."\r\n";
	}
}
echo '</div>'."\r\n";

$feedUrl = SMUGINS_PLUGIN_URL."/pages/feedReader.php";
$post_id = isset($_REQUEST['post_id'])?$_REQUEST['post_id']:0;

echo "<script type='text/javascript'>"."\r\n";
echo "//<![CDATA["."\r\n";
echo "jQuery('#accounts').data('postId', ".$post_id.");"."\r\n";
echo "jQuery('#accounts').data('cssClass', '".$db_options['css_class']."');"."\r\n";
echo "jQuery('#accounts').data('feedUrl', '".$feedUrl."');"."\r\n";
echo "jQuery('#accounts').data('fullSize', '".$db_options['full_size']."');"."\r\n";
echo "jQuery('#accounts').data('thumbSize', '".$db_options['thumbnail_size']."');"."\r\n";
echo "//]]>"."\r\n";
echo "</script>"."\r\n";
?>