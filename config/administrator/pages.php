<?php

/**
 * Pages model config
 *
 * @link https://github.com/ddpro/admin/blob/master/docs/model-configuration.md
 */

return [

    'title' => 'Pages',

    'single' => 'page',

    'model' => '\Delatbabel\ViewPages\Models\Vpage',

    'server_side' => true,

    /**
     * The display columns
     */
    'columns' => [
        'id',
        'pagekey' => [
            'title' => 'Page Key',
        ],
        'url' => [
            'title' => 'Page URL',
        ],
        'name' => [
            'title' => 'Name',
        ],
    ],

    /**
     * The filter set
     */
    'filters' => [
        'pagekey' => [
            'title' => 'Page Key',
        ],
        'url' => [
            'title' => 'Page URL',
        ],
        'category' => [
            'title'              => 'Category',
            'type'               => 'relationship',
            'name_field'         => 'name',
            'options_sort_field' => 'name',
        ],
    ],

    /**
     * The editable fields
     */
    'edit_fields' => [
        'pagekey' => [
            'title' => 'Page Key',
            'type'  => 'text',
        ],
        'url' => [
            'title' => 'Page URL',
            'type'  => 'text',
        ],
        'name' => [
            'title' => 'Name',
            'type'  => 'text',
        ],
        'description' => [
            'title' => 'Description',
            'type'  => 'text',
        ],
        'pagetype' => [
            'title' => 'Page Type',
            'type'  => 'text',
        ],
        'content' => [
            'title' => 'Content',
            'type'  => 'wysiwyg',
        ],
        'websites' => [
            'title'              => 'Websites',
            'type'               => 'relationship',
            'name_field'         => 'name',
            'options_sort_field' => 'name',
        ],
        'category' => [
            'title'              => 'Category',
            'type'               => 'relationship',
            'name_field'         => 'name',
            'options_sort_field' => 'name',
        ],
    ],

    'form_width'    => 700,
];
