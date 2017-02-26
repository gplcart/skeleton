<?php

/**
 * @package Skeleton module
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */

namespace gplcart\modules\skeleton\handlers;

use gplcart\modules\skeleton\models\Extractor as SkeletonExtractorModel;

class Extract
{

    /**
     * Max files to parse for one iteration
     */
    const LIMIT = 10;

    /**
     * Skeleton model instance
     * @var \gplcart\modules\skeleton\models\Extractor $extractor
     */
    protected $extractor;

    /**
     * Constructor
     * @param SkeletonExtractorModel $extractor
     */
    public function __construct(SkeletonExtractorModel $extractor)
    {
        $this->extractor = $extractor;
    }

    /**
     * Processes one extration job iteration
     * @param array $job
     */
    public function process(array &$job)
    {
        if (!isset($job['context']['extracted'])) {
            $job['context']['extracted'] = array();
        }

        if (!isset($job['context']['errors'])) {
            $job['context']['errors'] = array();
        }

        $options = array(
            'directory' => GC_ROOT_DIR,
            'limit' => array($job['context']['offset'], self::LIMIT),
            'scopes' => empty($job['data']['submitted']['hooks']) ? array() : $job['data']['submitted']['hooks']
        );

        $extracted = $this->extractor->getHooks($options);

        if (empty($extracted['files'])) {
            $job['status'] = false;
            $job['done'] = $job['total'];
            return null;
        }

        if (!empty($extracted['success'])) {
            $job['context']['extracted'] = array_replace($job['context']['extracted'], $extracted['success']);
        }

        if (!empty($extracted['errors'])) {
            $job['errors'] += count($extracted['errors']);
            $job['context']['errors'] += $extracted['errors'];
        }

        $job['context']['offset'] += count($extracted['files']);
        $job['done'] = $job['context']['offset'];
    }

}
