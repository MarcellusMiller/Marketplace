# Marketplace Platform — Requirements Document

---

## 1. Visão Geral

Este projeto consiste em uma plataforma de marketplace onde usuários podem se registrar, navegar por produtos e atuar como compradores ou vendedores.

Vendedores podem criar e gerenciar produtos, enquanto compradores podem navegar pelos produtos, adicionar itens ao carrinho, criar pedidos, efetuar pagamentos, acompanhar envio e se comunicar com vendedores através de mensagens em tempo real.

O principal objetivo deste projeto é demonstrar habilidades de desenvolvimento backend utilizando Laravel, incluindo:

- Autenticação e autorização
- Modelagem relacional de banco de dados
- WebSockets
- Integração com gateway de pagamento
- Integração ou simulação de API de envio
- Filas (queues)
- Documentação de API
- Boas práticas de arquitetura backend

---

## 2. Metas

- Construir um projeto backend próximo de um cenário real de produção utilizando Laravel.
- Demonstrar design de APIs REST.
- Demonstrar modelagem e relacionamentos de banco de dados.
- Implementar autenticação tradicional e OAuth.
- Implementar roles e autorização.
- Implementar mensagens em tempo real usando WebSockets.
- Integrar gateway de pagamento em ambiente sandbox.
- Integrar ou simular serviço de envio.
- Demonstrar uso de filas para processamento assíncrono.
- Realizar deploy do backend e frontend para demonstração pública.

---

## 3. Regras de Usuário

Um usuário pode possuir uma ou mais roles.

Roles disponíveis:

- Buyer
- Seller
- Admin

### Guest

- Pode visualizar produtos públicos.
- Pode navegar pelo marketplace.
- Pode se registrar.
- Pode realizar login.

### Buyer

- Pode navegar por produtos.
- Pode adicionar produtos ao carrinho.
- Pode criar pedidos.
- Pode realizar pagamentos.
- Pode visualizar histórico de pedidos.
- Pode conversar com vendedores após interação comercial.

### Seller

- Pode criar produtos.
- Pode atualizar seus próprios produtos.
- Pode remover seus próprios produtos.
- Pode visualizar pedidos relacionados aos seus produtos.
- Pode responder compradores em conversas permitidas.

### Admin

- Pode gerenciar usuários.
- Pode gerenciar produtos.
- Pode moderar atividades da plataforma.
- Pode desabilitar usuários ou produtos.

---

## 4. Requisitos Funcionais

### 4.1 Autenticação

- Usuários devem poder se registrar.
- Usuários devem poder realizar login.
- Usuários devem poder realizar logout.
- Usuários devem poder autenticar usando OAuth providers como Google ou GitHub.
- Usuários devem possuir uma ou mais roles.

---

### 4.2 Produtos

- Sellers devem poder criar produtos.
- Sellers devem poder atualizar apenas seus próprios produtos.
- Sellers devem poder remover apenas seus próprios produtos.
- Guests e Buyers devem poder listar produtos.
- Usuários devem poder filtrar produtos por categoria, nome e preço.
- Produtos devem pertencer a um vendedor.
- Produtos devem possuir status de disponibilidade.

---

### 4.3 Carrinho

- Buyers devem poder adicionar produtos ao carrinho.
- Buyers devem poder remover produtos do carrinho.
- Buyers devem poder atualizar quantidades dos itens.
- Buyers devem poder visualizar subtotal e total do carrinho.

---

### 4.4 Orders

- Buyers devem poder criar pedidos a partir do carrinho.
- Orders devem conter um ou mais produtos.
- Orders devem possuir status.

Status previstos:

- pending
- paid
- shipped
- delivered
- cancelled

- Sellers devem poder visualizar pedidos contendo seus próprios produtos.

---

### 4.5 Pagamentos

- Buyers devem poder realizar pagamentos utilizando gateway em modo sandbox.
- O sistema deve atualizar status do pedido após confirmação do pagamento.
- O sistema deve processar webhooks de pagamento.
- O sistema deve validar autenticidade dos webhooks.

---

### 4.6 Envio

- O sistema deve calcular ou simular custo de envio.
- Orders devem possuir status de envio.
- Orders podem possuir código de rastreamento.

---

### 4.7 Mensagens em Tempo Real

- Buyers devem poder iniciar conversas após interação comercial.
- Sellers devem poder responder compradores.
- Mensagens devem ser entregues em tempo real usando WebSockets.
- Conversas devem ser privadas entre comprador e vendedor.

---

### 4.8 Reviews

- Buyers devem poder avaliar produtos comprados.
- Reviews devem possuir nota e comentário.
- Sellers não podem avaliar seus próprios produtos.

---

### 4.9 Administração

- Admins devem poder visualizar usuários.
- Admins devem poder gerenciar produtos.
- Admins devem poder desabilitar usuários.
- Admins devem poder desabilitar produtos.

---

## 5. Requisitos Não Funcionais

### 5.1 Segurança

- Senhas devem ser armazenadas utilizando hashing seguro.
- Rotas protegidas devem exigir autenticação.
- Usuários não devem acessar recursos fora de suas permissões.
- Usuários não devem modificar recursos pertencentes a outros usuários.
- Webhooks de pagamento devem ser validados.
- Credenciais sensíveis devem permanecer em variáveis de ambiente.

---

### 5.2 Performance

- Listagem de produtos deve utilizar paginação.
- Processos pesados devem utilizar filas quando possível.
- Queries devem evitar problemas N+1 desnecessários.

---

### 5.3 Escalabilidade

- Frontend e backend devem permanecer desacoplados.
- Backend deve expor API REST.
- A aplicação deve suportar deploy utilizando Docker.

---

### 5.4 Confiabilidade

- Atualizações de pagamento devem ser tratadas de forma segura.
- Mudanças de status dos pedidos devem ser consistentes.
- Jobs falhos devem ser registrados.

---

### 5.5 Observabilidade

- Ações importantes devem ser logadas.
- Falhas de pagamento devem ser rastreáveis.
- Jobs falhos devem ser observáveis.

---

### 5.6 Manutenibilidade

- Código deve seguir convenções do Laravel.
- Regras de negócio devem permanecer separadas dos controllers quando possível.
- O projeto deve possuir documentação clara.
- Endpoints devem possuir documentação pública.

---

### 5.7 Testabilidade

- Principais regras de negócio devem possuir testes automatizados.
- Autenticação e autorização devem ser testadas.
- Fluxos de pedido e pagamento devem possuir cobertura de testes.

---

## 6. Entidades Principais

- User
- Role
- Product
- Category
- Cart
- CartItem
- Order
- OrderItem
- Payment
- Shipment
- Conversation
- Message
- Review

---

## 7. MVP Scope

A primeira versão deverá conter:

- Registro e login de usuários
- OAuth authentication
- Buyer e Seller roles
- CRUD de produtos
- Listagem pública de produtos
- Carrinho
- Criação de pedidos
- Pagamento sandbox
- Chat básico em tempo real
- Documentação de API
- Deploy do sistema

---

## 8. Fora do Escopo do MVP

- Sistema avançado de recomendações
- Dashboard administrativo complexo
- Múltiplas moedas
- Integração logística real em produção
- Sistema de reembolso
- Sistema de cupons
- Aplicativo mobile

---

## 9. Critérios de Sucesso

O projeto será considerado bem sucedido quando:

- Um recrutador puder acessar o frontend publicado.
- O frontend conseguir se comunicar com a API Laravel publicada.
- Usuários puderem se registrar, atuar como vendedores e criar produtos.
- Buyers puderem navegar produtos, criar pedidos e simular pagamentos.
- O sistema possuir documentação pública da API.
- Os relacionamentos do banco estiverem claramente documentados.
- O projeto possuir README profissional.
- O deploy estiver funcional e acessível.