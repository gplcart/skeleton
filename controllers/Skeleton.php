<?php

/**
 * @package Skeleton module
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */

namespace gplcart\modules\skeleton\controllers;

use gplcart\modules\skeleton\models\Generator as SkeletonGeneratorModel,
    gplcart\modules\skeleton\models\Extractor as SkeletonExtractorModel;
use gplcart\core\controllers\backend\Controller as BackendController;

/**
 * Handles incoming requests and outputs data related to Skeleton module
 */
class Skeleton extends BackendController
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
     * Constructor
     * @param SkeletonExtractorModel $extractor
     * @param SkeletonGeneratorModel $generator
     */
    public function __construct(SkeletonExtractorModel $extractor,
            SkeletonGeneratorModel $generator)
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
     * @return null
     */
    protected function generateFromJobSkeleton()
    {
        if (!$this->isQuery('skeleton_hooks_extracted')) {
            return null;
        }

        $job = $this->job->get('skeleton');

        if (!isset($job['context']['extracted'])) {
            return null;
        }

        $data = $job['data']['submitted'];
        $data['hooks'] = $job['context']['extracted'];

        if (!empty($job['errors'])) {
            $this->setMessage($this->text('@num errors occurred during hook extraction'), 'warning', true);
        }

        $this->generateSkeleton($data);
        return null;
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
        $this->validateComponent('skeleton');

        if ($this->hasErrors()) {
            return false;
        }

        $name = $this->getSubmitted('module.name');
        $module_id = $this->getSubmitted('module.id');

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
        $breadcrumbs = array();

        $breadcrumbs[] = array(
            'text' => $this->text('Dashboard'),
            'url' => $this->url('admin')
        );

        $this->setBreadcrumbs($breadcrumbs);
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
