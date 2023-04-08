<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

class CreateRoutePermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permission Generate And Save to DB';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $routes = Route::getRoutes()->getRoutes();
        foreach($routes as $route)
        {
            if($route->getName() !='' && $route->getAction()['middleware'][0]=='web')
            {
                $permission = Permission::where('name', $route->getName())->first();
                if(is_null($permission))
                {
                    permission::create(['name' => $route->getName()]);
                }
            }
        }
        $this->info('Permission Route Added Successfully');
    }
}
