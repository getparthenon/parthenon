<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\Athena\Routing;

use Parthenon\Athena\Controller\AthenaController;
use Parthenon\Athena\Controller\ExportController;
use Parthenon\Athena\Controller\NotificationController;
use Parthenon\Athena\SectionManager;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class AthenaRouteLoader extends Loader
{
    private $isLoaded = false;

    private SectionManager $sectionManager;
    private ?string $host;

    public function __construct(SectionManager $sectionManager, ?string $host)
    {
        $this->sectionManager = $sectionManager;
        $this->host = $host;
    }

    public function load($resource, ?string $type = null): mixed
    {
        if (true === $this->isLoaded) {
            throw new \RuntimeException('Do not add the "athena" loader twice');
        }

        $routes = new RouteCollection();

        foreach ($this->sectionManager->getSections() as $section) {
            $urlTag = $section->getUrlTag();
            $serviceName = $this->getServiceName(get_class($section));

            $this->addListRoute($urlTag, $serviceName, $routes);
            $this->addExportRoute($urlTag, $serviceName, $routes);
            $this->addCreateRoute($urlTag, $serviceName, $routes);
            $this->addEditRoute($urlTag, $serviceName, $routes);
            $this->addReadRoute($urlTag, $serviceName, $routes);
            $this->addDeleteRoute($urlTag, $serviceName, $routes);
            $this->addUndeleteRoute($urlTag, $serviceName, $routes);
        }

        $this->addDefaultRoutes($routes);

        $this->isLoaded = true;

        return $routes;
    }

    public function supports($resource, ?string $type = null): bool
    {
        return 'athena' === $type;
    }

    protected function addDefaultRoutes(RouteCollection $routes): void
    {
        $route = $this->createRoute('/athena/index', [
            '_controller' => AthenaController::class.'::index',
        ]);
        $routes->add('parthenon_athena_index', $route);
        $route = $this->createRoute('/athena/notification/index', [
            '_controller' => NotificationController::class.'::viewAll',
        ]);
        $routes->add('parthenon_athena_notification_list', $route);
        $route = $this->createRoute('/athena/notification/{id}/read', [
            '_controller' => NotificationController::class.'::markAsRead',
        ]);
        $routes->add('parthenon_athena_notification_read', $route);

        $route = $this->createRoute('/athena/export/email', [
            '_controller' => ExportController::class.'::emailWaiting',
        ]);
        $routes->add('parthenon_athena_export_email', $route);

        $route = $this->createRoute('/athena/export/download/{id}', [
            '_controller' => ExportController::class.'::downloadWaiting',
        ]);
        $routes->add('parthenon_athena_export_download', $route);

        if (!is_null($this->host)) {
            $route = $this->createRoute('/', [
                '_controller' => AthenaController::class.'::login',
            ]);
            $routes->add('parthenon_athena_landing', $route);
            $route = $this->createRoute('/login', [
                '_controller' => AthenaController::class.'::login',
            ]);
            $routes->add('parthenon_athena_login', $route);
        }
    }

    protected function addListRoute(string $urlTag, string $serviceName, RouteCollection $routes): void
    {
        $path = '/athena/'.$urlTag.'/list';
        $defaults = [
            '_controller' => 'athena_controller_'.$serviceName.'::showList',
        ];
        $route = $this->createRoute($path, $defaults);
        // add the new route to the route collection
        $routeName = 'parthenon_athena_crud_'.$urlTag.'_list';
        $routes->add($routeName, $route);
    }

    protected function addExportRoute(string $urlTag, string $serviceName, RouteCollection $routes): void
    {
        $path = '/athena/'.$urlTag.'/export';
        $defaults = [
            '_controller' => 'athena_controller_'.$serviceName.'::export',
        ];
        $route = $this->createRoute($path, $defaults);
        // add the new route to the route collection
        $routeName = 'parthenon_athena_crud_'.$urlTag.'_export';
        $routes->add($routeName, $route);
    }

    protected function addReadRoute(string $urlTag, string $serviceName, RouteCollection $routes): void
    {
        $path = '/athena/'.$urlTag.'/{id}/read';
        $defaults = [
            '_controller' => 'athena_controller_'.$serviceName.'::showRead',
        ];
        $route = $this->createRoute($path, $defaults);
        // add the new route to the route collection
        $routeName = 'parthenon_athena_crud_'.$urlTag.'_read';
        $routes->add($routeName, $route);
    }

    protected function addDeleteRoute(string $urlTag, string $serviceName, RouteCollection $routes): void
    {
        $path = '/athena/'.$urlTag.'/{id}/delete';
        $defaults = [
            '_controller' => 'athena_controller_'.$serviceName.'::delete',
        ];
        $route = $this->createRoute($path, $defaults);
        // add the new route to the route collection
        $routeName = 'parthenon_athena_crud_'.$urlTag.'_delete';
        $routes->add($routeName, $route);
    }

    protected function addUndeleteRoute(string $urlTag, string $serviceName, RouteCollection $routes): void
    {
        $path = '/athena/'.$urlTag.'/{id}/undelete';
        $defaults = [
            '_controller' => 'athena_controller_'.$serviceName.'::undelete',
        ];
        $route = $this->createRoute($path, $defaults);
        // add the new route to the route collection
        $routeName = 'parthenon_athena_crud_'.$urlTag.'_undelete';
        $routes->add($routeName, $route);
    }

    protected function addEditRoute(string $urlTag, string $serviceName, RouteCollection $routes): void
    {
        $path = '/athena/'.$urlTag.'/{id}/edit';
        $defaults = [
            '_controller' => 'athena_controller_'.$serviceName.'::edit',
        ];
        $route = $this->createRoute($path, $defaults);
        // add the new route to the route collection
        $routeName = 'parthenon_athena_crud_'.$urlTag.'_edit';
        $routes->add($routeName, $route);
    }

    protected function addCreateRoute(string $urlTag, string $serviceName, RouteCollection $routes): void
    {
        $path = '/athena/'.$urlTag.'/create';
        $defaults = [
            '_controller' => 'athena_controller_'.$serviceName.'::create',
        ];
        $route = $this->createRoute($path, $defaults);
        // add the new route to the route collection
        $routeName = 'parthenon_athena_crud_'.$urlTag.'_create';
        $routes->add($routeName, $route);
    }

    protected function createRoute(string $path, array $defaults): Route
    {
        $route = new Route($path, $defaults, [], [], $this->host);

        return $route;
    }

    private function getServiceName(string $className): string
    {
        $parts = explode('\\', $className);
        $className = end($parts);

        return strtolower($className);
    }
}
