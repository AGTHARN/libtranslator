<?php

/**
 *  ____                           _   _  ___
 * |  _ \ _ __ ___  ___  ___ _ __ | |_| |/ (_)_ __ ___
 * | |_) | '__/ _ \/ __|/ _ \ '_ \| __| ' /| | '_ ` _ \
 * |  __/| | |  __/\__ \  __/ | | | |_| . \| | | | | | |
 * |_|   |_|  \___||___/\___|_| |_|\__|_|\_\_|_| |_| |_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author  PresentKim (debe3721@gmail.com)
 * @link    https://github.com/PresentKim
 * @license https://www.gnu.org/licenses/lgpl-3.0 LGPL-3.0 License
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 *
 * @noinspection PhpIllegalPsrClassPathInspection
 * @noinspection SpellCheckingInspection
 * @noinspection PhpDocSignatureInspection
 * @noinspection RegExpRedundantEscape
 */

declare(strict_types=1);

namespace kim\present\lib\translator;

use function strlen;
use function explode;
use pocketmine\Server;
use function array_keys;
use function strtolower;
use function array_merge;
use function str_replace;
use function method_exists;
use function preg_match_all;
use pocketmine\player\Player;
use pocketmine\command\CommandSender;
use kim\present\lib\translator\converter\locale\LocaleConverter;

class Translator
{
    /** @var Language[] $languages */
    protected array $languages = [];

    /** @var Language|null $defaultLanguage */
    public ?Language $defaultLanguage = null;

    /**
     * __construct
     *
     * @param  @param Language[] $language
     * @param  Language|null $defaultLanguage
     * @return void
     */
    public function __construct(array $languages = [], ?Language $defaultLanguage = null)
    {
        $this->languages = $languages;
        $this->defaultLanguage = $defaultLanguage;
    }

    /**
     * translate
     *
     * @param  string $str - original string
     * @param  mixed[] $params - translate parameters
     * @param  string|null $locale - translate language locale. if null, translate by default language
     * @return string
     */
    public function translate(string $str, array $params = [], ?string $locale = null): string
    {
        $params = array_merge($params, DefaultParams::getAll());
        $lang = $this->getLanguage($locale);
        if ($lang !== null) {
            $parts = explode("%", $str);
            $str = "";
            $lastTranslated = false;
            foreach ($parts as $_ => $part) {
                $new = $lang->get($part) ?? $this->defaultLanguage->getNonNull($part);
                if (strlen($str) > 0 && $part === $new && !$lastTranslated) {
                    $str .= "%";
                }
                $lastTranslated = $part !== $new;

                $str .= $new;
            }
        }

        if (preg_match_all("/\{%([a-zA-Z0-9]+)\}/", $str, $matches, PREG_SET_ORDER) !== false) {
            foreach ($matches as $match) {
                if (isset($params[$match[1]])) {
                    $str = str_replace($match[0], $params[$match[1]], $str);
                }
            }
        }
        return $str;
    }

    /**
     * translateTo
     * 
     * @param string $str - original string
     * @param mixed[] $params - translate parameters
     * @param CommandSender|null $sender - translate target sender. if null, translate by default language
     * @return string
     */
    public function translateTo(string $str, array $params, ?CommandSender $sender = null): string
    {
        $locale = null;
        if ($sender !== null && method_exists($sender, 'getLocale') && !Server::getInstance()->isLanguageForced()) {
            /** @var Player $sender */
            $locale = LocaleConverter::convertIEFT($sender->getLocale());
        }
        return $this->translate($str, $params, $locale);
    }

    /**
     * getLanguages
     *
     * @return Language[]
     */
    public function getLanguages(): array
    {
        return $this->languages;
    }
    
    /**
     * getLocaleList
     *
     * @return string[]
     */
    public function getLocaleList(): array
    {
        return array_keys($this->getLanguages());
    }
    
    /**
     * getLanguage
     *
     * @param  string|null $locale
     * @return Language|null - if $locale is null, return default language
     */
    public function getLanguage(?string $locale = null): ?Language
    {
        return $this->languages[strtolower($locale ?? Server::getInstance()->getLanguage()->getLang())] ?? $this->defaultLanguage;
    }
    
    /**
     * getDefaultLanguage
     *
     * @return Language|null
     */
    public function getDefaultLanguage(): ?Language
    {
        return $this->defaultLanguage;
    }
    
    /**
     * setDefaultLanguage
     *
     * @param  Language|null $defaultLanguage
     * @return void
     */
    public function setDefaultLanguage(?Language $defaultLanguage): void
    {
        $this->defaultLanguage = $defaultLanguage;
    }
}
