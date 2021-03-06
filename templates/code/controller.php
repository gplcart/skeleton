/**
 * @package <?php echo $module['name']; ?> 
 * @author <?php echo $module['author']; ?> 
 * @copyright Copyright (c) <?php echo date('Y'); ?>, <?php echo $module['author']; ?> 
 * @license <?php echo $module['license_url']; ?> 
 */
 
namespace gplcart\modules\<?php echo $module['id']; ?>\controllers;

use gplcart\core\controllers\backend\Controller;

/**
 * Handles incoming requests and outputs data related to <?php echo $module['name']; ?> module
 * @todo Format the source code
 */
class Settings extends Controller
{
    
    /**
     * Route page callback to display the module settings page
     */
    public function editSettings()
    {
        $this->setTitleEditSettings();
        $this->setBreadcrumbEditSettings();
    
        $this->setData('settings', $this->getModuleSettings('<?php echo $module['id']; ?>'));
        
        $this->submitSettings();
        $this->outputEditSettings();
    }
    
    /**
     * Set title on the module settings page
     */
    protected function setTitleEditSettings()
    {
        $title = $this->text('Edit %name settings', array('%name' => $this->text('<?php echo $module['name']; ?>')));
        $this->setTitle($title);
    }
    
    /**
     * Set breadcrumbs on the module settings page
     */
    protected function setBreadcrumbEditSettings()
    {
        $breadcrumbs = array();

        $breadcrumbs[] = array(
            'text' => $this->text('Dashboard'),
            'url' => $this->url('admin')
        );

        $breadcrumbs[] = array(
            'text' => $this->text('Modules'),
            'url' => $this->url('admin/module/list')
        );

        $this->setBreadcrumbs($breadcrumbs);
    }
    
    /**
     * Saves the submitted settings
     */
    protected function submitSettings()
    {
        if ($this->isPosted('save') && $this->validateSettings()) {
            $this->updateSettings();
        }
    }
    
    /**
     * Validate submitted module settings
     * @return bool
     */
    protected function validateSettings()
    {
        $this->setSubmitted('settings');
        
        /*
        if ($this->getSubmitted('name', '') === '') {
            $this->setError('name', $this->text('Name is required'));
        }
         */

        return !$this->hasErrors();
    }
    
    /**
     * Update module settings
     */
    protected function updateSettings()
    {
        $this->controlAccess('module_edit');
        $this->module->setSettings('<?php echo $module['id']; ?>', $this->getSubmitted());
        $this->redirect('', $this->text('Settings have been updated'), 'success');
    }
    
    /**
     * Render and output the module settings page
     */
    protected function outputEditSettings()
    {
      $this->output('<?php echo $module['id']; ?>|settings');
    }
}
