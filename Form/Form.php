<?php

namespace Impactaweb\Crud\Form;

use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Impactaweb\Crud\Form\Traits\Fields;

/**
 * Class Form
 */
class Form
{

    use Fields;

    protected $fields = FieldAlias::fields;
    protected $panels = [];
    protected $idPanel = 0;
    protected $formId = 'form';
    protected $actions = [];
    protected $primaryKeyValue = '';
    protected $request;
    protected $rules = [];
    protected $baseAction;
    public $initial = [];
    public $template;
    public $formAction = '';
    public $method = 'POST';
    public $class;
    public $autoComplete = false;
    public $targetBlank = false;
    public $primaryKey = '';
    public $cancelVisible = true;
    public $cancelLinkUrl = '#';


    +/**
     * Starts a new form
     * Form constructor.
     * @param array $initial
     * @param string $primaryKey
     * @param array $options
     * @throws Exception
     */
    public function __construct(array $initial = [], string $primaryKey = '', array $options = [])
    {
        $this->initial = $initial;
        $this->request = request();
        $this->primaryKey = $primaryKey;

        # Build form method based on current URL
        $this->buildBaseAction();
        $this->buildFormMethod();
        $this->buildFormAction();
        $this->buildDefaultActions();
        $this->buildCancelLinkUrl();
        $this->buildPrimaryKeyValue();
        $this->buildIdField();
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
        $this->formAction = $route . '?' . getQueryString($this->request);
    }

    public function buildDefaultActions()
    {
        # Build default actions
        if (empty($this->actions)) {
            $this->actions = [
                "save_close" => [__('form::form.save_close')],
                "save" => [__('form::form.save')],
                "save_create" => [__('form::form.save_create')],
            ];
        }

        # Build save_next option if 'ids' exist in querystring
        if ($this->request->has('ids') && strpos(urldecode($this->request->get('ids')), ',') !== false) {
            $this->actions = array_merge(['save_next' => [__('form::form.save_next')], $this->actions);
        }
    }

    public function clearActions(array $actions) {
        foreach ($actions as $action) {
            unset($action, $actions);
        }
    }

    public function buildCancelLinkUrl()
    {
        # Build form cancel link based on current Url
        $this->cancelLinkUrl = getParameterFromRequest($this->request, 'redir');
        if ($this->cancelLinkUrl !== false) {
            $parameters = $this->request->route()->parameters();
            $this->cancelLinkUrl = clearUrl(route($this->baseAction . '.index', $parameters));
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
        if (($options['showId'] ?? true) && !empty($this->primaryKeyValue)) {
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
    public function action(string $action, string $label, string $routeName = ''): Form
    {
        $this->actions = array_merge([$action => [$label, $routeName]], $this->actions);
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

}
