<?php

/**
 * Pages model config
 *
 * @link https://github.com/ddpro/admin/blob/master/docs/model-configuration.md
 */

return array(

    'title' => 'Pages',

    'single' => 'page',

    'model' => '\Delatbabel\ViewPages\Models\Vpage',

    /**
     * The display columns
     */
    'columns' => array(
        'id',
        'pagekey' => array(
            'title' => 'Page Key',
        ),
        'url' => array(
            'title' => 'Page URL',
        ),
        'name' => array(
            'title' => 'Name',
        ),
    ),

    /**
     * The filter set
     */
    'filters' => array(
        'pagekey' => array(
            'title' => 'Page Key',
        ),
        'url' => array(
            'title' => 'Page URL',
        ),
        'category' => array(
            'title' => 'Category',
            'type' => 'relationship',
            'name_field' => 'name',
            'options_sort_field' => 'name',
        ),
    ),

    /**
     * The editable fields
     */
    'edit_fields' => array(
        'pagekey' => array(
            'title' => 'Page Key',
            'type' => 'text',
        ),
        'url' => array(
            'title' => 'Page URL',
            'type' => 'text',
        ),
        'name' => array(
            'title' => 'Name',
            'type' => 'text',
        ),
        'description' => array(
            'title' => 'Description',
            'type' => 'text',
        ),
        'pagetype' => array(
            'title' => 'Page Type',
            'type' => 'text',
        ),
        'content' => array(
            'title' => 'Content',
            'type' => 'wysiwyg',
        ),
        'websites' => array(
            'title' => 'Websites',
            'type' => 'relationship',
            'name_field' => 'name',
            'options_sort_field' => 'name',
        ),
        'category' => array(
            'title' => 'Category',
            'type' => 'relationship',
            'name_field' => 'name',
            'options_sort_field' => 'name',
        ),
    ),

    'form_width'    => 700,
);
