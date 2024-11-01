<?php
/**
 * Table headers.
 * @author dligthart
 */

$hidden = array();

foreach ( $columns as $column_key => $column_display_name ) {
	$class = ' class="manage-column';
	$class .= " column-$column_key";

	if ( 'cb' == $column_key ) {
		$class .= ' check-column';
	}
	elseif ( in_array($column_key, array('posts', 'comments', 'links')) ){
		$class .= ' num';
	}
	$class .= '"';

	$style = '';
	if ( in_array($column_key, $hidden) )
		$style = 'display:none;';

	if ( isset($styles[$type]) && isset($styles[$type][$column_key]) ){
		$style .= ' ' . $styles[$type][$column_key];
	}
	$style = ' style="' . $style . '"';
?>
	<th scope="col" <?php echo $id ? "id=\"$column_key\"" : ""; echo $class; echo $style; ?>><?php echo $column_display_name; ?></th>
<?php
}
?>