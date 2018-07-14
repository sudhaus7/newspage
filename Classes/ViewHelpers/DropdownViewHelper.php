<?php
namespace SUDHAUS7\Sudhaus7Newspage\ViewHelpers;

class DropdownViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{


    /**
     * Return an array for creating dummy content
     *
     * @return array
     * @throws nothing
     *
     */
    public function render()
    {
        $locale = 'de_DE.utf8';
        $year = date('Y');
        $mymonth = date('m');

        $aReturn = array();
        for ($i=$mymonth+1;$i<=12;$i++) {
            $month = str_pad($i, 2, '0', STR_PAD_LEFT);
            $date = new \DateTime(($year-1).'-'.$month.'-01');
            $formatter = new \IntlDateFormatter($locale, \IntlDateFormatter::LONG, \IntlDateFormatter::LONG, 'Europe/Berlin', \IntlDateFormatter::GREGORIAN, 'MMMM');
            $label = $formatter->format($date);
            $aReturn[]=array('month'=>($year-1).'-'.$month,'label'=>$label);
        }
        for ($i=1;$i<=$mymonth;$i++) {
            $month = str_pad($i, 2, '0', STR_PAD_LEFT);
            $date = new \DateTime($year.'-'.$month.'-01');
            $formatter = new \IntlDateFormatter($locale, \IntlDateFormatter::LONG, \IntlDateFormatter::LONG, 'Europe/Berlin', \IntlDateFormatter::GREGORIAN, 'MMMM');
            $label = $formatter->format($date);
            $aReturn[]=array('month'=>$year.'-'.$month,'label'=>$label);
        }


        return $aReturn;
    }
}
