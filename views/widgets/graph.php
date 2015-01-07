			<form id="graph_ctrl">
				<label><input type="radio" name="dataset" value="memory" checked><?php esc_html_e('Memory', 'opcache'); ?></label>
				<label><input type="radio" name="dataset" value="keys"><?php esc_html_e('Keys', 'opcache'); ?></label>
				<label><input type="radio" name="dataset" value="hits"><?php esc_html_e('Hits', 'opcache'); ?></label>
			</form>
			<div id="graph">
				<div id="stats"></div>
			</div>
