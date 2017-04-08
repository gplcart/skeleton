<?php

/**
 * @package Skeleton module
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */

namespace gplcart\modules\skeleton\models;

use gplcart\core\Model;
use gplcart\core\models\Language as LanguageModel;

/**
 * Methods to extract hooks from source files
 */
class Extractor extends Model
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
    const PATTERN_HOOK = '/->fire\s*\(\s*(.+?)\s*\)/s';

    /**
     * Pattern to extract method names
     */
    const PATTERN_FUNCTION = '/function\s+(\w+)\s*\(/';

    /**
     * Pattern to extract class namespaces
     */
    const PATTERN_NAMESPACE = '/^namespace\s+(.+?)\s*;/';

    /**
     * Pattern to extract class names
     */
    const PATTERN_CLASS = '/^class\s+(.+?)(\s|$)/';

    /**
     * The current method while parsing hooks
     * @var string
     */
    protected $current_function = '';

    /**
     * The current class namespace while parsing hooks
     * @var string
     */
    protected $current_namespace = '';

    /**
     * The current class while parsing hooks
     * @var string
     */
    protected $current_class = '';

    /**
     * Language model instance
     * @var \gplcart\core\models\Language $language
     */
    protected $language;

    /**
     * Constructor
     * @param LanguageModel $language
     */
    public function __construct(LanguageModel $language)
    {
        parent::__construct();

        $this->language = $language;
    }

    /**
     * Returns an array of hook scopes/types
     * @return array
     */
    public function getHookScopes()
    {
        $items = array(
            'construct' => $this->language->text('Bootstraping, class construction'),
            'cli' => $this->language->text('Command line interface'),
            'template' => $this->language->text('Rendering templates'),
            'theme' => $this->language->text('Theme setup, JS/CSS assets'),
            'library' => $this->language->text('3-d party libraries'),
            'route' => $this->language->text('URL routing'),
            'cron' => $this->language->text('Scheduled operations'),
            'address' => $this->language->text('User addresses'),
            'backup' => $this->language->text('Backup'),
            'cart' => $this->language->text('Shopping cart'),
            'category' => $this->language->text('Categories'),
            'city' => $this->language->text('Cities'),
            'collection' => $this->language->text('Collections'),
            'compare' => $this->language->text('Product comparison'),
            'condition' => $this->language->text('Trigger conditions'),
            'country' => $this->language->text('Countries'),
            'currency' => $this->language->text('Currencies'),
            'editor' => $this->language->text('Editing theme files'),
            'export' => $this->language->text('Export (products, categories etc.)'),
            'field' => $this->language->text('Product fields'),
            'file' => $this->language->text('Files'),
            'filter' => $this->language->text('HTML filters'),
            'imagestyle' => $this->language->text('Processing images'),
            'import' => $this->language->text('Import (products, categories etc.)'),
            'install' => $this->language->text('Installation'),
            'job' => $this->language->text('Bulk jobs'),
            'language' => $this->language->text('Languages, localization'),
            'mail' => $this->language->text('Sending E-mail'),
            'order' => $this->language->text('Ordering products'),
            'page' => $this->language->text('Pages'),
            'payment' => $this->language->text('Payment methods'),
            'price' => $this->language->text('Prices'),
            'product' => $this->language->text('Products'),
            'rating' => $this->language->text('Ratings'),
            'report' => $this->language->text('Reporting PHP errors, system events'),
            'review' => $this->language->text('Reviews'),
            'search' => $this->language->text('Searching'),
            'shipping' => $this->language->text('Shipping methods'),
            'sku' => $this->language->text('Product SKU'),
            'state' => $this->language->text('Country states'),
            'store' => $this->language->text('Stores'),
            'transaction' => $this->language->text('Payment transactions'),
            'trigger' => $this->language->text('Triggers'),
            'user' => $this->language->text('Users'),
            'validator' => $this->language->text('Validating'),
            'wishlist' => $this->language->text('Wishlists'),
            'zone' => $this->language->text('Geo zones'),
            'module' => $this->language->text('Modules'),
            'oauth' => $this->language->text('Oauth authentication')
        );

        asort($items);
        return $items;
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

        array_walk($exploded, function(&$param) {
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
        $scanned = $this->scan($options['directory']);

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

                $extracted['file'] = gplcart_relative_path($file);
                $prepared = $this->prepareHook($extracted);

                // Filter by scopes
                if (!empty($options['scopes']) && !$this->inScope($prepared['hook']['name'], $options['scopes'])) {
                    continue;
                }

                // Test extracted class and method
                if (!method_exists($prepared['namespaced_class'], $prepared['function'])) {
                    $errors[$prepared['hook']['name']] = $prepared;
                    continue;
                }

                $success[$prepared['hook']['name']] = $prepared;
            }
        }

        return array('success' => $success, 'errors' => $errors, 'files' => $scanned);
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
     */
    public function parse($file)
    {
        $this->current_class = $this->current_namespace = '';

        $handle = fopen($file, 'r');

        if (!is_resource($handle)) {
            trigger_error("Failed to open file $file");
            return array();
        }

        $row = 0;
        $extracted = array();
        $lines = self::MAX_LINES;

        while ($lines && $line = fgets($handle, self::MAX_COLS)) {

            $row++;
            $lines--;

            $namespace = $class = $function = $hook = array();

            preg_match(self::PATTERN_NAMESPACE, $line, $namespace);

            if (!empty($namespace[1])) {
                $this->current_namespace = $namespace[1];
            }

            preg_match(self::PATTERN_CLASS, $line, $class);

            if (!empty($class[1])) {
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
     * @return integer|array
     */
    public function scan($directory, $count = false)
    {
        $files = array_filter(gplcart_file_scan_recursive($directory), function($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'php';
        });

        if ($count) {
            return count($files);
        }

        sort($files);
        return $files;
    }

}
