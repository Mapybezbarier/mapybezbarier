<?php

namespace MP\Util\Paginator;

use Nette\Utils\Paginator;

/**
 * Tovarna na paginator.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class PaginatorFactory
{
    /**
     * @param int $limit
     * @param int $offset
     *
     * @return Paginator
     */
    public function create($limit = 1, $offset = 0)
    {
        $paginator = new Paginator();
        $paginator->setItemsPerPage((int) $limit);
        $paginator->setPage((int) $offset);

        return $paginator;
    }
}
