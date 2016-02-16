<?php

namespace MP\Exchange\Export;

use Goodby\CSV\Export\Standard\Collection\CallbackCollection;
use Goodby\CSV\Export\Standard\Exporter;
use Goodby\CSV\Export\Standard\ExporterConfig;
use MP\Exchange\Export\CsvResolver\CsvResolver;
use MP\Util\Arrays;
use MP\Util\Strings;

/**
 * Export do interniho CSV formatu
 */
class CsvExporter implements IExpoter
{
    /**
     * @param array $data
     * @return string
     */
    public function export(array $data)
    {
        $exporter = $this->getCsvExporter();
        $collection = new CallbackCollection($data, function($object) {
            $object = $this->exportCsvLine($object);
            return $object;
        });

        $tmpFile = TEMP_DIR . '/export.csv';
        $exporter->export($tmpFile, $collection);

        return file_get_contents($tmpFile);
    }

    /**
     * Pripravi exporter pro format interni CSV
     */
    public function getCsvExporter()
    {
        $config = new ExporterConfig();
        $config->setDelimiter(';');

        $ret = new Exporter($config);

        return $ret;
    }

    /**
     * Pripravi radek pro CSV - projde prilohy a sestavi
     * @param array $object data o objektu
     * @return array pole klicovane poradim sloupce v CSV
     */
    protected function exportCsvLine($object)
    {
        $ret = $this->exportMain($object);

        foreach (CsvResolver::getRelations() as $key => $starts) {
            $i = 0;

            foreach ($starts as $start) {
                $relationData = Arrays::get($object, [Strings::lower($key), $i], []) ?: [];
                $this->exportRelation($ret, $key, $relationData, $start);
            }
        }

        // Exporter potrebuje neprerusovanou radu klicu - doplnim o vynechane hodnoty
        $mask = array_fill(0, max(array_keys($ret)), null);
        $ret += $mask;
        ksort($ret);

        return $ret;
    }

    /**
     * @param array $object
     * @return array
     */
    protected function exportMain($object)
    {
        $ret = [];

        foreach (CsvResolver::getColsMap() as $col => $key) {
            $ret[$col-1] = $this->getCsvValue($object, $key);
        }

        return $ret;
    }

    /**
     * @param array $ret
     * @param string $key
     * @param array $relation
     * @param int $start
     */
    protected function exportRelation(&$ret, $key, $relation, $start)
    {
        foreach (CsvResolver::getColsMap($key) as $col => $key) {
            $ret[$start + $col - 2] = $this->getCsvValue($relation, $key);
        }
    }

    /**
     * Naformatuje hodnotu tak, aby se spravne reprezentovala pomoci Exporter do CSV
     * Metoda fputcsv, ktera ve finale sestavuje CSV, jinak prevadi false na prazdny retezec, ale zadouci je 0
     * @param $row
     * @param $key
     * @return mixed
     */
    protected function getCsvValue($row, $key)
    {
        $ret = Arrays::get($row, $key, null);

        if (is_bool($ret)) {
            $ret = intval($ret);
        }

        return $ret;
    }
}
