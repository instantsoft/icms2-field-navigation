<div class="row align-items-stretch">
    <?php if($previous){ ?>
        <div class="col-md mb-2 mb-md-0">
            <a class="<?php echo $field->getOption('prev_css', ''); ?> d-flex align-items-center w-100 h-100" href="<?php echo href_to($ctype['name'], $previous['slug'].'.html').($field->filter_dataset ? '?dataset='.$field->filter_dataset : ''); ?>" title="<?php html($previous['title']); ?>">
                <?php if($show_img && !empty($previous[$field->getOption('img_field')])){ ?>
                    <span class="mr-1 mr-lg-3">
                        <?php echo html_image($previous[$field->getOption('img_field')], $field->getOption('img_field_preset'), $previous['title']); ?>
                    </span>
                <?php } ?>
                <span class="w-100 h-100 d-flex align-items-center">
                    <span class="pr-2"><?php html_svg_icon('solid', 'arrow-left'); ?></span>
                    <?php if(empty($field->options['prev_title'])){ ?>
                        <?php html($previous['title']); ?>
                    <?php } else { ?>
                        <?php html($field->options['prev_title']); ?>
                    <?php } ?>
                </span>
            </a>
        </div>
    <?php } ?>
    <?php if($next){ ?>
        <div class="col-md">
            <a class="<?php echo $field->getOption('next_css', ''); ?> d-flex align-items-center w-100 h-100" href="<?php echo href_to($ctype['name'], $next['slug'].'.html').($field->filter_dataset ? '?dataset='.$field->filter_dataset : ''); ?>" title="<?php html($next['title']); ?>">
                <span class="w-100 h-100 d-flex align-items-center">
                    <?php if(empty($field->options['next_title'])){ ?>
                        <?php html($next['title']); ?>
                    <?php } else { ?>
                        <?php html($field->options['next_title']); ?>
                    <?php } ?>
                    <span class="pl-2"><?php html_svg_icon('solid', 'arrow-right'); ?></span>
                </span>
                <?php if($show_img && !empty($next[$field->getOption('img_field')])){ ?>
                    <span class="ml-1 ml-lg-3">
                        <?php echo html_image($next[$field->getOption('img_field')], $field->getOption('img_field_preset'), $next['title']); ?>
                    </span>
                <?php } ?>
            </a>
        </div>
    <?php } ?>
</div>