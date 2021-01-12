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

### Ferramentas para testas as rotas da API
- [Postman](https://www.postman.com/)
- [Insomnia Rest](https://insomnia.rest/)
