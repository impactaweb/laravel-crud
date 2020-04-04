<?php

namespace Impactaweb\Crud\Listing;

class Action {
    
    public $name;
    public $label;
    public $method;
    public $url;
    public $icon;

    public function __construct(string $name, string $label, string $method = 'GET', ?string $url = null, ?string $icon = null)
    {
        $this->name = $name;
        $this->label = $label;
        $this->method = strtoupper($method);
        $this->url = $url;
        $this->icon = $icon;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUrl(): string
    {
        $request = request();

        if (!empty($this->url)) {
            return $this->url;
        }
        
        $root = $request->root();
        $fullUrl = $request->fullUrl();
        $redir = urlencode(substr($fullUrl, strlen($root), strlen($fullUrl)));

        $url = '/' . request()->path();
        if ($this->method) {
            $url .= '/{id}/' . $this->name . '?ids={ids}';
        } else {
            $url .= '/' . $this->name . '?';
        }

        $url .= '&redir=' . $redir;
        return $url;
    }

    public function getIcon()
    {
        return $this->icon;
    }

}