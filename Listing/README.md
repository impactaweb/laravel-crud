# Listagem (Versão Português)

## Exemplo de Uso

```php
$lista = new Listing("id_chave_primaria", (new MinhaModel()), $campoOpcional);
/* Campo opcional:
    [
        'showID' => false, // Desativa a coluna padrão "ID"
    ]
*/

// Campo básico (campo, legenda)
$lista->field("id_teste", "Teste");

// Callback (campo, legenda, opções [callback])
$lista->field("id_teste", "Teste", ['callback' => function($dados) {
    // código aqui
    return '<b>' . $dados->id_teste . '</b>';
}]);
 
// Aplicar máscaras (campo, legenda, opcões [máscara])
// (Máscaras atualmente disponíveis: dm, dmY, dmYHi, dmYHis)
$lista->field("id_teste", "Teste", ['mask' => 'dmY']);

// Campo customizado com callback (legenda, callback)
$lista->customField("Teste", function($dados) {
    // código aqui
    return $dados->nome_do_campo;
});

// Link (legenda, url, css (opcional))
// IMPORTANTE: As url's aceitam parâmetros (variáveis que poderão vir da linha a ser  {algumacoisa} exibida ou da rota atual {url.algumacoisa})
$lista->linkField("Teste", '/url/{variavel}/(url.algumacoisa}');

// Botão (legenda, url, css (opcional))
$lista->buttonField("Teste", '/url/{variavel}/(url.algumacoisa}');

// Imagem (legenda, url da imagem, largura máxima, altura máxima)
$lista->imageField("Teste", '/url/{variavel}/(url.algumacoisa}.png', 100, 100);

// Template do Blade
$lista->bladeField("Teste", 'caminho.para.blade', []);

// Campo básico, com busca avançada a partir de uma lista
$lista->selectField("id_teste" , "Teste", [1 => 'Teste 1', 2 => 'Teste 2']);

// Limpar todas as actions (lista de ações (opcional))
$lista->clearActions(); // Aceita parâmetro array('nome_acao', ...)

// Ação customizada (nome, legenda, método, url, ícone FontAwesome, texto confirmação)
$lista->action('limpar', 'Limpar', 'GET' , '/path/limpar/{id_teste}', '', 'Certeza?');

// Saída
$html = $lista->render();
```
