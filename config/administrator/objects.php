<?php

/**
 * Objects model config
 *
 * @link https://github.com/ddpro/admin/blob/master/docs/model-configuration.md
 */

return array(

    'title' => 'Objects',

    'single' => 'object',

    'model' => '\Delatbabel\ViewPages\Models\Vobject',

    /**
     * The display columns
     */
    'columns' => array(
        'id',
        'objectkey' => array(
            'title' => 'Object Key',
        ),
        'name' => array(
            'title' => 'Name',
        ),
    ),

    /**
     * The filter set
     */
    'filters' => array(
        'objectkey' => array(
            'title' => 'Object Key',
        ),
        'name' => array(
            'title' => 'Name',
        ),
    ),

    /**
     * The editable fields
     */
    'edit_fields' => array(
        'objectkey' => array(
            'title' => 'Object Key',
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
        'content' => array(
            'title' => 'Content',
            'type' => 'wysiwyg',
        ),
        'website' => array(
            'title' => 'Website',
            'type' => 'relationship',
            'name_field' => 'name',
            'options_sort_field' => 'name',
        ),
    ),

    'form_width' => 700,
);
