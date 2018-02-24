<?php

/**
 * @package Skeleton module
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */

namespace gplcart\modules\skeleton\handlers;

use Exception;
use gplcart\modules\skeleton\models\Extractor;

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
     * Extract constructor.
     * @param Extractor $extractor
     */
    public function __construct(Extractor $extractor)
    {
        $this->extractor = $extractor;
    }

    /**
     * Processes one extration job iteration
     * @param array $job
     * @return null
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
            'directory' => GC_DIR_CORE,
            'limit' => array($job['context']['offset'], self::LIMIT),
            'scopes' => empty($job['data']['submitted']['hooks']) ? array() : $job['data']['submitted']['hooks']
        );

        try {
            $extracted = $this->extractor->getHooks($options);
        } catch (Exception $ex) {
            $job['context']['errors'] = array($ex->getMessage());
            $job['status'] = false;
            return null;
        }

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
