<?php

/**
 * @package Skeleton module
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */

namespace gplcart\modules\skeleton\models;

use RuntimeException;

/**
 * Methods to extract hooks from source files
 */
class Extractor
{

    /**
     * Max number of rows to parse
     */
    const MAX_LINES = 5000;

    /**
     * Max columns in a single line to parse
     */
    const MAX_COLS = 500;

    /**
     * Pattern to extract hook arguments
     */
    const PATTERN_HOOK = '/->attach\(\s*(.+?)\s*\)/s';

    /**
     * Pattern to extract method names
     */
    const PATTERN_FUNCTION = '/function +(\w+)\s*\(/';

    /**
     * Pattern to extract class namespaces
     */
    const PATTERN_NAMESPACE = '/^namespace +(.+?)\s*;/';

    /**
     * Pattern to extract class names
     */
    const PATTERN_CLASS = '/(?:^abstract +|^)class +(.+?)(\s+|\n\r|$)/';

    /**
     * The current method while parsing hooks
     * @var string
     */
    protected $current_function;

    /**
     * The current class name space while parsing hooks
     * @var string
     */
    protected $current_namespace;

    /**
     * The current class while parsing hooks
     * @var string
     */
    protected $current_class;

    /**
     * Returns an array of hook scopes/types
     * @return array
     */
    public function getHookScopes()
    {
        $scopes = gplcart_config_get(__DIR__ . '/../config/scopes.php');
        asort($scopes);
        return $scopes;
    }

    /**
     * Prepares extracted hook data
     * @param array $data
     * @return array
     */
    public function prepareHook(array $data)
    {
        $data['namespaced_class'] = $data['namespace'] . '\\' . $data['class'];

        $exploded = $this->prepareHookArguments(explode(',', $data['hook']), $data);

        // Shift hook name and trim single/double quotes from it
        // Use strtok() to get everything before | which separates hook name and module ID
        $name = strtok(preg_replace('/^(\'(.*)\'|"(.*)")$/', '$2$3', array_shift($exploded)), '|');

        array_walk($exploded, function (&$param) {
            if (strpos($param, '$') === 0) {
                $param = "&$param";
            }
        });

        $data['hook'] = array(
            'name' => $name,
            'arguments' => $exploded,
            'uppercase_name' => implode('', array_map('ucfirst', explode('.', $name)))
        );

        return $data;
    }

    /**
     * Returns an array of prepared hook arguments
     * @param array $arguments
     * @param array $data
     * @return array
     */
    protected function prepareHookArguments(array $arguments, array $data)
    {
        $i = 0;
        foreach ($arguments as &$argument) {
            $argument = trim($argument);
            // Replace arguments which aren't plain variables, e.g $data['key']
            // TODO: check uniqueness
            if (strpos($argument, '$') === 0 && preg_match('/^[A-Za-z0-9_\$]+$/', $argument) !== 1) {
                $argument = '$param' . $i;
            } else if ($argument === '$this') {
                // Replace $this argument with type hinting $object
                $argument = '\\' . $data['namespaced_class'] . ' $object';
            }

            $i++;
        }
        return $arguments;
    }

    /**
     * Returns an array of extracted hooks
     * @param array $options
     * @return array
     */
    public function getHooks(array $options)
    {
        $scanned = (array) $this->scan($options['directory']);

        // Pager
        if (!empty($options['limit'])) {
            list($start, $length) = $options['limit'];
            $scanned = array_slice($scanned, $start, $length, true);
        }

        if (empty($scanned)) {
            return array();
        }

        $success = $errors = array();

        foreach ($scanned as $file) {
            foreach ($this->parse($file) as $extracted) {

                $extracted['file'] = gplcart_path_relative($file);
                $prepared = $this->prepareHook($extracted);

                if (!empty($options['scopes']) && !$this->inScope($prepared['hook']['name'], $options['scopes'])) {
                    continue;
                }

                if (!method_exists($prepared['namespaced_class'], $prepared['function'])) {
                    $errors[$prepared['hook']['name']] = $prepared;
                    continue;
                }

                $success[$prepared['hook']['name']] = $prepared;
            }
        }

        return array(
            'success' => $success,
            'errors' => $errors,
            'files' => $scanned
        );
    }

    /**
     * Whether a given hook name is in array of scopes
     * @param string $hook
     * @param array $scopes
     * @return boolean
     */
    protected function inScope($hook, array $scopes)
    {
        foreach ($scopes as $scope) {
            $parts = explode('.', $hook);
            if (reset($parts) === $scope) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns an array of extracted hooks from a single file
     * @param string $file
     * @return array
     * @throws RuntimeException
     */
    public function parse($file)
    {
        $this->current_class = $this->current_namespace = null;

        $handle = fopen($file, 'r');

        if (!is_resource($handle)) {
            throw new RuntimeException("Failed to open file $file");
        }

        $row = 0;
        $extracted = array();
        $lines = self::MAX_LINES;

        while ($lines && $line = fgets($handle, self::MAX_COLS)) {

            $row++;
            $lines--;

            $namespace = $class = $function = $hook = array();

            preg_match(self::PATTERN_NAMESPACE, $line, $namespace);

            // Namespace and class name should occur once per file
            // If it has been set, skip others to avoid errors
            if (!isset($this->current_namespace) && !empty($namespace[1])) {
                $this->current_namespace = $namespace[1];
            }

            preg_match(self::PATTERN_CLASS, $line, $class);

            if (!isset($this->current_class) && !empty($class[1])) {
                $this->current_class = $class[1];
            }

            preg_match(self::PATTERN_FUNCTION, $line, $function);

            if (!empty($function[1])) {
                $this->current_function = $function[1];
            }

            preg_match(self::PATTERN_HOOK, $line, $hook);

            if (empty($hook[1])) {
                continue;
            }

            $extracted[] = array(
                'row' => $row,
                'hook' => $hook[1],
                'class' => $this->current_class,
                'function' => $this->current_function,
                'namespace' => $this->current_namespace
            );
        }

        fclose($handle);
        return $extracted;
    }

    /**
     * Returns an array of scanned files to extract hooks from or counts extracted items
     * @param string $directory
     * @param bool $count
     * @return integer|array
     */
    public function scan($directory, $count = false)
    {
        $files = array_filter(gplcart_file_scan_recursive($directory), function ($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'php';
        });

        if ($count) {
            return count($files);
        }

        sort($files);
        return $files;
    }

}
