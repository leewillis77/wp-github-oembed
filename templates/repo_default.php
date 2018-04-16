<?php

/*
 * Default template for github repositiories
 */
// @TODO: Add microdata!
// @TODO add relative time formats for the "Updated" string
?>
<div class="github-oembed github-oembed-repository">


    <!-- Container with all the info -->
    <div class="github-oembed-container">
<!-- Avatar -->
    <a class="github-oembed-avatar-block" href="<?php echo esc_url( $repo->owner->html_url ); ?>">
        <img src="<?php echo esc_url( $repo->owner->avatar_url ); ?>" class="github-oembed-avatar rounded-1" alt="@<?php echo $owner; ?>" width="48" height="48">
    </a>
      <!-- Title -->
      <div>
        <div class="github-oembed-title-block">
          <h3>
            <a href="<?php echo $repo->html_url; ?>">
              <span class="github-oembed-title-owner"><?php echo $owner; ?></span>/<span class="github-oembed-title-repo"><?php echo $repository; ?></span>
            </a>
          </h3>
        </div>

        <?php if( $repo->parent ){ ?>
            <span class="github-oembed-forkedfrom">
              Forked from <a href="<?php echo $repo->parent->html_url?>"><?php echo $repo->parent->full_name; ?></a>
            </span>
        <?php } ?>

        <!-- Description -->
        <p class="github-oembed-description"><?php echo esc_html( $repo->description ); ?></p>

        <!-- Details -->
        <div class="github-oembed-details-block">
            <?php if( $repo->language != '' ) { ?>
              <span class="github-oembed-repo-language lang_<?php echo strtolower(esc_attr($repo->language)); ?>"></span>
              <span class="github-oembed-detail-label" ><?php echo esc_attr( $repo->language ); ?> </span>
            <?php } ?>

            <?php if( $repo->stargazers_count > 0) { ?>
            <a class="github-oembed-detail-link github-oembed-detail-label" href="<?php esc_url( $repo->stargazers_url ); ?>">
              <svg aria-label="star" class="github-oembed-detail-icon .github-oembed-detail-icon-star" viewBox="0 0 14 16" version="1.1" width="14" height="16" role="img"><path fill-rule="evenodd" d="M14 6l-4.9-.64L7 1 4.9 5.36 0 6l3.6 3.26L2.67 14 7 11.67 11.33 14l-.93-4.74z"></path></svg>
              <?php echo esc_attr( $repo->stargazers_count ); ?>
            </a>
            <?php } ?>

            <?php if( $repo->network_count > 0 ){ ?>
                <a class="github-oembed-detail-link github-oembed-detail-label" href="<?php echo esc_url( $repo->html_url); ?>/network">
                  <svg aria-label="fork" class="github-oembed-detail-icon github-oembed-detail-icon-repo-forked" viewBox="0 0 10 16" version="1.1" width="10" height="16" role="img"><path fill-rule="evenodd" d="M8 1a1.993 1.993 0 0 0-1 3.72V6L5 8 3 6V4.72A1.993 1.993 0 0 0 2 1a1.993 1.993 0 0 0-1 3.72V6.5l3 3v1.78A1.993 1.993 0 0 0 5 15a1.993 1.993 0 0 0 1-3.72V9.5l3-3V4.72A1.993 1.993 0 0 0 8 1zM2 4.2C1.34 4.2.8 3.65.8 3c0-.65.55-1.2 1.2-1.2.65 0 1.2.55 1.2 1.2 0 .65-.55 1.2-1.2 1.2zm3 10c-.66 0-1.2-.55-1.2-1.2 0-.65.55-1.2 1.2-1.2.65 0 1.2.55 1.2 1.2 0 .65-.55 1.2-1.2 1.2zm3-10c-.66 0-1.2-.55-1.2-1.2 0-.65.55-1.2 1.2-1.2.65 0 1.2.55 1.2 1.2 0 .65-.55 1.2-1.2 1.2z"></path></svg>
                    <?php echo esc_attr($repo->network_count); ?>
                </a>
            <?php } ?>

            <?php if( $repo->license->spdx_id ){ ?>
            <span class="github-oembed-detail-label">
              <svg class="github-oembed-detail-icon github-oembed-detail-icon-law" viewBox="0 0 14 16" version="1.1" width="14" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M7 4c-.83 0-1.5-.67-1.5-1.5S6.17 1 7 1s1.5.67 1.5 1.5S7.83 4 7 4zm7 6c0 1.11-.89 2-2 2h-1c-1.11 0-2-.89-2-2l2-4h-1c-.55 0-1-.45-1-1H8v8c.42 0 1 .45 1 1h1c.42 0 1 .45 1 1H3c0-.55.58-1 1-1h1c0-.55.58-1 1-1h.03L6 5H5c0 .55-.45 1-1 1H3l2 4c0 1.11-.89 2-2 2H2c-1.11 0-2-.89-2-2l2-4H1V5h3c0-.55.45-1 1-1h4c.55 0 1 .45 1 1h3v1h-1l2 4zM2.5 7L1 10h3L2.5 7zM13 10l-1.5-3-1.5 3h3z"></path></svg>
                <?php echo esc_attr( $repo->license->spdx_id ); ?>
            </span>
            <?php } ?>

            <?php if( $repo->has_issues and $repo->open_issues_count > 0 ){ ?>
            <a class="github-oembed-detail-link github-oembed-detail-label" href="<?php echo esc_url( $repo->issues_url ); ?>">
                <?php if ($repo->open_issues_count == 1 ){ ?>
                    1 issue needs help
                <?php } else { ?>
                    <?php echo esc_attr( $repo->open_issues_count ); ?> issues need help
                <?php } ?>
             </a>
            <?php } ?>

            Updated on <?php echo date( "j M Y", strtotime( esc_attr( $repo->updated_at ) ) ); ?>
        </div>
      </div>
    </div>
</div>
