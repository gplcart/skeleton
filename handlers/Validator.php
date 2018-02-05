<?php

/**
 * @package Skeleton module
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */

namespace gplcart\modules\skeleton\handlers;

use gplcart\core\handlers\validator\Base as BaseValidator;
use gplcart\core\Module;

/**
 * Provides methods to validate Skeleton module data
 */
class Validator extends BaseValidator
{

    /**
     * Module class instance
     * @var \gplcart\core\Module $module
     */
    protected $module;

    /**
     * @param Module $module
     */
    public function __construct(Module $module)
    {
        $this->module = $module;
    }

    /**
     * Validates an array of submitted data while creating a skeleton
     * @param array $submitted
     * @param array $options
     * @return boolean|array
     */
    public function skeleton(array &$submitted, array $options = array())
    {
        $this->options = $options;
        $this->submitted = &$submitted;

        $this->validateModuleIdSkeleton();
        $this->validateModuleVersionSkeleton();
        $this->validateModuleCoreSkeleton();
        $this->validateModuleAuthorSkeleton();

        return $this->getResult();
    }

    /**
     * Validates module author name
     * @return boolean
     */
    protected function validateModuleAuthorSkeleton()
    {
        $value = $this->getSubmitted('module.author');

        if (empty($value)) {
            $vars = array('@field' => $this->translation->text('Author'));
            $error = $this->translation->text('@field is required', $vars);
            $this->setError('module.author', $error);
            return false;
        }

        return true;
    }

    /**
     * Validates a module ID
     */
    protected function validateModuleIdSkeleton()
    {
        $value = $this->getSubmitted('module.id');

        if (empty($value)) {
            $vars = array('@field' => $this->translation->text('ID'));
            $error = $this->translation->text('@field is required', $vars);
            $this->setError('module.id', $error);
            return false;
        }

        if (!$this->module->isValidId($value)) {
            $vars = array('@field' => $this->translation->text('ID'));
            $error = $this->translation->text('@field has invalid value', $vars);
            $this->setError('module.id', $error);
            return false;
        }

        return true;
    }

    /**
     * Validates a module version
     */
    protected function validateModuleVersionSkeleton()
    {
        $value = $this->getSubmitted('module.version');

        if (empty($value)) {
            $vars = array('@field' => $this->translation->text('Version'));
            $error = $this->translation->text('@field is required', $vars);
            $this->setError('module.version', $error);
            return false;
        }

        return true;
    }

    /**
     * Validates a module core compatibility
     */
    protected function validateModuleCoreSkeleton()
    {
        $value = $this->getSubmitted('module.core');

        if (empty($value)) {
            $vars = array('@field' => $this->translation->text('Core'));
            $error = $this->translation->text('@field is required', $vars);
            $this->setError('module.core', $error);
            return false;
        }

        // Check if the value starts with a number
        if (preg_match('/^\d/', $value) !== 1) {
            $vars = array('@field' => $this->translation->text('Core'));
            $error = $this->translation->text('@field has invalid value', $vars);
            $this->setError('module.core', $error);
            return false;
        }

        return true;
    }

}
