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

use Polyglot\ContextInterface;

class Polyglot implements \Polyglot\PolyglotInterface
{


    /**
     * @var ContextInterface[]
     */
    private $contexts = array();

    /**
     * @var ContextInterface
     */
    private $context = null;

    /**
     * @var string
     */
    private $defaultPath = null;

    /**
     * Constructor.
     *
     * @param string $defaultPath The default path where to find all the locales.
     *
     * @throws \RuntimeException
     *
     * @api
     */
    public function __construct ($defaultPath)
    {
        if (!extension_loaded('gettext')) {
            throw new \RuntimeException('Unable to setup Polyglot as gettext is not enabled.');
        }

        $this->defaultPath = $defaultPath;
    }

    /**
     * Tests whewther the given context is registered.
     *
     * @param string $name The context name.
     * @return boolean Return TRUE if the given context is registered, FALSE otherwise.
     */
    public function hasContext($name)
    {
        return !empty($this->contexts[$name]);
    }

    public function registerContext($name, $path = null)
    {
        if (empty($path) || !is_string($path)) {
            $path = $this->defaultPath;
        }

        $this->contexts[$name] = new \Polyglot\Context\Context($name, $path);

        return $this;
    }


    public function registerContexts()
    {
        foreach (func_get_args() as $name) {
            $this->registerContext($name);
        }

        return $this;
    }


    public function using($name)
    {
        if ($this->hasContext($name)) {
            $this->context = $this->contexts[$name];
        } else {
            throw new \DomainException('Invalid context.');
        }

        return $this;
    }


    public function get($name)
    {
        if (!$this->hasContext($name)) {
            throw new \InvalidArgumentException('Invalid context.');
        }
        return $this->contexts[$name];
    }


    public function getContexts()
    {
        return $this->contexts;
    }


    /**
     * {@inheritdoc}
     */
    public function translate($sentence, array $params = array())
    {
        return $this->context->translate($sentence, $params);
    }


}
