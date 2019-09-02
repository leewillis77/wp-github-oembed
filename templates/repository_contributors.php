<div class="github-embed github-embed-repo-contributors <?php echo $data['logo_class'] ?>">
	<p>
		<a href="<?php echo esc_attr( $data['repo']->html_url ) ?>" target="_blank">
			<strong>
				<?php echo esc_html( $data['repo']->description ) ?>
			</strong>
		</a>
		<br>
		<span class="github-heading">Contributors: </span>
		<ul class="github-repo-contributors">
			<?php foreach ( $data['contributors'] as $contributor ) : ?>
				<li class="github-repo-contributor">
					<img class="github-repo-contributor-avatar"
						src="<?php echo esc_url( add_query_arg( array( 's' => $data['gravatar_size'] ), $contributor->author->avatar_url ) ); ?>"
						alt="Picture of <?php echo esc_attr( $contributor->author->login ) ?>">
					<span class="github-repo-contributor-login">
						<a href="https://github.com/<?php echo esc_attr( $contributor->author->login ) ?>">
							<?php echo esc_attr( $contributor->author->login ) ?>
						</a>
					</span>
			<?php endforeach; ?>
		</ul>
		<div style="clear: both;"></div>
	</p>
</div>
