<?php

namespace Impactaweb\Crud\Listing;

use Illuminate\Support\Facades\Route;

class Action {
    
    public $name;
    public $label;
    public $method;
    public $url;
    public $icon;
    public $resourceCustomVerbs;
    public $message;

    public function __construct(string $name, string $label, string $method = 'GET', ?string $url = null, ?string $icon = null, ?string $message = null)
    {
        $this->name = $name;
        $this->label = $label;
        $this->method = in_array(strtoupper($method), ['GET', 'POST', 'PUT', 'PATCH', 'DELETE']) ? strtoupper($method) : 'GET';
        $this->icon = $icon;
        $this->message = $message;
        
        $this->setUrl($url);

        // load Resource Verbs
        $this->resourceCustomVerbs = Route::resourceVerbs();
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
        return $this->url;
    }

    public function setUrl(?string $url): void
    {
        // Usar o fillUrlParameters somente para URL's definidas pelo dev
        if (!empty($url)) {
            $this->url = Listing::fillUrlParameters($url);
            return;
        }

        $request = request();
        $actionName = $this->getName();
        $root = $request->root();
        $fullUrl = $request->fullUrl();
        $redir = urlencode(substr($fullUrl, strlen($root), strlen($fullUrl)));
        $url = '/' . request()->path();

        // Custom verbs
        // $editVerb = $this->resourceCustomVerbs['edit'] ?? 'edit';
        $createVerb = $this->resourceCustomVerbs['create'] ?? 'create';

        switch ($actionName) {
            case $createVerb:
                $url .= '/' . $createVerb . '?';
                break;

            case 'destroy':
                $url .= '/{id}?multiple={ids}';
                break;

            default:
                $url .= '/{id}/' . $actionName . '?ids={ids}';
                break;
        }

        $url .= '&redir=' . $redir;
        $this->url = $url;
    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function getConfirmationText(): ?string
    {
        $message = $this->message;

        if (!empty($message)) {
            return $message;
        }

        $destroyVerb = $this->resourceCustomVerbs['destroy'] ?? 'destroy';
        if ($this->getName() == $destroyVerb) {
            return 'Tem certeza que deseja excluir os itens selecionados?';
        }

        return null;
    }

    public function getVerb(): string
    {
        $actionName = $this->getName();
        $customVerbsFlip = array_flip($this->resourceCustomVerbs);
        return $customVerbsFlip[$actionName] ?? $actionName;
    }

}