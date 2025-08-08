<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Auth\Access\AuthorizationException;

trait AuthorizesRequests
{
    /**
     * Authorize a resource action based on the incoming request.
     *
     * @param  string $resource
     * @param  array $options
     * @return void
     */
    public function authorizeResourceWildcard(string $resource, array $options = []): void
    {
        $middleware = [];

        foreach ($this->resourceAbilityMap() as $method => $ability) {
            $middleware["can:{$resource}.{$ability}"][] = $method;
        }

        foreach ($middleware as $middlewareName => $methods) {
            $this->middleware($middlewareName, $options)->only($methods);
        }
    }

    /**
     * @param string $resource
     * @return void
     * @throws AuthorizationException
     */
    public function authorizeResourceCustom(string $resource): void
    {
        $isDoNotHavePermission = auth()
            ->user()
            ->getPermissionsViaRoles()
            ->where('name', $resource)
            ->isEmpty();

        if ($isDoNotHavePermission) throw new AuthorizationException;
    }

    /**
     * @param Event $event
     * @param string $resource
     * @param string $ability
     * @return void
     * @throws AuthorizationException
     */
    public function authorizeEventAndResource(Event $event, $resource, $ability): void
    {
        try {
            $this->authorizationEvents($event, $ability, true);
        } catch (\Throwable $eventException) {
            try {
                $this->authorizeResourceCustom("{$resource}.{$ability}");
            } catch (\Throwable $resourceException) {
                throw new AuthorizationException("Authorization failed for both Event and offlineActivities.{$ability}");
            }
        }
    }

    /**
     * Get the map of resource methods to ability names.
     *
     * @return array
     */
    protected function resourceAbilityMap(): array
    {
        return [
            'index' => 'viewAny',
            'show' => 'view',
            'create' => 'create',
            'store' => 'create',
            'edit' => 'update',
            'update' => 'update',
            'destroy' => 'delete',
        ];
    }
}
