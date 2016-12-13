<?php

namespace Delatbabel\ViewPages\Form;

use Illuminate\Http\Request;
use Log;

/**
 * Class PageForm
 *
 * This is a custom form handler for use when editing pages using ddpro-admin.
 *
 * public function getBladeViewIndex()
 * public function getIndexData(&$arrParams)
 * public function getBladeViewForm()
 * public function getFormData(&$arrParams)
 * public function saveFormData($request, $session, $modelName, $id)
 *
 * @link https://github.com/ddpro/admin
 */
class PageForm
{
    /**
     * Get custom view index name
     *
     * @return string
     */
    public function getBladeViewIndex()
    {
        return 'adminmodel_pages.index';
    }

    /**
     * Get data for index view
     *
     * @param $arrParams
     */
    public function getIndexData(&$arrParams)
    {
        $arrParams = array_merge($arrParams, [
            'sTitle' => 'Custom Page Index'
        ]);
    }

    /**
     * Get custom view form name
     *
     * @return string
     */
    public function getBladeViewForm()
    {
        return 'adminmodel_pages.form';
    }

    /**
     * Get data for form view
     *
     * @param $arrParams
     */
    public function getFormData(&$arrParams)
    {
        $arrParams = array_merge($arrParams, [
            'sTitle' => 'Custom Page Form'
        ]);
    }

    /**
     * Handle custom save form
     *
     * @param $request
     * @param $session
     * @param $modelName
     * @param $id
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function saveFormData(Request $request, $session, $modelName, $id)
    {
        Log::debug(__CLASS__ . ':' . __TRAIT__ . ':' . __FILE__ . ':' . __LINE__ . ':' . __FUNCTION__ . ':' .
            ' Save form handle from config');

        $config = app('itemconfig');

        /** @var \DDPro\Admin\Fields\Factory $fieldFactory */
        $fieldFactory = app('admin_field_factory');

        /** @var \DDPro\Admin\Actions\Factory $actionFactory */
        $actionFactory = app('admin_action_factory');

        /* Validate from form_request */
        if ($formRequestClass = $config->getOption('form_request')) {
            $request = app($formRequestClass);
        }

        /* Custom page_type */
        $request->merge([
            'pagetype' => ".blade.php",
        ]);

        $save = $config->save($request, $fieldFactory->getEditFields(), $actionFactory->getActionPermissions(), $id);
        if ($save !== true) {
            return redirect()->back()->withErrors($config->getCustomValidator());
        }
        // override the config options so that we can get the latest
        app('admin_config_factory')->updateConfigOptions();

        // grab the latest model data
        $columnFactory = app('admin_column_factory');
        $fields        = $fieldFactory->getEditFields();
        $model         = $config->getModel($id, $fields, $columnFactory->getIncludedColumns($fields));

        if ($model->exists) {
            $model = $config->updateModel($model, $fieldFactory, $actionFactory);
        }

        return redirect()->route('admin_index', [$modelName]);
    }
}
