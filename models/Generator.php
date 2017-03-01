<?php

/**
 * @package Skeleton module
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */

namespace gplcart\modules\skeleton\models;

use gplcart\core\Model;
use gplcart\core\helpers\Zip as ZipHelper;

/**
 * Methods to generate module skeletons
 */
class Generator extends Model
{

    /**
     * Name of folder that contains module controllers
     */
    const FOLDER_CONTROLLER = 'controllers';

    /**
     * Name of folder that contains module helpers
     */
    const FOLDER_HELPER = 'helpers';

    /**
     * Name of folder that contains module models
     */
    const FOLDER_MODEL = 'models';

    /**
     * Name of folder that contains module handlers
     */
    const FOLDER_HANDLER = 'handlers';

    /**
     * Name of folder that contains module templates
     */
    const FOLDER_TEMPLATE = 'templates';

    /**
     * Name of folder that contains module overrides
     */
    const FOLDER_OVERRIDE = 'override';

    /**
     * Name of folder that contains JS assets
     */
    const FOLDER_JS = 'js';

    /**
     * Name of folder that contains CSS assets
     */
    const FOLDER_CSS = 'css';

    /**
     * Name of folder that contains images
     */
    const FOLDER_IMAGE = 'image';

    /**
     * Zip class instance
     * @var \gplcart\core\helpers\Zip $zip
     */
    protected $zip;

    /**
     * Full path to a ZIP file containing generated module
     * @var string
     */
    protected $file;

    /**
     * Constructor
     * @param ZipHelper $zip
     */
    public function __construct(ZipHelper $zip)
    {
        parent::__construct();

        $this->zip = $zip;
    }

    /**
     * Returns an array of licenses and their URLs
     * @return array
     */
    public function getLicenses()
    {
        return array(
            'GNU General Public License 3.0' => 'https://www.gnu.org/licenses/gpl-3.0.en.html',
            'MIT License' => 'https://opensource.org/licenses/MIT',
            'Apache License 2.0' => 'https://www.apache.org/licenses/LICENSE-2.0',
            '3-Clause BSD License' => 'https://opensource.org/licenses/BSD-3-Clause',
            'GNU Lesser General Public License' => 'https://www.gnu.org/licenses/lgpl-3.0.en.html'
        );
    }

    /**
     * Generates module files and folders
     * @param array $data
     */
    public function generate(array $data)
    {
        $this->prepareData($data);

        $folder = $this->getModuleFolder($data['module']['id']);
        $this->createMainClass($folder, $data);

        if (!empty($data['structure'])) {
            $this->createStructure($folder, $data);
        }

        return $this->createZip($folder, $data);
    }

    /**
     * Creates various structure elements
     * @param string $folder
     * @param array $data
     */
    protected function createStructure($folder, array $data)
    {
        foreach ($data['structure'] as $element) {
            switch ($element) {
                case 'controller' :
                    $this->createStructureController($folder, $data);
                    break;
                case 'model':
                    $this->createStructureModel($folder, $data);
                    break;
                case 'helper':
                    $this->createStructureHelper($folder, $data);
                    break;
                case 'handler':
                    $this->createStructureHandler($folder, $data);
                    break;
                case 'override':
                    $this->createStructureOverride($folder, $data);
                    break;
                case 'template':
                    $this->createStructureTemplate($folder, $data);
                    break;
                case 'asset':
                    $this->createStructureAsset($folder, $data);
                    break;
            }
        }
    }

    /**
     * Creates asset structure
     * @param string $folder
     * @param array $data
     */
    protected function createStructureAsset($folder, array $data)
    {
        $subfolders = array(self::FOLDER_CSS, self::FOLDER_JS, self::FOLDER_IMAGE);
        foreach ($subfolders as $subfolder) {
            if ($this->prepareFolder("$folder/$subfolder")) {
                $filename = $subfolder == self::FOLDER_IMAGE ? 'image.png' : "common.$subfolder";
                $this->write("$folder/$subfolder/$filename", $subfolder, $data, false);
            }
        }
    }

    /**
     * Creates a controller class
     * @param string $folder
     * @param array $data
     */
    protected function createStructureController($folder, array $data)
    {
        $folder .= '/' . self::FOLDER_CONTROLLER;
        if ($this->prepareFolder($folder)) {
            $this->write("$folder/Settings.php", 'controller', $data);
        }
    }

    /**
     * Cretates module main class
     * @param string $folder
     * @param array $data
     * @return boolean
     */
    protected function createMainClass($folder, array $data)
    {
        if ($this->prepareFolder($folder)) {
            $this->write("$folder/{$data['module']['class_name']}.php", 'main', $data);
        }
    }

    /**
     * Creates a model class
     * @param string $folder
     * @param array $data
     */
    protected function createStructureModel($folder, array $data)
    {
        $folder .= '/' . self::FOLDER_MODEL;
        if ($this->prepareFolder($folder)) {
            $this->write("$folder/{$data['module']['class_name']}.php", 'model', $data);
        }
    }

    /**
     * Creates a helper class
     * @param string $folder
     * @param array $data
     */
    protected function createStructureHelper($folder, array $data)
    {
        $folder .= '/' . self::FOLDER_HELPER;
        if ($this->prepareFolder($folder)) {
            $this->write("$folder/{$data['module']['class_name']}.php", 'helper', $data);
        }
    }

    /**
     * Creates a handler class
     * @param string $folder
     * @param array $data
     */
    protected function createStructureHandler($folder, array $data)
    {
        $folder .= '/' . self::FOLDER_HANDLER;
        if ($this->prepareFolder($folder)) {
            $this->write("$folder/{$data['module']['class_name']}.php", 'handler', $data);
        }
    }

    /**
     * Creates module overrides
     * @param string $folder
     * @param array $data
     */
    protected function createStructureOverride($folder, array $data)
    {
        $folder .= '/' . self::FOLDER_OVERRIDE . "/modules/{$data['module']['id']}";
        if ($this->prepareFolder($folder)) {
            $this->write("$folder/{$data['module']['class_name']}Override.php", 'override', $data);
        }
    }

    /**
     * Creates a template sample
     * @param string $folder
     * @param array $data
     */
    protected function createStructureTemplate($folder, array $data)
    {
        $folder .= '/' . self::FOLDER_TEMPLATE;
        if ($this->prepareFolder($folder)) {
            $this->write("$folder/settings.php", 'template', $data);
        }
    }

    /**
     * Pack module folder into zip file
     * @param string $folder
     * @param array $data
     * @return bool
     */
    protected function createZip($folder, array $data)
    {
        $this->file = "$folder.zip";
        $result = $this->zip->folder("$folder/*", $this->file, $data['module']['id']);
        gplcart_file_delete_recursive($folder);
        return $result;
    }

    /**
     * Returns a path to zip file
     * @return string
     */
    public function getZip()
    {
        return $this->file;
    }

    /**
     * Returns a full path to the module folder
     * @param integer $module_id
     * @return string
     */
    protected function getModuleFolder($module_id)
    {
        return GC_PRIVATE_DOWNLOAD_DIR . "/$module_id";
    }

    /**
     * Recursively creates folders
     * @param string $folder
     * @return boolean
     */
    protected function prepareFolder($folder)
    {
        return (!file_exists($folder) && mkdir($folder, 0775, true));
    }

    /**
     * Prepares an array of data before rendering
     * @param array $data
     * @return string
     */
    protected function prepareData(array &$data)
    {
        $licenses = $this->getLicenses();

        $data['module']['class_name'] = $this->config->getModuleClassName($data['module']['id']);
        $data['module']['namespace'] = $this->config->getModuleClassNamespace($data['module']['id']);
        $data['module']['license_url'] = $licenses[$data['module']['license']] . ' ' . $data['module']['license'];
        return $data;
    }

    /**
     * Renders a template
     * @param string $template
     * @param array $data
     * @return string|null
     */
    protected function render($template, array $data)
    {
        $file = GC_MODULE_DIR . "/skeleton/templates/$template.php";

        if (!is_readable($file)) {
            return null;
        }

        extract($data, EXTR_SKIP);
        ob_start();
        include $file;
        return ob_get_clean();
    }

    /**
     * Writes to a file using a template as an source
     * @param string $file
     * @param string $template
     * @param array $data
     * @param bool $php
     * @return null
     */
    protected function write($file, $template, array $data, $php = true)
    {
        $content = $this->render($template, $data);

        if (!isset($content)) {
            return null;
        }

        if ($php) {
            $content = "<?php\n\n$content";
        }

        file_put_contents($file, $content);
    }

}
