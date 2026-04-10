# Template Laravel + WordPress + Docker

Base institucional para novos projetos com Laravel como aplicação principal, WordPress como CMS editorial e Docker para reproducibilidade local.

## O que este template entrega

- Laravel 12.
- WordPress desacoplado como origem editorial.
- Custom post type e taxonomia de blog já registrados no painel do WordPress.
- Leitura do WordPress por Query Builder em conexão dedicada, sem Corcel.
- Cache no Laravel para home, listagem, detalhe, relacionados e sitemap do blog.
- Invalidação de cache disparada pelo WordPress quando um post é salvo.
- Vite com Tailwind CSS v4 em modo CSS-first.
- Estratégia de front configurável para usar Tailwind, SCSS ou modo híbrido.
- Estrutura inicial de views, assets e i18n.
- Bootstrap local com Docker para app, vite, wordpress e mariadb.

## O que já fica reproduzível após o clone

- A stack sobe com Docker usando `.env.example` como base.
- O arquivo `database/database.sqlite` já existe no repositório para a conexão padrão do Laravel.
- O container do WordPress popula localmente o core em `wordpress` quando ele não estiver presente.
- O WordPress faz a instalação inicial automaticamente quando o banco ainda não foi inicializado.
- O plugin de `mu-plugins` para invalidação de cache já fica pronto na base.

## O que não vai junto automaticamente

Se você quer reproduzir exatamente o estado editorial criado localmente, precisa versionar um dump do banco do WordPress.

Sem dump, outro clone sobe a estrutura e instala um WordPress novo, mas não herda:

- posts e páginas cadastrados localmente
- menus criados no admin
- widgets e opções salvas no banco
- configurações de plugins gravadas no banco

## Setup rápido

1. Copie o ambiente:

```bash
cp .env.example .env
```

2. Ajuste as variáveis mais importantes no `.env` se necessário:

```env
APP_URL=http://localhost:8080
WP_HOME_URL=http://localhost:8081
WP_SITE_TITLE="Institucional Base"
WP_ADMIN_USER=admin
WP_ADMIN_PASSWORD=admin123456
WP_ADMIN_EMAIL=admin@example.com
LARAVEL_CACHE_INVALIDATION_URL=http://app/internal/wordpress/cache/invalidate
LARAVEL_CACHE_INVALIDATION_TOKEN=change-me
FRONTEND_STYLE_STRATEGY=hybrid
```

3. Gere a chave da aplicação:

```bash
sh docker/php/generate-app-key.sh
```

Esse script gera a chave pelo container, mas atualiza o `.env` pelo host. Isso evita erro de permissão no bind mount do `.env`.

4. Suba a stack:

```bash
docker compose up --build
```

## Serviços esperados

- Laravel: `http://localhost:8080`
- WordPress: `http://localhost:8081`
- Vite: `http://localhost:5173`
- MariaDB do WordPress: porta `3307`

## Padrão de integração com WordPress

O template usa uma conexão secundária chamada `wordpress` no Laravel.

As leituras do blog ficam encapsuladas em classes próprias, evitando query em controller:

- `app/Repositories/Wordpress/WordpressPostRepository.php`
- `app/Services/Wordpress/WordpressBlogService.php`
- `app/Services/Wordpress/WordpressCacheInvalidator.php`

O padrão da base é:

1. WordPress como CMS editorial.
2. Laravel como camada de renderização.
3. Query Builder para consultas ao banco do WordPress.
4. Cache no Laravel desde a leitura.

No WordPress, o template já registra por padrão:

- post type `blog_post`
- taxonomia `blog_category`

## Cache com atualização quando o post muda

O template já sai com um fluxo de invalidação controlada:

1. O WordPress escuta `save_post` em `wordpress/wp-content/mu-plugins/cache-invalidation.php`.
2. Ao salvar um post, envia uma chamada HTTP para o Laravel com token compartilhado.
3. O Laravel recebe no endpoint interno `/internal/wordpress/cache/invalidate`.
4. A aplicação incrementa a versão do cache e reaproveita novas chaves automaticamente.

Variáveis de ambiente relevantes:

- `LARAVEL_CACHE_INVALIDATION_URL`
- `LARAVEL_CACHE_INVALIDATION_TOKEN`
- `WORDPRESS_CACHE_STORE`
- `WORDPRESS_CACHE_TTL`

Esse endpoint não deve ser exposto sem token.

## Desenvolvimento de front

O template suporta três estratégias:

- `tailwind`: carrega apenas o CSS global em Tailwind.
- `sass`: carrega apenas SCSS global e entradas por página.
- `hybrid`: carrega Tailwind global junto com SCSS base e SCSS por página.

Controle pela variável:

```env
FRONTEND_STYLE_STRATEGY=hybrid
```

## Tailwind CSS v4 CSS-first

O CSS global do Tailwind fica em:

- `resources/assets/css/style.css`

Esse arquivo concentra:

- `@import "tailwindcss"`
- `@source` para views, app e js
- tokens básicos de tema
- estilos globais do template

O Vite usa o plugin oficial do Tailwind direto no build, sem `tailwind.config.js`.

## Estrutura base de pastas

### Assets

- `resources/assets/css`
- `resources/assets/js`
- `resources/assets/images`
- `resources/assets/scss`
- `resources/assets/svg`

### Views

- `resources/views/layouts`
- `resources/views/includes`
- `resources/views/components`
- `resources/views/pages`
- `resources/views/errors`

## WordPress automático

O bootstrap do container faz duas coisas no primeiro start:

1. Copia os arquivos base do WordPress para a pasta local `wordpress` se o core ainda não estiver presente.
2. Executa `wp core install` automaticamente se o banco ainda não tiver uma instalação pronta.

As credenciais iniciais do admin ficam no `.env`:

- `WP_ADMIN_USER`
- `WP_ADMIN_PASSWORD`
- `WP_ADMIN_EMAIL`

Se quiser desativar a instalação automática, defina:

```env
WP_AUTO_INSTALL=false
```

## Reproduzir também o conteúdo do WordPress

Se você quiser que outra pessoa clone e receba também os dados editoriais, coloque um dump SQL em `docker/mariadb/init`.

Exemplo:

- `docker/mariadb/init/010-wordpress.sql`

O MariaDB importa automaticamente arquivos dessa pasta somente na primeira inicialização do volume.

### Fluxo recomendado

1. Exporte o banco do WordPress para um arquivo `.sql`.
2. Salve esse arquivo dentro de `docker/mariadb/init`.
3. Commit o dump junto com o projeto.
4. Em uma máquina nova, rode `docker compose up --build`.

### Importante

Se o volume `mariadb-data` já existir, o MariaDB não reimporta os arquivos de `docker/mariadb/init` automaticamente.

Nesse caso, para testar um bootstrap limpo:

```bash
docker compose down -v
docker compose up --build
```

## SQLite do Laravel

O template usa `sqlite` como conexão padrão do Laravel por default.

Por isso o arquivo `database/database.sqlite` já vai versionado. Isso evita falha no clone novo quando algum comando ou request tocar a conexão padrão da app.

Se o projeto passar a usar outro banco para o Laravel, ajuste o `.env` normalmente.

## Como o WordPress entra no Git

Como template, o repositório não versiona o core completo do WordPress.

Fica versionado apenas o que faz sentido manter como base do projeto:

- `wordpress/wp-config-docker.php`
- temas próprios em `wordpress/wp-content/themes`
- plugins próprios em `wordpress/wp-content/plugins`
- `mu-plugins` em `wordpress/wp-content/mu-plugins`
- dump opcional em `docker/mariadb/init`

O core do WordPress e arquivos locais gerados no bootstrap ficam fora do Git.

## Observações

- O arquivo `.env` não deve ser versionado.
- `composer.lock` e `package-lock.json` devem continuar versionados para manter reproducibilidade.
- O Vite usa a porta definida em `VITE_PORT`. Se ela estiver ocupada, troque no `.env` antes de subir.
- O estado exato do conteúdo do WordPress continua dependendo do dump SQL quando houver conteúdo editorial relevante.