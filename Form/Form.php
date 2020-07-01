<?php

namespace Impactaweb\Crud\Form;

use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Impactaweb\Crud\Form\Traits\Fields;

/**
 * Class Form
 */
class Form
{

    use Fields;

    protected $fields = FieldAlias::fields;
    protected $rules = [];
    public $request;
    public $actions = [];
    public $panels = [];
    public $idPanel = 0;
    public $primaryKeyValue = '';
    public $baseAction;
    public $formId = 'form';
    public $initial = [];
    public $options = [];
    public $template;
    public $formAction = '';
    public $method = 'POST';
    public $class;
    public $autoComplete = false;
    public $targetBlank = false;
    public $primaryKey = '';
    public $cancelVisible = true;
    public $cancelLinkUrl = '#';


    /**
     * Starts a new form
     * Form constructor.
     * @param array $initial
     * @param string $primaryKey
     * @param array $options
     */
    public function __construct(array $initial = [], string $primaryKey = '', array $options = [])
    {
        $this->initial = $initial;
        $this->request = request();
        $this->primaryKey = $primaryKey;
        $this->options = $options;

        $this->buildBaseAction();
        $this->buildFormMethod();
        $this->buildFormAction();
        $this->buildDefaultActions();
        $this->buildCancelLinkUrl();
        $this->buildPrimaryKeyValue();

        return $this;
    }

    /**
     * Clear verbs from Action
     * Ex: foo.edit -> foo
     * @return string
     */
    public function buildBaseAction()
    {
        $actionName = $this->request->route()->getName();
        $this->baseAction = substr($actionName, 0, strrpos($actionName, "."));
    }

    public function buildFormMethod()
    {
        $method = $this->request->route()->getActionMethod();
        $this->method = $method == 'edit' ? 'PUT' : 'POST';
    }


    public function buildFormAction()
    {
        $method = $this->request->route()->getActionMethod();
        $methodActions = [
            'edit' => 'update',
            'create' => 'store'
        ];

        if (!isset($methodActions[$method])) {
            throw new Exception("Method has no action implemented");
        }

        $route = route($this->baseAction . '.' . $methodActions[$method], $this->request->route()->parameters());
        $this->formAction = $route . '?' . $this->getQueryString($this->request);
    }

    public function buildDefaultActions()
    {
        # Build default actions
        $this->actions = [
            "save_close"  => [__('form::form.save_close'), $this->baseAction . '.index'],
            "save" => [__('form::form.save'), $this->baseAction . '.edit'],
            "save_create" => [__('form::form.save_create'), $this->baseAction . '.create'],
        ];

        # Build save_next option if 'ids' exist in querystring
        if ($this->request->has('ids') && strpos(urldecode($this->request->get('ids')), ',') !== false) {
            $saveNext = ['save_next' => [__('form::form.save_next'), $this->baseAction . '.edit']];
            $this->actions = array_merge($saveNext, $this->actions);
        }
    }

    /**
     * @param array $actions
     * @return $this
     */
    public function clearActions($actions = [])
    {
        if (!empty($actions)) {
            foreach ((array) $actions as $actionName) {
                unset($this->actions[$actionName]);
            }
        } else {
            $this->actions = [];
        }
        return $this;
    }

    public function buildCancelLinkUrl()
    {
        # Build form cancel link based on current Url
        $this->cancelLinkUrl = urldecode($this->request->get('redir'));

        if ($this->cancelLinkUrl == false) {
            $parameters = $this->request->route()->parameters();
            $this->cancelLinkUrl = $this->clearUrl(route($this->baseAction . '.index', $parameters));
        }

    }

    public function buildPrimaryKeyValue()
    {
        # Build a hidden input from form ID
        $this->primaryKeyValue = $this->initial[$this->primaryKey] ?? '';
    }

    public function buildIdField()
    {
        # Display field ID automatically
        if (($this->options['showId'] ?? true) && !empty($this->primaryKeyValue)) {
            $this->show('ID', $this->primaryKeyValue);
        }
    }


    /**
     * Create a new form panel
     * @param string $title Panel Title
     * @param string $class Panel class
     * @return Form
     */
    public function panel(string $title = '', string $class = "")
    {
        $title = empty($title) ? __('form::form.panel_default') : $title;

        # Next Panel ID
        if (empty($id)) {
            $id = $this->idPanel++;
        }
        $this->panels[] = new Panel($title, $id, $class);

        # Se for o primeiro panel injeta o id
        $panel = end($this->panels);

        if(count($this->panels) === 1 && empty($panel->fields)) {
            $this->buildIdField();
        }

        return $this;
    }

    /**
     * Add a new field
     * @param string $type Field Type
     * @param string $name
     * @param string $label
     * @param array $options Extra options
     * @return $this
     */
    public function field(string $type, string $name, string $label, array $options = [])
    {
        try {
            $fieldClass = $this->getField($type);
        } catch (Exception $e) {
            return $this;
        }
        if (is_null($fieldClass)) {
            return $this;
        }

        # If the last element isn't a panel, a new panel is created
        if (empty($this->panels)) {
            $this->panel();
        }

        $panel = end($this->panels);
        $panel->fields[] = new $fieldClass($name, $label, $options, $type);

        return $this;
    }

    /**
     * Get field class based on FieldAlias
     * @param string $field Field alias
     * @return object
     * @throws Exception
     */
    protected function getField(string $field)
    {
        $fieldClass = isset($this->fields[$field]) ? $this->fields[$field] : null;
        if (!$fieldClass) {
            throw new Exception($field . " - Field doesn't exist.");
        }
        return $fieldClass;
    }

    /**
     * Render entire form
     * @return Factory|View
     */
    public function render()
    {
        # Build a new panel if variable panels is empty
        if (empty($this->panels)) {
            $panel = $this->panel();
            $this->panels[] = $panel;
        }

        $formTemplate = $this->template ?? config("form.templates.form");

        # Render form HTML
        return view($formTemplate, [
                "panelTemplate" => config('form.templates.panel'),
                "actionsTemplate" => config('form.templates.actions'),
                'firstAction' => array_key_first($this->actions),
                "form" => $this,
            ]
        );
    }

    /**
     * Add a new action button
     * @param string $action - Action name
     * @param string $label - Action Label
     * @param string $routeName
     * @return self
     */
    public function action(string $label, string $routeName = ''): Form
    {
        $this->actions = array_merge([Str::random(7) => [$label, $routeName]], $this->actions);
        return $this;
    }

    #########################
    #   GETTER E SETTERS    #
    #########################

    /**
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * Set rules array
     * @param Object $objRules
     */
    public function setRules(object $objRules): void
    {
        # Transform rules written as string into array
        # 'required|unique:posts|max:255',
        # TO
        # ['required', 'unique:posts', 'max:255'],

        $rules = $objRules->rules();
        foreach ($rules as $ind => $rule) {
            if (getType($rule) == 'string') {
                $rules[$ind] = explode('|', $rule);
            }
        }
        $this->rules = $rules;
    }

    private function clearUrl($url)
    {

        if (strpos($url, "?") !== false) {
            return substr($url, 0, strpos($url, "?"));
        }
        return $url;
    }

    /**
     * Search for specific parameter inside a querystring
     * @param $request
     * @param array $ignoreList
     * @return string
     */
    private function getQueryString($request, $ignoreList = [])
    {
        parse_str($request->getQueryString(), $queryArray);
        foreach ($ignoreList as $ignoreItem) {
            if (array_key_exists($ignoreItem, $queryArray)) {
                unset($queryArray[$ignoreItem]);
            }
        }
        return http_build_query($queryArray);
    }

}
