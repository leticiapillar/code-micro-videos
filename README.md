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

# Recria (rebuild) a imagem do docker
docker-compose up -d  --build
# Força reecriaa (rebuild) a imagem do docker
docker-compose up -d --force-recreate --build
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

- Criando uma migration de relacionamento
  * Relacionando de Videos com Categorias e Gêneros
  * Usar o seguinte padrão: ordem alfabética para o nome das tabelas, usar o nome no singular

```bash
# Cria a migração para o relacionamento entre Categoria e Video
$ php artisan make:migration create_category_video_table

# Cria a migração para o relacionamento entre Genero e Video
$ php artisan make:migration create_genre_video_table 

# Cria a migração para o relacionamento entre Category e Genero
$ php artisan make:migration create_category_genre_table
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
```php
public function rules()
{
    return [
        'name' => 'required|max:255',
        'is_active' => 'boolean'
    ];
}
```

### Validação customizada com o Laravel
- [Custom Validation Rules](https://laravel.com/docs/7.x/validation#custom-validation-rules)
```bash
# Criar uma classe de validação em /app/Rules
$ php artisan make:rule GenresHasCategoriesRule
Rule created successfully.

```

### Ferramentas para testas as rotas da API
- [Postman](https://www.postman.com/)
- [Insomnia Rest](https://insomnia.rest/)


### Testes automatizados no Laravel
- Comandos para executar os testes
```bash
$ php artisan make:test --help

# Por padrão cria a classe de teses em tests\Feature
$ php artisan make:test NameClassTest

# Criar uma classe de teste de integração em Feature/Models/CategoryTest
$ php artisan make:test Models/CategoryTest

# Cria a classe de unidade em tests\Unit
$ php artisan make:test NameClassTest --unit

# A classe tem que ter o sufixo Test no final do nome, NameClassTest
# O metodo para teste tem que inificar com o prefixo testNameMethod
$ php artisan make:test CategoryTest --unit

# Cria uma classe de unidade em Unit/Models/CategoryTest
$ php artisan make:test Models/CategoryTest --unit

# Executar todos os testes
$ vendor/bin/phpunit

# Executar todos os testes da classe Category
$ vendor/bin/phpunit --filter CategoryTest

# Executar os testes do metodo testExample da classe Category
# Se existir classes e metdos com nome iguais irá executar todos
$ vendor/bin/phpunit --filter CategoryTest::testExample

# Executar os teste pelo caminho relativo da classe Category
$ vendor/bin/phpunit tests/Unit/CategoryTest.php
$ vendor/bin/phpunit "Tests\\Unit\\CategoryTest"

# Executar o teste de um méetodo pelo caminho relativo da classe Category
$ vendor/bin/phpunit --filter testIfUseTraits tests/Unit/CategoryTest.php
```

### Execução dos testes do PHPUnit com VSCode
1. Habiltar a extensão no contexto do container `Better PHPUnit, calebporzio.better-phpunit, calebporzio`
2. Executar a aplicação no modo "Remote-Containers: Reopen in Container" para executar o docker subindo subindo a aplicação
3. Na extensão ir para: `Settings > Workspace > Better-phpunit: Phpunit binary` e informar o caminho do phpunit `"better-phpunit.phpunitBinary": "/laravel-quickstart/vendor/bin/phpunit"`, lembrando que `laravel-quickstart/` é o caminho configurado como workspaceFolder no devcontainer 
4. Executar os testes unitários: `shift + windows + p` > `Better PHPUnit`

- Os testes também podem ser executados pelo terminal do VSCode
```bash
$ /laravel-quickstart$ /laravel-quickstart/vendor/bin/phpunit
```

- Os testes também podem ser executados pelo terminal do container Docker
```bash
$ docker exec -it micro-videos-app bash
$ vendor/bin/phpunit
$ vendor/bin/phpunit --filter CategoryControllerTest
$ vendor/bin/phpunit --filter CategoryControllerTest::testInvalidationData
```

### Execução do bash no container databse
```bash
$ docker exec -it micro-videos-db bash
$ mysql -uroot -proot
$ use code_micro_videos_test
$ show tables;
```

### Processamento de Imagem (GD)
- [Link](https://www.php.net/manual/pt_BR/book.image.php)
- Alteração no arquivo Dockerfile para instalar os pacotes: `freetype-dev, libjpeg-turbo-dev, libpng-dev`
- Executar os camandos de configuração do GD
```
RUN docker-php-ext-configure gd --with-gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/include/
RUN docker-php-ext-install -j$(nproc) 
````

### Google Cloud Storage
- Lib para o PHP: laravel: [Superbalist / laravel-google-cloud-storage](https://github.com/Superbalist/laravel-google-cloud-storage)
- Adicionar manual no arquivo `app.php`
```
'providers' => [
    // ...
    Superbalist\LaravelGoogleCloudStorage\GoogleCloudStorageServiceProvider::class,
]
```
- Se ocorrer o erro `InvalidArgumentException : Driver [gcs] is not supported.`, limpar o cache em `bootstrap/cache`
- Erro de ACL: `Cannot insert legacy ACL for an object when uniform bucket-level access is enabled. Read more at`, 
  [Bug ACL GCS](https://forum.code.education/forum/topico/bug-acl-gcs-221/),
  [Error: Bucket Policy Only #80](https://github.com/Superbalist/laravel-google-cloud-storage/issues/80)
```
Google\Cloud\Core\Exception\BadRequestException: { 
    "error": { 
        "code": 400, 
        "message": "Cannot insert legacy ACL for an object when uniform bucket-level access is enabled. Read more at https://cloud.google.com/storage/docs/uniform-bucket-level-access", 
        "errors": [ 
            { 
                "message": "Cannot insert legacy ACL for an object when uniform bucket-level access is enabled. Read more at https://cloud.google.com/storage/docs/uniform-bucket-level-access", 
                "domain": "global", 
                "reason": "invalid" 
            } 
        ]
    }
```


### Google Cloud Key Management
- [Link](https://cloud.google.com/security-key-management?hl=pt-br)
- Documentação:[Como criptografar e descriptografar dados com uma chave simétrica](https://cloud.google.com/kms/docs/encrypt-decrypt?hl=pt-br)
- Comandos
```
# Configuraro o gcloud com o projeto 
$ gcloud init
$ gcloud kms

# Encriptar a chave
$ gcloud kms encrypt \
    --key service-accoount-storage \
    --keyring codeflix \
    --location global  \
    --plaintext-file ./storage/credentials/google/service-account-storage.json \
    --ciphertext-file ./storage/credentials/google/service-account-storage.json.enc


# Dencriptar a chave
$ gcloud kms decrypt \
    --key service-accoount-storage \
    --keyring codeflix \
    --location global  \
    --plaintext-file ./storage/credentials/google/service-account-storage.json \
    --ciphertext-file ./storage/credentials/google/service-account-storage.json.enc
```


### Como acessar os arquivos de upload local
- Criar um link simbólico para a pasta storage/app/public
```bash
$ docker exec -it micro-videos-app bash
$ php artisan storage:link
$ ls -la public/storage/videos/
$ php artisan tinker
>>> $video = \App\Models\Video::first();
>>> $video->video_file_url
=> "http://localhost:8000/storage/videos/00b1dd8f-0e5f-453f-a104-666f6cb23ac5/qzfB0vL4SlwpqXs7bXtn56PnyrQDmXwz66kM7kDA.gif"
>>> $video->thumb_file_url
=> "http://localhost:8000/storage/videos/00b1dd8f-0e5f-453f-a104-666f6cb23ac5/Gzk80ZgO1nSYDDFGD4QS31lJzoKDeSfP5tFB9i1v.jpg"
```


### Laravel: Conhecendo o API Resource
- Documentação: [Eloquent: API Resources](https://laravel.com/docs/6.x/eloquent-resources)
- Recurso para a serializacção e transformação dos modelos em Json
- Cria as classes resources em `App\Http\Resources`
```bash
$ docker exec -it micro-videos-app bash
# Cria o resource para Category
$ php artisan make:resource CategoryResource
# Cria o resource para CastMember
$ php artisan make:resource CastMemberResource
# Cria o resource para Genre
$ php artisan make:resource GenreResource
# Cria o resource para Video
$ php artisan make:resource VideoResource
```


### Observações
- Ao executar os testes unitário apareceu o erro abaixo:
```bash
Warning: Use of undefined constant PASSWORD_ARGON2_DEFAULT_MEMORY_COST - assumed 'PASSWORD_ARGON2_DEFAULT_MEMORY_COST' (this will throw an Error in a future version of PHP) in /Users/leticiapillar/projects/courses/code-education/microsservices/code-micro-videos/config/hashing.php on line 47
```
- Então comentei o bloco abaixo no arquivo `config/hashing.php`
```php
    // 'argon' => [
    //     'memory' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
    //     'threads' => PASSWORD_ARGON2_DEFAULT_THREADS,
    //     'time' => PASSWORD_ARGON2_DEFAULT_TIME_COST,
    // ],
```

### Problemas encontrados

**Erro ao rodar o step de migration no build da GCP**
- Ao executar a etapa de migração no GCP aparecei o seguinte erro:
```
 Illuminate\Database\QueryException  : SQLSTATE[HY000] [2002] php_network_getaddresses: getaddrinfo failed: Name does not resolve (SQL: select * from information_schema.tables where table_schema = code_micro_videos and table_name = migrations and table_type = 'BASE TABLE')
 ```
- Para corrigir, foram feitos alguns ajustes no arquivo `docker-compose.cloudbuild.yaml` nas configurações do serviço de banco de dados, `service db `:
1. Removido o mapeamento de volune já que estamos forçando o usuário mysql com ID 1000 e no Google Cloud, , dá problema de permissão e não sobe.
2. Removida a variável MYSQL_USER=root, isso funcionava até a última versão do MySQL 5.7, agora ele verifica se a variável é root e lança um erro dizendo que o root já existe.
3. Outro erro que apareceu foi na execução dos testes uniários no GCP. Como estamos usando dois banco de dados, um para a aplicação e outro para os testes unitários, o mapeamento do entrypoint do MySQL para executar o script SQL e criar os dois bancos deve está configurado corretamente. Dessa forma não precisamos informar o valor para a variável de ambiente `MYSQL_DATABASE`.

- Mais detalhes sobre esses problemas estão neste post do [forum do curso](https://forum.code.education/forum/topico/erro-ao-rodar-o-step-de-migration-no-build-da-gcp-182/)
