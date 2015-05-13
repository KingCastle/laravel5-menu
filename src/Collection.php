<?php namespace Complay\Menu;

use Illuminate\Support\Collection as BaseCollection;

class Collection extends BaseCollection
{
    /**
     * Add attributes to a collection of items.
     *
     * @param  mixed
     *
     * @return Complay\Menu\Collection
     */
    public function attr()
    {
        $args = func_get_args();
        $this->each(function ($item) use ($args) {
            if (count($args) >= 2) {
                $item->attr($args[0], $args[1]);
            } else {
                $item->attr($args[0]);
            }
        });

        return $this;
    }

    /**
     * Add meta data to a collection of items.
     *
     * @param  mixed
     *
     * @return Complay\Menu\Collection
     */
    public function data()
    {
        $args = func_get_args();

        $this->each(function ($item) use ($args) {
            if (count($args) >= 2) {
                $item->data($args[0], $args[1]);
            } else {
                $item->data($args[0]);
            }
        });

        return $this;
    }

    /**
     * Appends text or HTML to a collection of items.
     *
     * @param  string
     *
     * @return Complay\Menu\Collection
     */
    public function enclose($html)
    {
        $this->each(function ($item) use ($html) {
            if(is_array($html))
                $item->title = $html[0] . $item->title . $html[1];
        });
        return $this;
    }
    
    /**
     * Appends text or HTML to a collection of items.
     *
     * @param  string
     *
     * @return Complay\Menu\Collection
     */
    public function append($html)
    {
        $this->each(function ($item) use ($html) {
            $item->title .= $html;
        });
        return $this;
    }

    /**
     * Prepends text or HTML to a collection of items.
     *
     * @param  string
     *
     * @return Complay\Menu\Collection
     */
    public function prepend($html)
    {
        $this->each(function ($item) use ($html) {
            $item->title = $html.$item->title;
        });

        return $this;
    }
}
