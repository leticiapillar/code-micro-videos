# Curso: Desenvolvimento de Aplicações Modernas e Escaláveis com Microsserviços

by [Code.education](https://code.education/)


## Módulo: Microsserviço: Catálogo de vídeos com Laravel ( Back-end )

- Microsserviço de catálogo

### Rodar a aplicação
- Crie os containers com Docker

```bash
# Iniciar a aplicação, mantém o console travado, exibindo o log de inicialização dos serviços
$ docker-compose up
# Iniciar a aplicação, não trava o console
$ docker-compose up -d
# Exibe o log do serviço no console
$ docker logs micro-videos-app -f

# Parar a aplicação
$ docker-compose down
```

- Accesse no browser
```
http://localhost:8000
```

### PHP Comandos
- Criando as Models

```bash
# Executa o help do comando artisan make:model
$ php artisan make:model -- help

# Criar a Model Category dentro de Models, atenção para a posição da barra.
# Este comando cria a Model, Factory, Migration, Seeder e Controller
$ php artisan make:model Models/Category --all
```

- Criando uma seeder
```bash
# Criar a Seed Categories
$ php artisan make:seeder CategoriesTableSeeder

# A migração dos dados tem que ser executada dentro do container 
# Acessa o bash do container
$ docker exec -it micro-videos-app bash

# Faz a migração da Seed
$ php artisan migrate --seed

# O Tinker é um console interativo do Laravel, um shell do PHP com acesso às classes do nosso projeto.
$ php artisan tinker
>>> \App\Models\Category::all();

# Limpa o banco, executa um roll back e executa as migrações novamente
$ php artisan migrate:refresh --seed

# Apaga todo o banco, e executa as migrações
$ php artisan migrate:fresh --seed
```

- Listando as rotas das Controllers
Este comando ajuda a verificar se as rotas estão corretas após alterar as controllers.
```bash
# Lista a estrutura de rotas dos recursos
$ php artisan route:list
```

#### Tinker (REPL)
- [Laravel tinker](https://laravel.com/docs/7.x/artisan#tinker)
- Ferramente para executar comandos no shell, esta ferramenta deve ser ativada no container do serviço
```bash
$ docker exec -it micro-videos-app bash
/var/www$ php artisan tinker
```
- Exclusão lógica, comandos com o tinker.
```bash
# Seta a model que deseja usar
$ use \App\Models\Category;

# Executou a exclusão lógica (campo deleted_at com a data da exclusão)
>>> Category::find(1)->delete();
=> true

# Retorna apenas os registros ativos
>>> Category::find(1);
=> null

# Retorna o registro excluido
>>> Category::withTrashed()->find(1);

# Retorna todos os registros ativos e inativos (exclusão lógica)
>>> Category::withTrashed()->get();

# Retorna apenas os registros excluidos
>>> Category::onlyTrashed()->get();

# Reverte o registro excluido, seta null no campo deleted_at
>>> Category::onlyTrashed()->find(1)->restore();
=> true

# Força a exclusão fisica do registro
>>> Category::find(1)->forceDelete();
=> true
```


### Validação dos parametros recebidos na request
Podemos validar os parametros recebidos na request de duas formas:
1. Validação na controller: Validação dos campos recebidos pela request
```PHP
$this->validate($request, [
    'name' => 'required|max:255',
    'is_active' => 'boolean'
]);
```

2. Criando uma classe Request para ser utilizada na Controller
```bash
# Cria uma classe Request para a Category
$ php artisan make:request CategoryRequest
```
- Nesta classe implementamos as rules, validações dos campos recebidos na request
```PHP
public function rules()
{
    return [
        'name' => 'required|max:255',
        'is_active' => 'boolean'
    ];
}
```


### Ferramentas para testas as rotas da API
- [Postman](https://www.postman.com/)
- [Insomnia Rest](https://insomnia.rest/)
