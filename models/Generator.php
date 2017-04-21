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
     * The current module folder
     * @var string
     */
    protected $folder;

    /**
     * An array of module data
     * @var array
     */
    protected $data = array();

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
            'GPL-3.0+' => 'https://www.gnu.org/licenses/gpl-3.0.en.html',
            'MIT' => 'https://opensource.org/licenses/MIT',
            'Apache-2.0' => 'https://www.apache.org/licenses/LICENSE-2.0',
            'BSD-3-Clause' => 'https://opensource.org/licenses/BSD-3-Clause'
        );
    }

    /**
     * Generates module files and folders
     * @param array $data
     * @return bool
     */
    public function generate(array $data)
    {
        $this->setData($data);
        $this->createMainClass();

        if (!empty($this->data['structure'])) {
            $this->createStructure();
        }

        return $this->createZip();
    }

    /**
     * Creates various structure elements
     */
    protected function createStructure()
    {
        foreach ($this->data['structure'] as $element) {
            switch ($element) {
                case 'controller' :
                    $this->createStructureController();
                    break;
                case 'model':
                    $this->createStructureModel();
                    break;
                case 'helper':
                    $this->createStructureHelper();
                    break;
                case 'handler':
                    $this->createStructureHandler();
                    break;
                case 'override':
                    $this->createStructureOverride();
                    break;
                case 'template':
                    $this->createStructureTemplate();
                    break;
                case 'asset':
                    $this->createStructureAsset();
                    break;
            }
        }

        $this->write("{$this->folder}/README.md", 'readme');
        $this->write("{$this->folder}/.gitignore", 'gitignore');
        $this->write("{$this->folder}/composer.json", 'composer');
        $this->write("{$this->folder}/.scrutinizer.yml", 'scrutinizer');
    }

    /**
     * Creates asset structure
     */
    protected function createStructureAsset()
    {
        $subfolders = array(self::FOLDER_CSS, self::FOLDER_JS, self::FOLDER_IMAGE);

        foreach ($subfolders as $subfolder) {
            if ($this->prepareFolder("{$this->folder}/$subfolder")) {
                $filename = $subfolder == self::FOLDER_IMAGE ? 'image.png' : "common.$subfolder";
                $this->write("{$this->folder}/$subfolder/$filename", $subfolder);
            }
        }
    }

    /**
     * Creates a controller class
     */
    protected function createStructureController()
    {
        $folder = $this->folder . '/' . self::FOLDER_CONTROLLER;

        if ($this->prepareFolder($folder)) {
            $this->write("$folder/Settings.php", 'controller');
        }
    }

    /**
     * Cretates module main class
     */
    protected function createMainClass()
    {
        if ($this->prepareFolder($this->folder)) {
            $this->write("{$this->folder}/{$this->data['module']['class_name']}.php", 'main');
        }
    }

    /**
     * Creates a model class
     */
    protected function createStructureModel()
    {
        $folder = $this->folder . '/' . self::FOLDER_MODEL;

        if ($this->prepareFolder($folder)) {
            $this->write("$folder/{$this->data['module']['class_name']}.php", 'model');
        }
    }

    /**
     * Creates a helper class
     */
    protected function createStructureHelper()
    {
        $folder = $this->folder . '/' . self::FOLDER_HELPER;

        if ($this->prepareFolder($folder)) {
            $this->write("$folder/{$this->data['module']['class_name']}.php", 'helper');
        }
    }

    /**
     * Creates a handler class
     */
    protected function createStructureHandler()
    {
        $folder = $this->folder . '/' . self::FOLDER_HANDLER;

        if ($this->prepareFolder($folder)) {
            $this->write("$folder/{$this->data['module']['class_name']}.php", 'handler');
        }
    }

    /**
     * Creates module overrides
     */
    protected function createStructureOverride()
    {
        $folder = $this->folder . '/' . self::FOLDER_OVERRIDE . "/modules/{$this->data['module']['id']}";

        if ($this->prepareFolder($folder)) {
            $this->write("$folder/{$this->data['module']['class_name']}Override.php", 'override');
        }
    }

    /**
     * Creates a template sample
     */
    protected function createStructureTemplate()
    {
        $folder = $this->folder . '/' . self::FOLDER_TEMPLATE;

        if ($this->prepareFolder($folder)) {
            $this->write("$folder/settings.php", 'template');
        }
    }

    /**
     * Pack module folder into zip file
     * @return bool
     */
    protected function createZip()
    {
        $this->file = "{$this->folder}.zip";

        if (is_file($this->file)) {
            unlink($this->file);
        }

        $result = $this->zip->folder($this->folder, $this->file, $this->data['module']['id']);
        gplcart_file_delete_recursive($this->folder);
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
     * Recursively creates folders
     * @param string $folder
     * @return bool
     */
    protected function prepareFolder($folder)
    {
        return !is_dir($folder) && mkdir($folder, 0644, true);
    }

    /**
     * Prepares an array of data before rendering
     * @param array $data
     * @return string
     */
    protected function setData(array &$data)
    {
        $licenses = $this->getLicenses();

        $data['module']['class_name'] = $this->config->getModuleClassName($data['module']['id']);
        $data['module']['namespace'] = $this->config->getModuleClassNamespace($data['module']['id']);
        $data['module']['license_url'] = $licenses[$data['module']['license']] . ' ' . $data['module']['license'];

        $this->folder = gplcart_file_unique(GC_PRIVATE_DOWNLOAD_DIR . "/skeleton/{$data['module']['id']}");
        return $this->data = $data;
    }

    /**
     * Renders a template
     * @param string $template
     * @return string|null
     */
    protected function render($template)
    {
        $file = GC_MODULE_DIR . "/skeleton/templates/$template.php";

        if (!is_file($file)) {
            return null;
        }

        extract($this->data, EXTR_SKIP);
        ob_start();
        include $file;
        return ob_get_clean();
    }

    /**
     * Writes to a file using a template as an source
     * @param string $file
     * @param string $template
     * @return null
     */
    protected function write($file, $template)
    {
        $content = $this->render($template);

        if (!isset($content)) {
            return null;
        }

        if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            $content = "<?php\n\n$content";
        }

        file_put_contents($file, $content);
    }

}
