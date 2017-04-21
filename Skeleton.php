<?php

/**
 * @package Skeleton module
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */

namespace gplcart\modules\skeleton;

use gplcart\core\Module;

/**
 * Main class for Skeleton module
 */
class Skeleton extends Module
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Module info
     * @return array
     */
    public function info()
    {
        return array(
            'name' => 'Skeleton',
            'version' => '1.0.0-dev',
            'description' => 'A tool that allows developers to generate blank modules for different purposes',
            'author' => 'Iurii Makukh',
            'core' => '1.x'
        );
    }

    /**
     * Implements hook "route.list"
     * @param array $routes
     */
    public function hookRouteList(array &$routes)
    {
        $routes['admin/tool/skeleton'] = array(
            'menu' => array('admin' => 'Create module'),
            'handlers' => array(
                'controller' => array('gplcart\\modules\\skeleton\\controllers\\Skeleton', 'editSkeleton')
            )
        );
    }

    /**
     * Implements hook "job.handlers"
     * @param array $handlers
     */
    public function hookJobHandlers(array &$handlers)
    {
        $handlers['skeleton'] = array(
            'handlers' => array(
                'process' => array('gplcart\\modules\\skeleton\\handlers\\Extract', 'process')
            ),
        );
    }

    /**
     * Implements hook "validator.handlers"
     * @param array $handlers
     */
    public function hookValidatorHandlers(array &$handlers)
    {
        $handlers['skeleton'] = array(
            'handlers' => array(
                'validate' => array('gplcart\\modules\\skeleton\\handlers\\Validator', 'skeleton')
            ),
        );
    }

    /**
     * Implements hook "cron"
     */
    public function hookCron()
    {
        // Automatically delete created files older than 1 day
        $lifespan = 86400;
        $directory = GC_PRIVATE_DOWNLOAD_DIR . '/skeleton';
        if (is_dir($directory)) {
            gplcart_file_delete($directory, array('zip'), $lifespan);
        }
    }

}
