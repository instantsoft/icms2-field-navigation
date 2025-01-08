<?php

class fieldNavigation extends cmsFormField {

    public $title       = LANG_PARSER_NAVIGATION_TITLE;
    public $is_virtual  = true;
    public $allow_index = false;
    public $sql         = '';

    public $excluded_controllers = ['forms', 'users', 'groups'];

    protected $ctype       = null;
    protected $dataset     = null;
    public $filter_dataset = false;

    private $model = null;

    public function __construct($name, $options = false) {

        parent::__construct($name, $options);

        $this->model = cmsCore::getModel('content');
    }

    public function getOptions() {

        return [
            new fieldCheckbox('show_prev', [
                'title'   => LANG_PARSER_NAVIGATION_SHOW_PREV,
                'default' => 1
            ]),
            new fieldString('prev_css', [
                'title' => LANG_PARSER_NAVIGATION_CSS,
                'default' => 'btn btn-secondary',
                'visible_depend' => ['options:show_prev' => ['show' => ['1']]]
            ]),
            new fieldCheckbox('show_next', [
                'title'   => LANG_PARSER_NAVIGATION_SHOW_NEXT,
                'default' => 1
            ]),
            new fieldString('next_css', [
                'title' => LANG_PARSER_NAVIGATION_CSS,
                'default' => 'btn-secondary',
                'visible_depend' => ['options:show_next' => ['show' => ['1']]]
            ]),
            new fieldString('prev_title', [
                'title' => LANG_PARSER_NAVIGATION_PREV_TITLE,
                'hint'  => LANG_PARSER_NAVIGATION_T_HINT
            ]),
            new fieldString('next_title', [
                'title' => LANG_PARSER_NAVIGATION_NEXT_TITLE,
                'hint'  => LANG_PARSER_NAVIGATION_T_HINT
            ]),
            new fieldCheckbox('show_img', [
                'title' => LANG_PARSER_NAVIGATION_SHOW_IMG
            ]),
            new fieldList('img_field', [
                'title'     => LANG_PARSER_NAVIGATION_IMG_FIELD,
                'disable_array_key_rules' => true,
                'generator' => function () {

                    $ctype_name = cmsCore::getInstance()->request->get('ctype_name', '');

                    if (!$ctype_name) {
                        return [];
                    }

                    $fields = cmsCore::getModel('content')->
                            getContentFields($ctype_name);

                    $fields = cmsEventsManager::hook('ctype_content_fields', $fields);

                    foreach ($fields as $key => $field) {
                        if (!in_array($field['type'], ['image', 'images'])) {
                            unset($fields[$key]);
                        }
                    }

                    return array_collection_to_list($fields, 'name', 'title');
                },
                'visible_depend' => ['options:show_img' => ['show' => ['1']]]
            ]),
            new fieldList('img_field_preset', [
                'title'     => LANG_PARSER_NAVIGATION_IMG_FIELD_PRESET,
                'generator' => function () {
                    return cmsCore::getModel('images')->getPresetsList();
                },
                'visible_depend' => ['options:show_img' => ['show' => ['1']]]
            ]),
            new fieldList('order_by', [
                'title'                   => LANG_SORTING,
                'default'                 => 'date_pub',
                'disable_array_key_rules' => true,
                'generator'               => function () {

                    $ctype_name = cmsCore::getInstance()->request->get('ctype_name', '');

                    if (!$ctype_name) {
                        return [];
                    }

                    $fields = cmsCore::getModel('content')->
                            getContentFields($ctype_name);

                    $fields = cmsEventsManager::hook('ctype_content_fields', $fields);

                    foreach ($fields as $key => $field) {
                        if ((!$field['handler']->allow_index || $field['handler']->filter_type === false) && $field['type'] != 'parent') {
                            unset($fields[$key]);
                        }
                    }

                    return array_collection_to_list($fields, 'name', 'title');
                }
            ]),
            new fieldList('order_to', [
                'title'   => LANG_PARSER_NAVIGATION_ORDER_TO,
                'default' => 'desc',
                'items'   => [
                    'asc'  => LANG_SORTING_ASC,
                    'desc' => LANG_SORTING_DESC
                ]
            ]),
            new fieldList('dataset_id', [
                'title'                   => LANG_PARSER_NAVIGATION_DATASET_ID,
                'hint'                    => LANG_PARSER_NAVIGATION_DATASET_ID_HINT,
                'disable_array_key_rules' => true,
                'generator'               => function () {

                    $datasets_list = ['0' => ''];

                    $ctype_name = cmsCore::getInstance()->request->get('ctype_name', '');

                    if (!$ctype_name) {
                        return $datasets_list;
                    }

                    $model = cmsCore::getModel('content');

                    $ctype = $model->getContentTypeByName($ctype_name);

                    if ($ctype) {
                        $datasets = $model->getContentDatasets($ctype['id']);
                        if ($datasets) {
                            $datasets_list = ['0' => ''] + array_collection_to_list($datasets, 'id', 'title');
                        }
                    }

                    return $datasets_list;
                }
            ]),
            new fieldCheckbox('filter_cat', [
                'title' => LANG_PARSER_NAVIGATION_FILTER_CAT
            ]),
            new fieldCheckbox('filter_user', [
                'title' => LANG_PARSER_NAVIGATION_FILTER_USER
            ]),
            new fieldCheckbox('filter_folder', [
                'title' => LANG_PARSER_NAVIGATION_FILTER_FOLDER
            ]),
            new fieldCheckbox('filter_group', [
                'title' => LANG_PARSER_NAVIGATION_FILTER_GROUP
            ]),
            new fieldCheckbox('filter_folder_strict', [
                'title' => LANG_PARSER_NAVIGATION_FILTER_FOLDER_STRICT
            ]),
            new fieldCheckbox('filter_group_strict', [
                'title' => LANG_PARSER_NAVIGATION_FILTER_GROUP_STRICT
            ]),
            new fieldList('template', [
                'title'     => LANG_PARSER_NAVIGATION_TEMPLATE,
                'hint'      => LANG_PARSER_NAVIGATION_TEMPLATE_HINT,
                'generator' => function () {

                    $current_tpls = cmsCore::getFilesList('templates/' . cmsConfig::get('template') . '/assets/fields/', 'navigation*.tpl.php');
                    $default_tpls = cmsCore::getFilesList('templates/default/assets/fields/', 'navigation*.tpl.php');
                    $tpls         = array_unique(array_merge($current_tpls, $default_tpls));
                    $items        = [];

                    foreach ($tpls as $tpl) {
                        $items[str_replace('.tpl.php', '', $tpl)] = str_replace('.tpl.php', '', $tpl);
                    }
                    asort($items);

                    return $items;
                }
            ])
        ];
    }

    public function getStringValue($value) {
        return '';
    }

    public function parseTeaser($value) {
        return '';
    }

    public function parse($value) {

        if (empty($this->item['id']) || empty($this->item['ctype_name'])) {
            return '';
        }

        if (empty($this->item[$this->options['order_by']])) {
            return '';
        }

        $this->ctype = $this->model->getContentTypeByName($this->item['ctype_name']);

        if (!$this->ctype) {
            return '';
        }

        if (empty($this->item['folder_id']) && $this->getOption('filter_folder_strict')) {
            return '';
        }

        if (empty($this->item['parent_id']) && $this->getOption('filter_group_strict')) {
            return '';
        }

        // грузим набор
        $this->loadDataset();

        // если есть набор, смотрим его сортировку
        if ($this->dataset) {
            if (!empty($this->dataset['sorting'])) {
                $last_sorting              = end($this->dataset['sorting']);
                $this->options['order_by'] = $last_sorting['by'];
                $this->options['order_to'] = $last_sorting['to'];
            }
        }

        $previous = $next = [];

        if ($this->options['order_to'] === 'asc') {

            if ($this->getOption('show_prev')) {
                $previous = $this->setFilter()->getNext($this->options['order_by'], ($this->options['order_to'] === 'asc' ? 'desc' : 'asc'));
            }

            if ($this->getOption('show_next')) {
                $next = $this->setFilter()->getPrevious($this->options['order_by'], $this->options['order_to']);
            }

        } else {

            if ($this->getOption('show_next')) {
                $next = $this->setFilter()->getNext($this->options['order_by'], $this->options['order_to']);
            }

            if ($this->getOption('show_prev')) {
                $previous = $this->setFilter()->getPrevious($this->options['order_by'], ($this->options['order_to'] === 'asc' ? 'desc' : 'asc'));
            }
        }

        if (!$previous && !$next) {
            return '';
        }

        $template = cmsTemplate::getInstance();

        $css_file = $template->getTplFilePath('css/field_navigation/' . $this->ctype['name'] . '/styles.css', false);
        if (!$css_file) {
            $css_file = $template->getTplFilePath('css/field_navigation/styles.css', false);
        }

        $template->addCSSFromContext($css_file);

        return $template->renderFormField($this->getOption('template'), [
            'show_img' => $this->getOption('show_img') && $this->getOption('img_field'),
            'field'    => $this,
            'ctype'    => $this->ctype,
            'previous' => $previous,
            'next'     => $next
        ]);
    }

    public function getInput($value) {
        return '';
    }

    private function loadDataset() {

        if ($this->dataset === null) {

            $dataset_id = $this->getOption('dataset_id');

            $dataset_name = cmsCore::getInstance()->request->get('dataset', '');

            if ($dataset_name) {

                $dataset_id = false;

                $this->dataset = $this->model->getItemByField('content_datasets', 'name', $dataset_name, function ($item, $model) {

                    $item['filters'] = cmsModel::yamlToArray($item['filters']);
                    $item['sorting'] = cmsModel::yamlToArray($item['sorting']);

                    return $item;
                });

                if ($this->dataset) {
                    $this->filter_dataset = $this->dataset['name'];
                }
            }

            if ($dataset_id) {
                $this->dataset = $this->model->getContentDataset($dataset_id);
            }
        }

        return $this;
    }

    private function setFilter() {

        $this->loadDataset();

        if ($this->dataset) {
            $this->model->applyDatasetFilters($this->dataset, true);
        }

        if (!empty($this->item['category_id']) && $this->getOption('filter_cat')) {
            $this->model->filterEqual('category_id', $this->item['category_id']);
        }

        if (!empty($this->item['user_id']) && $this->getOption('filter_user')) {
            $this->model->filterEqual('user_id', $this->item['user_id']);
        }

        if (!empty($this->item['folder_id']) && $this->getOption('filter_folder')) {
            $this->model->filterEqual('folder_id', $this->item['folder_id']);
        }

        if (!empty($this->item['parent_id']) && $this->getOption('filter_group')) {
            $this->model->filterEqual('parent_type', 'group')->filterEqual('parent_id', $this->item['parent_id']);
        }

        $privacy_filter_disabled = false;

        if (!empty($this->ctype['options']['privacy_type']) && in_array($this->ctype['options']['privacy_type'], ['show_title', 'show_all'], true)) {
            $privacy_filter_disabled = true;
        }

        if (cmsUser::isAllowed($this->ctype['name'], 'view_all')) {
            $privacy_filter_disabled = true;
        }

        if (!$privacy_filter_disabled) {
            $this->model->filterPrivacy();
        }

        $this->model->filterHiddenParents()->
                filterEqual('is_approved', 1)->
                filterEqual('is_pub', 1);

        $this->model->filterIsNull('is_deleted');

        return $this;
    }

    private function getPrevious($order_field, $orderto) {

        $this->model->filterStart()->
                filterGt($order_field, $this->item[$order_field])->
                filterOr()->
                filterStart()->
                filterEqual($order_field, $this->item[$order_field])->
                filterAnd()->
                filterGt('id', $this->item['id'])->
                filterEnd()->
                filterEnd();

        $this->model->orderByList([
            [
                'by' => $order_field,
                'to' => $orderto
            ],
            [
                'by' => 'id',
                'to' => $orderto
            ]
        ]);

        return $this->getModelItem();
    }

    private function getNext($order_field, $orderto) {

        $this->model->filterStart()->
                filterLt($order_field, $this->item[$order_field])->
                filterOr()->
                filterStart()->
                filterEqual($order_field, $this->item[$order_field])->
                filterAnd()->
                filterLt('id', $this->item['id'])->
                filterEnd()->
                filterEnd();

        $this->model->orderByList([
            [
                'by' => $order_field,
                'to' => $orderto
            ],
            [
                'by' => 'id',
                'to' => $orderto
            ]
        ]);

        return $this->getModelItem();
    }

    private function getModelItem() {

        $this->model->useCache("content.item.{$this->item['ctype_name']}");

        $select = ['id', 'slug', 'title'];

        if ($this->getOption('show_img') && $this->getOption('img_field')) {
            $select[] = $this->getOption('img_field');
        }

        $this->model->selectList($select, true);

        return $this->model->getItem($this->model->getContentTypeTableName($this->item['ctype_name']));
    }

}
