<div class="github-embed github-embed-repository <?= $data['logo_class'] ?>">
    <p>
        <a href="<?= esc_attr( $data['repo']->html_url ) ?>" target="_blank">
			<strong>
				<?= esc_html( $data['repo']->description ) ?>
			</strong>
		</a>
		<br>
        <a href="<?= esc_attr( $data['repo']->html_url ) ?>" target="_blank"><?= esc_html( $data['repo']->html_url ) ?></a><br>
        <a href="<?= esc_attr( $data['repo']->html_url ) ?>/network" target="_blank"><?= esc_html( number_format_i18n( $data['repo']->forks_count ) ) ?></a> forks.<br>
        <a href="<?= esc_attr( $data['repo']->html_url ) ?>/stargazers" target="_blank"><?= esc_html( number_format_i18n( $data['repo']->stargazers_count ) ) ?></a> stars.<br>
        <a href="<?= esc_attr( $data['repo']->html_url ) ?>/issues" target="_blank"><?= esc_html( number_format_i18n( $data['repo']->open_issues_count ) ) ?></a> open issues.<br>
        Recent commits:
        <ul class="github_commits">
            <?php
            $i = 0;
            foreach ( $data['commits'] as $commit ) : ?>
                <li class="github_commit">
                    <a href="https://github.com/<?= $data['owner'] ?>/<?= $data['repository'] ?>/commit/<?= esc_attr( $commit->sha ) ?>" target="_blank"><?= esc_html( $commit->commit->message ) ?></a>, <?= esc_html( $commit->commit->committer->name ); ?>
                </li>
            <?php
                if (++$i == 5) break;
            endforeach;
            ?>
        </ul>
    </p>
</div>
