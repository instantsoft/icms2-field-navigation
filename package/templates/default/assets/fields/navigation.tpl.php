<div class="ctype_navigation">
    <?php if($previous){ ?>
        <div class="previous_ctype_navigation">
            <a href="<?php echo href_to($ctype['name'], $previous['slug'].'.html').($field->filter_dataset ? '?dataset='.$field->filter_dataset : ''); ?>" title="<?php html($previous['title']); ?>">
                &larr; <?php if(empty($field->options['prev_title'])){ ?>
                    <?php html($previous['title']); ?>
                <?php } else { ?>
                    <?php html($field->options['prev_title']); ?>
                <?php } ?>
            </a>
        </div>
    <?php } ?>
    <?php if($next){ ?>
        <div class="next_ctype_navigation">
            <a href="<?php echo href_to($ctype['name'], $next['slug'].'.html').($field->filter_dataset ? '?dataset='.$field->filter_dataset : ''); ?>" title="<?php html($next['title']); ?>">
                <?php if(empty($field->options['next_title'])){ ?>
                    <?php html($next['title']); ?>
                <?php } else { ?>
                    <?php html($field->options['next_title']); ?>
                <?php } ?> &rarr;
            </a>
        </div>
    <?php } ?>
</div>