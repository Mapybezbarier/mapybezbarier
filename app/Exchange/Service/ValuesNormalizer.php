<?php

namespace MP\Exchange\Service;

use MP\Manager\Resolver\ElevatorEnumValueResolver;
use MP\Manager\Resolver\ObjectEnumValueResolver;
use MP\Manager\Resolver\PlatformEnumValueResolver;
use MP\Manager\Resolver\RampSkidsEnumValueResolver;
use MP\Manager\Resolver\WcEnumValueResolver;
use MP\Object\ObjectHelper;
use MP\Object\ObjectMetadata;
use MP\Util\Strings;
use Nette\Utils\DateTime;
use Nette\Utils\Validators;

/**
 * Sluzba pro normalizaci hodnot mapoveho objektu.
 *
 * Vyuziva se v ramci importu jeste pred fazi validace.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ValuesNormalizer
{
    /** @var ExchangeMetadata */
    protected $metadata;

    /** @var ObjectEnumValueResolver */
    protected $objectEnumValueResolver;

    /** @var RampSkidsEnumValueResolver */
    protected $rampSkidsEnumValueResolver;

    /** @var PlatformEnumValueResolver */
    protected $platformEnumValueResolver;

    /** @var ElevatorEnumValueResolver */
    protected $elevatorEnumValueResolver;

    /** @var WcEnumValueResolver */
    protected $wcEnumValueResolver;

    /**
     * @param ExchangeMetadata $metadata
     * @param ObjectEnumValueResolver $objectEnumValueResolver
     * @param RampSkidsEnumValueResolver $rampSkidsEnumValueResolver
     * @param PlatformEnumValueResolver $platformEnumValueResolver
     * @param ElevatorEnumValueResolver $elevatorEnumValueResolver
     * @param WcEnumValueResolver $wcEnumValueResolver
     */
    public function __construct(
        ExchangeMetadata $metadata,
        ObjectEnumValueResolver $objectEnumValueResolver,
        RampSkidsEnumValueResolver $rampSkidsEnumValueResolver,
        PlatformEnumValueResolver $platformEnumValueResolver,
        ElevatorEnumValueResolver $elevatorEnumValueResolver,
        WcEnumValueResolver $wcEnumValueResolver
    )
    {
        $this->metadata = $metadata;
        $this->objectEnumValueResolver = $objectEnumValueResolver;
        $this->rampSkidsEnumValueResolver = $rampSkidsEnumValueResolver;
        $this->platformEnumValueResolver = $platformEnumValueResolver;
        $this->elevatorEnumValueResolver = $elevatorEnumValueResolver;
        $this->wcEnumValueResolver = $wcEnumValueResolver;
    }

    /**
     * Normalizuje objekt
     *
     * @param array $object
     */
    public function normalize(array &$object)
    {
        $attachements = ObjectHelper::getAttachements($object);

        $this->normalizeTypes($object);
        $this->normalizeEnums($object);

        foreach ($attachements[ObjectMetadata::RAMP_SKIDS] as &$rampSkid) {
            $this->normalizeTypes($rampSkid, ObjectMetadata::RAMP_SKIDS);
            $this->normalizeEnums($rampSkid, ObjectMetadata::RAMP_SKIDS);
        }

        $object[ObjectMetadata::RAMP_SKIDS] = $attachements[ObjectMetadata::RAMP_SKIDS];

        foreach ($attachements[ObjectMetadata::PLATFORM] as &$platform) {
            $this->normalizeTypes($platform, ObjectMetadata::PLATFORM);
            $this->normalizeEnums($platform, ObjectMetadata::PLATFORM);
        }

        $object[ObjectMetadata::PLATFORM] = $attachements[ObjectMetadata::PLATFORM];

        foreach ($attachements[ObjectMetadata::ELEVATOR] as &$elevator) {
            $this->normalizeTypes($elevator, ObjectMetadata::ELEVATOR);
            $this->normalizeEnums($elevator, ObjectMetadata::ELEVATOR);
        }

        $object[ObjectMetadata::ELEVATOR] = $attachements[ObjectMetadata::ELEVATOR];

        foreach ($attachements[ObjectMetadata::ELEVATOR] as &$wc) {
            $this->normalizeTypes($wc, ObjectMetadata::WC);
            $this->normalizeEnums($wc, ObjectMetadata::WC);
        }

        $object[ObjectMetadata::WC] = $attachements[ObjectMetadata::WC];

        // pokud neni vyplnen nazev casti obce, pouziju obec
        if (!empty($object[ObjectMetadata::CITY]) && empty($object[ObjectMetadata::CITY_PART])) {
            $object[ObjectMetadata::CITY_PART] = $object[ObjectMetadata::CITY];
        }

        // normalizuji PSC
        if (!empty($object[ObjectMetadata::ZIP_CODE])) {
            $object[ObjectMetadata::ZIP_CODE] = Strings::replace($object[ObjectMetadata::ZIP_CODE], '/\s+/', '');
        }
    }

    /**
     * Normalizuje hodnoty podle datoveho typu.
     *
     * @param array $values
     * @param string|null $namespace
     */
    protected function normalizeTypes(array &$values, $namespace = null)
    {
        foreach ($values as $key => &$value) {
            if (is_null($value) || Validators::is($value, 'scalar')) {
                switch ($this->metadata->getType($namespace, $key)) {
                    case ExchangeMetadata::TYPE_STRING:
                        $value = Strings::trim($value);
                    break;

                    case ExchangeMetadata::TYPE_DATE_TIME:
                        $value = DateTime::from($value);
                    break;

                    case ExchangeMetadata::TYPE_NUMBER:
                        if (Validators::is($value, 'string')) {
                            $value = Strings::replace($value, '/,/', '.', 1);
                        }
                    break;

                    case ExchangeMetadata::TYPE_BOOLEAN:
                        if ('true' === $value || '1' === $value) {
                            $value = true;
                        } else if ('false' === $value || '0' === $value) {
                            $value = false;
                        }
                    break;
                }
            } else {
                throw new \Nette\InvalidArgumentException("Cannot normalize non-scalar value for key '{$key}'");
            }
        }
    }

    /**
     * Normalizuje hodnoty ciselniku.
     *
     * @param array $values
     * @param string null $namespace
     */
    protected function normalizeEnums(array &$values, $namespace = null)
    {
        switch ($namespace) {
            case ObjectMetadata::RAMP_SKIDS:
                $resolver = $this->rampSkidsEnumValueResolver;
            break;

            case ObjectMetadata::PLATFORM:
                $resolver = $this->platformEnumValueResolver;
            break;

            case ObjectMetadata::ELEVATOR:
                $resolver = $this->elevatorEnumValueResolver;
            break;

            case ObjectMetadata::WC:
                $resolver = $this->wcEnumValueResolver;
            break;

            default:
                $resolver = $this->objectEnumValueResolver;
        }

        $values = $resolver->normalize($values);
    }
}
