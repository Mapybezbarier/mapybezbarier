<?php

namespace MP\Module\Admin\Component\BackupList;

use MP\Component\AbstractControl;
use Nette\Application\Responses\FileResponse;
use Nette\Utils\Finder;

/**
 * Komponenta pro vykresleni seznamu odkazu ke stazeni zaloh DB
 */
class BackupListControl extends AbstractControl
{
    const DAILY_DIR = 'daily';
    const MONTHLY_DIR = 'monthly';

    /**
     * @var string
     */
    protected $backupDir;

    /**
     * @param string $backupDir
     */
    public function __construct($backupDir)
    {
        $this->backupDir = $backupDir;
    }

    public function render()
    {
        $template = $this->getTemplate();

        $dailyDir = $this->backupDir . DIRECTORY_SEPARATOR . self::DAILY_DIR;
        $monthlyDir = $this->backupDir . DIRECTORY_SEPARATOR . self::MONTHLY_DIR;

        $template->dailyItems = Finder::findFiles('*.dump')->in($dailyDir);
        $template->monthlyItems = Finder::findFiles('*.dump')->in($monthlyDir);

        try {
            $template->render();
        } catch (\UnexpectedValueException $e) {
            $template->dailyItems = $template->monthlyItems = [];
            $template->render();
        }
    }

    /**
     * Stazeni souboru se zalohou DB
     * @param int $order poradi v adresari
     * @param string $type typ zalohy
     */
    public function handleDownload($order, $type)
    {
        $dir = $this->backupDir . DIRECTORY_SEPARATOR . $type;

        $i = 1;

        foreach (Finder::findFiles('*.dump')->in($dir) as $file) {
            if ($order == $i) {
                $this->presenter->sendResponse(new FileResponse($file));
                break;
            }

            $i++;
        }
    }
}
