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
