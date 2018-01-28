<?php

/**
 * @package Skeleton module
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */

namespace gplcart\modules\skeleton\models;

use gplcart\core\helpers\Zip as ZipHelper;
use gplcart\core\Module;

/**
 * Methods to generate module skeletons
 */
class Generator
{

    /**
     * Module class instance
     * @var \gplcart\core\Module $module
     */
    protected $module;

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
     * The current module directory
     * @var string
     */
    protected $directory;

    /**
     * An array of module data
     * @var array
     */
    protected $data = array();

    /**
     * @param Module $module
     * @param ZipHelper $zip
     */
    public function __construct(Module $module, ZipHelper $zip)
    {
        $this->zip = $zip;
        $this->module = $module;
    }

    /**
     * Returns an array of licenses and their URLs
     * @return array
     */
    public function getLicenses()
    {
        return array(
            'GPL-3.0-or-later' => 'https://www.gnu.org/licenses/gpl-3.0.en.html',
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
        $this->createManifest();
        $this->createStructure();

        return $this->createZip();
    }

    /**
     * Create module manifest file
     */
    protected function createManifest()
    {
        $data = array(
            'name' => $this->data['module']['name'],
            'core' => $this->data['module']['core'],
            'author' => $this->data['module']['author'],
            'license' => $this->data['module']['license'],
            'version' => $this->data['module']['version'],
            'description' => $this->data['module']['description']
        );

        if (!empty($this->data['structure']) && in_array('configurable', $this->data['structure'])) {
            $data['configure'] = 'admin/module/settings/' . $this->data['module']['id'];
            $data['settings'] = array();
        }

        $json = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        file_put_contents("$this->directory/module.json", $json);
    }

    /**
     * Creates various structure elements
     */
    protected function createStructure()
    {
        $this->write("$this->directory/README.md", 'readme');
        $this->write("$this->directory/.gitignore", 'gitignore');
        $this->write("$this->directory/composer.json", 'composer');
        $this->write("$this->directory/.scrutinizer.yml", 'scrutinizer');

        if (empty($this->data['structure'])) {
            return null;
        }

        foreach ($this->data['structure'] as $element) {
            switch ($element) {
                case 'controller':
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
                case 'locale':
                    $this->createStructureTranslation();
                    break;
                case 'tests':
                    $this->createStructureTests();
                    break;
                case 'config_files':
                    $this->createStructureConfigFiles();
                    break;
            }
        }
    }

    /**
     * Creates asset structure
     */
    protected function createStructureAsset()
    {
        foreach (array('css', 'js') as $folder) {
            if ($this->prepareFolder("$this->directory/$folder")) {
                $this->write("$this->directory/$folder/common.$folder", $folder);
            }
        }

        $this->generateImage();
    }

    /**
     * Generate an image sample
     * @return bool
     */
    protected function generateImage()
    {
        $directory = "$this->directory/image";

        if (!mkdir($directory, 0775, true)) {
            return false;
        }

        $im = imagecreate(100, 100);
        imagecolorallocate($im, 255, 255, 255);
        $text = imagecolorallocate($im, 0, 0, 255);
        imagestring($im, 5, 0, 0, $this->data['module']['name'], $text);
        $result = imagepng($im, "$directory/image.png");
        imagedestroy($im);
        return $result;
    }

    /**
     * Creates translations structure
     */
    protected function createStructureTranslation()
    {
        $folder = "$this->directory/translations";

        if ($this->prepareFolder($folder)) {
            $name = $this->data['module']['name'];
            gplcart_file_csv("$folder/en.csv", array($name, $name));
        }
    }

    /**
     * Creates a controller class
     */
    protected function createStructureController()
    {
        $folder = "$this->directory/controllers";

        if ($this->prepareFolder($folder)) {
            $this->write("$folder/Settings.php", 'controller');
        }
    }

    /**
     * Creates module main class
     */
    protected function createMainClass()
    {
        if ($this->prepareFolder($this->directory)) {
            $this->write("$this->directory/{$this->data['module']['class_name']}.php", 'main');
        }
    }

    /**
     * Creates a model class
     */
    protected function createStructureModel()
    {
        $folder = "$this->directory/models";

        if ($this->prepareFolder($folder)) {
            $this->write("$folder/{$this->data['module']['class_name']}.php", 'model');
        }
    }

    /**
     * Creates a helper class
     */
    protected function createStructureHelper()
    {
        $folder = "$this->directory/helpers";

        if ($this->prepareFolder($folder)) {
            $this->write("$folder/{$this->data['module']['class_name']}.php", 'helper');
        }
    }

    /**
     * Creates a handler class
     */
    protected function createStructureHandler()
    {
        $folder = "$this->directory/handlers";

        if ($this->prepareFolder($folder)) {
            $this->write("$folder/{$this->data['module']['class_name']}.php", 'handler');
        }
    }

    /**
     * Creates module overrides
     */
    protected function createStructureOverride()
    {
        $folder = "$this->directory/override/classes/modules/{$this->data['module']['id']}";

        if ($this->prepareFolder($folder)) {
            $this->write("$folder/{$this->data['module']['class_name']}Override.php", 'override');
        }
    }

    /**
     * Creates a template sample
     */
    protected function createStructureTemplate()
    {
        $folder = "$this->directory/templates";

        if ($this->prepareFolder($folder)) {
            $this->write("$folder/settings.php", 'template');
        }
    }

    /**
     * Creates files for unit testing
     */
    protected function createStructureTests()
    {
        $dir = "$this->directory/tests";

        if ($this->prepareFolder($dir)) {
            $this->prepareFolder("$dir/support");
            $this->write("$this->directory/phpunit.xml", 'test_xml');
            $this->write("$dir/support/bootstrap.php", 'test_bootstrap');
            $this->write("$dir/{$this->data['module']['class_name']}.php", 'test');
        }
    }

    /**
     * Creates config files structure
     */
    protected function createStructureConfigFiles()
    {
        $dir = "$this->directory/config";

        if ($this->prepareFolder($dir)) {
            $this->write("$dir/common.php", 'config');
        }
    }

    /**
     * Pack module folder into zip file
     * @return bool
     */
    protected function createZip()
    {
        $this->file = "$this->directory.zip";

        if (is_file($this->file)) {
            unlink($this->file);
        }

        $result = $this->zip->directory($this->directory, $this->file, $this->data['module']['id']);
        gplcart_file_delete_recursive($this->directory);
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
        return !file_exists($folder) && mkdir($folder, 0775, true);
    }

    /**
     * Prepares an array of data before rendering
     * @param array $data
     * @return array
     */
    protected function setData(array &$data)
    {
        $licenses = $this->getLicenses();
        $class = $this->module->getClass($data['module']['id']);

        $data['module']['namespace'] = $class;
        $data['module']['class_name'] = substr($class, strrpos($class, '\\') + 1);
        $data['module']['license_url'] = $licenses[$data['module']['license']] . ' ' . $data['module']['license'];

        $this->directory = gplcart_file_private_module('skeleton', $data['module']['id'], true);
        return $this->data = $data;
    }

    /**
     * Renders a template
     * @param string $template
     * @return string|null
     */
    protected function render($template)
    {
        $file = GC_DIR_MODULE . "/skeleton/templates/code/$template.php";

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
     * @return null|bool
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

        return file_put_contents($file, $content) !== false;
    }

}
