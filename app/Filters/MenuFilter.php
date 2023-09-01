<?php

namespace App\Filters;

use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;

class MenuFilter implements FilterInterface
{
    public function transform($item)
    {
        if(isset($item['route']))
        {
            if(auth()->user()->can($item['route']))
            {
                $item['restricted'] = false; 
            }else{
                $item['restricted'] = true; 
            }
        }
        \Log::debug([$item]);
        return $item;
    }
}