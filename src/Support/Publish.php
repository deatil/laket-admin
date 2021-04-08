<?php

declare (strict_types = 1);

namespace Laket\Admin\Support;

use Laket\Admin\Traits\Macroable;

/**
 * 推送
 *
 * @create 2021-3-26
 * @author deatil
 */
class Publish
{
    use Macroable;
    
    /**
     * The paths that should be published.
     *
     * @var array
     */
    public $publishes = [];

    /**
     * The paths that should be published by group.
     *
     * @var array
     */
    public $publishGroups = [];

    /**
     * Register paths to be published by the publish command.
     *
     * @param  array  $paths
     * @param  mixed  $groups
     * @return void
     */
    public function publishes($class, array $paths, $groups = null)
    {
        $this->ensurePublishArrayInitialized($class);

        $this->publishes[$class] = array_merge($this->publishes[$class], $paths);

        foreach ((array) $groups as $group) {
            $this->addPublishGroup($group, $paths);
        }
    }

    /**
     * Ensure the publish array for the service provider is initialized.
     *
     * @param  string  $class
     * @return void
     */
    protected function ensurePublishArrayInitialized($class)
    {
        if (! array_key_exists($class, $this->publishes)) {
            $this->publishes[$class] = [];
        }
    }

    /**
     * Add a publish group / tag to the service provider.
     *
     * @param  string  $group
     * @param  array  $paths
     * @return void
     */
    protected function addPublishGroup($group, $paths)
    {
        if (! array_key_exists($group, $this->publishGroups)) {
            $this->publishGroups[$group] = [];
        }

        $this->publishGroups[$group] = array_merge(
            $this->publishGroups[$group], $paths
        );
    }

    /**
     * Get the paths to publish.
     *
     * @param  string|null  $provider
     * @param  string|null  $group
     * @return array
     */
    public function pathsToPublish($provider = null, $group = null)
    {
        if (! is_null($paths = $this->pathsForProviderOrGroup($provider, $group))) {
            return $paths;
        }

        return collect($this->publishes)->reduce(function ($paths, $p) {
            return array_merge($paths, $p);
        }, []);
    }

    /**
     * Get the paths for the provider or group (or both).
     *
     * @param  string|null  $provider
     * @param  string|null  $group
     * @return array
     */
    protected function pathsForProviderOrGroup($provider, $group)
    {
        if ($provider && $group) {
            return $this->pathsForProviderAndGroup($provider, $group);
        } elseif ($group && array_key_exists($group, $this->publishGroups)) {
            return $this->publishGroups[$group];
        } elseif ($provider && array_key_exists($provider, $this->publishes)) {
            return $this->publishes[$provider];
        } elseif ($group || $provider) {
            return [];
        }
    }

    /**
     * Get the paths for the provider and group.
     *
     * @param  string  $provider
     * @param  string  $group
     * @return array
     */
    protected function pathsForProviderAndGroup($provider, $group)
    {
        if (! empty($this->publishes[$provider]) && ! empty($this->publishGroups[$group])) {
            return array_intersect_key($this->publishes[$provider], $this->publishGroups[$group]);
        }

        return [];
    }

    /**
     * Get the service providers available for publishing.
     *
     * @return array
     */
    public function publishableProviders()
    {
        return array_keys($this->publishes);
    }

    /**
     * Get the groups available for publishing.
     *
     * @return array
     */
    public function publishableGroups()
    {
        return array_keys($this->publishGroups);
    }
}
