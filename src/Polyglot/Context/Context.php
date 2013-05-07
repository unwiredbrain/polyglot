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

/**
 * @author Massimo Lombardo <unwiredbrain@gmail.com>
 */
class Context implements \Polyglot\PolyglotInterface
{

    /**
     * @var string
     */
    private $name = null;

    /**
     * @var string
     */
    private $path = null;

    /**
     * Constructor.
     *
     * @param string $name The context name.
     * @param string $path Where to find the .po/.mo files.
     * @param string $encoding The encoding in which the .po/.mo files are
     *                         expected to be. Optional, default is UTF-8.
     *
     * @api
     */
    public function __construct ($name, $path, $encoding = 'UTF-8')
    {
        $this->name = $name;
        $this->path = $path;
        $this->encoding = $encoding;
        bindtextdomain($name, $path);
        bind_textdomain_codeset($name, $encoding);
    }

    protected function pluralize ($msgid, $msgid_plural, $n) {
        return dngettext($this->name, $msgid, $msgid_plural, $n);
    }

    protected function interpolate ($sentence, array $params)
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
        $msgid_plural = $sentence . '__PLURAL';
        $n = 1;
        if (!empty($params['count']) && is_numeric($params['count'])) {
            $n = intval($params['count']);
        }
        $sentence = $this->pluralize($msgid, $msgid_plural, $n);
        $sentence = $this->interpolate($sentence, $params);
        return $this->name . ': ' . $sentence;
    }

}
