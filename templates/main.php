/**
 * @package <?php echo $module['name']; ?> 
 * @author <?php echo $module['author']; ?> 
 * @author Skeleton https://github.com/gplcart/skeleton 
 * @copyright Copyright (c) <?php echo date('Y'); ?>, <?php echo $module['author']; ?> 
 * @license <?php echo $module['license_url']; ?> 
 */

namespace gplcart\modules\<?php echo $module['id']; ?>;

/**
 * Main class for <?php echo $module['name']; ?> module
 * @todo Format the source code
 */
class <?php echo $module['class_name']; ?> 
{
    /**
     * Constructor
     * Inject your dependencies here
     */
    public function __construct()
    {
      // Your code
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
            //'dependencies' => array(),
            //'settings' => array(),
            //'configure' => '',
            //'type' => 'module',
            //'image' => '',
            //'key' => '',
            //'directory' => ''
        );
    }
<?php if(!empty($hooks)) { ?><?php foreach($hooks as $hook) { ?>
    
    /**
     * Implements hook "<?php echo $hook['hook']['name']; ?>"
     * @uses \<?php echo $hook['namespaced_class']; ?>::<?php echo $hook['function']; ?> 
     * @see <?php echo $hook['file']; ?> 
<?php if(!empty($hook['hook']['arguments'])) {?><?php foreach($hook['hook']['arguments'] as $argument) { ?>
     * @param mixed <?php echo ltrim($argument, '&'); ?> 
<?php } ?><?php } ?>
     */
    public function hook<?php echo $hook['hook']['uppercase_name']; ?>(<?php echo implode(', ', $hook['hook']['arguments']); ?>)
    {
        // Your code
    }
<?php } ?><?php } ?>
}
