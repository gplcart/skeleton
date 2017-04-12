/**
 * @package <?php echo $module['name']; ?> 
 * @author <?php echo $module['author']; ?> 
 * @copyright Copyright (c) <?php echo date('Y'); ?>, <?php echo $module['author']; ?> 
 * @license <?php echo $module['license_url']; ?> 
 */

namespace gplcart\modules\<?php echo $module['id']; ?>;

use gplcart\core\Module;

/**
 * Main class for <?php echo $module['name']; ?> module
 * @todo Format the source code
 */
class <?php echo $module['class_name']; ?> extends Module 
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
       
      // WARNING: This constructor is called on each request.
      // Instantiating module specific classes here may cause site-wide performance issues
      // Use $this->getInstance('namespace\\YourClass'); to get a class instance right in your methods
    }

    /**
     * Module info
     * @return array
     */
    public function info()
    {
        return array(
            'name' => '<?php echo $module['name']; ?>',
            'version' => '<?php echo $module['version']; ?>',
            'description' => '<?php echo $module['description']; ?>',
            'author' => '<?php echo $module['author']; ?>',
            'core' => '<?php echo $module['core']; ?>',
            'license' => '<?php echo $module['license']; ?>', 
<?php if(!empty($structure) && in_array('configurable', $structure)) { ?>
            'configure' => 'admin/module/settings/<?php echo $module['id']; ?>', 
<?php } ?>
            //'dependencies' => array(),
            //'settings' => array(),
            //'type' => 'module',
            //'image' => ''
        );
    }
<?php if(!empty($hooks)) { ?><?php foreach($hooks as $hook) { ?>
    
    /**
     * Implements hook "<?php echo $hook['hook']['name']; ?>"
     * @uses \<?php echo $hook['namespaced_class']; ?>::<?php echo $hook['function']; ?> 
     * @see <?php echo $hook['file']; ?> 
<?php if(!empty($hook['hook']['arguments'])) {?><?php foreach($hook['hook']['arguments'] as $argument) { ?>
<?php if(strpos($argument, '\gplcart') === 0) {?>
     * @param <?php echo ltrim($argument, '&'); ?> 
<?php } else { ?>
     * @param mixed <?php echo ltrim($argument, '&'); ?> 
<?php } ?>
<?php } ?><?php } ?>
     */
    public function hook<?php echo $hook['hook']['uppercase_name']; ?>(<?php echo implode(', ', $hook['hook']['arguments']); ?>)
    {
<?php if($hook['hook']['uppercase_name'] == 'RouteList' && !empty($structure) && in_array('configurable', $structure)) { ?>
        // Module settings page
        $routes['admin/module/settings/<?php echo $module['id']; ?>'] = array(
            'access' => 'module_edit',
            'handlers' => array(
                'controller' => array('gplcart\\modules\\<?php echo $module['id']; ?>\\controllers\\Settings', 'editSettings')
            )
        );
<?php } else { ?>
        // Your code
<?php } ?>   
    }
<?php } ?><?php } ?>
}
