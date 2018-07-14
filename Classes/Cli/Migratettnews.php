<?php
/**
 * Created by PhpStorm.
 * User: frank
 * Date: 20/05/14
 * Time: 14:12
 */

namespace SUDHAUS7\Sudhaus7Newspage\Cli;

//if (!defined('TYPO3_cliMode')) die('You cannot run this script directly!');

use SUDHAUS7\Sudhaus7Base\Tools\Globals;

/**
 * Class Cloneproducts
 *
 * @package SUDHAUS7\Sudhaus7Newspage\Cli
 */
class Migratettnews extends \TYPO3\CMS\Core\Controller\CommandLineController
{
    public $cli_help = [
        'name'        => 'Migrate TT_NEWS to Sudhaus7 Newspage',
        'synopsis'    => '###OPTIONS###',
        'description' => '',
        'examples'    => 'cli_dispatch.phpsh migratettnews --source 123 --target 24 --type news',
        'options'     => '',
        'license'     => 'GNU GPL - free software!',
        'author'      => 'Frank Berger',
    ];

    /**
     * @var $db \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    public $db;
    public $pid;
    public $uid;
    public $map = [
        'projects' => 3,
        'events'   => 2,
        'news'     => 1,
    ];
    public $sorting = 0;
    public $row;
    public $rowen;
    public $cats = [];

    public function __construct()
    {
        $this->db            = $GLOBALS['TYPO3_DB'];
        $this->cli_options[] = [
            '-h',
            'This help',
        ];
        $this->cli_options[] = [
            '--source 24',
            'Page ID from which tt_news are read',
        ];
        $this->cli_options[] = [
            '--target 24',
            'The Page ID to migrate to',
        ];
        $this->cli_options[] = [
            '--type [projects|news|events]',
            'The Page ID to migrate to',
        ];
        parent::__construct();
    }

    public function main()
    {
        /** @var  $GLOBALS [TYPO3_DB] */
        $this->cli_validateArgs();
        if (! isset($this->cli_args['--source'])) {
            $this->cli_help();
            exit;
        }

        if (! isset($this->cli_args['--target'])) {
            $this->cli_help();
            exit;
        }
        if (! isset($this->cli_args['--type'])) {
            $this->cli_help();
            exit;
        }


        $src    = (int) $this->cli_args['--source'][0];
        $target = (int) $this->cli_args['--target'][0];
        $type   = $this->map[ $this->cli_args['--type'][0] ];

        print_r([
            $src,
            $target,
            $type,
        ]);
        $res = Globals::db()->sql_query('SELECT * FROM tt_news WHERE pid=' . $src .
                                         ' AND deleted=0 AND hidden=0 AND sys_language_uid=0');
        while ($row = Globals::db()->sql_fetch_assoc($res)) {
            $this->getTtnewsTranslation($row);
            $pageid = $this->createPage($target);

            $this->createElement($pageid, $type);
            $this->createContent($pageid, $type);
            $this->createGallery($pageid, $type);

            $this->createYoutube($pageid, $type);
            $this->createSpendeLink($pageid, $type);
            $this->createDownloads($pageid, $type);
        }
    }

    public function getTtnewsTranslation($row)
    {
        if ($row['geodata'] > 0) {
            $res            = Globals::db()->sql_query(
                'SELECT geodata FROM tt_news_geodata WHERE irre_parentid=' . $row['uid'] .
                ' and irre_parenttable="tt_news" and deleted=0 and hidden=0 order by sorting asc limit 1'
            );
            $geo            = Globals::db()->sql_fetch_assoc($res);
            $row['geodata'] = $geo['geodata'];
        } else {
            $row['geodata'] = '';
        }

        if ($row['related'] > 0) {
            $row['related_data'] = [];
            $res                 = Globals::db()->sql_query(
                'SELECT * FROM tt_news_related_mm WHERE uid_local=' . $row['uid'] . ' order by sorting asc'
            );
            while ($related = Globals::db()->sql_fetch_assoc($res)) {
                if (! isset($row['related_data'][ $related['tablenames'] ])) {
                    $row['related_data'][ $related['tablenames'] ] = [];
                }
                $row['related_data'][ $related['tablenames'] ][] = $related['uid_foreign'];
            }
        }

        $this->row = $row;


        $res  = Globals::db()->sql_query(
            'SELECT title,short,bodytext,sys_language_uid,imagecaption,imagetitletext,imagealttext FROM tt_news ' .
            'WHERE  l18n_parent=' . $row['uid'] . ' AND deleted=0 AND hidden=0  AND sys_language_uid=1'
        );
        $lang = Globals::db()->sql_fetch_assoc($res);
        if ($lang) {
            foreach ($lang as $k => $v) {
                if (! empty($v)) {
                    $row[ $k ] = $v;
                }
            }
        }
        $this->rowen = $row;

        $this->cats = [];
        if ($this->row['category'] > 0) {
            $res = Globals::db()->sql_query(
                'select uid_foreign from tt_news_cat_mm where uid_local=' . $this->row['uid']
            );
            while ($cat = Globals::db()->sql_fetch_row($res)) {
                $this->cats[] = $cat[0];
            }
        }
    }

    public function createPage($pid)
    {
        $title   = $this->row['title'];
        //$titleen = $this->rowen['title'];

        Globals::db()->exec_INSERTquery('pages', [
            'pid'     => $pid,
            'title'   => $title,
            'crdate'  => time(),
            'tstamp'  => time(),
            'doktype' => 1,
        ]);
        $uid = Globals::db()->sql_insert_id();

        return $uid;
    }

    public function createElement($pid, $type)
    {
        $time          = time();
        $this->sorting = 16;
        if ($type === 2) {
            $this->sorting = 8196;
        }
        Globals::db()->exec_INSERTquery('tt_content', [
            'pid'                      => $pid,
            'crdate'                   => $time,
            'tstamp'                   => $time,
            'sorting'                  => $this->sorting,
            'header'                   => $this->row['title'],
            'bodytext'                 => $this->row['short'],
            'CType'                    => 'sudhaus7newspage_element',
            'tx_sudhaus7newspage_from' => $this->row['datetime'],
            'tx_sudhaus7newspage_type' => $type,
        ]);
        $uid   = Globals::db()->sql_insert_id();
        $files = $this->parsefilemeta($this->row);
        if (! empty($files)) {
            $this->createImage($uid, $pid, $files);
        }
        foreach ($this->cats as $k => $cat) {
            Globals::db()->exec_INSERTquery('tx_sudhaus7newspage_domain_tag_mm', [
                'uid_local'   => $uid,
                'uid_foreign' => $cat,
                'sorting'     => $k,

            ]);
        }


        if ($type === 1) {
            Globals::db()->exec_UPDATEquery('tt_content', 'uid=' . $uid, [
                'tx_sudhaus7newspage_from'     => $this->row['datetime'],
                'tx_sudhaus7newspage_showdate' => $this->row['showdate'],

            ]);
        }
        if ($type === 2) {
            $this->sorting = 16;
            Globals::db()->exec_UPDATEquery('tt_content', 'uid=' . $uid, [
                'tx_sudhaus7newspage_from' => $this->row['tx_mblnewsevent_from'] + $this->row['tx_mblnewsevent_from_time'],
                'tx_sudhaus7newspage_to' => $this->row['tx_mblnewsevent_to'] + $this->row['tx_mblnewsevent_to_time'],
                'tx_sudhaus7newspage_who' => $this->row['tx_mblnewsevent_organizer'],
                'tx_sudhaus7newspage_place' => $this->row['tx_mblnewsevent_where'],
                'tx_sudhaus7newspage_showdate' => $this->row['showdate'],
            ]);
        }
    }

    /**
     * @return array
     */
    public function parsefilemeta($row, $type = 'default')
    {
        $method = 'filemeta' . $type;
        $files  = [];
        if (method_exists($this, $method)) {
            list($images, $desc, $alt, $title) = $this->$method($row);


            foreach ($images as $k => $v) {
                $f = [
                    'name'  => $v,
                    'title' => '',
                    'alt'   => '',
                    'desc'  => '',
                ];
                if (isset($desc[ $k ])) {
                    $f['desc'] = $desc[ $k ];
                }
                if (isset($alt[ $k ])) {
                    $f['alt'] = $alt[ $k ];
                }
                if (isset($title[ $k ])) {
                    $f['title'] = $title[ $k ];
                }
                $files[] = $f;
            }
        }

        return $files;
    }

    public function createImage($uid, $pid, $files, $lang = 0, $table = 'tt_content', $field = 'image')
    {
        if (! is_array($files)) {
            $files = trim($files);
        }
        if (! is_array($files) && empty($files)) {
            return;
        }
        if (! is_array($files)) {
            $files = [ [ 'name' => $files, 'desc' => '', 'title' => '', 'alt' => '' ] ];
        }
        $time = time();
        foreach ($files as $sorting => $file) {
            if (empty($file['name'])) {
                continue;
            }
            $fileid = Globals::db()->exec_SELECTgetRows(
                'uid,storage,identifier',
                'sys_file',
                sprintf('`name`="%s"', $file['name']),
                '',
                '`storage` desc'
            );
            if (! empty($fileid)) {
                $founduid = false;
                foreach ($fileid as $conf) {
                    if (! $founduid) {
                        switch ($conf['storage']) {

                            case 1:
                                if (is_file(PATH_site . 'fileadmin' . $conf['identifier'])) {
                                    $founduid = $conf['uid'];
                                }
                                break;
                            default:
                                if (is_file(PATH_site . substr($conf['identifier'], 1))) {
                                    $founduid = $conf['uid'];
                                }
                                break;
                        }
                    }
                }
                if ($founduid) {
                    Globals::db()->debugOutput = true;
                    Globals::db()->exec_INSERTquery('sys_file_reference', [
                        'pid'              => $pid,
                        'uid_local'        => $founduid,
                        'table_local'      => 'sys_file',
                        'uid_foreign'      => $uid,
                        'tablenames'       => $table,
                        'fieldname'        => $field,
                        'sorting_foreign'  => $sorting,
                        'sorting'          => $sorting,
                        'tstamp'           => $time,
                        'crdate'           => $time,
                        'cruser_id'        => 2,
                        'sys_language_uid' => $lang,
                        'description'      => $file['desc'],
                        'alternative'      => $file['alt'],
                        'title'            => $file['title'],
                    ]);
                    Globals::db()->debugOutput = false;
                } else {
                    echo 'ERROR FILEID';
                    print_r([
                        $founduid,
                        $fileid,
                        PATH_site . substr($fileid[0]['identifier'], 1),
                        is_file(PATH_site . substr($fileid[0]['identifier'], 1)),
                    ]);
                    exit;
                }
            } else {
                echo 'ERROR FILE';
                print_r([ $file ]);
            }
        }
    }

    public function createContent($pid, $type)
    {
        $this->sorting = $this->sorting + 16;
        $time          = time();
        Globals::db()->exec_INSERTquery('tt_content', [
            'pid'       => $pid,
            'bodytext'  => $this->row['bodytext'],
            'CType'     => 'text',
            'colPos'    => 0,
            'tstamp'    => $time,
            'crdate'    => $time,
            'cruser_id' => 2,
            'sorting'   => $this->sorting,
        ]);
        $uid = Globals::db()->sql_insert_id();
        if ($this->rowen['sys_language_uid'] == 1) {
            Globals::db()->exec_INSERTquery('tt_content', [
                'pid'              => $pid,
                'bodytext'         => $this->rowen['bodytext'],
                'CType'            => 'text',
                'colPos'           => 0,
                'tstamp'           => $time,
                'crdate'           => $time,
                'cruser_id'        => 2,
                'sorting'          => $this->sorting,
                'sys_language_uid' => 1,
                'l18n_parent'      => $uid,
            ]);
        }
    }

    public function createGallery($pid, $type)
    {
        $this->sorting = $this->sorting + 16;
        $time          = time();

        if (! empty($this->row['image2'])) {
            $files = $this->parsefilemeta($this->row, 'img2');
            if (! empty($files)) {
                Globals::db()->exec_INSERTquery('tt_content', [
                    'pid'       => $pid,
                    'CType'     => 'gallery',
                    'assets'    => sizeof($files),
                    'colPos'    => 0,
                    'tstamp'    => $time,
                    'crdate'    => $time,
                    'cruser_id' => 2,
                    'sorting'   => $this->sorting,
                ]);
                $uid = Globals::db()->sql_insert_id();
                $this->createImage($uid, $pid, $files);
                if ($this->rowen['sys_language_uid'] == 1) {
                    Globals::db()->exec_INSERTquery('tt_content', [
                        'pid'              => $pid,
                        'CType'            => 'gallery',
                        'colPos'           => 0,
                        'assets'           => 1,
                        'tstamp'           => $time,
                        'crdate'           => $time,
                        'cruser_id'        => 2,
                        'sorting'          => $this->sorting,
                        'sys_language_uid' => 1,
                        'l18n_parent'      => $uid,
                    ]);
                    $languid = Globals::db()->sql_insert_id();
                    $this->createImage($languid, $pid, $files, 1);
                }
            }
        }
    }

    public function createYoutube($pid, $type)
    {
        if (! empty($this->row['youtube'])) {
            $this->sorting = $this->sorting + 16;
            $time          = time();
            Globals::db()->exec_INSERTquery('tt_content', [
                'pid'         => $pid,
                'header'      => $this->row['youtube_title'],
                'CType'       => 'list',
                'list_type'   => 'sudhaus7_html5media_pi3',
                'pi_flexform' => '<' . '?' . 'xml version="1.0" encoding="utf-8" standalone="yes" ' . '?' . '>
<T3FlexForms>
    <data>
        <sheet index="sDEF">
            <language index="lDEF">
                <field index="videolink">
                    <value index="vDEF">' . $this->row['youtube'] . '</value>
                </field>
                <field index="videotype">
                    <value index="vDEF">youtube</value>
                </field>
            </language>
        </sheet>
    </data>
</T3FlexForms>',
                'colPos'      => 0,
                'tstamp'      => $time,
                'crdate'      => $time,
                'cruser_id'   => 2,
                'sorting'     => $this->sorting,
            ]);
            $uid = Globals::db()->sql_insert_id();
            if ($this->rowen['sys_language_uid'] == 1) {
                Globals::db()->exec_INSERTquery('tt_content', [
                    'pid'              => $pid,
                    'header'           => $this->rowen['youtube_title'],
                    'CType'            => 'list',
                    'list_type'        => 'sudhaus7_html5media_pi3',
                    'pi_flexform'      => '<' . '?' . 'xml version="1.0" encoding="utf-8" standalone="yes" ' . '?' . '>
<T3FlexForms>
    <data>
        <sheet index="sDEF">
            <language index="lDEF">
                <field index="videolink">
                    <value index="vDEF">' . $this->row['youtube'] . '</value>
                </field>
                <field index="videotype">
                    <value index="vDEF">youtube</value>
                </field>
            </language>
        </sheet>
    </data>
</T3FlexForms>',
                    'colPos'           => 0,
                    'tstamp'           => $time,
                    'crdate'           => $time,
                    'cruser_id'        => 2,
                    'sorting'          => $this->sorting,
                    'sys_language_uid' => 1,
                    'l18n_parent'      => $uid,
                ]);
            }
        }
    }

    public function createSpendeLink($pid, $type)
    {
        if ($this->row['showspendelink'] > 0) {
            $this->sorting = $this->sorting + 16;
            $time          = time();
            Globals::db()->exec_INSERTquery('tt_content', [
                'pid'         => $pid,
                'CType'       => 'list',
                'list_type'   => 'sudhaus7template_spendelink',
                'pi_flexform' => '<' . '?' . 'xml version="1.0" encoding="utf-8" standalone="yes" ' . '?' . '>
<T3FlexForms>
    <data>
        <sheet index="sDEF">
            <language index="lDEF">
                <field index="settings.project">
                    <value index="vDEF">' . $this->row['title'] . '</value>
                </field>
                <field index="settings.spendeid">
                    <value index="vDEF">' . $this->row['spendeid'] . '</value>
                </field>
                <field index="settings.spendebetrag">
                    <value index="vDEF">' . $this->row['spendebetrag'] . '</value>
                </field>
            </language>
        </sheet>
    </data>
</T3FlexForms>',
                'colPos'      => 0,
                'tstamp'      => $time,
                'crdate'      => $time,
                'cruser_id'   => 2,
                'sorting'     => $this->sorting,
            ]);
            Globals::db()->exec_INSERTquery('tt_content', [
                'pid'         => $pid,
                'CType'       => 'list',
                'list_type'   => 'sudhaus7template_spendelink',
                'pi_flexform' => '<' . '?' . 'xml version="1.0" encoding="utf-8" standalone="yes" ' . '?' . '>
<T3FlexForms>
    <data>
        <sheet index="sDEF">
            <language index="lDEF">
                <field index="settings.project">
                    <value index="vDEF">' . $this->row['title'] . '</value>
                </field>
                <field index="settings.spendeid">
                    <value index="vDEF">' . $this->row['spendeid'] . '</value>
                </field>
                <field index="settings.spendebetrag">
                    <value index="vDEF">' . $this->row['spendebetrag'] . '</value>
                </field>
            </language>
        </sheet>
    </data>
</T3FlexForms>',
                'colPos'      => 2,
                'tstamp'      => $time,
                'crdate'      => $time,
                'cruser_id'   => 2,
                'sorting'     => 1,
            ]);
        }
    }

    public function createDownloads($pid, $type)
    {
        $this->sorting = $this->sorting + 16;
        $time          = time();

        if (! empty($this->row['news_files']) || $this->row['related'] > 0) {
            $files = $this->parsefilemeta($this->row, 'files');
            $pages = isset($this->row['related_data']) && isset($this->row['related_data']['pages']) ? implode(
                ',',
                $this->row['related_data']['pages']
            ) : '';
            $news  = isset($this->row['related_data']) && isset($this->row['related_data']['tt_news']) ? implode(
                ',',
                $this->row['related_data']['tt_news']
            ) : '';
            Globals::db()->exec_INSERTquery('tt_content', [
                'pid'       => $pid,
                'CType'     => 'uploads',
                'media'     => sizeof($files),
                'pages'     => $pages,
                'news'      => $news,
                'colPos'    => 0,
                'tstamp'    => $time,
                'crdate'    => $time,
                'cruser_id' => 2,
                'sorting'   => $this->sorting,
            ]);
            $uid = Globals::db()->sql_insert_id();
            $this->createImage($uid, $pid, $files, 0, 'tt_content', 'media');
            if ($this->rowen['sys_language_uid'] == 1) {
                Globals::db()->exec_INSERTquery('tt_content', [
                    'pid'              => $pid,
                    'CType'            => 'uploads',
                    'media'            => sizeof($files),
                    'pages'            => $pages,
                    'news'             => $news,
                    'colPos'           => 0,
                    'tstamp'           => $time,
                    'crdate'           => $time,
                    'cruser_id'        => 2,
                    'sorting'          => $this->sorting,
                    'sys_language_uid' => 1,
                    'l18n_parent'      => $uid,
                ]);
                $languid = Globals::db()->sql_insert_id();
                $this->createImage($languid, $pid, $files, 1, 'tt_content', 'media');
            }
        }
    }

    /**
     * @param $row
     *
     * @return array
     */
    public function filemetadefault($row)
    {
        $images = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $row['image']);
        $desc   = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode("\n", $row['imagecaption']);
        $alt    = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode("\n", $row['imagealttext']);
        $title  = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode("\n", $row['imagetitletext']);

        return [
            $images,
            $desc,
            $alt,
            $title,
        ];
    }

    public function filemetaimg2($row)
    {
        $images = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $row['image2']);
        $desc   = $row['image2_caption'];

        return [
            $images,
            $desc,
            [],
            [],
        ];
    }

    public function filemetafiles($row)
    {
        $images = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $row['news_files']);
        $desc   = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode("\n", $row['news_files_caption']);

        return [
            $images,
            $desc,
            [],
            $desc,
        ];
    }
}

$SOBE = new Migratettnews();
$SOBE->main();
