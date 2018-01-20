<?php

namespace MP\Service;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\LexerConfig;
use MP\Mapper\RuianMapper;
use MP\Util\ConvertIconvEncoding;
use MP\Util\Transaction\DibiTransaction;
use Nette\Utils\DateTime;
use Nette\Utils\FileSystem;
use Nette\Utils\Finder;
use Tracy\Debugger;

/**
 * Sluzba pro pravidelny import adresnich mist z RUIAN
 * RUAIN nema zadne verejne API, zpracovavame davku CSV z http://nahlizenidokn.cuzk.cz/StahniAdresniMistaRUIAN.aspx
 */
class RuianSyncService
{
    /** @var DibiTransaction */
    protected $transaction;

    /** @var  RuianMapper */
    protected $mapper;

    /** @var string adresa zdrojoveho souboru pro pripojeni */
    const URL_PREFIX = 'http://vdp.cuzk.cz/vymenny_format/csv/';

    protected $ruianTempDir;

    /**
     * @param DibiTransaction $transaction
     * @param RuianMapper $mapper
     */
    public function __construct(DibiTransaction $transaction, RuianMapper $mapper)
    {
        $this->transaction = $transaction;
        $this->mapper = $mapper;

        $this->ruianTempDir = TEMP_DIR . '/ruian';
    }

    /**
     * Pokusi se dohledat zazipovana CSV, rozbalit je a rozparsovat
     * @return int pocet zpracovanych adresnich mist
     */
    public function import()
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        Debugger::enable(Debugger::PRODUCTION);

        $this->cleanUp();
        $this->prepareImportFiles();
        $ret = $this->dbImport();
        $this->cleanUp();

        return $ret;
    }

    /**
     * Stahne vzdaleny ZIP soubor a rozbali jej
     */
    protected function prepareImportFiles()
    {
        $date = new DateTime('last day of previous month');
        $filename = self::URL_PREFIX . $date->format('Ymd') . "_OB_ADR_csv.zip";

        $tmpZip = $this->ruianTempDir . '/tmp.zip';

        if ($handle = @fopen($filename, 'rb')) {
            $write = fopen($tmpZip, 'wb');

            while (!feof($handle)) {
                $buffer = fread($handle, 5120);
                fwrite($write, $buffer);
            }

            fclose($handle);
            fclose($write);
        }

        $zip = new \ZipArchive();

        if (true === $zip->open($tmpZip)) {
            $zip->extractTo(TEMP_DIR . '/ruian');
            $zip->close();
        } else {
            throw new \MP\Exception\RuntimeException('RUIAN download error');
        }
    }

    /**
     * Pred i po importu provedu promazani temp souboru, adresar vytvorim prazdny
     */
    protected function cleanUp()
    {
        FileSystem::delete($this->ruianTempDir);
        FileSystem::createDir($this->ruianTempDir);
    }

    /**
     * V transakci provedu import ze stazenych CSV nahrazenim puvodnich dat
     * @return int pocet naimportovanych adresnich mist
     */
    protected function dbImport()
    {
        $ret = 0;
        $csvValues = [];

        $this->transaction->begin();
        $this->mapper->prepareImport();

        /** @var $lexer Lexer */
        /** @var $interpreter Interpreter */
        list($lexer, $interpreter) = $this->getCsvInterpreterLexer();

        $interpreter->addObserver(function(array $columns) use(&$ret, &$csvValues) {
            $csvValues[] = [
                'id' => $columns[0],
                'city%sN' => $columns[2],
                'city_momc%sN' => $columns[4],
                'city_part%sN' => $columns[8],
                'street%sN' => $columns[10],
                'street_no_is_alternative%b' => ('č.p.' !== $columns[11]), // pokud neni cislo popisne, tak oznacim
                'street_desc_no%iN' => $columns[12],
                'street_orient_no%iN' => $columns[13],
                'street_orient_symbol%sN' => $columns[14],
                'zipcode%sN' => $columns[15],
            ];

            $ret++;
        });

        $csvDir = $this->ruianTempDir . '/CSV';

        foreach (Finder::findFiles('*.csv')->in($csvDir) as $filename => $file) {
            $url = ConvertIconvEncoding::getFilterURL($filename, 'CP1250', 'UTF-8');
            $csvValues = [];
            $lexer->parse($url, $interpreter);

            if ($csvValues) {
                $this->mapper->insertAddresses($csvValues);
            }
        }

        if ($ret) {
            $this->mapper->finishImport();
            $this->transaction->commit();
        } else {
            $this->transaction->rollback();
            throw new \MP\Exception\RuntimeException('RUIAN sync error');
        }

        return $ret;
    }

    /**
     * Pripravi Interpreter a Lexer pro import dat z CSV ve formatu RUIAN
     * @return array
     */
    protected function getCsvInterpreterLexer()
    {
        $csvConfig = new LexerConfig();
        $csvConfig
            ->setIgnoreHeaderLine(true)
            ->setDelimiter(';');

        ConvertIconvEncoding::register();
        $lexer = new Lexer($csvConfig);
        $interpreter = new Interpreter();

        return [$lexer, $interpreter];
    }
}
