<?php namespace Complay\Menu;

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
     * @return \Complay\Menu\Menu
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
     * Create a new menu instance from json object.
     * @param  string  $name
     * @param  string  $json
     * 
     * @return \Complay\Menu\Menu
     */
    protected function makeFromJson($name, $json) {
        $menu = $this->make($name, function($m) use(&$json) {

                    $menuTreeTraverse = function(&$json, &$parentItem = null) use (&$menuTreeTraverse, &$m) {

                        foreach ($json as $menuTreeItem) {
                            $htmlAttr = $menuTreeItem['data'];
                            $uri = isset($htmlAttr['uri'])? $htmlAttr['uri']: '';
                            //unset($htmlAttr['uri']);
                            $dataAttr = $this->prefixKeys($htmlAttr, 'data');
                            $attr = $htmlAttr + $dataAttr;
                            //

                            if ($parentItem) {
                                $m->item(strtolower($parentItem['text']))->add($menuTreeItem['text'], $uri)->attr($attr);
                            } else {
                                $m->add($menuTreeItem['text'], $uri)->attr($attr);
                            }

                            if (!empty($menuTreeItem['children'])) {
                                $menuTreeTraverse($menuTreeItem['children'], $menuTreeItem);
                            }
                        }
                    };
                    $menuTreeTraverse($json);
                });

        return $menu;
    }
    
    /**
     * Add prefix to keys of given array.
     * @param  array  $array
     * @param  string  $prefix
     * 
     * @return array
     */    
    protected function prefixKeys($array, $prefix) {
        $return = [];
        foreach ($array as $k => $v) {
            $return[$prefix . '-' . $k] = $v;
        }

        return $return;
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
     * @return \Complay\Menu\Item
     */
    public function get($key)
    {
        $return = $this->collection->get($key);
        
        if (!is_null($return)) {
            return $return;
        }

        if (ctype_digit($key)) {
            $navigation = Navigation::find($key);
        } else {
            $navigation = Navigation::where('name', 'LIKE', $key)->first();
        }
        
        if (!is_null($navigation)) {
            return $this->makeFromJson($navigation->name, $navigation->object);
        }

        return null;
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
