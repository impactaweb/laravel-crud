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
$lista->text("id_teste", "Teste");

// Callback (campo, legenda, opções [callback])
$lista->field("id_teste", "Teste", ['callback' => function($dados) {
    // código aqui
    return '<b>' . $dados->id_teste . '</b>';
}]);
 
// Aplicar máscaras (campo, legenda, opcões [máscara])
// (Máscaras atualmente disponíveis: dm, dmY, dmYHi, dmYHis)
$lista->text("id_teste", "Teste", ['mask' => 'dmY']);

// Campo customizado com callback (legenda, callback)
$lista->custom("Teste", function($dados) {
    // código aqui
    return $dados->nome_do_campo;
});

// Link (legenda, url, css (opcional))
// IMPORTANTE: As url's aceitam parâmetros (variáveis que poderão vir da linha a ser  {algumacoisa} exibida ou da rota atual {url.algumacoisa})
$lista->link("Teste", '/url/{variavel}/(url.algumacoisa}');

// Link com prefixo da url do storage
$lista->storageLink('arquivo', 'Arquivo', 'path/{url.teste}/{arquivo}');

// Botão (legenda, url, css (opcional))
$lista->button("Teste", '/url/{variavel}/(url.algumacoisa}');

// Imagem (legenda, url da imagem, largura máxima, altura máxima)
$lista->image("Teste", '/url/{variavel}/(url.algumacoisa}.png', 100, 100);

// Template do Blade
$lista->blade("Teste", 'caminho.para.blade', []);

// Campo básico, com busca avançada a partir de uma lista
$lista->select("id_teste" , "Teste", [1 => 'Teste 1', 2 => 'Teste 2']);

// Flags com ação para ativar/desativar
$lista->flag('flag_ativo', 'Ativo')

// Limpar todas as actions (lista de ações (opcional))
$lista->clearActions(); // Aceita parâmetro array('nome_acao', ...)

// Ação customizada (legenda, url, ícone class, método (GET, POST), texto confirmação)
$lista->action('Configurar', '/path/{url.teste}/configurar');
$lista->action('Limpar', '/path/limpar/{id_teste}', '', 'fas fa-broom', 'Certeza?');

// Saída
$html = $lista->render();
```
