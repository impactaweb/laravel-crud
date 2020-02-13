<?php

namespace Impactaweb\Crud\Form;

use Impactaweb\Crud\Form\Fields\IdField;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Exception;

/**
 * Class Form
 */
class Form
{

	/**
	 * @var array - Array com os fields disponíveis
	 */
	protected $fields = FieldAlias::fields;

	protected $panels = [];

	public $initial = [];

	protected $template;

	protected $idPanel = 0;

	protected $formId = 'form';

	protected $action = '';

	protected $method = 'POST';

	protected $class;

	protected $hidden = [];

	protected $html = [];

	protected $actions = [];

	protected $autoComplete = false;

	protected $targetBlank = false;

	private $primaryKey = '';

	private $primaryKeyValue = '';

	private $cancelVisible = true;

	private $cancelLinkUrl = '#';

	protected $request;

	/**
	 * Array com regras para serem utilizadas na
	 * criação dos fields
	 * @var array
	 */
	private $rules = [];

	/**
	 * Starts a new form with initial data
	 * @param array $initial Initial form data
	 */
	public function __construct(array $initial = [])
	{
		$this->initial = $initial;
		$this->request = request();

		# Build form method based on current URL
		try {
			$this->setMethod(FormUrls::actionMethod());
		} catch (Exception $e){
			$this->setMethod('');
		}

		# Build form action based on current URL
		try {
			$this->setAction(FormUrls::action());
		} catch (Exception $e){
			$this->setAction('');
		}

		# Build form cancel link based on current Url
		try {
			$this->setCancelLinkUrl(FormUrls::redir('cancel'));
		} catch (Exception $e){
			$this->setAction('');
		}

		# Build a hidden input from form ID
		if (isset($initial[$this->primaryKey])) {
			$this->primaryKeyValue = $initial[$this->primaryKey];
		}

		return $this;
	}

	/**
	 * Create a new form panel
	 * @param string $title Panel Title
	 * @param string $class Panel class
	 * @return Form
	 */
	public function panel(string $title = '', string $class = "")
	{
		if ($title == '') {
			$title = __('form::form.panel_default');
		}

		# Next Panel ID
		if (empty($id)) {
			$id = $this->idPanel++;
		}
		$this->panels[] = new Panel($title, $id, $class);
		return $this;
	}

	/**
	 * Add a new field
	 * @param string $type    Field Type
	 * @param string $name
	 * @param string $label
	 * @param array  $context Extra context
	 * @return $this
	 */
	public function field(string $type, string $name, string $label, array $context = [])
	{
		try {
			$fieldClass = $this->getField($type);
		} catch (Exception $e){
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
		$panel->fields[] = new $fieldClass($name, $label, $context, $type);

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
		} else {
			$panel = end($this->panels);
		}

		# Build default actions
		if (empty($this->actions)) {
			$this->actions = [
				["save_close", __('form::form.save_close')],
				["save", __('form::form.save')],
				["save_create", __('form::form.save_create')],
			];
		}

		# Build save_next option if 'ids' exist in querystring
		if ($this->request->has('ids') && strpos(urldecode($this->request->get('ids')), ',') !== false) {
			$this->actions = array_merge(
				[['save_next', __('form::form.save_next')]],
				$this->actions
			);
		}

		# Make a separation between primary and secondary actions
		$primaryAction = $this->actions[0];
		$secondaryActions = array_slice($this->actions, 1);

		$formTemplate = $this->template ?? config("form.templates.form");

		# Render form HTML
		return view($formTemplate, [
				"panels" => $this->panels,
				"primaryAction" => $primaryAction,
				"secondaryActions" => $secondaryActions,
				"method" => $this->method,
				"action" => $this->action,
				"formId" => $this->formId,
				"formClass" => $this->class,
				"primaryKeyValue" => $this->primaryKeyValue,
				"primaryKey" => $this->primaryKey,
				"targetBlank" => $this->targetBlank,
				"autoComplete" => $this->getAutoComplete(),
				"panelTemplate" => config('form.templates.panel'),
				"actionsTemplate" => config('form.templates.actions'),
				"isCancelVisible" => $this->isCancelVisible(),
				"cancelUrl" => $this->getCancelLinkUrl(),
				"form" => $this,
			]
		);
	}

	/**
	 * Add a new action button
	 * @param string $action - Action name
	 * @param string $label  - Action Label
	 * @return self
	 */
	public function action(string $action, string $label): Form
	{
		$this->actions[] = [$action, $label];
		return $this;
	}

	#########################
	#   GETTER E SETTERS    #
	#########################

	/**
	 * Set the value of template
	 * @param string $template Form template
	 * @return  self
	 */
	public function setTemplate($template): Form
	{
		$this->template = $template;
		return $this;
	}

	/**
	 * Set the value of action
	 * @param $action
	 * @return  self
	 */
	public function setAction(string $action)
	{
		$this->action = $action;
		return $this;
	}

	/**
	 * Get the value of autoComplete
	 */
	public function getAutoComplete()
	{
		return $this->autoComplete;
	}


	/**
	 * Set the value of autoComplete
	 * @return  self
	 */
	public function setAutoComplete($autoComplete)
	{
		$this->autoComplete = $autoComplete;
		return $this;
	}

	/**
	 * @param string $method
	 * @return $this
	 */
	public function setMethod(string $method)
	{
		$this->method = $method;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getMethod()
	{
		return $this->method;
	}


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
	public function setRules(Object $objRules): void
	{
		# O array com regras é uma estrutura de chave
		# valor, sendo que o valor pode ser do tipo array ou string.
		# Ex:
		# 'title' => 'required|unique:posts|max:255',
		# OU
		# 'title' => ['required', 'unique:posts', 'max:255'],
		# O padrão adotato será os valores separados com array
		$rules = $objRules->rules();
		foreach ($rules as $ind => $rule) {
			if (getType($rule) == 'string') {
				$rules[$ind] = explode('|', $rule);
			}
		}
		$this->rules = $rules;
	}

	/**
	 * @return bool
	 */
	public function isCancelVisible(): bool
	{
		return $this->cancelVisible;
	}

	/**
	 * @param bool $CancelVisible
	 */
	public function setCancelVisible(bool $CancelVisible): void
	{
		$this->cancelVisible = $CancelVisible;
	}

	/**
	 * @return string
	 */
	public function getCancelLinkUrl(): string
	{
		return $this->cancelLinkUrl;
	}

	/**
	 * @param string $cancelLinkUrl
	 */
	public function setCancelLinkUrl(string $cancelLinkUrl): void
	{
		$this->cancelLinkUrl = $cancelLinkUrl;
	}


	/**
	 * @return string
	 */
	public function getPrimaryKey(): string
	{
		return $this->primaryKey;
	}

	/**
	 * @param string $primaryKey
	 */
	public function setPrimaryKey(string $primaryKey): void
	{
		$this->primaryKey = $primaryKey;
	}

}
