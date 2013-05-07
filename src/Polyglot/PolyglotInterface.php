<?php

/**
 * This file is part of the Polyglot package.
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed along with this source code.
 *
 * @license https://github.com/unwiredbrain/polyglot/blob/master/LICENSE MIT License
 */

namespace Polyglot;

/**
 * @author Massimo Lombardo <unwiredbrain@gmail.com>
 */
interface PolyglotInterface
{

    /**
     * Translates a given sentence.
     *
     * @param string $sentence A sentence to translate.
     * @param array $params A key/value list of parameters to interpolate.
     * @return string A ResultInterface result object.
     */
    public function translate($sentence, array $params = array());

}
