# Template Laravel + WordPress + Docker

Base para começar projeto com Laravel no front da aplicação e WordPress como CMS editorial.

## O que já vem pronto

- Laravel 12
- WordPress em container separado
- blog lido direto do banco do WordPress, sem Corcel
- custom post type `blog_post` e taxonomia `blog_category`
- cache no Laravel para home, blog, post, relacionados e sitemap
- invalidação de cache quando o WordPress salva conteúdo
- Vite com Tailwind CSS v4 em modo CSS-first
- suporte a `tailwind`, `sass` ou `hybrid`

## Subir o projeto

1. Criar o `.env`:

```bash
cp .env.example .env
```

2. Ajustar o que precisar no `.env`. O básico é isso:

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

3. Gerar a chave da app:

```bash
sh docker/php/generate-app-key.sh
```

4. Subir a stack:

```bash
docker compose up --build
```

## Endereços locais

- Laravel: `http://localhost:8080`
- WordPress: `http://localhost:8081`
- Vite: `http://localhost:5173`
- MariaDB do WordPress: `3307`

## Como o blog funciona

O Laravel usa a conexão `wordpress` para ler o conteúdo editorial.

As classes principais dessa integração são:

- `app/Repositories/Wordpress/WordpressPostRepository.php`
- `app/Services/Wordpress/WordpressBlogService.php`
- `app/Services/Wordpress/WordpressCacheInvalidator.php`

O WordPress já sobe com:

- post type `blog_post`
- taxonomia `blog_category`

## Cache do blog

O cache é feito no Laravel e a invalidação vem do WordPress.

Fluxo:

1. O Laravel guarda em cache as leituras do blog.
2. Quando um post é salvo no WordPress, o `save_post` dispara o mu-plugin.
3. O WordPress chama `/internal/wordpress/cache/invalidate` com token.
4. O Laravel incrementa a versão do cache e passa a ignorar as chaves antigas.

Variáveis relacionadas:

- `LARAVEL_CACHE_INVALIDATION_URL`
- `LARAVEL_CACHE_INVALIDATION_TOKEN`
- `WORDPRESS_CACHE_STORE`
- `WORDPRESS_CACHE_TTL`

## Frontend

O template suporta três estratégias:

- `tailwind`: só Tailwind
- `sass`: só SCSS
- `hybrid`: Tailwind + SCSS

Controle por:

```env
FRONTEND_STYLE_STRATEGY=hybrid
```

O CSS global do Tailwind fica em `resources/assets/css/style.css`.

## O que entra no clone

Depois do clone, a pessoa já recebe:

- a stack Docker pronta
- `database/database.sqlite` no repositório
- bootstrap automático do WordPress
- mu-plugins versionados
- estrutura base de assets e views

## O que não entra no clone

O conteúdo editorial do WordPress não vai junto sozinho.

Sem dump SQL, outro clone sobe um WordPress novo e não herda:

- posts e páginas criados localmente
- menus
- widgets
- opções salvas no banco
- configurações de plugins

Se quiser subir o projeto já com conteúdo editorial, adicione um dump em `docker/mariadb/init`, por exemplo:

- `docker/mariadb/init/010-wordpress.sql`

Esse dump só é importado na primeira criação do volume do MariaDB.

Para testar do zero:

```bash
docker compose down -v
docker compose up --build
```

## WordPress no Git

O core completo do WordPress não é versionado.

Fica no repositório só o que faz sentido manter como base:

- `wordpress/wp-config-docker.php`
- `wordpress/wp-content/themes`
- `wordpress/wp-content/plugins`
- `wordpress/wp-content/mu-plugins`
- `docker/mariadb/init`

## Observações

- não versionar `.env`
- manter `composer.lock` e `package-lock.json`
- se a porta do Vite estiver ocupada, ajustar `VITE_PORT`
- se o projeto deixar de usar SQLite no Laravel, ajustar o `.env`