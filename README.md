# Módulo 7 — Precificação de Propostas para Licitações

Projeto integrador (Laravel 13 + Tailwind CSS 4) que implementa o **Módulo 7** de um sistema maior voltado à participação de empresas em **licitações públicas** cadastradas no PNCP (Portal Nacional de Contratações Públicas). Enquanto outros módulos do sistema (Módulo 4) cuidam do cadastro de empresas e do cruzamento/pontuação entre editais e fornecedores, este módulo é responsável pela **precificação da proposta**: calcular o BDI, montar o preço mínimo viável de cada item, sinalizar risco de inexequibilidade e manter um histórico auditável dos custos usados como referência.

## 📋 O que a aplicação faz

A aplicação tem três telas principais (ver menu lateral: **Propostas**, **Calculadora BDI**, **Histórico de Custos**):

### 1. Calculadora de BDI
Calcula o **BDI (Bonificações e Despesas Indiretas)** a partir do regime tributário da empresa, usando a fórmula:

```
BDI = [ (1 + %Desp. Administrativas) × (1 + %Desp. Financeiras) × (1 + %Lucro Bruto) / (1 − %Tributos) ] − 1
```

- **Lucro Real** → tributos considerados: ISS + PIS + COFINS (IRPJ e CSLL ficam de fora, por regra de negócio).
- **Simples Nacional** → tributos = alíquota consolidada do Simples informada para a empresa.
- O regime tributário e a alíquota do Simples vêm do cadastro fiscal da empresa (`EmpresaFiscal`), que é obrigatório antes de qualquer cálculo.

### 2. Propostas de Preço
Monta o **preço mínimo** de um item de proposta a partir dos componentes de custo (custo base, frete, garantia, mão de obra, instalação, impostos), aplicando margem de lucro e o BDI calculado:

```
Preço mínimo = (Σ custos) × (1 + margem de lucro) × (1 + BDI)
```

O resultado é comparado ao **preço estimado no PNCP** para o mesmo item e classificado com um "semáforo":

| Status     | Significado                                   |
|------------|------------------------------------------------|
| 🟢 Verde    | Preço calculado abaixo do estimado — margem ok |
| 🟡 Amarelo  | Sem valor de referência ou preços iguais       |
| 🔴 Vermelho | Preço acima do estimado — margem negativa      |

Se o desconto em relação ao valor estimado do PNCP ultrapassar **70%**, a proposta é automaticamente marcada com `alerta_inexequibilidade`, sinalizando risco de proposta inexequível. Há também um modo de **preview em tempo real** (via AJAX, sem salvar) para simular valores antes de confirmar a proposta.

### 3. Histórico de Custos
Registra documentos de referência (orçamento, cotação, nota fiscal, etc.) vinculados a uma proposta, com valor e data. Essa tabela é **imutável por regra de negócio**: uma vez criado, um registro não pode ser editado nem excluído (tentativas de `PUT`/`DELETE` retornam `405`), garantindo rastreabilidade do custo que embasou o cálculo.

## 🔗 Integração com o Módulo 4

Este módulo consome dados de empresas e editais que, no sistema completo, vivem em um banco compartilhado (Supabase) mantido pelo Módulo 4:

- `mod4_tempmod2` — empresas/fornecedores cadastrados.
- `mod4_tempmod1` — editais (licitações) identificados.
- `mod4_analysis` — resultado do cruzamento edital × empresa (`match_score`, decisão `go/no_go`, status de processamento).

Para desenvolvimento local, essas três tabelas existem como **stubs** (migrations próprias, populadas por seeders), espelhando o schema real. Isso permite rodar e testar o Módulo 7 de forma isolada, sem depender do banco compartilhado.

## 🛠️ Tecnologias Utilizadas

**Backend:** Laravel 13.8 · PHP 8.3+ · SQLite (padrão, configurável) · Laravel Tinker
**Frontend:** Tailwind CSS 4 · Vite 8 · Blade
**Qualidade:** PHPUnit 12 · Laravel Pint · Laravel Pail (logs em tempo real) · Concurrently

## 📁 Estrutura do Projeto

```
projeto_integrador_modulo_7/
├── app/
│   ├── Enums/                 # RegimeTributario, MargemStatus
│   ├── Http/Controllers/      # PropostaController, BDIController, HistoricoController
│   ├── Http/Requests/         # Validação (StorePropostaRequest, StoreBDIRequest)
│   ├── Models/                # PropostaPreco, HistoricoCusto, EmpresaFiscal, User
│   ├── Repositories/          # EditalRepository (acesso às tabelas do Módulo 4)
│   └── Services/              # CalculadoraBDIService, CalculadoraPrecoMinimoService
├── database/
│   ├── migrations/            # Tabelas do M7 + stubs do M4
│   └── seeders/                # Dados de exemplo
├── resources/views/
│   ├── propostas/              # Listagem, criação e detalhe de propostas
│   ├── bdi/                    # Calculadora de BDI
│   ├── historico/              # Histórico de custos
│   └── layouts/                # Layout base com sidebar de navegação
├── routes/web.php              # Rotas da aplicação
└── tests/
    ├── Unit/                   # Testes dos services de cálculo
    └── Feature/
```

## 🗃️ Modelo de Dados (Módulo 7)

| Tabela                     | Descrição                                                        |
|----------------------------|-------------------------------------------------------------------|
| `mod7_propostas_precos`    | Propostas de preço calculadas (custos, margem, BDI, preço mínimo, status) |
| `mod7_historico_custos`    | Histórico imutável de documentos de referência de custo (append-only) |
| `mod7_empresa_fiscal`      | Perfil fiscal da empresa (regime tributário e alíquota do Simples) |

## 🚏 Rotas Principais

| Método | Rota                          | Descrição                                  |
|--------|-------------------------------|---------------------------------------------|
| GET    | `/propostas`                  | Lista de propostas                          |
| GET    | `/propostas/create`           | Formulário de nova proposta                 |
| POST   | `/propostas`                  | Calcula e salva uma proposta                |
| GET    | `/propostas/{proposta}`       | Detalhe da proposta                         |
| POST   | `/propostas/preview`          | Simula o cálculo sem salvar (AJAX)          |
| GET    | `/propostas/editais/{empresa}`| Editais disponíveis para uma empresa (AJAX) |
| GET    | `/bdi`                        | Calculadora de BDI                          |
| POST   | `/bdi/calcular`                | Calcula o BDI (AJAX)                        |
| GET    | `/historico`                  | Lista o histórico de custos                 |
| POST   | `/historico`                  | Registra um novo documento de custo         |

## 🚀 Instalação e Setup

### Pré-requisitos
- PHP 8.3 ou superior
- Composer
- Node.js e NPM
- Git

### Passos de Instalação

1. **Clone o repositório**
```bash
git clone https://github.com/nati-keller/projeto_integrador_modulo_7.git
cd projeto_integrador_modulo_7
```

2. **Execute o script de setup automático**
```bash
composer setup
```

Este comando instala as dependências PHP e Node, cria o `.env`, gera a chave da aplicação, roda as migrations e compila os assets.

### Setup Manual (alternativa)

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
```

### Popular o banco com dados de exemplo

```bash
php artisan db:seed
```

Isso inclui empresas/editais de teste (stub do M4) e propostas de exemplo, úteis para explorar as três telas sem cadastrar nada manualmente.

## 💻 Desenvolvimento

Inicie todos os processos de uma vez (servidor, fila, logs e Vite):
```bash
composer dev
```

Ou individualmente:
```bash
php artisan serve   # servidor Laravel
npm run dev          # Vite em modo desenvolvimento
```

## 🧪 Testes

```bash
composer test
```

Os testes unitários cobrem as regras de negócio dos dois services principais:
- `CalculadoraBDIServiceTest` — fórmula do BDI para Lucro Real e Simples Nacional, incluindo validações de limites.
- `CalculadoraPrecoMinimoServiceTest` — cálculo do preço mínimo, classificação de margem e alerta de inexequibilidade.

## ⚙️ Configuração

Copie `.env.example` para `.env` e ajuste conforme necessário. Por padrão o projeto usa **SQLite** (`DB_CONNECTION=sqlite`); para outro banco, atualize as variáveis `DB_*` e rode `php artisan migrate` novamente.

Este projeto está licenciado sob a licença MIT.

