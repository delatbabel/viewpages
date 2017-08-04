<?php
/**
 * Pages model config
 *
 * @link https://github.com/ddpro/admin/blob/master/docs/model-configuration.md
 */
return [
    'title'              => 'Pages',
    'single'             => 'page',
    'model'              => \Delatbabel\ViewPages\Models\Vpage::class,
    'server_side'        => true,
    /**
     * The display columns
     */
    'columns'            => [
        'id'   => [
            'title' => 'ID',
        ],
        'pagekey'    => [
            'title' => 'Page Key',
        ],
        'url'        => [
            'title' => 'Page URL',
        ],
        'name'       => [
            'title' => 'Name',
        ],
        'created_at' => [
            'type'  => 'date',
            'title' => 'Create Date',
        ],
        'category'   => [
            'title'        => 'Category',
            'type'         => 'relationship',
            'relationship' => 'category',
            'select'       => '(:table).name',
        ],
    ],
    /**
     * The filter set
     */
    'filters'            => [
        'pagekey'  => [
            'title' => 'Page Key',
        ],
        'url'      => [
            'title' => 'URL',
        ],
        'category' => [
            'title'                 => 'Category',
            'type'                  => 'relationship',
            'name_field'            => 'name',
            'options_sort_field'    => 'name',
            'options_filter'        => '\Delatbabel\NestedCategories\Helpers\CategoryHelper::filterCategoriesByParentSlug',
            'options_filter_params' => ['page-types']
        ],
    ],
    /**
     * The editable fields
     */
    'rules'              => [
        'category' => 'required',
    ],
    'edit_fields'        => [
        'pagetype'    => [
            'title'   => 'Page Type',
            'type'    => 'text',
            'visible' => false,
        ],
        'pagekey'     => [
            'title' => 'Page Key',
            'type'  => 'text',
        ],
        'url'         => [
            'title' => 'URL',
        ],
        'name'        => [
            'title' => 'Name',
            'type'  => 'text',
        ],
        'description' => [
            'title' => 'Description',
            'type'  => 'text',
        ],
        'category'    => [
            'title'                 => 'Category <span class="text-danger">*</span>',
            'type'                  => 'relationship',
            'name_field'            => 'name',
            'options_sort_field'    => 'name',
            'options_filter'        => '\Delatbabel\NestedCategories\Helpers\CategoryHelper::filterCategoriesByParentSlug',
            'options_filter_params' => ['page-types']
        ],
        'websites'    => [
            'title'              => 'Websites',
            'type'               => 'relationship',
            'name_field'         => 'name',
            'options_sort_field' => 'name',
        ],
        'content'     => [
            'title'  => 'Content',
            'type'   => 'html',
            'height' => 20, //optional, defaults to 100
        ],
    ],
    'controller_handler' => \Delatbabel\ViewPages\Http\Controllers\PageController::class,
];
