<?php namespace Mascame\Hooky;

use Illuminate\Support\Collection;

class Hook
{
    /**
     * @var array
     */
    protected $hooks = [];

    /**
     * Returns all attached hooks until this moment
     *
     * @return array
     */
    public function getAll()
    {
        return array_keys($this->hooks);
    }

    /**
     * @param string $hook
     * @param string|array $handlers
     */
    public function to($hook, $handlers)
    {
        $this->hooks[$this->key($hook)][] = $handlers;
    }

    /**
     * @param $hook
     * @param $data
     * @return mixed
     */
    public function fire($hook, $data = [])
    {
        if (! $this->hasHandlers($hook)) {
            return $data;
        }

        $iterator = new \ArrayIterator($this->getHandlers($hook));

        $currentHandler = $iterator->current();

        return $this->dispatchHandler($currentHandler, $data, $iterator);
    }

    /**
     * @param HookContract|\Closure $handler
     * @param $data
     * @param $iterator
     * @return mixed
     * @throws InvalidHookException
     */
    protected function dispatchHandler($handler, $data, $iterator)
    {
        if (is_callable($handler)) {
            return $handler($data, $this->nextHandler($iterator));
        }

        if (! is_object($handler)) {
            $handler = new $handler;
        }

        if (! is_a($handler, HookContract::class)) {
            throw new InvalidHookException();
        }

        return $handler->handle($data, $this->nextHandler($iterator));
    }

    /**
     * @param \ArrayIterator $iterator
     * @return \Closure
     */
    protected function nextHandler(\ArrayIterator $iterator)
    {
        $iterator->next();

        return function($data) use ($iterator) {
            $nextHandler = $iterator->current();

            if (! $nextHandler) return $data;

            return $this->dispatchHandler($nextHandler, $data, $iterator);
        };
    }

    /**
     * Flattens the handlers because $handlers can be provided as array
     *
     * @param $hook
     * @return array
     */
    protected function getHandlers($hook)
    {
        return $this->flatten(
            $this->hooks[$this->key($hook)]
        );
    }

    /**
     * @param $hook
     * @return bool
     */
    protected function hasHandlers($hook)
    {
        $hook = $this->key($hook);

        return isset($this->hooks[$hook]) && ! empty($this->hooks[$hook]);
    }

    /**
     * Get the hook key name
     *
     * @param $hook
     * @return mixed
     */
    protected function key($hook)
    {
        return $hook;
    }

    /**
     * Flatten a multi-dimensional array into a single level with support for Collection.
     *
     * @param  array  $array
     * @param  int  $depth
     * @return array
     */
    protected function flatten($array, $depth = INF)
    {
        if (function_exists('array_flatten')) {
            return array_flatten($array, $depth);
        }

        return array_reduce($array, function ($result, $item) use ($depth) {
            $item = $item instanceof Collection ? $item->all() : $item;

            if (! is_array($item)) {
                return array_merge($result, [$item]);
            } elseif ($depth === 1) {
                return array_merge($result, array_values($item));
            } else {
                return array_merge($result, static::flatten($item, $depth - 1));
            }
        }, []);
    }

}
