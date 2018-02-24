<?php

/**
 * @package Skeleton module
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */

namespace gplcart\modules\skeleton\controllers;

use gplcart\core\controllers\backend\Controller;
use gplcart\modules\skeleton\models\Extractor;
use gplcart\modules\skeleton\models\Generator;

/**
 * Handles incoming requests and outputs data related to Skeleton module
 */
class Skeleton extends Controller
{

    /**
     * Extractor model instance
     * @var \gplcart\modules\skeleton\models\Extractor $extractor
     */
    protected $extractor;

    /**
     * Module generator instance
     * @var \gplcart\modules\skeleton\models\Generator $generator
     */
    protected $generator;

    /**
     * Skeleton constructor.
     * @param Extractor $extractor
     * @param Generator $generator
     */
    public function __construct(Extractor $extractor, Generator $generator)
    {
        parent::__construct();

        $this->extractor = $extractor;
        $this->generator = $generator;
    }

    /**
     * Displays the generate skeleton page
     */
    public function editSkeleton()
    {
        $this->downloadSkeleton();
        $this->setTitleEditSkeleton();
        $this->setBreadcrumbEditSkeleton();

        $hooks = gplcart_array_split($this->extractor->getHookScopes(), 2);

        $this->setData('hooks', $hooks);
        $this->setData('licenses', $this->generator->getLicenses());

        $author = $this->getUser('name') . ' <' . $this->getUser('email') . '>';
        $this->setData('author', $author);

        $this->submitSkeleton();
        $this->generateFromJobSkeleton();
        $this->outputEditSkeleton();
    }

    /**
     * Generate a module skeleton after hook extraction
     */
    protected function generateFromJobSkeleton()
    {
        if ($this->isQuery('skeleton_hooks_extracted')) {

            $job = $this->job->get('skeleton');

            if (isset($job['context']['extracted'])) {

                $data = $job['data']['submitted'];
                $data['hooks'] = $job['context']['extracted'];

                if (!empty($job['errors'])) {
                    $this->setMessage($this->text('@num errors occurred during hook extraction'), 'warning', true);
                }

                $this->generateSkeleton($data);
            }
        }
    }

    /**
     * Handles submitted data
     */
    protected function submitSkeleton()
    {
        if ($this->isPosted('create') && $this->validateSkeleton()) {
            $this->createSkeleton();
        }
    }

    /**
     * Validates an array of submitted data
     */
    protected function validateSkeleton()
    {
        $this->setSubmitted('skeleton', null, false);

        $this->validateElement('module.id', 'required');
        $this->validateElement('module.core', 'required');
        $this->validateElement('module.author', 'required');
        $this->validateElement('module.version', 'required');

        if ($this->hasErrors()) {
            return false;
        }

        $module_id = $this->getSubmitted('module.id');

        if (!$this->module->isValidId($module_id)) {
            $error = $this->text('@field has invalid value', array('@field' => $this->text('ID')));
            $this->setError('module.id', $error);
        }

        $core = $this->getSubmitted('module.core');

        if (preg_match('/^\d/', $core) !== 1) {
            $error = $this->text('@field has invalid value', array('@field' => $this->text('Core')));
            $this->setError('module.core', $error);
        }

        if ($this->hasErrors()) {
            return false;
        }

        $name = $this->getSubmitted('module.name');

        if (empty($name)) {
            $this->setSubmitted('module.name', $module_id);
        }

        return true;
    }

    /**
     * Creates a skeleton for a module
     */
    protected function createSkeleton()
    {
        if ($this->isSubmitted('hooks')) {
            $this->setExtractionJobSkeleton();
        } else {
            $this->generateSkeleton($this->getSubmitted());
        }
    }

    /**
     * Generate skeleton files
     * @param array $data
     */
    protected function generateSkeleton(array $data)
    {
        if (!$this->generator->generate($data)) {
            $this->redirect('', $this->text('An error occurred'), 'warning');
        }

        $file = gplcart_string_encode($this->generator->getZip());
        $vars = array('@url' => $this->url('', array('download' => $file)));
        $this->redirect('', $this->text('Module has been generated. <a href="@url">Download</a>', $vars), 'success');
    }

    /**
     * Output generated skeleton to download
     */
    protected function downloadSkeleton()
    {
        $path = $this->getQuery('download');

        if (!empty($path)) {
            $this->download(gplcart_string_decode($path));
        }
    }

    /**
     * Set up hook extraction job
     */
    protected function setExtractionJobSkeleton()
    {
        $job = array(
            'id' => 'skeleton',
            'total' => $this->getTotalExtractSkeleton(),
            'data' => array('submitted' => $this->getSubmitted()),
            'redirect' => array(
                'finish' => $this->url('', array('skeleton_hooks_extracted' => 1))
            ),
            'message' => array(
                'process' => $this->text('Extracting hooks from the source files...'),
            ),
        );

        $this->job->submit($job);
    }

    /**
     * Returns a total number of scanned files to extract hooks from
     * @return integer
     */
    protected function getTotalExtractSkeleton()
    {
        return (int) $this->extractor->scan(GC_DIR_CORE, true);
    }

    /**
     * Set title on the edit module page
     */
    protected function setBreadcrumbEditSkeleton()
    {
        $breadcrumb = array(
            'text' => $this->text('Dashboard'),
            'url' => $this->url('admin')
        );

        $this->setBreadcrumb($breadcrumb);
    }

    /**
     * Set title on the generate skeleton page
     */
    protected function setTitleEditSkeleton()
    {
        $this->setTitle($this->text('Skeleton'));
    }

    /**
     * Render and output the generate skeleton page
     */
    protected function outputEditSkeleton()
    {
        $this->output('skeleton|edit');
    }

}
