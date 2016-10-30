<?php namespace Mascame\Hooky;

/**
 * Leverages Laravel's Event system
 *
 * Class LaravelHook
 * @package Mascame\Artificer\Hooks
 */
class LaravelHook extends Hook
{

    /**
     * @var string
     */
    protected $prepend = 'hook.';

    /**
     * @param string $hook
     * @param string|array $handlers
     */
    public function to($hook, $handlers) {
        \Event::listen($this->key($hook), function() use ($handlers) {
            return $handlers;
        });
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
            \Event::fire($this->key($hook))
        );
    }


    /**
     * @param $hook
     * @return bool
     */
    protected function hasHandlers($hook)
    {
        return ! empty($this->getHandlers($hook));
    }

    /**
     * @param $hook
     * @return mixed
     */
    protected function key($hook)
    {
        return $this->prepend . $hook;
    }

}
