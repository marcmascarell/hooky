<?php namespace Mascame\Hooky;

interface HookContract
{

    /**
     * @param $data
     * @param $next
     */
    public function handle($data, $next);

}
