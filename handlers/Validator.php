<?php

/**
 * @package Skeleton module
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */

namespace gplcart\modules\skeleton\handlers;

use gplcart\core\Config;
use gplcart\core\handlers\validator\Base as BaseValidator;

/**
 * Provides methods to validate Skeleton module data
 */
class Validator extends BaseValidator
{

    /**
     * Config class instance
     * @var \gplcart\core\Config $config
     */
    protected $config;

    /**
     * Constructor
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        parent::__construct();

        $this->config = $config;
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
            $vars = array('@field' => $this->language->text('Author'));
            $error = $this->language->text('@field is required', $vars);
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
            $vars = array('@field' => $this->language->text('ID'));
            $error = $this->language->text('@field is required', $vars);
            $this->setError('module.id', $error);
            return false;
        }

        if (!$this->config->validModuleId($value)) {
            $vars = array('@field' => $this->language->text('ID'));
            $error = $this->language->text('@field has invalid value', $vars);
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
            $vars = array('@field' => $this->language->text('Version'));
            $error = $this->language->text('@field is required', $vars);
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
            $vars = array('@field' => $this->language->text('Core'));
            $error = $this->language->text('@field is required', $vars);
            $this->setError('module.core', $error);
            return false;
        }

        // Check if the value starts with a number
        if (preg_match('/^\d/', $value) !== 1) {
            $vars = array('@field' => $this->language->text('Core'));
            $error = $this->language->text('@field has invalid value', $vars);
            $this->setError('module.core', $error);
            return false;
        }

        return true;
    }

}
