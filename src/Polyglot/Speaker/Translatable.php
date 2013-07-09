<?php

/**
 * This file is part of the Polyglot package.
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed along with this source code.
 *
 * @license https://github.com/unwiredbrain/polyglot/blob/master/LICENSE MIT License
 */

namespace Polyglot\Speaker;

/**
 * @author Massimo Lombardo <unwiredbrain@gmail.com>
 */
interface Translatable
{

    /**
     * Interpolates a given sentence using the specified parameters.
     *
     * @param  string $sentence A sentence to translate.
     * @param  array  $context  A key/value list of parameters to interpolate.
     *
     * @return string The interpolated sentence.
     */
    public function interpolate($sentence, array $context = array());

    /**
     * Translates a given sentence.
     *
     * @param  string $sentence A sentence to translate.
     * @param  array  $context  A key/value list of parameters to interpolate.
     *
     * @return string The translated sentence.
     */
    public function translate($sentence, array $context = array());

}
