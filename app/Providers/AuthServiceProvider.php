<?php

namespace App\Providers;

use App\Models\Ambulancias;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Policies\UserPolicy;
use App\Policies\AmbulanciasPolicy;
use App\Policies\RolePolicy;
use App\Policies\PermissionPolicy;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Althinect\FilamentSpatieRolesPermissions\Concerns\HasSuperAdmin;
use Althinect\FilamentSpatieRolesPermissions\Concerns\HasRoles;
use App\Policies\ActivityPolicy;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role as ModelsRole;
use Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */

    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        // User::class => UserPolicy::class,
    ];
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        Gate::policy(AuthenticationLog::class, ActivityPolicy::class);
        Gate::policy(Activity::class, ActivityPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Permission::class, PermissionPolicy::class);
        //
    }

}
