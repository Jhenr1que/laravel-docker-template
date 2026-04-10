# Template Laravel + WordPress + Docker

Base institucional para reaproveitar em novos projetos com Docker.

## O que este template entrega

- Laravel 12.
- WordPress desacoplado para conteudo editorial.
- Corcel para leitura do banco do WordPress.
- Docker com app, vite, wordpress e mariadb.
- Estrutura inicial de views, estilos e i18n.
- Bootstrap local.

## O que ja fica reproduzivel apos o clone

- A stack sobe com Docker usando `.env.example` como base.
- O arquivo `database/database.sqlite` ja existe no repositorio para a conexao padrao do Laravel.
- O container do WordPress popula localmente o core em `wordpress` quando ele nao estiver presente.
- O WordPress faz a instalacao inicial automaticamente quando o banco ainda nao foi inicializado.

## O que nao vai junto automaticamente

Se voce quer reproduzir exatamente o estado editorial que criou localmente, voce precisa versionar um dump do banco do WordPress.

Sem dump, outro clone vai subir a estrutura e instalar um WordPress novo, mas nao vai herdar:

- posts e paginas cadastrados localmente
- menus criados no admin
- widgets e opcoes salvas no banco
- configuracoes de plugins gravadas no banco

## Setup rapido

1. Copie o ambiente:

```bash
cp .env.example .env
```

2. Ajuste as variaveis mais importantes no `.env` se necessario:

```env
APP_URL=http://localhost:8080
WP_HOME_URL=http://localhost:8081/
WP_HOME=http://localhost:8081/wp-admin
WP_SITE_TITLE="Institucional Base"
WP_ADMIN_USER=admin
WP_ADMIN_PASSWORD=admin123456
WP_ADMIN_EMAIL=admin@example.com
```

3. Gere a chave da aplicacao:

```bash
sh docker/php/generate-app-key.sh
```

Esse script gera a chave pelo container, mas atualiza o `.env` pelo host. Isso evita o erro de permissao que pode acontecer quando o Docker tenta escrever diretamente no bind mount do arquivo `.env`.

4. Suba a stack:

```bash
docker compose up --build
```

## Servicos esperados

- Laravel: `http://localhost:8080`
- WordPress: `http://localhost:8081`
- Vite: `http://localhost:5173`
- MariaDB do WordPress: porta `3307`

## Desenvolvimento de front

Com a stack subida via Docker, alteracoes em SCSS, JS e assets devem refletir direto no Laravel em desenvolvimento.

Isso depende de o container da app enxergar a pasta `public` do projeto para ler o arquivo `public/hot` gerado pelo Vite. Sem isso, a app pode continuar servindo o build antigo de `public/build` e parecer que a edicao nao funcionou.

## WordPress automatico

O bootstrap do container faz duas coisas no primeiro start:

1. Copia os arquivos base do WordPress para a pasta local `wordpress` se o core ainda nao estiver presente.
2. Executa `wp core install` automaticamente se o banco ainda nao tiver uma instalacao pronta.

As credenciais iniciais do admin ficam no `.env`:

- `WP_ADMIN_USER`
- `WP_ADMIN_PASSWORD`
- `WP_ADMIN_EMAIL`

Se quiser desativar a instalacao automatica, defina:

```env
WP_AUTO_INSTALL=false
```

## Reproduzir tambem o conteudo do WordPress

Se voce quiser que outra pessoa clone e receba tambem os dados editoriais que voce criou, coloque um dump SQL em `docker/mariadb/init`.

Exemplo de caminho:

- `docker/mariadb/init/010-wordpress.sql`

O MariaDB importa automaticamente arquivos dessa pasta somente na primeira inicializacao do volume.

### Fluxo recomendado para versionar o estado editorial

1. Exporte o banco do WordPress para um arquivo `.sql`.
2. Salve esse arquivo dentro de `docker/mariadb/init`.
3. Commit o dump junto com o projeto.
4. Em uma maquina nova, rode `docker compose up --build`.

### Importante

Se o volume `mariadb-data` ja existir, o MariaDB nao reimporta os arquivos de `docker/mariadb/init` automaticamente.

Nesse caso, para testar um bootstrap limpo, remova os volumes antes de subir de novo:

```bash
docker compose down -v
docker compose up --build
```

## SQLite do Laravel

O template usa `sqlite` como conexao padrao do Laravel por default.

Por isso o arquivo `database/database.sqlite` ja vai versionado. Isso evita falha no clone novo quando algum comando ou request tocar a conexao padrao da app.

Se o projeto passar a usar outro banco para o Laravel, ajuste o `.env` normalmente.

## Como o WordPress entra no Git

Como template, o repositorio nao versiona o core completo do WordPress.

Fica versionado apenas o que faz sentido manter como base do projeto:

- `wordpress/wp-config-docker.php`
- temas proprios em `wordpress/wp-content/themes`
- plugins proprios em `wordpress/wp-content/plugins`
- `mu-plugins` em `wordpress/wp-content/mu-plugins`
- dump opcional em `docker/mariadb/init`

O core do WordPress e arquivos locais gerados no bootstrap ficam fora do Git.

Isso deixa o template menor, reduz ruido no repositório e mantém a atualização do WordPress centralizada na imagem Docker.

## Observacoes

- O arquivo `.env` nao deve ser versionado.
- `composer.lock` e `package-lock.json` devem continuar versionados para manter reproducibilidade.
- O Vite usa a porta definida em `VITE_PORT`. Se ela estiver ocupada, troque no `.env` antes de subir.
- O template foi ajustado para ser mais previsivel em `git clone + docker compose up`, mas o estado exato do conteudo do WordPress depende do dump SQL quando houver conteudo editorial relevante.