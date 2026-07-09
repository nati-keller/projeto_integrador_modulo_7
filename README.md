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

## 👤 Autor

**Natália Keller**
- GitHub: [@nati-keller](https://github.com/nati-keller)

## 📄 Licença

Este projeto está licenciado sob a licença MIT.

# Projeto Integrador - Módulo 7

## 📋 Sobre o Projeto

Um projeto integrativo desenvolvido com **Laravel 13** e **Tailwind CSS**, utilizando as melhores práticas modernas de desenvolvimento web. Este projeto é um esqueleto de aplicação Laravel pronto para ser expandido com funcionalidades específicas.

## 🛠️ Tecnologias Utilizadas

### Backend
- **Laravel 13.8** - Framework web progressivo PHP
- **PHP 8.3+** - Linguagem de programação
- **SQLite** - Banco de dados padrão (configurável)
- **Laravel Tinker** - REPL interativo para Laravel

### Frontend
- **Tailwind CSS 4.0** - Framework CSS utilitário
- **Vite 8.0** - Bundler e dev server moderno
- **JavaScript ES6+** - Linguagem de programação

### Ferramentas de Desenvolvimento
- **Composer** - Gerenciador de dependências PHP
- **NPM** - Gerenciador de pacotes Node.js
- **PHPUnit 12** - Framework de testes unitários
- **Pest** - Framework de testes (suporte)
- **Pint** - Formatador de código PHP
- **Laravel Pail** - Monitor de logs em tempo real
- **Concurrently** - Execute múltiplos processos simultaneamente

## 📦 Dependências Principais

### Dependências de Produção
```json
{
  "php": "^8.3",
  "laravel/framework": "^13.8",
  "laravel/tinker": "^3.0"
}
```

### Dependências de Desenvolvimento
- `fakerphp/faker` - Gerador de dados fake para testes
- `laravel/pint` - Linter e formatador de código
- `mockery/mockery` - Mock objects para testes
- `nunomaduro/collision` - Relatórios de erro melhorados
- `phpunit/phpunit` - Framework de testes

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

Este comando irá:
- Instalar dependências PHP via Composer
- Copiar `.env.example` para `.env` (se não existir)
- Gerar chave de aplicação
- Executar migrações do banco de dados
- Instalar dependências Node.js
- Compilar assets frontend

### Setup Manual (alternativa)

```bash
# Instalar dependências PHP
composer install

# Configurar variáveis de ambiente
cp .env.example .env

# Gerar chave de aplicação
php artisan key:generate

# Executar migrações
php artisan migrate

# Instalar dependências Node.js
npm install

# Compilar assets
npm run build
```

## 💻 Desenvolvimento

### Iniciar servidor de desenvolvimento

Execute todos os processos simultaneamente:
```bash
composer dev
```

Este comando inicia:
- Servidor Laravel (`php artisan serve`)
- Queue listener (`php artisan queue:listen`)
- Monitor de logs (`php artisan pail`)
- Vite dev server (`npm run dev`)

### Compilar assets em desenvolvimento
```bash
npm run dev
```

### Compilar assets para produção
```bash
npm run build
```

## 🧪 Testes

### Executar testes
```bash
composer test
```

Este comando irá:
- Limpar cache de configuração
- Executar suite de testes com PHPUnit

### Executar testes com PHPUnit diretamente
```bash
php artisan test
```

## 📁 Estrutura do Projeto

```
projeto_integrador_modulo_7/
├── app/                      # Código da aplicação (Controllers, Models, etc)
├── bootstrap/               # Bootstrap da aplicação
├── config/                  # Arquivos de configuração
├── database/               # Migrations, seeders e factories
├── public/                 # Assets públicos e index.php
├── resources/              # Views, CSS, JavaScript
├── routes/                 # Definição de rotas
├── storage/                # Logs, uploads, cache
├── tests/                  # Testes automatizados
├── composer.json           # Dependências PHP
├── composer.lock           # Lock file do Composer
├── package.json            # Dependências Node.js
├── package-lock.json       # Lock file do NPM
├── vite.config.js          # Configuração do Vite
├── phpunit.xml             # Configuração do PHPUnit
├── .env.example            # Exemplo de variáveis de ambiente
└── README.md               # Este arquivo
```

## ⚙️ Configuração

### Variáveis de Ambiente

Copie `.env.example` para `.env` e configure as seguintes variáveis:

```env
APP_NAME=Laravel
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
# ou MySQL, PostgreSQL, etc

APP_KEY=base64:xxxxx (gerado automaticamente)
```

### Banco de Dados

O projeto vem configurado com SQLite por padrão. Para usar outro banco de dados:

1. Atualize `DB_CONNECTION` em `.env`
2. Configure as credenciais de conexão
3. Execute migrações: `php artisan migrate`

## 📚 Recursos Úteis

- [Documentação Laravel](https://laravel.com/docs)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Vite Documentation](https://vitejs.dev/)
- [Laravel Tinker](https://laravel.com/docs/tinker)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)

## 🔒 Segurança

Se descobrir uma vulnerabilidade de segurança, por favor envie um email ao desenvolvedor ao invés de usar o sistema de issues.

## 📄 Licença

Este projeto está licenciado sob a licença MIT. Veja o arquivo LICENSE para mais detalhes.

## 👤 Autor

**Natália Keller**
- GitHub: [@nati-keller](https://github.com/nati-keller)

## 🤝 Contribuindo

Contribuições são bem-vindas! Sinta-se à vontade para abrir uma issue ou enviar um pull request.

### Passos para Contribuir
1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📞 Suporte

Se tiver dúvidas ou problemas, abra uma [issue](https://github.com/nati-keller/projeto_integrador_modulo_7/issues) no repositório.

---

**Criado com ❤️ usando Laravel e Tailwind CSS**
