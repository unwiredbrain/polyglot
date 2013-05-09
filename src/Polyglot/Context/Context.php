<?php

/**
 * This file is part of the Polyglot package.
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed along with this source code.
 *
 * @license https://github.com/unwiredbrain/polyglot/blob/master/LICENSE MIT License
 */

namespace Polyglot\Context;

use \Polyglot\Translatable;

/**
 * @author Massimo Lombardo <unwiredbrain@gmail.com>
 */
class Context implements Translatable
{

    /**
     * @var string
     */
    private $name = null;

    /**
     * Constructor.
     *
     * @param string $name The context name.
     *
     * @api
     */
    public function __construct ($name)
    {
        $this->name = $name;
    }

    protected function pluralize ($msgid, $msgid_plural, $n) {
        return dngettext($this->name, $msgid, $msgid_plural, $n);
    }

    /**
     * {@inheritdoc}
     */
    public function interpolate ($sentence, array $params)
    {
        // For each parameter given...
        foreach ($params as $param => $value) {
            // ...which has a string or numeric parameter value...
            if (is_string($value) || is_numeric($value)) {
                // ...replace every occurrence of ${param} with its value.
                $sentence = preg_replace('/\$\{(?:' . $param . ')\}/m', $value, $sentence);
            }
        }

        return $sentence;
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
