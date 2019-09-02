<div class="github-embed github-embed-milestone-summary <?php echo $data['logo_class'] ?>">
	<p>
		<a href="<?php echo esc_attr( $data['repo']->html_url ) ?>" target="_blank">
			<strong>
				<?php echo esc_html( $data['repo']->description ) ?>
			</strong>
		</a>
		<br>
		<span class="github-heading">Milestone: </span>
		<span class="github-milestone-title"><?php echo esc_html( $data['summary']->title ) ?></span>
		<br>
		<span class="github-heading">Issues: </span>
		<span class="github-milestone-issues">
			<?php echo esc_html( number_format_i18n( $data['summary']->open_issues ) ) ?> open, <?php echo esc_html( number_format_i18n( $data['summary']->closed_issues ) ) ?> closed.
		</span>
		<br>
		<?php if ( ! empty( $data['summary']->due_on ) ) : ?>
			<span class="github-heading">Due: </span>
			<span class="github-milestone-due-date">
				<?php echo esc_html( date_format( date_create( $data['summary']->due_on ), 'jS F Y' ) ) ?>
			</span>
			<br>
		<?php endif ?>
		<p class="github-milestone-description">
			<?php echo nl2br( esc_html( $data['summary']->description ) ) ?>
		</p>
		<br>
	</p>
</div>
