# HelpDesk — Documento de Features

> **Stack:** Laravel + Filament · Spatie Permission · Eloquent
> **Referência visual:** [Figma – Plataforma de Chamados (Rocketseat)](https://www.figma.com/community/file/1506654636739959765/plataforma-de-chamados)

---

## 1. Visão Geral

Sistema de gestão de chamados (tickets) com três perfis de acesso distintos, painel administrativo construído em **Filament** e controle de permissões via **Spatie Permission**. O objetivo principal é servir de laboratório para os recursos avançados do ecossistema Laravel.

---

## 2. Personas & Roles

| Role | Descrição | Nível de acesso |
|---|---|---|
| **admin** | Gestão completa do sistema | Total |
| **technician** (atendente) | Executa e resolve chamados atribuídos | Parcial — só vê os próprios chamados |
| **client** (cliente) | Abre e acompanha suas solicitações | Restrito — só vê os próprios chamados |

> **Implementação:** `spatie/laravel-permission` com Gates/Policies registradas dentro do Filament.

---

## 3. Modelos de Dados

### 3.1 `User`
| Campo | Tipo | Notas |
|---|---|---|
| `id` | uuid / bigint | PK |
| `name` | string | |
| `email` | string | unique |
| `password` | string | bcrypt |
| `phone` | string\|null | |
| `created_at` / `updated_at` | timestamp | |

**Relacionamentos:** tem muitos `Ticket` (como cliente), tem muitos `Ticket` (como técnico), tem muitos `Comment`.

---

### 3.1.1 `Client` e `Technician` (entidades que herdam de `User`)

Cliente e técnico são modelos próprios que **estendem `User`** e compartilham a tabela `users` (single table). A distinção é feita pela role do Spatie.

| Model | Role | Status |
|---|---|---|
| `App\Models\Client` | `client` | Implementado |
| `App\Models\Technician` | `technician` | Planejado (mesmo padrão) |

O comportamento comum fica no trait `App\Models\Concerns\HasRoleScope`, e cada model só implementa `roleName()`:
- **Global scope** — consultas no model retornam apenas usuários com a role (`Client::all()` = só clientes).
- **Role automática** — ao criar o registro, a role é atribuída (`Client::create()` já sai com `client`).
- **Tabela `users`** — `getTable()` fixo, sem tabelas separadas.
- **Morph class = `User`** — o Spatie grava `model_has_roles.model_type` como `App\Models\User`, então roles e relações polimórficas valem tanto para `User` quanto para a subclasse.

> **Atenção:** `auth()->user()` sempre retorna `User` (é o model do provider de auth). Quando precisar da entidade específica, use `Client::find(auth()->id())`.

---

### 3.2 `Category` (Serviços)
| Campo | Tipo | Notas |
|---|---|---|
| `id` | bigint | PK |
| `name` | string | Ex: "Suporte de Software", "Manutenção de Hardware" |
| `is_active` | boolean | default `true` |

---

### 3.3 `Ticket` (Chamado)
| Campo | Tipo | Notas |
|---|---|---|
| `id` | bigint | PK |
| `code` | string | Código único gerado automaticamente (ex: `#0001`) |
| `title` | string | Título do chamado |
| `description` | text | Descrição detalhada |
| `status_id` | FK | → `statuses` |
| `priority` | enum | `low`, `medium`, `high`, `urgent` |
| `category_id` | FK | → `categories` |
| `client_id` | FK | → `users` (role: client) — relação `belongsTo(Client::class)` |
| `technician_id` | FK\|null | → `users` (role: technician) — relação `belongsTo(Technician::class)` |
| `value` | decimal\|null | Valor do serviço (ex: R$ 300,00) |
| `deadline` | date\|null | Prazo de conclusão |
| `closed_at` | timestamp\|null | Preenchido ao fechar |
| `created_at` / `updated_at` | timestamp | |

---

### 3.4 `Status`
| Campo | Tipo | Notas |
|---|---|---|
| `id` | bigint | PK |
| `name` | string | Ex: "Aberto", "Em andamento", "Concluído", "Cancelado" |
| `color` | string | Hex ou classe de cor para badge |

> **Seedable:** os status iniciais devem ser criados via seeder.

---

### 3.5 `Comment` (Histórico / Anotação)
| Campo | Tipo | Notas |
|---|---|---|
| `id` | bigint | PK |
| `ticket_id` | FK | → `tickets` |
| `user_id` | FK | → `users` (quem comentou) |
| `body` | text | Conteúdo da mensagem |
| `is_internal` | boolean | `true` = nota interna (só admin/técnico vê) |
| `created_at` / `updated_at` | timestamp | |

---

## 4. Módulo de Autenticação

### 4.1 Telas (vistas no Figma)
- **Login** — email + senha + botão "Entrar" + link "Criar conta"
- **Cadastro** — nome, email, telefone, senha, confirmar senha + botão "Cadastrar" + link "Já tenho conta"

### 4.2 Regras de negócio
- Login com email + senha
- Registro cria um **`Client`** (`RegisteredClientController`), que recebe a role **`client`** automaticamente
- Técnicos e Admins são criados apenas pelo Admin
- Senha mínima: 8 caracteres
- Redirecionamento pós-login baseado na role:
  - `admin` / `technician` → Painel Filament (`/admin`)
  - `client` → Portal do cliente (`/portal`)

---

## 5. Painel Admin — Filament Resources

### 5.1 `TicketResource` — Chamados (visão completa)

**Listagem:**
- Colunas: `#Código`, `Título`, `Serviço (Category)`, `Valor`, `Técnico`, `Status` (badge colorido), ações
- Filtros: `status`, `priority`, `category`, `technician`, `created_at` (range)
- Pesquisa global por título/código
- Ordenação por data de criação e prazo

**Formulário de criação/edição:**
- Título, descrição
- Select: serviço (category), técnico (technician)
- Select: status, prioridade
- Input: valor (decimal), prazo (date)

**View / Detalhe do chamado:**
- Cabeçalho: título, código, status badge
- Painel lateral com: valor, prazo, técnico, cliente, serviço
- Histórico de comentários (Relation Manager)
- Campo para adicionar novo comentário (com toggle "nota interna")

**Actions customizadas no Resource:**
| Action | Gatilho | Comportamento |
|---|---|---|
| `AtribuirAMim` | Row action | Define `technician_id` = usuário logado |
| `FecharTicket` | Row/Bulk action | Muda status para "Concluído", preenche `closed_at` |
| `ReabrirTicket` | Row action | Muda status para "Aberto", limpa `closed_at` |

---

### 5.2 `TechnicianResource` — Técnicos

> Model: `Technician` (planejado, ver 3.1.1).

**Listagem:**
- Colunas: nome, e-mail, telefone, qtd de chamados ativos, ações (editar/excluir)

**Formulário:**
- Nome, e-mail, telefone, senha (create only)
- Atribui automaticamente role `technician`

> **Tela específica "Perfil do Técnico"** (vista no Figma):
> - Exibe dados pessoais (nome, e-mail, telefone)
> - Lista de chamados atribuídos a ele com status e valor
> - Botões: Editar dados, Excluir técnico

---

### 5.3 `ClientResource` — Clientes

> Model: `Client` — o global scope já restringe a listagem a usuários com role `client`.

**Listagem:**
- Colunas: nome, e-mail, data de cadastro, qtd de chamados, ações

**Detalhe do cliente:**
- Modal com informações básicas e confirmação de exclusão

**Funcionalidades:**
- Excluir cliente (com confirmação em modal)
- Ver chamados vinculados

---

### 5.4 `CategoryResource` — Serviços

**Listagem:**
- Colunas: nome, status ativo/inativo (toggle switch), ações

**Formulário:**
- Nome do serviço
- Toggle: ativo/inativo

**Actions:**
- Criar novo serviço (modal)
- Editar serviço (modal)
- Excluir (com confirmação em modal)

> Serviços inativos não aparecem no formulário de abertura de chamado pelo cliente.

---

### 5.5 `StatusResource` — Status (opcional/administrativo)

- CRUD simples: nome + cor
- Seeder com os status padrão

---

## 6. Painel Filament — Widgets do Dashboard (Admin)

| Widget | Tipo | Dados exibidos |
|---|---|---|
| `TicketsByStatusWidget` | `StatsOverviewWidget` | Total de chamados por status (aberto, em andamento, concluído) |
| `OpenTicketsChart` | `ChartWidget` (bar/donut) | Chamados abertos por categoria/prioridade |
| `AverageResponseTime` | `StatsOverviewWidget` | Tempo médio de `created_at` até primeiro comentário |
| `RecentTicketsWidget` | Tabela rápida | Últimos 5 chamados abertos |

---

## 7. Portal do Cliente (`/portal`)

> Área separada do painel Filament, acessível apenas para usuários com role `client`.

### 7.1 Meus Chamados

**Listagem:**
- Colunas: Data de abertura, Código, Título, Serviço, Valor, Técnico, Status (badge), ações
- Botão "Criar chamado" fixo no topo
- Paginação

### 7.2 Criar Chamado (`Novo chamado`)

**Formulário em duas colunas (layout visto no Figma):**

*Coluna esquerda (Informações):*
- Título (obrigatório)
- Local / Endereço
- Status (definido automaticamente como "Aberto")
- Descrição

*Coluna direita (Resumo/Preview):*
- Serviço selecionado (select)
- Valor estimado (readonly, preenchido pelo serviço)
- Técnico responsável (readonly — atribuído pelo admin/sistema)
- Botão "Criar chamado"

### 7.3 Chamado Detalhado

- Cabeçalho: título, código, status badge
- Campos: descrição, local, serviço, valor total
- Painel com informações: técnico, prazo, valor detalhado
- Histórico de comentários (apenas os públicos, `is_internal = false`)
- Campo para o cliente adicionar resposta/comentário

### 7.4 Perfil do Usuário

- **Modal "Perfil":**
  - Nome, e-mail, telefone
  - Botão "Salvar"

- **Modal "Alterar senha":**
  - Senha atual
  - Nova senha
  - Confirmar nova senha
  - Botão "Salvar"

---

## 8. Portal do Técnico (Filament — visão filtrada)

> O técnico acessa o painel Filament, mas com scope restrito via Policy.

### 8.1 Meus Chamados

- Mesma listagem do Admin, porém filtrada por `technician_id = auth()->id()`
- Agrupamento visual por status (badges coloridos)
- Exibe: data, código, título, serviço, valor, status

### 8.2 Chamado Detalhado (visão técnico)

- Detalhes do chamado: título, descrição, serviço, valor
- Informações: técnico responsável (si mesmo), prazo, valor
- Histórico com comentários públicos **e** notas internas
- Ações disponíveis:
  - Adicionar comentário/nota
  - "Fechar ticket" (muda status para Concluído)
  - "Reabrir ticket"

### 8.3 Actions disponíveis para o Técnico

| Action | Comportamento |
|---|---|
| `AdicionarComentário` | Modal com textarea + toggle "nota interna" |
| `FecharTicket` | Muda status para Concluído, preenche `closed_at` |
| `ReabrirTicket` | Muda status para Aberto |

---

## 9. Controle de Acesso (Spatie Permission + Policies)

### 9.1 Roles e permissões

```
admin
  ├── ticket: view_any, view, create, update, delete
  ├── comment: view_any, view, create, update, delete
  ├── user (technician): view_any, view, create, update, delete
  ├── user (client): view_any, view, update, delete
  ├── category: view_any, view, create, update, delete
  └── status: view_any, view, create, update, delete

technician
  ├── ticket: view (own), update (own), comment
  └── comment: view (own tickets), create

client
  ├── ticket: view (own), create
  └── comment: view (own, public only), create
```

### 9.2 Filament Policies

| Resource | Policy Aplicada |
|---|---|
| `TicketResource` | `TicketPolicy` — técnico só acessa scope próprio |
| `TechnicianResource` | Apenas `admin` |
| `ClientResource` | Apenas `admin` |
| `CategoryResource` | Apenas `admin` |
| Dashboard widgets | Apenas `admin` |

---

## 10. Funcionalidades Técnicas Laravel/Filament

### 10.1 Filament Features
- `Resource` com `RelationManager` (Ticket ↔ Comments)
- Formulários com `Tabs`, `Section`, layout em grid
- `Actions` customizadas inline e em bulk
- `Filters` com `SelectFilter`, `DateRangeFilter`, `TernaryFilter`
- `StatsOverviewWidget` e `ChartWidget` no dashboard
- `Notifications` do Filament após actions (toast)

### 10.2 Eloquent
- Relacionamentos: `belongsTo`, `hasMany`, `belongsToMany`
- Global Scopes para role-based filtering
- Accessors para formatação de valor (R$) e código (#0001)
- Observers para: gerar código automático ao criar ticket, preencher `closed_at`
- Seeders: Status padrão, Roles e permissões, usuário Admin padrão

### 10.3 Spatie Packages
- `spatie/laravel-permission` — roles, policies, middleware

---

## 11. Ordem de Implementação (Roadmap)

### Fase 1 — Base
- [ ] Instalar Laravel, Filament, Spatie Permission
- [ ] Migrations: `users`, `roles/permissions`, `categories`, `statuses`, `tickets`, `comments`
- [ ] Seeders: Status, Roles, Admin user
- [ ] Autenticação (Filament + portal cliente)
- [ ] Middleware de role-redirect

### Fase 2 — Admin Panel (Filament)
- [ ] `TicketResource` com filtros e colunas
- [ ] `RelationManager` Comments em Ticket
- [ ] `TechnicianResource`
- [ ] `ClientResource`
- [ ] `CategoryResource`
- [ ] Actions: AtribuirAMim, FecharTicket, ReabrirTicket

### Fase 3 — Acesso por Role
- [ ] Policies para cada Resource
- [ ] Global Scope em Ticket (técnico vê apenas os seus)
- [ ] Ocultação de menu/widgets por role

### Fase 4 — Portal do Cliente
- [ ] Layout base do portal (`/portal`)
- [ ] Listagem "Meus chamados"
- [ ] Formulário "Novo chamado"
- [ ] Detalhe do chamado + comentários
- [ ] Modais de Perfil e Alteração de senha

### Fase 5 — Dashboard & Métricas
- [ ] `StatsOverviewWidget` por status
- [ ] `ChartWidget` chamados por categoria
- [ ] Widget tempo médio de resposta
- [ ] Widget últimos chamados

---

## 12. Paleta de Status (referência visual)

| Status | Cor sugerida |
|---|---|
| Aberto | Azul (`#3B82F6`) |
| Em andamento | Amarelo/Laranja (`#F59E0B`) |
| Concluído | Verde (`#10B981`) |
| Cancelado | Vermelho (`#EF4444`) |
| Aguardando | Roxo (`#8B5CF6`) |

---

*Documento gerado com base no design Figma da Rocketseat e no plano de execução do projeto HelpDesk.*
