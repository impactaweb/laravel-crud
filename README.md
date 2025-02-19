# Laravel CRUD

Laravel CRUD Tools

- :link: - [01 - Instalação](https://github.com/impactaweb/laravel-crud/wiki/01.-Instala%C3%A7%C3%A3o)
- :link: - [02 - Criando um CRUD completo](https://github.com/impactaweb/laravel-crud/wiki/02.-Como-criar-um-crud-completo)
- :link: - [03 - Criando CRUD a partir de uma model](https://github.com/impactaweb/laravel-crud/wiki/03.-Criando-um-CRUD-apartir-de-uma-Model)
- :link: - [04 - Visão geral - Campos e Panels](https://github.com/impactaweb/laravel-crud/wiki/04.-Vis%C3%A3o-Geral---Campos-e-Panels)
- :link: - [05 - Utilizando Multiselect](https://github.com/impactaweb/laravel-crud/wiki/05.---Utilizando-multiselect)
- :link: - [06 - Traits do CRUD](https://github.com/impactaweb/laravel-crud/wiki/06.-Traits-do-Crud)
- :link: - [07 - Dependências Javascript](https://github.com/impactaweb/laravel-crud/wiki/07.-Instalando-Depend%C3%AAncias-do-Javascript)
- :link: - [08 - Callbacks e Notificações](https://github.com/impactaweb/laravel-crud/wiki/08.-Callbacks-do-Crud-e-Alertas---Notifica%C3%A7%C3%B5es)
- :link: - [09 - Upload de Arquivos](https://github.com/impactaweb/laravel-crud/wiki/09.-Upload-de-arquivos-no-CRUD)
- :link: - [10 - Listagem](https://github.com/impactaweb/laravel-crud/wiki/10.-Listagem)

## Dependências JS:

1. momentjs - momentjs.com
2. jQuery - jquery.com

## Novidades

### 19/02/2025 - Set searchbar
Agora é possível exibir ou não a searchbar do listing, por padrão e vem setado a exibição como true
para não exibir basta setar o listing assim:
```php
$lista = new Listing();

$lista->setSearchBar(false);

);
```
