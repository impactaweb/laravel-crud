<?php

namespace Impactaweb\Crud\Form;

/**
 * Class Panel
 * @package App\Lib\Formulario
 */
class Panel
{
    /**
     * @var string
     */
    protected $id = "";
    /**
     * @var array
     */
    public $attrs = array();
    /**
     * @var array
     */
    public $fields = array();
    /**
     * @var string
     */
    public $title = "";
    /**
     * @var string
     */
    private $class;

    /**
     * @param string $title Panel Title
     * @param string $id    Panel ID
     * @param string $class Panel Class
     */
    public function __construct(string $title, string $id, string $class = "")
    {
        $this->title = $title;
        $this->class = $class;
        $this->id = $id;
    }

    /**
     * Returns form panel ID
     */
    public function getPanelId(): ?string
    {
        return $this->id;
    }

}
