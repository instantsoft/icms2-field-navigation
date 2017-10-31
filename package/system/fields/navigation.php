<?php
/******************************************************************************/
//                                                                            //
//                                InstantMedia                                //
//	 		                                                                  //
//                               written by Fuze                              //
//                    https://instantvideo.ru/copyright.html                  //
//                                                                            //
/******************************************************************************/
class fieldNavigation extends cmsFormField {

    public $title       = LANG_PARSER_NAVIGATION_TITLE;
    public $sql         = 'TINYINT(1) UNSIGNED NULL DEFAULT 1';
    public $is_virtual  = true;
    public $allow_index = false;

    protected $ctype = null;
    protected $dataset = null;
    public $filter_dataset = false;

    /**
     * Магия для работы кэширования (var_export)
     * @param string $name
     * @return object
     */
    public function __get($name) {
        if($name == 'model'){
            $core = cmsCore::getInstance();
            if(!isset($core->field_nav_model)){
                $core->field_nav_model = cmsCore::getModel('content');
            }
            return $core->field_nav_model;
        }
    }

    public function getOptions(){

        return array(
            new fieldCheckbox('show_prev', array(
                'title'   => LANG_PARSER_NAVIGATION_SHOW_PREV,
                'default' => 1
            )),
            new fieldCheckbox('show_next', array(
                'title'   => LANG_PARSER_NAVIGATION_SHOW_NEXT,
                'default' => 1
            )),
            new fieldString('prev_title', array(
                'title' => LANG_PARSER_NAVIGATION_PREV_TITLE,
                'hint'  => LANG_PARSER_NAVIGATION_T_HINT
            )),
            new fieldString('next_title', array(
                'title' => LANG_PARSER_NAVIGATION_NEXT_TITLE,
                'hint'  => LANG_PARSER_NAVIGATION_T_HINT
            )),
            new fieldList('order_by', array(
                'title'   => LANG_SORTING,
                'default' => 'date_pub',
                'generator' => function () {

                    $fields = cmsCore::getModel('content')->
                            getContentFields(cmsCore::getInstance()->request->get('ctype_name', ''));

                    $fields = cmsEventsManager::hook('ctype_content_fields', $fields);

                    foreach($fields as $key => $field){
                        if((!$field['handler']->allow_index || $field['handler']->filter_type === false) && $field['type'] != 'parent'){
                            unset($fields[$key]);
                        }
                    }

                    return array_collection_to_list($fields, 'name', 'title');

                }
            )),
            new fieldList('order_to', array(
                'title'   => LANG_PARSER_NAVIGATION_ORDER_TO,
                'default' => 'desc',
                'items'   => array(
                    'asc'  => LANG_SORTING_ASC,
                    'desc' => LANG_SORTING_DESC
                )
            )),
            new fieldList('dataset_id', array(
                'title' => LANG_PARSER_NAVIGATION_DATASET_ID,
                'hint'  => LANG_PARSER_NAVIGATION_DATASET_ID_HINT,
                'generator' => function () {

                    $datasets_list = array('0'=>'');

                    $model = cmsCore::getModel('content');

                    $ctype = $model->getContentTypeByName(cmsCore::getInstance()->request->get('ctype_name', ''));

                    if($ctype){
                        $datasets = $model->getContentDatasets($ctype['id']);
                        if ($datasets){ $datasets_list = array('0'=>'') + array_collection_to_list($datasets, 'id', 'title'); }
                    }

                    return $datasets_list;

                }
            )),
            new fieldCheckbox('filter_cat', array(
                'title' => LANG_PARSER_NAVIGATION_FILTER_CAT
            )),
            new fieldCheckbox('filter_user', array(
                'title' => LANG_PARSER_NAVIGATION_FILTER_USER
            )),
            new fieldCheckbox('filter_folder', array(
                'title' => LANG_PARSER_NAVIGATION_FILTER_FOLDER
            )),
            new fieldCheckbox('filter_group', array(
                'title' => LANG_PARSER_NAVIGATION_FILTER_GROUP
            )),
            new fieldCheckbox('filter_folder_strict', array(
                'title' => LANG_PARSER_NAVIGATION_FILTER_FOLDER_STRICT
            )),
            new fieldCheckbox('filter_group_strict', array(
                'title' => LANG_PARSER_NAVIGATION_FILTER_GROUP_STRICT
            )),
            new fieldList('template', array(
                'title' => LANG_PARSER_NAVIGATION_TEMPLATE,
                'hint'  => LANG_PARSER_NAVIGATION_TEMPLATE_HINT,
                'generator' => function () {

                    $current_tpls = cmsCore::getFilesList('templates/'.cmsConfig::get('template').'/assets/fields/', 'navigation*.tpl.php');
                    $default_tpls = cmsCore::getFilesList('templates/default/assets/fields/', 'navigation*.tpl.php');
                    $tpls = array_unique(array_merge($current_tpls, $default_tpls));
                    $items = array();
                    if ($tpls) {
                        foreach ($tpls as $tpl) {
                           $items[str_replace('.tpl.php', '', $tpl)] = str_replace('.tpl.php', '', $tpl);
                        }
                        asort($items);
                    }
                    return $items;

                }
            )),
        );

    }

    public function parseTeaser($value){
        return '';
    }

    public function parse($value) {

        if(empty($this->item) || empty($this->item['ctype_name'])){
            return '';
        }

        if(empty($this->item[$this->options['order_by']])){
            return '';
        }

        $this->ctype = $this->model->getContentTypeByName($this->item['ctype_name']);

        if(!$this->ctype){
            return '';
        }

        if(empty($this->item['folder_id']) && $this->getOption('filter_folder_strict')){
            return '';
        }

        if(empty($this->item['parent_id']) && $this->getOption('filter_group_strict')){
            return '';
        }

        $previous = $next = array();

        if($this->options['order_to'] == 'asc'){

            if($this->getOption('show_prev')){
                $previous = $this->setFilter()->getNext($this->options['order_by'], ($this->options['order_to'] == 'asc' ? 'desc' : 'asc'));
            }

            if($this->getOption('show_next')){
                $next = $this->setFilter()->getPrevious($this->options['order_by'], $this->options['order_to']);
            }

        } else {

            if($this->getOption('show_next')){
                $next = $this->setFilter()->getNext($this->options['order_by'], $this->options['order_to']);
            }

            if($this->getOption('show_prev')){
                $previous = $this->setFilter()->getPrevious($this->options['order_by'], ($this->options['order_to'] == 'asc' ? 'desc' : 'asc'));
            }

        }

        if(!$previous && !$next){
            return '';
        }

        $template = cmsTemplate::getInstance();

        $css_file = $template->getTplFilePath('css/field_navigation/'.$this->ctype['name'].'/styles.css', false);
        if(!$css_file){
            $css_file = $template->getTplFilePath('css/field_navigation/styles.css', false);
        }

        $template->addCSSFromContext($css_file);

        return $template->renderFormField($this->getOption('template'), array(
            'field'    => $this,
            'ctype'    => $this->ctype,
            'previous' => $previous,
            'next'     => $next
        ));

    }

    public function getInput($value) {
        return '';
    }

    public function store($value, $is_submitted, $old_value=null){
        return 1;
    }

    private function loadDataset() {

        if($this->dataset === null){

            $dataset_id = $this->getOption('dataset_id');

            $dataset_name = cmsCore::getInstance()->request->get('dataset', '');

            if($dataset_name){

                $dataset_id = false;

                $this->dataset = $this->model->getItemByField('content_datasets', 'name', $dataset_name, function($item, $model){

                    $item['filters'] = $item['filters'] ? cmsModel::yamlToArray($item['filters']) : array();
                    $item['sorting'] = $item['sorting'] ? cmsModel::yamlToArray($item['sorting']) : array();

                    return $item;

                });

                if($this->dataset){
                    $this->filter_dataset = $this->dataset['name'];
                }

            }

            if ($dataset_id){

                $this->dataset = $this->model->getContentDataset($dataset_id);

            }

        }

        return $this;

    }

    private function setFilter() {

        $this->loadDataset();

        if($this->dataset){
            $this->model->applyDatasetFilters($this->dataset, true);
        }

        if(!empty($this->item['category_id']) && $this->getOption('filter_cat')){
            $this->model->filterEqual('category_id', $this->item['category_id']);
        }

        if(!empty($this->item['user_id']) && $this->getOption('filter_user')){
            $this->model->filterEqual('user_id', $this->item['user_id']);
        }

        if(!empty($this->item['folder_id']) && $this->getOption('filter_folder')){
            $this->model->filterEqual('folder_id', $this->item['folder_id']);
        }

        if(!empty($this->item['parent_id']) && $this->getOption('filter_group')){
            $this->model->filterEqual('parent_type', 'group')->filterEqual('parent_id', $this->item['parent_id']);
        }

        $privacy_filter_disabled = false;

        if (!empty($this->ctype['options']['privacy_type']) && in_array($this->ctype['options']['privacy_type'], array('show_title', 'show_all'), true)) {
            $privacy_filter_disabled = true;
        }

        if (cmsUser::isAllowed($this->ctype['name'], 'view_all')) {
            $privacy_filter_disabled = true;
        }

        if (!$privacy_filter_disabled) { $this->model->filterPrivacy(); }

        $this->model->filterHiddenParents()->
                filterEqual('is_approved', 1)->
                filterEqual('is_pub', 1);

        // проверка для совместимости
        if(method_exists($this->model, 'filterAvailableOnly')){
            $this->model->filterIsNull('is_deleted');
        }

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

        $this->model->orderByList(array(
            array(
                'by' => $order_field,
                'to' => $orderto
            ),
            array(
                'by' => 'id',
                'to' => $orderto
            )
        ));

        return $this->model->getItem($this->model->table_prefix.$this->item['ctype_name']);

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

        $this->model->orderByList(array(
            array(
                'by' => $order_field,
                'to' => $orderto
            ),
            array(
                'by' => 'id',
                'to' => $orderto
            )
        ));

        return $this->model->getItem($this->model->table_prefix.$this->item['ctype_name']);

    }

}
