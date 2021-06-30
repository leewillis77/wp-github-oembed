<div class="github-embed github-embed-repository <?php echo $data['logo_class'] ?>">
    <p>
        <a href="<?php echo esc_attr( $data['repo']->html_url ) ?>" target="_blank">
			<strong>
				<?php echo esc_html( $data['repo']->description ) ?>
			</strong>
		</a>
		<br>
        <a href="<?php echo esc_attr( $data['repo']->html_url ) ?>" target="_blank"><?php echo esc_html( $data['repo']->html_url ) ?></a><br>
        <a href="<?php echo esc_attr( $data['repo']->html_url ) ?>/network" target="_blank"><?php echo esc_html( number_format_i18n( $data['repo']->forks_count ) ) ?></a> forks.<br>
        <a href="<?php echo esc_attr( $data['repo']->html_url ) ?>/stargazers" target="_blank"><?php echo esc_html( number_format_i18n( $data['repo']->stargazers_count ) ) ?></a> stars.<br>
        <a href="<?php echo esc_attr( $data['repo']->html_url ) ?>/issues" target="_blank"><?php echo esc_html( number_format_i18n( $data['repo']->open_issues_count ) ) ?></a> open issues.<br>
        <details <?php echo $data['details_expanded'] ? 'open' : ''; ?>>
            <summary>Recent commits:</summary>
            <ul class="github_commits">
                <?php
                $i = 0;
                foreach ( $data['commits'] as $commit ) : ?>
                    <li class="github_commit">
                        <a href="https://github.com/<?php echo $data['owner_slug'] ?>/<?php echo $data['repo_slug'] ?>/commit/<?php echo esc_attr( $commit->sha ) ?>" target="_blank"><?php echo esc_html( $commit->commit->message ) ?></a>, <?php echo esc_html( $commit->commit->committer->name ); ?>
                    </li>
                <?php
                    if (++$i == 5) break;
                endforeach;
                ?>
            </ul>
        </details>
    </p>
</div>
