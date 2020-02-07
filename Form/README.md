## Lib - Formulários Dinâmicos

###### Iniciando um Formulário

Abra o console da aplicação e digite o comando conforme
exemplo:

`vagrant@homestead:/var/www/impacta/laravel$: php artisan make:form Cliente`

No exemplo o comando iniciou um formulário vazio em

`'/app/Forms/FormCliente.php'`

###### Escrevendo o formulário

No formulário criado pelo comando, for gerado um arquivo com
o seguinte conteúdo:

```php
class FormTeste extends FormBase
 {
 
     public function renderForm()
     {
 
         $this
 
             # Aba 1
             ->panel("My panel Title")
             ->field('select', 'id_input', "Nome",  [
                     'ajuda' => 'Help tooltip',
                     'class' => 'mb-5',
                     'options' => [
                         '1' => 'Opção 1',
                         '2' => 'Opção 2',
                     ],
                     'attrs' => [
                         'data-mask' => '00000-000'
                     ],
                 ]
             );
 
 
         return $this->render();
     }
}
```

**Funções e Classes:**

- Aba - Chama uma nova aba para o formulário, com o título da aba.
- Campo - Função que cria um novo campo para o formulário, de acordo com o 
arquivo 'AliasCampos.php', que contém todos os 'apelidos' dos campos.
- render - Renderiza o formulário

- FormBase - Insere funcionalidades extras na classe de formulário, para
uso dentro dos controladores.


###### Utilizando o formulário no controlador

Dentro do controlador instancie um novo formulário,
a requisição será importante para o formulário realizar as 
consultas para construção dos campos.

Também é necessário informar a 'action' do formulário, para o 'frontend'
realizar a submissão na URL correta.

```php
$form = new FormPeriodosRecursos($this->request);
$form->setAction($rota);
return $form->renderForm();
```

###### Carregando dados iniciais no formulário

Se o formulário for utilizado para edição é possível carregar dados
iniciais, conforme exemplo abaixo:

`$form = new FormPeriodosRecursos($this->request, $dadosIniciais);`

Esses 'dadosIniciais' deve ser um array 'chave-valor', estruturado da seguinte
forma:

```php
$dadosIniciais = [
    'id_concurso' => '12',
    'nome_candidato' => 'Joaquim', ...
]
```


###### Criando novos campos / Editando campos existentes

Para criar novos campos, vá até a pasta _**'app/Lib/Formulario/campos'**_
e crie um novo campo, seguindo o mesmo padrão dos demais.

```php
<?php
 namespace App\Lib\FormMaker\Fields;

 class MyField extends BaseField
 {
     protected $template = 'includes.formulario.myfield';
 }
```

O código será algo conforme exemplo acima.
Para adicionar novos campos, pasta acrescentar novos atributos:

```php
<?php 
 class MyField extends BaseField
 {
    protected $template = 'includes.formulario.myfield';
    protected $meucampo1 = array();
    protected $meucampo2 = ['teste' => 'Hello!'];
    protected $meucampo3 = 2000;

 }
```

O template deste campo deve estar em _**resources/views/formulario**_

Exemplo:

```html
<div class="form-group">
    {{ $meucampo3 }}
    <div>
        <h2>{{ $meucampo2 }}</h2>
    </div>
</div>
```

No arquivo _**'app/Lib/Formulario/AliasCampos.php'**_ deve existir um 'apelido' para o seu campo.

**Validações**

As validações do formulário são feitas dentro do controlador, no atributo 'rules'.

```php
     protected $rules = [
         'id_etapa' => 'required',
         'tipo' => 'required',
         'periodorecurso' => 'required'
     ];
```

Para validar e retornar os erros:

```php
 $validator = $this->validaRequest();
 if ($validator->fails()) {
    return response($validator->messages()->toJson(), 400);
 }
```
