/**
 * @package <?php echo $module['name']; ?> 
 * @author <?php echo $module['author']; ?> 
 * @copyright Copyright (c) <?php echo date('Y'); ?>, <?php echo $module['author']; ?> 
 * @license <?php echo $module['license_url']; ?> 
 */

namespace gplcart\modules\<?php echo $module['id']; ?>\tests;

use gplcart\tests\phpunit\support\UnitTest;

/**
 * Test cases for <?php echo $module['name']; ?> module
 * @todo Format the source code
 */
class <?php echo $module['class_name']; ?>Test extends UnitTest 
{

    /**
     * Object class instance
     * @var \gplcart\modules\<?php echo $module['id']; ?>\<?php echo $module['class_name']; ?> $object
     */
    protected $object;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = $this->getInstance('gplcart\\modules\\<?php echo $module['id']; ?>\\<?php echo $module['class_name']; ?>');
        parent::setUp();
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->object = null;
        parent::tearDown();
    }
}
