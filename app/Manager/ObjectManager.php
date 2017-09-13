<?php

namespace MP\Manager;

use Dibi\Row;
use MP\Mapper\IMapper;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ObjectManager extends AbstractEnumManager implements ILangAwareManager
{
    use TLangAwareManager;

    /**
     * @param int $id
     */
    public function remove($id)
    {
        $this->mapper->deleteById($id);
    }

    /**
     * Vyhleda objekt na zaklade jineho objektu.
     *
     * Musi se shodovat:
     *  RUIAN ID nebo GPS souradnice
     *  nazev
     *  certifikovanost
     *
     * Pokud najdu vice objektu, preferuji ten se stejnym source_id
     *
     * @param array $object
     *
     * @return array|null
     */
    public function findByObject(array $object)
    {
        $restrictor = [];

        if (!empty($object['ruian_address'])) {
            $restrictor[] = ["[ruian_address] = %i", $object['ruian_address']];
        } else if (!empty($object['longitude']) && !empty($object['latitude'])) {
            $restrictor[] = ["[longitude] = %f", $object['longitude']];
            $restrictor[] = ["[latitude] = %f", $object['latitude']];
        }

        $restrictor[] = [
            "EXISTS (
                SELECT 1
                FROM [map_object_lang]
                WHERE
                    [map_object_id] = [map_object].[" . IMapper::ID . "]
                    AND [title] = %s
                    AND [" . IMapper::LANG . "] = %s
            )", $object['title'], $this->lang->getLang()
        ];

        return $this->findOneBy($restrictor, [['([source_id] = %i)', $object['source_id'], IMapper::ORDER_DESC]]);
    }

    /**
     * Dohleda data nutna pro vykresleni markeru na mape
     *
     * @param array|null $restrictor
     *
     * @return Row[]
     */
    public function findMarkers($restrictor = null)
    {
        return $this->mapper->selectMarkers($restrictor) ?: [];
    }

    /**
     * @param array|null $restrictor
     *
     * @return array
     */
    public function findIds($restrictor)
    {
        return $this->mapper->selectIds($restrictor) ?: [];
    }

    /**
     * @param array|null $restrictor
     *
     * @return array
     */
    public function findCompareHashes($restrictor)
    {
        return $this->mapper->selectCompareHashes($restrictor) ?: [];
    }

    /**
     * @param array|null $restrictor
     *
     * @return array
     */
    public function findSuggestions($restrictor)
    {
        return $this->mapper->selectSuggestions($restrictor) ?: [];
    }

    /**
     * Zaverzuje objekt.
     *
     * @param array $object
     *
     * @return array
     */
    public function backupObject($object)
    {
        $this->mapper->backup($object['object_id']);

        unset($object[IMapper::ID]);

        return $object;
    }

    /**
     * Smaze objekt.
     *
     * @param array $source
     */
    public function removeObjectsBySource($source)
    {
        $this->mapper->deleteBySource($source[IMapper::ID]);
    }

    /**
     * Vrati pocet objektu seskupenych podle regionu
     * @return Row[]
     */
    public function getRegionsStats()
    {
        return $this->mapper->selectRegionsStats() ?: [];
    }

    /**
     * Vrati pocet objektu seskupenych podle typu objektu
     * @return Row[]
     */
    public function getTypesStats()
    {
        return $this->mapper->selectTypesStats() ?: [];
    }

    /**
     * Slouci dva objekty.
     *
     * @param int $source
     * @param int $destination
     *
     * @return int
     */
    public function joinObjects($source, $destination)
    {
        return $this->mapper->join($source, $destination);
    }
}
