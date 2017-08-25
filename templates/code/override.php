/**
 * @package <?php echo $module['name']; ?> 
 * @author <?php echo $module['author']; ?> 
 * @copyright Copyright (c) <?php echo date('Y'); ?>, <?php echo $module['author']; ?> 
 * @license <?php echo $module['license_url']; ?> 
 */

namespace gplcart\modules\<?php echo $module['id']; ?>\override\classes\modules\<?php echo $module['id']; ?>;

use gplcart\modules\<?php echo $module['id']; ?>\<?php echo $module['class_name']; ?> as <?php echo $module['class_name']; ?>Module;

/**
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
        
        print_r('Overridden <?php echo $module['name']; ?> module constructor in effect!');
    }
}
