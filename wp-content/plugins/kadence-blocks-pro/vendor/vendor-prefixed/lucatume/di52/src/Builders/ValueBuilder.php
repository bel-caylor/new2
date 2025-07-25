<?php
/**
 * A builder wrapping a value that will return upon build.
 *
 * @package lucatume\DI52
 *
 * @license GPL-3.0
 * Modified by kadencewp on 22-August-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace KadenceWP\KadenceBlocksPro\lucatume\DI52\Builders;

/**
 * Class ValueBuilder
 *
 * @package KadenceWP\KadenceBlocksPro\lucatume\DI52\Builders
 */
class ValueBuilder implements BuilderInterface
{
    /**
     * The value the instance of the builder was built for.
     *
     * @var mixed
     */
    private $value;

    /**
     * ValueBuilder constructor.
     *
     * @param mixed $value The value to build the instance of the builder for.
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Builds and returns an instance of the builder built on the specified value.
     *
     * @param mixed $value The value the instance of the builder should be built for.
     *
     * @return ValueBuilder An instance of the builder built on the specified value.
     */
    public static function of($value)
    {
        return $value instanceof self ? $value : new self($value);
    }

    /**
     * Returns the value wrapped by the builder.
     *
     * @return mixed The value wrapped by the builder.
     */
    public function build()
    {
        return $this->value;
    }
}
