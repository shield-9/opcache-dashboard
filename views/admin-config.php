		<div class="wrap">
			<h2><?php _e('OPcache Configurations', 'opcache'); ?></h2>
			<form method="get">
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
				<?php $data->display() ?>
			</form>
		</div><!-- wrap -->
