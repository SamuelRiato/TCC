# Thiago Riato Personal Studio

Site/app do estúdio em **PHP + HTML + CSS + JavaScript**, baseado nas 4 telas de referência
(Login, Cadastro, Início e Perfil), com as páginas Treinos e Agenda para o menu funcionar.

## Como rodar

Requer PHP 7.4+ (recomendado 8.x) com a extensão `mbstring` (já vem ativa na maioria das instalações).

```bash
cd thiago-riato-studio
php -S localhost:8000 -t public
```

Abra http://localhost:8000. Na primeira vez, crie uma conta em **Cadastro**.

No XAMPP/Apache: coloque a pasta em `htdocs` e acesse `http://localhost/thiago-riato-studio/public/`.
Em hospedagem compartilhada, aponte a raiz do site para a pasta `public/` (as pastas `src/` e `storage/` ficam fora da web).

## Estrutura

```
public/            páginas e assets (raiz do site)
  login.php  cadastro.php  inicio.php  treinos.php  agenda.php  perfil.php  logout.php
  api/presenca.php   confirma presença (JSON)
  assets/css/style.css   assets/js/app.js
  uploads/avatars/   fotos de perfil
src/               código PHP (config, helpers, usuários, layout)
storage/users.json dados dos usuários (criado automaticamente)
```

## Onde editar

- `src/config.php`: agenda semanal (`AGENDA`), catálogo de treinos (`TREINOS`), objetivos e meta semanal.
- `public/assets/css/style.css`: cores e tamanhos no bloco `:root` no topo.

## Segurança já incluída

Senhas com `password_hash`, token CSRF em todos os formulários e na API, sessão com cookie `HttpOnly`/`SameSite`,
bloqueio de 1 min após 5 tentativas de login, upload de foto validado pelo conteúdo (JPG/PNG/WEBP até 2 MB) e
saída sempre escapada.

## Próximo passo para produção

Os usuários ficam em `storage/users.json` (bom para protótipo). Para produção, troque as funções de `src/users.php`
por MySQL/PDO mantendo as mesmas assinaturas. A recuperação de senha por e-mail ainda não existe.
