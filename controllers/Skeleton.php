<?php

/**
 * @package GPL Cart core
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
     * Displays the extractor page
     */
    public function editSkeleton()
    {


        $this->setTitleEditSkeleton();
        $this->setBreadcrumbEditSkeleton();

        $hooks = gplcart_array_split($this->extractor->getHookScopes(), 2);

        $this->setData('hooks', $hooks);
        $this->setData('licenses', $this->getLicenses());

        $this->submitSkeleton();
        $this->setJob();

        $this->generateSkeleton();

        $this->outputEditSkeleton();
    }

    /**
     * Returns an array of licenses
     * @return array
     */
    protected function getLicenses()
    {
        return array(
            'MIT License' => 'https://opensource.org/licenses/MIT',
            'Apache License 2.0' => 'https://www.apache.org/licenses/LICENSE-2.0',
            '3-Clause BSD License' => 'https://opensource.org/licenses/BSD-3-Clause',
            'GNU General Public License 3.0' => 'https://www.gnu.org/licenses/gpl-3.0.en.html',
            'GNU Lesser General Public License' => 'https://www.gnu.org/licenses/lgpl-3.0.en.html'
        );
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

        $this->setSubmitted('skeleton');
        $this->validate('skeleton');

        if ($this->isError()) {
            return false;
        }

        $name = $this->getSubmitted('module.name');

        if (empty($name)) {
            $this->setSubmitted('module.name', $this->getSubmitted('module.id'));
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
            $this->generateSkeleton();
        }
    }

    /**
     * Generate skeleton files
     */
    protected function generateSkeleton()
    {
        $data = $this->getSubmittedSkeleton();

        if (empty($data)) {
            return null;
        }

        $result = $this->generator->generate($data);

        if ($result === true) {
            $this->redirect('', $this->text('Modules has been created'), 'success');
        }

        $vars = array('!errors' => implode('<br>', (array) $result));
        $message = $this->text('One or more errors occurred while creating modules:!errors', $vars);
        $this->redirect('', $message, 'warning');
    }

    /**
     * Get data either from submitted form or finished job
     * @return array
     */
    protected function getSubmittedSkeleton()
    {
        $data = $this->getSubmitted();

        if ($this->isQuery('skeleton_hooks_extracted')) {

            $job = $this->job->get('skeleton');
            if (!isset($job['context']['extracted'])) {
                return null;
            }

            $data = $job['data']['submitted'];
            $data['hooks'] = $job['context']['extracted'];
            if (!empty($job['errors'])) {
                $this->setMessage($this->text('@num errors occurred during hook extraction'), 'warning', true);
            }
        }

        return $data;
    }

    /**
     * Set up hook extraction job
     */
    protected function setExtractionJobSkeleton()
    {
        $job = array(
            'id' => 'skeleton',
            'data' => array('submitted' => $this->getSubmitted()),
            'total' => $this->getTotalExtractSkeleton(),
            'redirect' => array(
                'finish' => $this->url('', array('skeleton_hooks_extracted' => 1))
            )
        );

        $this->job->submit($job);
    }

    /**
     * Returns a total number of scanned files to extract hooks from
     * @return integer
     */
    protected function getTotalExtractSkeleton()
    {
        return (int) $this->extractor->scan(GC_CORE_DIR, true);
    }

    /**
     * Set title on the edit module page
     */
    protected function setBreadcrumbEditSkeleton()
    {
        $breadcrumb = array(
            'url' => $this->url('admin'),
            'text' => $this->text('Dashboard')
        );

        $this->setBreadcrumb($breadcrumb);
    }

    /**
     * Set title on the edit module page
     */
    protected function setTitleEditSkeleton()
    {
        $this->setTitle($this->text('Skeleton'));
    }

    /**
     * Render and output the edit module page
     */
    protected function outputEditSkeleton()
    {
        $this->output('skeleton|edit');
    }

}
