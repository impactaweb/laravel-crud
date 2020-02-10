<?php

namespace Impactaweb\Crud\Form;

use Exception;

class FormUrls
{

    public $request;
    public function __construct()
    {
        $this->request = request();
    }

    /**
     * Returns form action based on current route
     * @return string
     * @throws Exception
     */
    public static function action()
    {
        $formUrls = new FormUrls();
        return $formUrls->getActionUrl();
    }

    /**
     * Returns form method based on current route
     * @return string
     */
    public static function actionMethod()
    {
        $formUrls = new FormUrls();
        return $formUrls->getActionMethod();
    }

    /**
     * Returns action URL based on current route - with querystring
     * @return string
     * @throws Exception
     */
    public function getActionUrl()
    {
        $actionBase = $this->getActionBase();
        $method = $this->request->route()->getActionMethod();

        $methodActions = [
            'edit' => 'update',
            'create' => 'store'
        ];

        if (!isset($methodActions[$method])) {
            throw new Exception("Method has no action implemented");
        }

        $route =  route($actionBase . '.' . $methodActions[$method], $this->request->route()->parameters());
        return $route . '?' . $this->getQueryString();
    }

    /**
     * Returns method based on current route
     * @return string
     */
    public function getActionMethod()
    {
        $method = $this->request->route()->getActionMethod();
        if ($method == 'edit') {
            return "PUT";
        }
        return "POST";
    }


    /**
     * Build redirect link based on submit button (action)
     * @param string $action
     * @param null   $id
     * @return string
     * @throws Exception
     */
    public static function redir($action = "auto", $id = null)
    {

        $formUrls = new FormUrls();

        if ($action == 'auto') {
            $action = $formUrls->getParameterFromRequest('action');
        }

        /**
         * Actions:
         * save/cancel: ir para o redir
         * save_keep: salva e volta pra edit do que foi salvo
         * save_next: salva e vai para o próximo "id"
         * save_create: salva e volta para o create
         *
         */
        switch ($action) {
            case 'save':
                if (!$id) {
                    throw new Exception("ID not defined");
                }
                $redir = $formUrls->getSaveKeepUrl($id);
                break;

            case 'save_create':
                $redir = $formUrls->getSaveCreateUrl();
                break;

            case 'save_next':
                if (!$id) {
                    throw new Exception("ID not defined");
                }
                $redir = $formUrls->getSaveNextUrl($id);
                break;

            default:
                $redir = $formUrls->getRedirFromRequest();
                break;
        }

        return $redir;
    }

    /**
     * Build save_keep URL
     * @param $id
     * @return string
     */
    public function getSaveKeepUrl($id)
    {
        # Base action -> ex: mycontroller.edit (full) / mycontroller (base)
        $actionBase = $this->getActionBase();
        # Full url based on base action with all parameters
        $fullUrl = route($actionBase . '.edit', array_merge($this->request->route()->parameters(), [$id]));
        # Clear all query strings
        $route = $this->clearUrl($fullUrl);
        # Add querystrings from current route
        $route .= "?" . $this->getQueryString();
        return $route;
    }

    /**
     * Build save_create URL
     * @return string
     */
    public function getSaveCreateUrl()
    {
        # Base action -> ex: mycontroller.edit (full) / mycontroller (base)
        $actionBase = $this->getActionBase();
        # Build route
        $fullUrl = route($actionBase . '.create', $this->request->route()->parameters());
        # Clear all querystrings
        $route = $this->clearUrl($fullUrl);
        # Add querystring form current route
        $route .= "?" . $this->getQueryString(['ids']);
        return $route;
    }

    /**
     * Build save_next URL
     * @param $id
     * @return string
     */
    public function getSaveNextUrl($id)
    {
        # Catch parameter ids from current request
        # Ex: ?ids=1,2,3,4,5&redir=/home

        $ids = $this->getParameterFromRequest('ids');
        if ($ids === false) {
            # Sends user to redir url if ids doesn't exists
            return $this->getRedirFromRequest();
        }

        # Explode ids and search current id
        $ids = explode(',', $ids);
        $idIndex = array_search($id, $ids);

        if ($idIndex === false || $idIndex >= count($ids) - 1) {
            # Sends user to redir url if current id is the last one
            return $this->getRedirFromRequest();
        }

        # Switch entry ID to the next ID
        $nextId = $ids[$idIndex + 1];
        $parameters = $this->request->route()->parameters();
        array_pop($parameters);
        $parameters[] = $nextId;

        # Build new URL based in the next ID
        $actionBase = $this->getActionBase();
        $fullUrl = route($actionBase . '.edit', $parameters);
        return $this->clearUrl($fullUrl) . '?' . $this->getQueryString();
    }

    /**
     * Clear verbs from Action
     * Ex: periodorecurso.edit -> periodorecurso
     * @return string
     */
    public function getActionBase()
    {
        $actionName = $this->request->route()->getName();
        return substr($actionName, 0, strrpos($actionName, "."));
    }

    /**
     * Search 'redir' inside request json or request querystring
     * @return string
     */
    public function getRedirFromRequest()
    {
        // Search 'redir' in request
        if (($redir = $this->getParameterFromRequest('redir')) !== false) {
            return $redir;
        }

        // If 'redir' doesn't exist return to index route
        $actionBase = $this->getActionBase();
        return $this->clearUrl(route($actionBase . '.index', $this->request->route()->parameters()));
    }


    /**
     * Search defined parameter in request
     * @param string $parameter
     * @return bool|string
     */
    private function getParameterFromRequest(string $parameter)
    {
        // Search 'parameter' in request
        if ($this->request->has($parameter)) {
            return urldecode($this->request->get($parameter));
        }

        // Search 'parameter' in json
        if ($this->request->json()->has($parameter)) {
            return urldecode($this->request->json()->get($parameter));
        }

        return false;
    }

    /**
     * Clear querystring form URL
     * @param $url
     * @return string
     */
    public function clearUrl($url)
    {

        if (strpos($url, "?") !== false) {
            return substr($url, 0, strpos($url, "?"));
        }
        return $url;
    }

    /**
     * Search for specific parameter inside a querystring
     * @param array $ignoreList
     * @return string
     */
    private function getQueryString($ignoreList = [])
    {
        parse_str($this->request->getQueryString(), $queryArray);
        foreach ($ignoreList as $ignoreItem) {
            if (array_key_exists($ignoreItem, $queryArray)) {
                unset($queryArray[$ignoreItem]);
            }
        }

        return http_build_query($queryArray);
    }

}
