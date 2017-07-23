/**
 * @package <?php echo $module['name']; ?> 
 * @author <?php echo $module['author']; ?> 
 * @copyright Copyright (c) <?php echo date('Y'); ?>, <?php echo $module['author']; ?> 
 * @license <?php echo $module['license_url']; ?> 
 */

namespace gplcart\modules\<?php echo $module['id']; ?>\override\modules\<?php echo $module['id']; ?>;

use gplcart\modules\<?php echo $module['id']; ?>\<?php echo $module['class_name']; ?> as <?php echo $module['class_name']; ?>Module;

/**
 * Overrides <?php echo $module['name']; ?> module info() method
 * @todo Format the source code
 */
class <?php echo $module['class_name']; ?>Override extends <?php echo $module['class_name']; ?>Module
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Overrides module info() method
     */
    public function info()
    {
        $info = parent::info();
        $info['name'] .= ' (Overridden)';
        return $info;
    }
}
