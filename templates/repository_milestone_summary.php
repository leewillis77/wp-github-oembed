<div class="github-embed github-embed-milestone-summary <?= $data['logo_class'] ?>">
	<p>
		<a href="<?= esc_attr( $data['repo']->html_url ) ?>" target="_blank">
			<strong>
				<?= esc_html( $data['repo']->description ) ?>
			</strong>
		</a>
		<br>
		<span class="github-heading">Milestone: </span>
		<span class="github-milestone-title"><?= esc_html( $data['summary']->title ) ?></span>
		<br>
		<span class="github-heading">Issues: </span>
		<span class="github-milestone-issues">
			<?= esc_html( number_format_i18n( $data['summary']->open_issues ) ) ?> open, <?= esc_html( number_format_i18n( $data['summary']->closed_issues ) ) ?> closed.
		</span>
		<br>
		<?php if ( ! empty( $data['summary']->due_on ) ) : ?>
			<span class="github-heading">Due: </span>
			<span class="github-milestone-due-date">
				<?= esc_html( date_format( date_create( $data['summary']->due_on ), 'jS F Y' ) ) ?>
			</span>
			<br>
		<?php endif ?>
		<p class="github-milestone-description">
			<?= nl2br( esc_html( $data['summary']->description ) ) ?>
		</p>
		<br>
	</p>
</div>
