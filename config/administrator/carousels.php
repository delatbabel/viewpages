<?php

/**
 * Carousel model config
 *
 * @link https://github.com/ddpro/admin/blob/master/docs/model-configuration.md
 */

return [

    'title' => 'Carousels',

    'single' => 'Carousel',

    'model' => \Delatbabel\ViewPages\Models\Carousel::class,

    'server_side' => true,

    'deletable' => true,

    'activation'   => true,

    /**
     * The filterable fields
     *
     * @type array
     */
    'filters'      => [
        'status' => [
            'title'   => 'Status',
            'type'    => 'enum',
            'options' => [
                ''         => 'All',
                'active'   => 'Active',
                'inactive' => 'Inactive'
            ],
            'default' => 'active'
        ]
    ],

    /**
     * The display columns
     */
    'columns'      => [
        'batch_select'          => [
            'title'    => '',
            'output'   => '\DDPro\Admin\Helpers\AdminHelper::getOutputForCheckbox',
            'sortable' => false
        ],
        'id'                    => [
            'title' => 'ID',
        ],
        'key'                   => [
            'title' => 'Location'
        ],
        'name'                  => [
            'title' => 'Name'
        ],
        'display_for_logged_in' => [
            'title' => 'Viewed By'
        ],
        'created_at'            => [
            'type'  => 'date',
            'title' => 'Added Date'
        ],
        'display_status'        => [
            'title' => 'Status'
        ]
    ],

    /**
     * The editable fields
     */
    'form_request' => \Delatbabel\ViewPages\Http\Requests\CarouselFormRequest::class,
    'edit_fields'  => [
        'key'            => [
            'title'   => 'Carousel Location <span class="text-danger">*</span>',
            'type'    => 'enum',
            'options' => [
                'top_carousel'      => 'Top',
                'bottom_carousel'   => 'Bottom',
                'hidden'            => 'Hidden',
            ],
        ],
        'name'           => [
            'title' => 'Name <span class="text-danger">*</span>'
        ],
        'for_logged_in'  => [
            'title'   => 'For',
            'type'    => 'enum',
            'options' => [
                '2' => 'All',
                '1' => 'Members Only',
                '0' => 'Non-Members Only'
            ]
        ],
        'display_days'   => [
            'title' => 'Days to Display for Members',
        ],
        'start_date'     => [
            'title' => 'Start Date',
            'type'  => 'date',
        ],
        'end_date'       => [
            'title' => 'End Date',
            'type'  => 'date',
        ],
        'carouselimages' => [
            'type'               => 'relationship',
            'title'              => 'Images',
            'name_field'         => 'name',
            'options_sort_field' => 'lft'
        ]
    ],
];
