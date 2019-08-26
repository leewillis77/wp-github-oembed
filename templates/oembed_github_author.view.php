<div class="github-embed github-embed-user <?= $data['logo_class'] ?>">
	<p>
		<a href="https://github.com/<?= esc_attr( $data['owner'] ) ?>" target="_blank">
			<strong>
				<?= esc_html( $data['owner'] ) ?>
			</strong>
		</a>
		<br>
		<?= esc_html( number_format_i18n( $data['owner_info']->public_repos ) ) ?> repositories, <?= esc_html( number_format_i18n( $data['owner_info']->followers ) ) ?> followers.
	</p>
</div>
