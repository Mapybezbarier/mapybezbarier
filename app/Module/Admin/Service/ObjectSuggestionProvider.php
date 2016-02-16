<?php

namespace MP\Module\Admin\Service;

use MP\Object\ObjectHelper;
use MP\Util\Strings;
use Nette\Http\Request;

/**
 * Provider napovedy objektu
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ObjectSuggestionProvider
{
    /** @var Request */
    protected $request;

    /** @var ObjectService */
    protected $objectService;

    /** @var ObjectRestrictorBuilder */
    protected $restrictorBuilder;

    /**
     * @param Request $request
     * @param ObjectService $objectService
     * @param ObjectRestrictorBuilder $restrictorBuilder
     */
    public function __construct(Request $request, ObjectService $objectService, ObjectRestrictorBuilder $restrictorBuilder)
    {
        $this->request = $request;
        $this->objectService = $objectService;
        $this->restrictorBuilder = $restrictorBuilder;
    }

    /**
     * @param int|null $id
     *
     * @return array
     */
    public function provide($id = null)
    {
        $term = $this->request->getQuery(ObjectRestrictorBuilder::RESTRICTION_TERM, null);

        if ($term) {
            $restrictor = $this->prepareRestrictor($term, $id);

            $objects = $this->objectService->getObjectsSuggestions($restrictor);

            $payload = [];

            foreach ($objects as $object) {
                $address =  ObjectHelper::getAddressString($object);

                $payload[] = [
                    'id' => $object['object_id'],
                    'label' => $object['title'] . ($address ? " - {$address}" : null),
                    'value' => $object['title'],
                ];
            }

            return $payload;
        } else {
            throw new \Nette\InvalidStateException("Missing or empty term parameter");
        }
    }

    /**
     * @param string $term
     * @param int|null $id
     *
     * @return array|null
     */
    protected function prepareRestrictor($term, $id = null)
    {
        $restrictions = [
            ObjectRestrictorBuilder::RESTRICTION_TERM => Strings::trim($term),
            ObjectRestrictorBuilder::RESTRICTION_USER => false,
        ];

        $restrictor = $this->restrictorBuilder->getRestrictor($restrictions);

        if ($id) {
            $restrictor[] = ["[object_id] != %i", $id];
        }

        return $restrictor;
    }
}
