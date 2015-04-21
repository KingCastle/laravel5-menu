<?php namespace Kiwina\Menu;

use Illuminate\Html\HtmlBuilder;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Config\Repository;
use Illuminate\View\Factory;
class Menu
{
    /**
     * Menu collection.
     *
     * @var Illuminate\Support\Collection
     */
    protected $collection;

    /**
     * Configuration data.
     *
     * @var array
     */
    protected $config;
	/**
	 * @var \Illuminate\View\Factory
	 */
	protected $view;
    /**
     * Initializing the menu builder.
     */
    public function __construct(array $config,Factory $view,HtmlBuilder $html, UrlGenerator $url)
    {
        $this->config = $config;
        $this->view = $view;
        $this->html       = $html;
		$this->url        = $url;
        // creating a collection for storing menus
        $this->collection = new Collection;
        
    }

    /**
     * Create a new menu instance.
     *
     * @param string   $name
     * @param callable $callback
     *
     * @return \Kiwina\Menu\Menu
     */
    public function make($name, $callback)
    {
        if (is_callable($callback)) {
            $menu = new Builder($name, $this->loadConf($name), $this->html, $this->url);

            // Registering the items
            call_user_func($callback, $menu);

            // Storing each menu instance in the collection
            $this->collection->put($name, $menu);

            // Make the instance available in all views
            $this->view->share($name, $menu);

            return $menu;
        }
    }

    /**
     * Loads and merges configuration data.
     *
     * @param string $name
     *
     * @return array
     */
    public function loadConf($name)
    {
        $name = strtolower($name);

        if (isset($this->config[$name]) && is_array($this->config[$name])) {
            return array_merge($this->config['default'], $this->config[$name]);
        }

        return $this->config['default'];
    }

    /**
     * Return Menu instance from the collection by key.
     *
     * @param string $key
     *
     * @return \Kiwina\Menu\Item
     */
    public function get($key)
    {
        return $this->collection->get($key);
    }

    /**
     * Return Menu collection.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Alias for getCollection.
     *
     * @return \Illuminate\Support\Collection
     */
    public function all()
    {
        return $this->collection;
    }
}
