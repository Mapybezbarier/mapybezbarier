<?php

namespace MP\Service;

use MP\Component\Mailer\GeocodingMailer;
use MP\Component\Mailer\IMessageFactory;
use MP\Manager\GeocodingManager;
use MP\Manager\ObjectManager;
use MP\Object\ObjectHelper;
use Nette\Localization\ITranslator;

/**
 * Sluzba pro dohledavani GPS souradnic z adresy
 * Vyuziva Google Maps Geocoding API
 */
class GeocodingService
{
    /** max. pocet dotazu na API v 1 davce */
    const BATCH_LIMIT = 100;

    /** delay mezi jednotlivymi requesty na API - v mikrosekundach */
    const BATCH_TIMEOUT = 100000;

    /** @var \GoogleMapsGeocoder */
    protected $geocoder;

    /** @var GeocodingManager */
    protected $manager;

    /** @var ObjectManager */
    protected $objectManager;

    /** @var ITranslator */
    protected $translator;

    /** @var GeocodingMailer */
    protected $mailer;

    /**
     * @param \GoogleMapsGeocoder $geocoder
     * @param GeocodingManager $geocodingManager
     * @param ObjectManager $objectManager
     * @param GeocodingMailer $mailer
     * @param ITranslator $translator
     */
    public function __construct(
        \GoogleMapsGeocoder $geocoder, GeocodingManager $geocodingManager, ObjectManager $objectManager,
        GeocodingMailer $mailer, ITranslator $translator
    )
    {
        $this->geocoder = $geocoder;
        $this->manager = $geocodingManager;
        $this->objectManager = $objectManager;
        $this->mailer = $mailer;
        $this->translator = $translator;
    }

    /**
     * Pokusi se dohledat GPS pro adresy ze zacatku fronty
     * Pripadne chyby z davky posle na e-mail
     *
     * @param bool $priorityOnly
     *
     * @return array (int celkovy pocet, array report)
     */
    public function processQueue($priorityOnly = false)
    {
        $batchLimit = self::BATCH_LIMIT;
        $batchTimeout = self::BATCH_TIMEOUT;

        $reportItems = [
            'multiple' => [], // vice nez jeden vysledek
            'zero' => [], // zadny vysledek
            'error' => [], // jina chyba
        ];

        $items = $this->manager->getQueue($priorityOnly, $batchLimit);

        foreach ($items as $item) {
            $address = ObjectHelper::getAddressString($item);

            if ($address) {
                $this->geocoder->setAddress($address);
                $response = $this->geocoder->geocode(true);
            }

            if (empty($response) || empty($response['status'])) {
                $reportItems['error'][] = $item;
            } else {
                $this->processResponse($response, $item, $reportItems);
            }

            usleep($batchTimeout);
        }

        $this->sendReport($reportItems);

        return [count($items), $reportItems];
    }

    /**
     * Zpracuje odpoved z API
     *
     * @param array $response odpoved z API
     * @param array $item zpracovavany objekt
     * @param array $reportItems podklady pro report
     */
    private function processResponse($response, $item, &$reportItems)
    {
        $deleteFromQueue = false;

        if (\GoogleMapsGeocoder::STATUS_SUCCESS == $response['status']) {
            $deleteFromQueue = true;

            if (count($response['results']) > 1) {
                $reportItems['multiple'][] = $item;
            }

            $mapObjectData = [
                'id' => $item['map_object_id'],
                'longitude' => $response['results'][0]['geometry']['location']['lng'],
                'latitude' => $response['results'][0]['geometry']['location']['lat'],
            ];

            $this->objectManager->persist($mapObjectData);
        } else if (\GoogleMapsGeocoder::STATUS_NO_RESULTS == $response['status']) {
            $deleteFromQueue = true;
            $reportItems['zero'][] = $item;
        } else {
            $reportItems['error'][] = $item;
        }

        if ($deleteFromQueue) {
            $this->manager->remove($item['id']);
        } else {
            $item['result'] = $response['status'];
            $this->manager->persist(['id' => $item['id'], 'result' => $response['status']]);
        }
    }

    /**
     * Pokud pri zpraxcovnai davky nejake problemy, poslu report
     *
     * @param array $reportItems
     */
    private function sendReport($reportItems)
    {
        if ($reportItems['multiple'] || $reportItems['zero'] || $reportItems['error']) {
            $this->mailer->send([
                IMessageFactory::FROM => ADMIN_MAIL,
                IMessageFactory::TO => ADMIN_MAIL,
                IMessageFactory::SUBJECT => $this->translator->translate('messages.geocoding.report.subject'),
                IMessageFactory::DATA => $reportItems,
            ]);
        }
    }

    /**
     * Overi hodnoty objektu a pokud nema GPS souradnice, tak prida do fronty
     *
     * @param array $object
     * @param boolean $priority
     */
    public function checkGps($object, $priority)
    {
        if (empty($object['longitude']) || empty($object['latitude'])) {
            $this->manager->persist([
                'object_id' => $object['object_id'],
                'priority' => $priority,
            ]);
        }
    }
}
