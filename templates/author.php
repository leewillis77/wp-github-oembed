<div class="github-embed github-embed-user <?php echo $data['logo_class'] ?>">
	<p>
		<a href="https://github.com/<?php echo esc_attr( $data['owner'] ) ?>" target="_blank">
			<strong>
				<?php echo esc_html( $data['owner'] ) ?>
			</strong>
		</a>
		<br>
		<?php echo esc_html( number_format_i18n( $data['owner_info']->public_repos ) ) ?> repositories, <?php echo esc_html( number_format_i18n( $data['owner_info']->followers ) ) ?> followers.
	</p>
</div>
