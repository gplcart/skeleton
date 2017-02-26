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
use gplcart\core\models\Language as LanguageModel;

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
     * Language model instance
     * @var \gplcart\core\models\Language $language
     */
    protected $language;

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
     * @param LanguageModel $language
     */
    public function __construct(LanguageModel $language, ZipHelper $zip)
    {
        parent::__construct();

        $this->zip = $zip;
        $this->language = $language;
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
        $result = $this->createMainClass($folder, $data);

        if ($result !== true) {
            return $result;
        }

        if (!empty($data['structure'])) {
            $result = $this->createStructure($folder, $data);
        }

        if ($result !== true) {
            return $result;
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
        $results = array();

        foreach ($data['structure'] as $element) {
            switch ($element) {
                case 'controller' :
                    $results[] = $this->createStructureController($folder, $data);
                    break;
                case 'model':
                    $results[] = $this->createStructureModel($folder, $data);
                    break;
                case 'helper':
                    $results[] = $this->createStructureHelper($folder, $data);
                    break;
                case 'handler':
                    $results[] = $this->createStructureHandler($folder, $data);
                    break;
                case 'override':
                    $results[] = $this->createStructureOverride($folder, $data);
                    break;
                case 'template':
                    $results[] = $this->createStructureTemplate($folder, $data);
                    break;
            }
        }

        $errors = array_filter($results, function($result) {
            return $result !== true;
        });

        return empty($errors) ? true : end($errors);
    }

    /**
     * Creates a controller class
     * @param string $folder
     * @param array $data
     * @return string|bool
     */
    protected function createStructureController($folder, array $data)
    {
        $folder .= '/' . self::FOLDER_CONTROLLER;
        $result = $this->prepareFolder($folder);

        if ($result === true) {
            return $this->write("$folder/Settings.php", 'module/controller', $data);
        }
        return $result;
    }

    /**
     * Cretates module main class
     * @param string $folder
     * @param array $data
     * @return boolean
     */
    protected function createMainClass($folder, array $data)
    {
        $result = $this->prepareFolder($folder);

        if ($result !== true) {
            return $result;
        }

        return $this->write("$folder/{$data['module']['class_name']}.php", 'module/main', $data);
    }

    /**
     * Creates a model class
     * @param string $folder
     * @param array $data
     */
    protected function createStructureModel($folder, array $data)
    {
        $folder .= '/' . self::FOLDER_MODEL;
        $result = $this->prepareFolder($folder);

        if ($result === true) {
            return $this->write("$folder/Model.php", 'module/model', $data);
        }
        return $result;
    }

    /**
     * Creates a helper class
     * @param string $folder
     * @param array $data
     * @return string|bool
     */
    protected function createStructureHelper($folder, array $data)
    {
        $folder .= '/' . self::FOLDER_HELPER;
        $result = $this->prepareFolder($folder);

        if ($result === true) {
            return $this->write("$folder/Helper.php", 'module/helper', $data);
        }

        return $result;
    }

    /**
     * Creates a handler class
     * @param string $folder
     * @param array $data
     */
    protected function createStructureHandler($folder, array $data)
    {
        $folder .= '/' . self::FOLDER_HANDLER;
        $result = $this->prepareFolder($folder);

        if ($result === true) {
            return $this->write("$folder/Handler.php", 'module/handler', $data);
        }

        return $result;
    }

    /**
     * Creates module overrides
     * @param string $folder
     * @param array $data
     */
    protected function createStructureOverride($folder, array $data)
    {
        $folder .= '/' . self::FOLDER_OVERRIDE . "/modules/{$data['module']['id']}";
        $result = $this->prepareFolder($folder);

        if ($result === true) {
            return $this->write("$folder/{$data['module']['class_name']}Override.php", 'module/override', $data);
        }

        return $result;
    }

    /**
     * Creates a template sample
     * @param string $folder
     * @param array $data
     */
    protected function createStructureTemplate($folder, array $data)
    {
        $folder .= '/' . self::FOLDER_TEMPLATE;
        $result = $this->prepareFolder($folder);

        if ($result === true) {
            return $this->write("$folder/template.php", 'module/template', $data);
        }
        return $result;
    }

    /**
     * Pack module folder into zip file
     * @param string $folder
     * @param array $data
     * @return bool
     */
    protected function createZip($folder, array $data)
    {
        $zip = "$folder.zip";
        $result = $this->zip->folder("$folder/*", $zip, $data['module']['id']);

        if ($result !== true) {
            return $this->language->text('Unable to create @path', array('@path' => $zip));
        }

        $this->file = $zip;
        gplcart_file_delete_recursive($folder);
        return true;
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
     * Creates recursive folders
     * @param string $folder
     * @return boolean
     */
    protected function prepareFolder($folder)
    {
        if (file_exists($folder)) {
            return $this->language->text('@path already exists', array('@path' => $folder));
        }

        if (!mkdir($folder, 0775, true)) {
            return $this->language->text('Unable to create @path', array('@path' => $folder));
        }

        return true;
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
     * @return string
     */
    protected function render($template, array $data)
    {
        extract($data, EXTR_SKIP);
        ob_start();
        include GC_MODULE_DIR . "/skeleton/templates/$template.php";
        return ob_get_clean();
    }

    /**
     * Writes to a file using a template as an source
     * @param string $file
     * @param string $template
     * @param array $data
     * @return boolean
     */
    protected function write($file, $template, array $data)
    {
        $content = "<?php\n\n" . $this->render($template, $data);

        if (file_put_contents($file, $content) === false) {
            return $this->language->text('Unable to write to file @path', array('@path' => $file));
        }
        return true;
    }

}
