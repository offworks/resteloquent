<?php

namespace Restery\Eloquent;

use Exedra\Routing\Factory;
use Exedra\Routing\Group;
use Exedra\Routing\Route;
use Illuminate\Database\Eloquent\Model;
use Restery\Eloquent\Contracts\Resourceful;

class RoutingHandler implements \Exedra\Contracts\Routing\RoutingHandler
{
    /** @var \ReflectionClass[] */
    protected $classes = [];

    /**
     * Validate given execute handler pattern
     * @param mixed $pattern
     * @return boolean
     */
    public function validateHandle($pattern)
    {
        if (is_string($pattern) && strpos($pattern, 'eloquent=') !== false)
            return true;

        return false;
    }

    /**
     * Resolve handle into Closure or callable
     * @param string $pattern
     * @return \Closure|callable
     */
    public function resolveHandle($pattern)
    {
        @list($eloquent, $action) = explode('=', $pattern);


    }

    /**
     * Validate group pattern
     *
     * @param mixed $pattern
     * @param Route|null $parentRoute
     * @return boolean
     */
    public function validateGroup($pattern, Route $parentRoute = null)
    {
        if (is_array($pattern)) {
            return true;
        }

        if (is_string($pattern) && class_exists($pattern) && (($refClass = new \ReflectionClass($pattern)))->isSubclassOf(Model::class)) {
            $this->classes[$pattern] = $refClass;
            return true;
        }

        return false;
    }

    protected function resolveArray(Factory $factory, array $classes, Route $parentRoute = null)
    {
        $group = $factory->createGroup([], $parentRoute);

        foreach ($classes as $class) {
            if (!is_string($class))
                throw new \Exception('Class name must be string');

            $group->addRoute($factory->createRoute($group, $this->resolveRouteName($class), [])->group($class));
        }

        return $group;
    }

    protected function getRef($class)
    {
        if (!isset($this->classes[$class]))
            $this->classes[$class] = new \ReflectionClass($class);

        return $this->classes[$class];
    }

    protected function resolveRouteName($class)
    {
        return $this->getRef($class)->newInstanceArgs()->getTable();
    }

    /**
     * Resolve group pattern
     *
     * @param Factory $factory
     * @param mixed $pattern
     * @param Route|null $parentRoute
     * @return Group
     */
    public function resolveGroup(Factory $factory, $pattern, Route $parentRoute = null)
    {
        if (is_array($pattern))
            return $this->resolveArray($factory, $pattern, $parentRoute);

        $group = $factory->createGroup([], $parentRoute);

        $name = $this->resolveRouteName($pattern);

        $primaryKey = 'id';

        $resourceKey = $name . '.' . $primaryKey;

        $path = $name;

        $group['index']->get('/' . $path)->execute('eloquent=index');
        $group['store']->post('/' . $path)->execute('eloquent=store');
        $group['show']->get('/' . $path . '/:' . $resourceKey)->execute('eloquent=show');
        $group['update']->put('/' . $path . '/:' . $resourceKey)->execute('eloquent=update');
        $group['destroy']->delete('/' . $path . '/:' . $resourceKey)->execute('eloquent=destroy');

        $route = $factory->createRoute($group, $name, []);

        $group->addRoute($route);

        return $group;
    }
}