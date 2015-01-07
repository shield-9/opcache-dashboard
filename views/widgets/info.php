			<div class="info-widget">
				<h4><?php esc_html_e('Copyright', 'opcache'); ?></h4>
				<p>
					&copy;2012-2014 <a href="http://www.extendwings.com/" target="_blank">Daisuke Takahashi(Extend Wings)</a>
					Portions &copy;2010-2012 Web Online.
				<p>
					<?php printf(
						esc_html__('This software is licensed under %s.', 'opcache'),
						sprintf(
							'<a href="%1$s"><img id="agpl-logo" src="%2$s" alt="GNU AFFERO GENERAL PUBLIC LICENSE, Version 3"></a>',
							esc_url( OPcache_dashboard::$PLUGIN_URL . 'LICENSE'),
							esc_url( OPcache_dashboard::$PLUGIN_URL . 'images/agpl.svg')
						)
					); ?>
			</div>
			<div class="info-widget">
				<h4><?php esc_html_e('Contact', 'opcache'); ?></h4>
				<div>
					<?php esc_html_e('If you want to contact Daisuke Takahashi(Extend Wings), you can use:', 'opcache'); ?>
					<ul class="contact-list">
						<li>
							<?php printf(
								'<a href="%1$s" target="_blank">%2$s</a> %3$s',
								'https://wordpress.org/support/plugin/opcache',
								esc_html__('Plugin Support Forum', 'opcache'),
								esc_html__('(This forum is visible for everyone.)', 'opcache')
							); ?>
						<li>
							<?php printf(
								esc_html__('%1$sFor Confidential information%2$s, %3$s or %4$s is recommended due to security considerations.', 'opcache'),
								'<strong>',
								'</strong>',
								sprintf(
									'<a href="https://plus.google.com/+DaisukeTakahashi0120" target="_blank">%s</a>',
									esc_html__('Google Hangouts (Message)', 'opcache')
								),
								sprintf(
									'<a href="https://www.facebook.com/messages/daisuke.takahashi.0120" target="_blank">%s</a>',
									esc_html__('Facebook Message', 'opcache')
								)
							); ?>
					</ul>
				</div>
			</div>
			<div class="info-widget">
				<h4>
					<span class="genericon genericon-github"></span>
					<img id="github-logo" alt="GitHub Logo" src="<?php echo esc_url( OPcache_dashboard::$PLUGIN_URL . 'images/github.svg'); ?>">
				</h4>
				<p>
					<iframe class="github-button" seamless src="<?php echo esc_url( OPcache_dashboard::$PLUGIN_URL . 'github-btn.html?user=shield-9&repo=opcache-dashboard&type=watch&count=true'); ?>" style="width: 85px;"></iframe>
					<iframe class="github-button" seamless src="<?php echo esc_url( OPcache_dashboard::$PLUGIN_URL . 'github-btn.html?user=shield-9&repo=opcache-dashboard&type=fork&count=true'); ?>" style="width: 85px;"></iframe>
					<iframe class="github-button" seamless src="<?php echo esc_url( OPcache_dashboard::$PLUGIN_URL . 'github-btn.html?user=shield-9&type=follow'); ?>" style="width: 135px;"></iframe>
			</div>
			<div class="info-widget">
				<h4><?php esc_html_e('Feedback', 'opcache'); ?></h4>
				<p>
					<?php printf(
						'We are waiting for your feedback at %1$sPlugin Review%2$s.',
						'<a href="https://wordpress.org/support/view/plugin-reviews/opcache" target="_blank">',
						'</a>'
					); ?>
			</div>
