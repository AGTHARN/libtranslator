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
 */

declare(strict_types=1);

namespace kim\present\lib\translator;

use function array_map;
use function strtolower;
use function file_exists;
use function parse_ini_string;
use function file_get_contents;

class Language
{
    /** @var string[] $map - id => text */
    protected array $map = [];
    /** @var string $locale - Locale name (ISO_639-3 code) */
    protected string $locale;
    
    /**
     * __construct
     *
     * @param  string[] $map
     * @param  string $locale
     * @return void
     */
    public function __construct(array $map, string $locale)
    {
        $this->map = $map;
        $this->locale = $locale;
    }
    
    /**
     * getLocale
     *
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }
    
    /**
     * get
     *
     * @param  string $id
     * @return string|null
     */
    public function get(string $id): ?string
    {
        return $this->map[$id] ?? null;
    }
    
    /**
     * getNonNull
     *
     * @param  string $id
     * @return string
     */
    public function getNonNull(string $id): string
    {
        return $this->map[$id] ?? $id;
    }
    
    /**
     * getName
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->getNonNull("language.name");
    }
 
    /**
     * fromContents
     *
     * @param  string $contents
     * @param  string $locale
     * @return Language - the loaded language from contents
     */
    public static function fromContents(string $contents, string $locale): Language
    {
        return new Language(array_map("stripcslashes", parse_ini_string($contents, false, INI_SCANNER_RAW)), strtolower($locale));
    }
    
    /**
     * fromFile
     *
     * @param  string $path
     * @param  string $locale
     * @return Language|null the loaded language from file
     */
    public static function fromFile(string $path, string $locale): ?Language
    {
        if (!file_exists($path))
            return null;

        return self::fromContents(file_get_contents($path), $locale);
    }
}
