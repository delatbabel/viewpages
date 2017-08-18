<?php

/**
 * Photo Galleries model config
 *
 * @link https://github.com/ddpro/admin/blob/master/docs/model-configuration.md
 */

return [

    'title' => 'Carousel Images',

    'single' => 'Carousel Image',

    'model' => \Delatbabel\ViewPages\Models\CarouselImage::class,

    'server_side' => true,

    'deletable'     => true,

    'activation'    => true,

    /**
     * The filterable fields
     *
     * @type array
     */
    'filters' => [
        'carousels' => [
            'type' => 'relationship',
            'title' => 'Carousels',
            'name_field' => 'name',
            'options_sort_field' => 'name',
        ],
        'status' => [
            'title' => 'Status',
            'type' => 'enum',
            'options' => [
                '' => 'All',
                'active' => 'Active',
                'inactive' => 'Inactive'
            ],
            'default' => 'active'
        ],
    ],

    /**
     * The display columns
     */
    'columns' => [
        'batch_select' => [
            'title' => '',
            'output' => '\DDPro\Admin\Helpers\AdminHelper::getOutputForCheckbox',
            'sortable' => false
        ],
        'lft' => [ // This column enable reordering feature
            'title' => 'Order'
        ],
        'id' => [
            'title' => 'ID',
        ],
        'carousels' => [
            'title' => 'Carousels',
            'relationship' => 'carousels',
            'select' => "GROUP_CONCAT((:table).name ORDER BY (:table).name ASC SEPARATOR ', ')",
        ],
        'name' => [
            'title' => 'Name'
        ],
        'preview' => [
            'title' => 'Preview'
        ],
        'clicks' => [
            'title' => 'Clicks'
        ],
        'created_at' => [
            'type'  => 'date',
            'title' => 'Added Date'
        ],
        'display_status' => [
            'title' => 'Status'
        ]
    ],

    /**
     * The editable fields
     */
    'form_request' => \Delatbabel\ViewPages\Http\Requests\CarouselImageFormRequest::class,
    'edit_fields' => [
        'carousels' => array(
            'type' => 'relationship',
            'title' => 'Carousels <span class="text-danger">*</span>',
            'name_field' => 'name',
            'options_sort_field' => 'name',
        ),
        'name' => [
            'title' => 'Name <span class="text-danger">*</span>'
        ],
        'path' => [
            'title' => 'Image',
            'type' => 'image',
            'naming' => 'random',
            'length' => 20,
            'location' => 'uploads/carousels/'
        ],
        'url' => [
            'title' => 'Link'
        ],
        'use_html' => [
            'title' => 'Use HTML',
            'type' => 'bool',
            'attributes' => ['class' => 'col-md-1']
        ],
        'html' => [
            'title' => 'HTML',
            'type' => 'wysiwyg'
        ],
        'displaying_time' => [
            'title' => 'Displaying time <span class="text-danger">*</span>',
            'type' => 'text'
        ],
    ]
];
