<?php
namespace Delatbabel\ViewPages\Http\Controllers;

use DDPro\Admin\Http\Controllers\AdminModelController;
use DDPro\Admin\Http\ViewComposers\ModelViewComposer;

/**
 * Class PageController
 * Custom Controller Handler for Page Model
 *
 * @package Delatbabel\ViewPages\Http\Controllers
 */
class PageController extends AdminModelController
{
    /**
     * Show list
     *
     * @param $modelName
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index($modelName)
    {
        $bladeName = 'admin.model.pages.index';
        \View::composer($bladeName, ModelViewComposer::class);

        return $this->view = view($bladeName);
    }

    /**
     * Edit/New
     *
     * @param string $modelName
     * @param int    $itemId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function item($modelName, $itemId = 0)
    {
        $bladeName = 'admin.model.pages.form';
        \View::composer($bladeName, ModelViewComposer::class);
        parent::item($modelName, $itemId);

        return $this->view = view($bladeName, $this->view->getData());
    }

    /**
     * Save Model
     *
     * @param string $modelName
     * @param null   $id
     * @return string
     */
    public function save($modelName, $id = null)
    {
        // Custom page_type
        $this->request->merge([
            'pagetype' => ".blade.php",
        ]);

        return parent::save($modelName, $id);
    }
}
