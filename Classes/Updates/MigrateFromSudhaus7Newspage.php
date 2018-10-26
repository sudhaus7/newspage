<?php
/**
 * Created by PhpStorm.
 * User: frank
 * Date: 25.10.18
 * Time: 16:08
 */

namespace SUDHAUS7\Newspage\Updates;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\AbstractUpdate;


class MigrateFromSudhaus7Newspage extends AbstractUpdate
{
    
    private $fields = [
        'from',
        'tag',
        'showdate',
        'showtime',
        'highlight',
    ];
    
    /**
     * Get title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return 'Migrates data from newspage';
    }
    
    public function checkForUpdate(&$description)
    {
        if ($this->isWizardDone()) {
            return false;
        }
        $count = 0;
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tt_content');
       
        foreach ($this->fields as $field) {
            $res = $connection->executeQuery(sprintf("show fields from tt_content like '%s'",$field));
            $count = $count + $res->rowCount();
        }
    
        $res = $connection->executeQuery('show tables like "tx_sudhaus7newspage_domain_model_tag"');
        $count = $count + $res->rowCount();
        $res = $connection->executeQuery('show tables like "tx_sudhaus7newspage_domain_tag_mm"');
        $count = $count + $res->rowCount();
        
        $res = $connection->executeQuery('select count(*) as xcount from tt_content where CType="sudhaus7newspage_element"');
        $row = $res->fetch(\PDO::FETCH_ASSOC);
        $count = $count + $row['xcount'];
        
        $res = $connection->executeQuery('select count(*) as xcount from tt_content where list_type="sudhaus7newspage_plugin" and CType="list"');
        $row = $res->fetch(\PDO::FETCH_ASSOC);
        $count = $count + $row['xcount'];
        
        return $count > 0;
        
    }
    
    public function performUpdate(array &$dbQueries, &$customMessage)
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tt_content');
    
        foreach ($this->fields as $field) {
            $res = $connection->executeQuery(sprintf("show fields from tt_content like '%s'",$field));
            if($res->rowCount() > 0) {
                $dbQueries[] = sprintf('update tt_content set tx_newspage_%1$s=tx_sudhaus7newspage_%1$s', $field);
                $dbQueries[] = sprintf('alter table tt_content DROP tx_sudhaus7newspage_%s', $field);
            }
        }
        $res = $connection->executeQuery('show tables like "tx_sudhaus7newspage_domain_model_tag"');
        if ($res->rowCount() > 0) {
            $dbQueries[] ='insert IGNORE into tx_newspage_domain_model_tag (uid,pid,tstamp,crdate,cruser_id,deleted,hidden,t3_origuid,sys_language_uid,l10n_parent,l10n_diffsource,relation,icon,parent_tag,title) SELECT (uid,pid,tstamp,crdate,cruser_id,deleted,hidden,t3_origuid,sys_language_uid,l10n_parent,l10n_diffsource,relation,icon,parent_tag,title) from tx_sudhaus7newspage_domain_model_tag';
        }
        $res = $connection->executeQuery('show tables like "tx_sudhaus7newspage_domain_tag_mm"');
        if ($res->rowCount() > 0) {
            $dbQueries[] = 'insert IGNORE into tx_newspage_domain_tag_mm  SELECT * from tx_sudhaus7newspage_domain_tag_mm';
        }
    
        $dbQueries[] = 'update tt_content set CType="newspage_element" where CType="sudhaus7newspage_element"';
        $dbQueries[] = 'update tt_content set list_type="newspage_plugin" where list_type="sudhaus7newspage_plugin" and CType="list"';
        
        return true;
    }
}
