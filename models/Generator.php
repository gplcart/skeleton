<?php

/**
 * @package Skeleton
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */

namespace gplcart\modules\skeleton\models;

use gplcart\core\Model;
use gplcart\core\models\Language as LanguageModel;

/**
 * Methods to generate module skeletons
 */
class Generator extends Model
{

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
     * 
     * @param array $data
     * @param array $hooks
     */
    public function generate(array $data)
    {
        $this->createMainClass($data);
    }

    /**
     * 
     * @param array $data
     */
    protected function createMainClass(array $data)
    {
        // Create module folder
        $folder = GC_PRIVATE_DOWNLOAD_DIR . "/{$data['module']['id']}";

        //TODO: check existance in validator
        if (file_exists($folder)) {
            return $this->language->text('Module folder @path already exists', array('@path' => $folder));
        }

        if (!mkdir($folder, 0775, true)) {
            return $this->language->text('Unable to create module folder @path', array('@path' => $folder));
        }

        $data['module']['namespace'] = $this->config->getModuleClassNamespace($data['module']['id']);
        $data['module']['class_name'] = $this->config->getModuleClassName($data['module']['id']);

        $content = "<?php\n\n" . $this->render('module/main', $data);

        $file = "$folder/{$data['module']['class_name']}.php";

        if (file_put_contents($file, $content) === false) {
            return $this->language->text('Unable to create main module file @path', array('@path' => $file));
        }

        if (!class_exists($data['module']['namespace'])) {
            $this->language->text('Class @namespace not found or has invalid code', array('@namespace' => $data['module']['namespace']));
        }

        return true;
    }

    /**
     * 
     * @param type $template
     * @param array $data
     * @return type
     */
    protected function render($template, array $data)
    {
        extract($data, EXTR_SKIP);
        ob_start();
        include GC_MODULE_DIR . "/skeleton/templates/$template.php";
        return ob_get_clean();
    }

}
