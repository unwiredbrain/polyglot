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

use Polyglot\Speaker\Translatable;

/**
 * @author Massimo Lombardo <unwiredbrain@gmail.com>
 */
class Context implements Translatable
{
    /**
     * The context name.
     *
     * @var string
     */
    private $name = null;

    /**
     * Constructor.
     *
     * @param string $name The context name.
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Retrieves the pluralized version of a sentence according to the language locale pluralization rules.
     *
     * @param string $msgid The singular version of the sentence.
     * @param string $msgid_plural The plural version of the sentence.
     * @param int $n The number to use to choose which version to pick.
     *
     * @return string Either the singular or pluralized version of the sentence.
     */
    protected function pluralize($msgid, $msgid_plural, $n) {
        return dngettext($this->name, $msgid, $msgid_plural, $n);
    }

    /**
     * {@inheritdoc}
     *
     * @internal This implementation is inspired to the PRS-3 one.
     */
    function interpolate($sentence, array $context = array())
    {
        // build a replacement array with braces around the context keys.
        $interpolations = array();

        foreach ($context as $tag => $value) {
            $interpolations['${' . $tag . '}'] = $value;
        }

        // interpolate replacement values into the sentence and return
        return strtr($sentence, $interpolations);
    }

    /**
     * {@inheritdoc}
     */
    public function translate($sentence, array $params = array())
    {
        $msgid = $sentence;
        $msgid_plural = '';
        $n = 1;

        if (!empty($params['count']) && is_numeric($params['count'])) {
            $n = intval($params['count']);
        }

        if (abs($n) > 1) {
            $msgid_plural = $sentence;
        }

        $sentence = $this->pluralize($msgid, $msgid_plural, $n);
        $sentence = $this->interpolate($sentence, $params);

        return $sentence;
    }

}
