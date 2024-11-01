<?php
// Table headers.
$columns['cb'] = '<input type="checkbox" />';
$columns['name'] =  __('name', 'wp-hyves');
$columns['username'] = 	__('username', 'wp-hyves');
$columns['email'] = 	__('email','wp-hyves');
$columns['password'] = 	__('', 'wp-hyves');
?>
<!-- wrap -->
<div class="wrap">

<?php if(function_exists('screen_icon')) { screen_icon('users'); } ?>
<h2><?php echo wp_specialchars(__('Currently Imported Friends', 'wp-hyves')); ?></h2>

<!--subsubsub menu -->
<div class="filter">
<form id="list-filter" action="" method="get">
<ul class="subsubsub">
  </ul>
</form>
</div>
<!-- end subsubsub menu -->

<!-- data table -->
<form id="posts-filter" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">	
	<!-- navigation -->
	
	<!-- end navigation -->

	<?php if ( is_wp_error( $wp_user_search->search_errors ) ) : ?>
	<!-- start error -->
		<div class="error">
			<ul>
			<?php
				//foreach ( $wp_user_search->search_errors->get_error_messages() as $message )
					//echo "<li>$message</li>";
			?>
			</ul>
		</div>
	<!-- end error -->
	<?php endif; ?>

	<!-- data -->
	<table class="widefat fixed" cellspacing="0">
		<thead>
			<tr class="thead">
				<?php include('table-headers.php'); ?>
			</tr>
		</thead>
		
		<?php $i = 0; foreach($users as $u):  ?>
		
		<tr class="<?php if($i % 2 == 0){ echo 'alternate '; } $i++; ?>author-self status-publish">
		
		<th class="check-column" scope="row">
			<input type="checkbox" value="<?php echo $u['id']; ?>" name="userids[]"/>
		</th>
	 
		<td><?php echo $u['img_html']; ?> <?php echo $u['firstname']; ?> <?php echo $u['lastname']?></td>
		
		<td><?php echo $u['username']; ?></td>
		
		<td>
			<?php echo $u['email']; if(!isset($email)) _e('none', 'wp-hyves'); ?>
		</td>
		
		<td><?php echo $u['password']; ?></td>
		
		</tr>
		
		<?php endforeach; ?>

	</table>
	<!-- end data -->

</form>
<!-- end data table -->
<div class="tablenav">
	<br class="clear"/>
</div>
</div> <!-- end wrap -->