<?php
/**
 * Created by PhpStorm.
 * User: frank
 * Date: 21/07/16
 * Time: 18:17
 */

namespace SUDHAUS7\Newspage\Hooks\Backend;

class BackendEvaluator
{
    public function deevaluateFieldValue($_params)
    {
        if (empty($_params['value'])) {
            $utcTimeZone = new \DateTimeZone('UTC');
            /** @var \DateTime $utcDateTime */
            $utcDateTime = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('DateTime', 'now', $utcTimeZone);
            $currentTimeZone = new \DateTimeZone(date_default_timezone_get());
            $utcDateTime->setTimezone($currentTimeZone);
            $offset = $utcDateTime->getOffset();
            return $utcDateTime->getTimestamp()+$offset;
            //return mktime(gmdate('H')+4,gmdate('i'),0,gmdate('m'),gmdate('d'),gmdate('Y'));
        }
        return $_params['value'];
    }
}
