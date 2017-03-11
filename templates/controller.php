/**
 * @package <?php echo $module['name']; ?> 
 * @author <?php echo $module['author']; ?> 
 * @author Skeleton https://github.com/gplcart/skeleton 
 * @copyright Copyright (c) <?php echo date('Y'); ?>, <?php echo $module['author']; ?> 
 * @license <?php echo $module['license_url']; ?> 
 */
 
namespace gplcart\modules\<?php echo $module['id']; ?>\controllers;

use gplcart\core\controllers\backend\Controller as BackendController;

/**
 * Handles incoming requests and outputs data related to <?php echo $module['name']; ?> module
 * @todo Format the source code
 */
class Settings extends BackendController
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Route page callback to display the module settings page
     */
    public function editSettings()
    {
        $this->outputEditSettings();
    }
    
    /**
     * Render and output the module settings page
     */
    protected function outputEditSettings()
    {
      $this->output('<?php echo $module['id']; ?>|settings');
    }
}
