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

use \Polyglot\Context\Context;
use \Polyglot\Translatable;

/**
 * @author Massimo Lombardo <unwiredbrain@gmail.com>
 */
class Polyglot implements Translatable
{

    /**
     * The default, fallback locale.
     */
    const DEFAULT_LOCALE = 'en_US';

    /**
     * @var Context
     */
    private $context = null;

    /**
     * @var Context[]
     */
    private $contexts = array();

    /**
     * @var string
     */
    private $path = null;

    /**
     * @var Polyglot
     */
    protected static $instance = null;

    /**
     * Set the specified locale for the given category.
     *
     * Example usage:
     *
     * <code>
     * $polyglot = \Polyglot::getInstance();
     * $polyglot::monetary('da_DK');
     * $polyglot::time('en_CA');
     * </code>
     *
     * @method bool collate($locale) Set the given locale for the LC_COLLATE category.
     * @method bool ctype($locale) Set the given locale for the LC_CTYPE category.
     * @method bool monetary($locale) Set the given locale for the LC_MONETARY category.
     * @method bool numeric($locale) Set the given locale for the LC_NUMERIC category.
     * @method bool time($locale) Set the given locale for the LC_TIME category.
     * @method bool messages($locale) Set the given locale for the LC_MESSAGES category.
     *
     * @param string $method The category to set/modify.
     * @param array $params The locale to use.
     *
     * @return Polyglot Returns an instance of Polyglot, so to enable fluent interfaces.
     *
     * @throws \DomainException
     * @throws \InvalidArgumentException
     */
    public function __call ($method, $params)
    {
        // Clean up.
        $method = strtolower($method);

        // Compute the category: $method = blah --> $category = LC_BLAH
        $category = 'LC_' . strtoupper($method);

        // Bail out if LC_BLAH is not defined.
        if (!defined($category)) {
            throw new \DomainException('Unsupported locale category given.');
        }

        // Bail out if no parameter was given.
        if (empty($params[0]) || !is_string($params[0])) {
            throw new \InvalidArgumentException('Invalid locale given.');
        }

        // Take the first parameter, forget about the other ones.
        $locale = $params[0];

        // Set the locale for the category.
        putenv(sprintf('%s=%s', $category, $locale));
        setlocale(constant($category), array(
            $locale . '.UTF-8@euro',
            $locale . '.UTF-8',
            $locale
        ));

        return $this;
    }

    /**
     * Constructor.
     *
     * @param string $path The default path where to find all the locales.
     *
     * @throws \RuntimeException
     *
     * @api
     */
    private function __construct ($locale = self::DEFAULT_LOCALE)
    {
        if (!extension_loaded('gettext')) {
            throw new \RuntimeException('Unable to setup Polyglot as gettext is not enabled.');
        }

        $this->all($locale);
    }

    /**
     * Tests whether the internal context member has been initialized.
     *
     * @throws \UnexpectedValueException
     */
    private function doSanityCheck()
    {
        if (!($this->context instanceof Context)) {
            throw new \UnexpectedValueException('No context specified.');
        }
    }

    /**
     * Returns the Context instance identified by the specified name.
     *
     * Use this method to ease repetitive tasks.
     *
     * @param string $name The context name.
     *
     * @return Polyglot\Context Returns an instance of Context.
     *
     * @throws \InvalidArgumentException
     */
    public function getContext($name)
    {
        if (!$this->hasContext($name)) {
            throw new \InvalidArgumentException('Invalid context.');
        }

        return $this->contexts[$name];
    }

    /**
     * Returns the registered contexts.
     *
     * @return array The registered contexts.
     */
    public function getContexts()
    {
        return $this->contexts;
    }

    /**
     * Returns the singleton instance.
     *
     * @return Polyglot Returns the instance of Polyglot.
     */
    public static function getInstance ()
    {
        if (NULL === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Returns the path where all the .po/.mo files are stored.
     *
     * @return string The path where all the .po/.mo files are stored.
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function interpolate($sentence, array $params = array())
    {
        $this->doSanityCheck();
        return $this->context->interpolate($sentence, $params);
    }

    /**
     * Tests whewther the given context is registered.
     *
     * @param string $name The context name.
     *
     * @return boolean Returns TRUE if the given context is registered, FALSE otherwise.
     */
    public function hasContext($name)
    {
        return !empty($this->contexts[$name]);
    }

    /**
     * Registers multiple contexts.
     *
     * Example usage:
     *
     * <code>
     * $polyglot = \Polyglot::getInstance();
     * $polyglot->register('formats', 'messages', 'menus', 'alerts');
     * ...
     * $polyglot->using('alerts')->translate('Preferences successfully saved.');
     * </code>
     *
     * @param string[] A list of contexts.
     *
     * @return Polyglot Returns an instance of Polyglot, so to enable fluent interfaces.
     */
    public function register()
    {
        foreach (func_get_args() as $name) {
            $this->contexts[$name] = new \Polyglot\Context\Context($name);

            bind_textdomain_codeset($name, 'UTF-8');
            bindtextdomain($name, $this->path);
        }

        return $this;
    }

    /**
     * Instructs Polyglot on where to find all the contexts' .po/.mo files.
     *
     * @param string $path The path where to find all the contexts' .po/.mo files.
     *
     * @return Polyglot Returns an instance of Polyglot, so to enable fluent interfaces.
     *
     * @throws \InvalidArgumentException
     */
    public function setPath($path)
    {
        if (!is_readable($path)) {
            throw new \InvalidArgumentException('Path not found or not readable.');
        }

        $this->path = $path;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function translate($sentence, array $params = array())
    {
        $this->doSanityCheck();
        return $this->context->translate($sentence, $params);
    }

    /**
     * Alters the internal context.
     *
     * Use this method to ease quick, one-off tasks.
     *
     * Example usage:
     *
     * <code>
     * $polyglot = \Polyglot::getInstance();
     * $polyglot->register('formats', 'messages', 'menus', 'alerts');
     * ...
     * $polyglot->using('alerts')->translate('Preferences successfully saved.');
     * </code>
     *
     * @param string $name The context name.
     *
     * @return Polyglot Returns an instance of Polyglot, so to enable fluent interfaces.
     *
     * @throws \DomainException
     */
    public function using($name)
    {
        if ($this->hasContext($name)) {
            $this->context = $this->contexts[$name];
        } else {
            throw new \DomainException('Invalid context.');
        }

        return $this;
    }

}
