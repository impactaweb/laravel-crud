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
    public $attrs = [];
    /**
     * @var array
     */
    public $fields = [];
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
    public function __construct(string $title, string $id, string $class = "", $attrs = [])
    {
        $this->title = $title;
        $this->class = $class;
        $this->id = $id;
        $this->attrs = $attrs;
    }

    /**
     * Returns form panel ID
     */
    public function getPanelId(): ?string
    {
        return $this->id;
    }

}
