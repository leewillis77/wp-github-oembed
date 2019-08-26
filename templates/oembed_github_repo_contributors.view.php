<div class="github-embed github-embed-repo-contributors <?= $data['logo_class'] ?>">
	<p>
		<a href="<?= esc_attr( $data['repo']->html_url ) ?>" target="_blank">
			<strong>
				<?= esc_html( $data['repo']->description ) ?>
			</strong>
		</a>
		<br>
		<span class="github-heading">Contributors: </span>
		<ul class="github-repo-contributors">
			<?php foreach ( $data['contributors'] as $contributor ) : ?>
				<li class="github-repo-contributor">
					<img class="github-repo-contributor-avatar"
						src="<?= esc_url( add_query_arg( array( 's' => $data['gravatar_size'] ), $contributor->author->avatar_url ) ); ?>"
						alt="Picture of <?= esc_attr( $contributor->author->login ) ?>">
					<span class="github-repo-contributor-login">
						<a href="https://github.com/<?= esc_attr( $contributor->author->login ) ?>">
							<?= esc_attr( $contributor->author->login ) ?>
						</a>
					</span>
			<?php endforeach; ?>
		</ul>
		<div style="clear: both;"></div>
	</p>
</div>
