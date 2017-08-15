<?php

namespace MP\Component;

use MP\Util\Strings;
use Nette\Application\UI\Control;
use Nette\Application\UI\ITemplate;
use Nette\Reflection\ClassType;

/**
 * Abstraktni predek komponent. Pridava podporu automatickeho dohledani sablony.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
abstract class AbstractControl extends Control
{
    /** @var String absolutni cesta ke slozce */
    protected $baseDir;

    /**
     * @override Dohladava a nastavuje sablonu a vychozi promenne sablony
     *
     * @param null|string $file
     *
     * @return ITemplate
     */
    public function getTemplate($file = null)
    {
        /** @var ITemplate */
        $template = parent::getTemplate();

        $this->setTemplateFile($template, $file);

        return $template;
    }

    /**
     * Automaticky natavi soubor sablony do dane sablony. Pokud neni dany nazev sablony, pouzije se stejny jako je nazev controlu
     *
     * @param ITemplate $template
     * @param null|string $templateFile
     *
     * @throws \Nette\FileNotFoundException
     */
    protected function setTemplateFile(ITemplate $template, $templateFile = null)
    {
        $filePath = $this->findTemplateFile($templateFile);

        $template->setFile($filePath);
    }

    /**
     * Dle nazvu sablony - vztahuji se na nej pravidla viz komentar @see self::findTemplateFile() - sestavi absolutni cestu k sablone
     *
     * @param string $templateFile
     * @param bool $need pokud je true, vyhodi exceptionu, pokud neni sabona nalezena. Jinak vraci null.
     *
     * @throws \Nette\FileNotFoundException
     * @return null|string
     */
    protected function findTemplateFile($templateFile, $need = true)
    {
        if (null === $templateFile) { // null -> pouzijem nazev komponenty
            $templateFile = $this->getReflection()->getShortName();
            $fileName = "$templateFile.latte";
        } elseif (false !== strpos($templateFile, '/')) { // je tam absolutni cesta - neresim, nastavi co dal
            $fileName = $templateFile;
        } elseif (strpos($templateFile, '.') === 0) { // zacina teckou -> hledame NazevKomponenty[.$templateFile].latte
            $templateFile = $this->getReflection()->getShortName() . $templateFile . '.latte';
            $fileName = $templateFile;
        } else {
            $fileName = "$templateFile.latte";
        }

        if ($need && null === $fileName) {
            throw new \Nette\FileNotFoundException("Template '$templateFile' was not found in expected directories");
        }

        $controlTemplatePath = $this->getTemplateDir() . '/' . $fileName;

        return $controlTemplatePath;
    }

    /**
     * @param string|null $baseDir pokud neni baseDir uvedena, bere se hodnota, kterou vraci funkec getBaseDir()
     *
     * @return string
     */
    protected function getTemplateDir($baseDir = null)
    {
        $baseDir = $baseDir ?? $this->getBaseDir();

        return "$baseDir/template";
    }

    /**
     * Vraci slozku komponenty (ve ktere se nachazi jeji trida)
     *
     * @return string
     */
    protected function getBaseDir()
    {
        if (null === $this->baseDir) {
            $fileName = $this->getReflection()->getFileName();
            $this->baseDir = dirname($fileName);
        }

        return $this->baseDir;
    }

    /**
     * Dle nazvu tridy vygeneruje nazev komponenty, jako webalizovany nazev tridy bez namespacu
     *
     * @return string
     */
    protected static function generateName()
    {
        $reflection = ClassType::from(static::class);

        $name = Strings::webalize($reflection->getName(), null, true, true);

        return $name;
    }

    /**
     * @override Delegace na presenter.
     *
     * @param string $message
     * @param string $type
     *
     * @return \stdClass
     */
    public function flashMessage($message, $type = FlashMessageControl::TYPE_INFO)
    {
        return $this->getPresenter()->flashMessage($message, $type);
    }
}
