<?php

namespace App\Translations;

use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Translator implements TranslatorInterface, TranslatorBagInterface
{
    protected const TRANS = [
        'year'   => 'ปี',
        'month'  => 'เดือน',
        'week'   => 'สัปดาห์',
        'day'    => 'วัน',
        'hour'   => 'ชั่วโมง',
        'minute' => 'นาที',
        'second' => 'วินาที',
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
        //return strtoupper($id); // Verify calling this class
        return $parameters[':count'] . self::TRANS[$id];
    }

    public function getCatalogue(string $locale = null): MessageCatalogueInterface
    {
        //return $this->translator->getCatalogue($locale);
        return new \Symfony\Component\Translation\MessageCatalogue('th_TH');
    }

    public function getCatalogues(): array
    {
        return $this->translator->getCatalogues();
    }

    public function getLocale(): string
    {
        return $this->translator->getLocale();
    }
}
