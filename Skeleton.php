<?php

/**
 * @package Skeleton module
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */

namespace gplcart\modules\skeleton;

/**
 * Main class for Skeleton module
 */
class Skeleton
{

    /**
     * Module info
     * @return array
     */
    public function info()
    {
        return array(
            'name' => 'Skeleton',
            'version' => '1.0.0-alfa.2',
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
            'menu' => array('admin' => 'Skeleton'),
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

}
