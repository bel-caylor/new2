<?php
/**
 * Builds and sets up the correct builder for a binding.
 *
 * @package lucatume\DI52
 *
 * @license GPL-3.0
 * Modified by kadencewp on 22-August-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace KadenceWP\KadenceBlocksPro\lucatume\DI52\Builders;

use Closure;
use KadenceWP\KadenceBlocksPro\lucatume\DI52\Container;
use KadenceWP\KadenceBlocksPro\lucatume\DI52\NotFoundException;

/**
 * Class Factory
 *
 * @package KadenceWP\KadenceBlocksPro\lucatume\DI52\Builders
 */
class Factory
{
    /**
     * A reference to the resolver that should be used to resolve the implementations.
     *
     * @var Resolver
     */
    protected $resolver;
    /**
     * A reference to the DI container builder will be built for.
     *
     * @var Container
     */
    protected $container;

    /**
     * BuilderFactory constructor.
     * @param Container $container A reference to the DI container the builder is working for.
     * @param Resolver  $resolver A reference to the resolver builders will use to resolve to implementations.
     */
    public function __construct(Container $container, Resolver $resolver)
    {
        $this->container = $container;
        $this->resolver = $resolver;
    }

    /**
     * Returns the correct builder for a value.
     *
     * @param  string|class-string|mixed  $id                 The string id to provide a builder for, or a value.
     * @param  mixed                      $implementation     The implementation to build the builder for.
     * @param  string[]|null              $afterBuildMethods  A list of methods that should be called on the built
     *                                                        instance after it's been built.
     * @param  mixed                      ...$buildArgs       A set of arguments to pass that should be used to build
     *                                                        the instance, if any.
     *
     * @return BuilderInterface A builder instance.
     *
     * @throws NotFoundException If a builder cannot find its implementation target.
     */
    public function getBuilder($id, $implementation = null, array $afterBuildMethods = null, ...$buildArgs)
    {
        if ($implementation === null) {
            $implementation = $id;
        }
        if (is_string($implementation) && is_string($id)) {
            if (class_exists($implementation)) {
                return new ClassBuilder($id, $this->resolver, $implementation, $afterBuildMethods, ...$buildArgs);
            }
            return new ValueBuilder($implementation);
        }

        if ($implementation instanceof BuilderInterface) {
            return $implementation;
        }

        if ($implementation instanceof Closure) {
            return new ClosureBuilder($this->container, $implementation);
        }

        if (is_callable($implementation)) {
            return new CallableBuilder($this->container, $implementation);
        }

        return new ValueBuilder($implementation);
    }

    /**
     * Sets the container the builder should use.
     *
     * @since TBD
     *
     * @param Container $container The container to bind.
     *
     * @return void
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Sets the resolver the container should use.
     *
     * @since TBD
     *
     * @param Resolver $resolver The resolver the container should use.
     *
     * @return void
     */
    public function setResolver(Resolver $resolver)
    {
        $this->resolver = $resolver;
    }
}
