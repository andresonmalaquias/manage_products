# **Projeto Manage Products**

**Sistema de gerenciamento de produtos, utilizando Laravel para o desenvolvimento da API, MySQL para armazenamento de dados e VueJS para o frontend.**

## **Configuração do Ambiente**

### **Clonar o Repositório**

**Clone o repositório utilizando o comando abaixo:**

```bash
git clone https://github.com/andresonmalaquias/cme.git
```

### **Variáveis de Ambiente**

**As variáveis de ambiente podem ser acessadas e modificadas no arquivo **``** localizado na raiz do projeto.**

### **Iniciar o Docker**

**Inicie os containers Docker com o comando:**

```bash
docker-compose up --build -d
```

### **Executando Migrações**

**Aplique as migrações do banco de dados com o comando:**

```bash
docker exec -it manage_products_backend php artisan migrate
```

## 🔑 **Usuário Padrão Criado**

Após configurar o ambiente, você pode acessar com as seguintes credenciais padrão:

- **email:** `admin@admin.com`
- **password:** `Password123#`
