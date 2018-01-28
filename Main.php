<?php

/**
 * @package Skeleton module
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */

namespace gplcart\modules\skeleton;

use gplcart\core\Container;

/**
 * Main class for Skeleton module
 */
class Main
{

    /**
     * Implements hook "module.install.before"
     * @param null|string $result
     */
    public function hookModuleInstallBefore(&$result)
    {
        if (!class_exists('ZipArchive')) {
            $result = $this->getTranslationModel()->text('Class ZipArchive does not exist');
        }
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

    /**
     * Implements hook "cron.run.after"
     */
    public function hookCronRunAfter()
    {
        gplcart_file_empty(gplcart_file_private_module('skeleton'), array('zip'), 24 * 60 * 60);
    }

    /**
     * Translation UI model class instance
     * @return \gplcart\core\models\Translation
     */
    protected function getTranslationModel()
    {
        return Container::get('gplcart\\core\\models\\Translation');
    }

}
