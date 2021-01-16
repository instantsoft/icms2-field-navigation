<div class="row">
    <?php if($previous){ ?>
        <div class="col-md mb-2 mb-md-0">
            <a class="btn btn-secondary btn-block" href="<?php echo href_to($ctype['name'], $previous['slug'].'.html').($field->filter_dataset ? '?dataset='.$field->filter_dataset : ''); ?>" title="<?php html($previous['title']); ?>">
                <?php html_svg_icon('solid', 'arrow-left'); ?> <?php if(empty($field->options['prev_title'])){ ?>
                    <?php html($previous['title']); ?>
                <?php } else { ?>
                    <?php html($field->options['prev_title']); ?>
                <?php } ?>
            </a>
        </div>
    <?php } ?>
    <?php if($next){ ?>
        <div class="col-md">
            <a class="btn btn-secondary active btn-block" href="<?php echo href_to($ctype['name'], $next['slug'].'.html').($field->filter_dataset ? '?dataset='.$field->filter_dataset : ''); ?>" title="<?php html($next['title']); ?>">
                <?php if(empty($field->options['next_title'])){ ?>
                    <?php html($next['title']); ?>
                <?php } else { ?>
                    <?php html($field->options['next_title']); ?>
                <?php } ?> <?php html_svg_icon('solid', 'arrow-right'); ?>
            </a>
        </div>
    <?php } ?>
</div>