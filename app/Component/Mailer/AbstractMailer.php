<?php

namespace MP\Component\Mailer;

use MP\Component\AbstractControl;
use MP\Util\Arrays;
use MP\Util\Lang\Lang;
use Nette\Application\UI\ITemplate;
use Nette\Application\UI\ITemplateFactory;
use Nette\Mail\IMailer;
use Nette\Mail\Message;
use Pelago\Emogrifier;

/**
 * Abstraktni predek komponent s podporou zaslani e-mailu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class AbstractMailer extends AbstractControl
{
    /** @var MessageFactory */
    protected $messageFactory;

    /** @var IMailer */
    protected $mailer;

    /** @var Lang */
    protected $lang;

    /**
     * @param ITemplateFactory $templateFactory
     * @param IMessageFactory $messageFactory
     * @param IMailer $mailer
     * @param Lang $lang
     */
    public function __construct(ITemplateFactory $templateFactory, IMessageFactory $messageFactory, IMailer $mailer, Lang $lang)
    {
        $this->templateFactory = $templateFactory;
        $this->messageFactory = $messageFactory;
        $this->mailer = $mailer;
        $this->lang = $lang;
    }

    /**
     * Odesle zpravu.
     *
     * @param array $messageSettings
     */
    public function send(array $messageSettings)
    {
        $message = $this->prepareMessage($messageSettings);

        $this->mailer->send($message);
    }

    /**
     * @param array $messageSettings
     *
     * @return Message
     */
    protected function prepareMessage(array $messageSettings)
    {
        $message = $this->messageFactory->create($messageSettings);

        if (null == $message->getBody()) {
            $template = $this->getTemplate();
            $template->data = Arrays::get($messageSettings, IMessageFactory::DATA, []);
            $this->setTemplateParams($template);
            $cssFile = $this->getCssContent(ASSET_DIR . '/css/@mail.css');

            $emogrifier = new Emogrifier();
            $emogrifier->setHtml((string) $template);
            $emogrifier->setCss($cssFile);

            $message->setHtmlBody($emogrifier->emogrify());
        }

        return $message;
    }

    /**
     * Nasetuje promenne pro template
     *
     * @param ITemplate $template
     */
    protected function setTemplateParams(ITemplate $template)
    {
        $template->lang = $this->lang->getLocale();
        $template->baseImageUrl = $template->baseUrl  . '/asset/img/mail';
        $template->tableParam = " cellspacing='0' cellpadding='0' border='0'";
    }

    /**
     * Vraci obsah CSS souboru
     *
     * @param string $cssFile
     *
     * @return string
     * @throws \Nette\IOException
     */
    protected function getCssContent($cssFile)
    {
        if (false === is_file($cssFile)) {
            throw new \Nette\IOException("File '$cssFile' does not exist or is not a file.");
        }

        $content = file_get_contents($cssFile);

        if (false === $content) {
            throw new \Nette\IOException("Unable to read content of file '$cssFile'");
        }

        return $content;
    }
}
