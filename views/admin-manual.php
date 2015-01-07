		<div class="wrap">
			<h2><?php _e('OPcache Manual Cache Control', 'opcache'); ?></h2>
			<h3><?php _e('Compile File', 'opcache'); ?></h3>
			<form method="post">
				<input name="action" type="hidden" value="compile" />
				<?php wp_nonce_field('opcache_ctrl'); ?>
				<table class="form-table">
					<tr class="form-field form-required">
						<th scope="row"><label for="compile-file">File Path to compile</label></th>
						<td><input name="file" type="text" id="compile-file" /></td>
				</table>
				<p class="submit"><input type="submit" name="compile" class="button button-primary" value="Compile" />
			</form>
			<h3><?php _e('Invalidate File', 'opcache'); ?></h3>
			<form method="post">
				<input name="action" type="hidden" value="invalidate" />
				<?php wp_nonce_field('opcache_ctrl'); ?>
				<table class="form-table">
					<tr class="form-field form-required">
						<th scope="row"><label for="invalidate-file">File Path to invalidate</label></th>
						<td><input name="file" type="text" id="invalidate-file" /></td>
					<tr>
						<th scope="row"><label for="invalidate-force">Force Invalidate</label></th>
						<td>
							<label>
								<input type="checkbox" name="force" id="invalidate-force" checked>
								The script will be invalidated regardless of whether invalidation is necessary.
							</label>
						</td>
				</table>
				<p class="submit"><input type="submit" name="invalidate" class="button button-primary" value="Invalidate" />
			</form>
		</div><!-- wrap -->
