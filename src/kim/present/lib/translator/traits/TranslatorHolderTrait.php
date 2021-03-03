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

namespace kim\present\lib\translator\traits;

use kim\present\lib\translator\Translator;
use pocketmine\plugin\PluginBase;

/**
 * This trait override most methods in the {@link PluginBase} abstract class.
 */
trait TranslatorHolderTrait{
    /** @var Translator */
    private $translator;

    public function getTranslator() : Translator{
        return $this->translator;
    }

    /**
     * Load language with save default language resources
     */
    public function loadLanguage(?string $locale = null) : void{
        $this->saveDefaultLocales();

        /** @noinspection PhpParamsInspection */
        $this->translator = new Translator($this);
        if($locale !== null){
            $this->translator->setDefaultLocale($locale);
        }
    }

    /**
     * Save default language resources
     */
    public function saveDefaultLocales(){
        foreach($this->getResources() as $filePath => $info){
            if(preg_match('/^locale\/[a-zA-Z]{3}\.ini$/', $filePath)){
                $this->saveResource($filePath);
            }
        }
    }
}