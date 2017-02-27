/** 
 * @package <?php echo $module['name']; ?> 
 * @author <?php echo $module['author']; ?> 
 * @author Skeleton https://github.com/gplcart/skeleton 
 * @copyright Copyright (c) <?php echo date('Y'); ?>, <?php echo $module['author']; ?> 
 * @license <?php echo $module['license_url']; ?> 
 */

namespace gplcart\modules\<?php echo $module['id']; ?>\models;

use gplcart\core\Model as CoreModel;

/**
 * Manages basic behaviors and data related to <?php echo $module['name']; ?> module
 * @todo Format the source code
 */
class Model extends CoreModel
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }
}
