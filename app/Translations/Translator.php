<?php

namespace App\Translations;

use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Translation\Translator as BaseTranslator;

class Translator implements TranslatorInterface, TranslatorBagInterface
{
    protected const TRANS = [
        'year'   => 'y',
        'month'  => 'm',
        'week'   => 'w',
        'day'    => 'd',
        'hour'   => 'h',
        'minute' => 'm',
        'second' => 's',
    ];

    // public function trans(string $id, array $parameters = [], string $domain = null, string $locale = null)
    // {
    //     return $parameters[':count'] . self::TRANS[$id];
    // }

    // public function getCatalogue(string $locale = null)
    // {
    //     return new \Symfony\Component\Translation\MessageCatalogue('pl_PL');
    // }

    public function trans(string $id, array $parameters = [], string $domain = null, string $locale = null): string
    {
        //return $this->translator->trans($id,$parameters,$domain,$locale);
        return strtoupper($id); // Verify calling this class
    }

    public function getCatalogue(string $locale = null): MessageCatalogueInterface
    {
        return $this->translator->getCatalogue($locale);
    }

    public function getLocale(): string
    {
        return $this->translator->getLocale();
    }
    public function setLocale(string $locale)
    {
        $this->translator->setLocale($locale);
    }
}
