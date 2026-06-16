
-------------------

## 1. Visão Geral

Este documento define a modelagem inicial do banco de dados da plataforma de marketplace.

O sistema será baseado em usuários com múltiplas roles, produtos pertencentes a vendedores, carrinho, pedidos, pagamentos, envio, conversas em tempo real e avaliações.

--------------------------------

## 2. Tabelas Principais

### users

Representa os usuários da plataforma

Campos principais:

```
id  
name  
email  
password  
avatar_url  
email_verified_at  
created_at  
updated_at
```

Relacionamentos:

User has many Products
User has many Orders
User has one Cart
User has many Reviews
User belongs to many Roles via Spatie Permission

------------------------------------

### roles

Representa as roles disponíveis no sistema, gerenciadas pelo Spatie Permission.

Exemplos:

```
buyer
seller
admin
```


Campos principais:

````
id 
name
guard_name
created_at
updated_at
````

---------------------------------------------------------------------

### model_has_roles

Tabela polimórfica do Spatie Permission que associa models às roles.

Campos principais:

```
role_id
model_type
model_id
```

Relacionamentos:

User belongs to many Roles via model_has_roles
Role belongs to many Users via model_has_roles

----------------

### Categories

Representa categorias dos produtos.

Campos:

```
id
name
slug
created_at
updated_at
```

Relacionamentos:

Category has many Products

-------------------------

### Products

Representa os produtos cadastrados pelos vendedores.

Campos: 
```
id
seller_id
category_id
name
slug
description
price_cents
stock
status
created_at
updated_at
```


Status possíveis:

active
inactive
out_of_stock
disabled

Relacionamentos:

Product belongs to User as Seller
Product belongs to Category
Product has many OrderItems
Product has many CartItems
Product has many ProductImages

--------------------------------

product_images

Representa imagens dos produtos.

Campos:

```
id  
product_id  
url  
is_main  
created_at  
updated_at
```

Relacionamentos:

ProductImage belongs to Product

-----------

### Carts

Representa o carrinho ativo de um comprador

Campos: 

```
id  
user_id  
created_at  
updated_at
```


Relacionamentos:

Cart belongs to User
Cart has many CartItems

------------

### Cart_items

Representa os itens dentro do carrinho.

Campos:

```
id  
cart_id  
product_id  
quantity  
unit_price_cents  
created_at  
updated_at
```

Relacionamentos:

CartItem belongs to Cart  
CartItem belongs to Product

-----------------

Orders

Representa pedidos feitos por compradores.

Campos:

```
id
buyer_id
status
subtotal_cents
shipping_total_cents
total_cents
created_at
updated_at
```


Status possíveis:

pending
paid
shipped
delivered
cancelled


Relacionamentos:

Order belongs to User as buyer
Order has many OrderItems
Order has one Payment
Order has one Shipment

----------------------

### Order_items

Representa os produtos dentro de um pedido.

Campos: 

```
id
order_id
product_id
seller_id
quantity
unit_price_cents
total_cents
created_at
updated_at
```


Relacionamentos:

OrderItem belongs to Order
OrderItem belongs to Product
OrderItem belongs to User as seller


Observação:

O campo `seller_id` é importante para facilitar consultas de pedidos por vendedor.

-------------------------------------------------

### payments

Representa informações de pagamento.


Campos:

```
id
order_id
provider
provider_payment_id
status
amount_cents
paid_at
created_at
updated_at
```

Providers possíveis:

```
stripe
mercado_pago
fake_gateway
```

Status possíveis:

```
pending
approved
failed
refunded
cancelled
```

Relacionamentos:

Payment belongs to Order

--------------------

### Shipments

Representa informações de envio.

Campos:

```
id
order_id
status
shipping_cost_cents
tracking_code
estimated_delivery_date
shipped_at
delivered_at
created_at
updated_at
```


Status possíveis:

```
pending 
processing
shipped
delivered
cancelled
```

Relacionamentos:

Shipment belongs to Order

-------------


Conversations

Representa uma conversa entre comprador e vendedor.

Campos:

```
id
buyer_id
seller_id
order_id
created_at
updated_at
```


Relacionamentos:

Conversation belongs to User as buyer
Conversation belongs to User as seller
Conversation belongs to Order
Conversation has many Messages


Regra:

Uma conversa só pode existir após uma interação comercial.
No MVP, essa interação será a criação de um order.


---------------------

###  Messages

Representa mensagens enviadas dentro de uma conversa

Campos:

```
id
conversation_id
sender_id
message
read_at
created_at
updated_at
```


Relacionamentos:

Message belongs to Conversation
Message belongs to User as sender

-----------------------------

reviews

Representa avaliações feitas por compradores.


Campos:

```
id  
buyer_id  
product_id  
order_id  
rating  
comment  
created_at  
updated_at
```

Relacionamentos:


Review belongs to User as buyer
Review belongs to Product
Review belongs to Order

Regras:

Buyer só pode avaliar produto que comprou.
Seller não pode avaliar o próprio produto.
Rating deve estar entre 1 e 5.

-------------------------------------------------------------


## 3. Relacionamentos Principais


```
User N:N Role via Spatie Permission

User 1:N Product
Category 1:N Product

User 1:1 Cart
Cart 1:N CartItem
Product 1:N CartItem

User 1:N Order
Order 1:N OrderItem
Product 1:N OrderItem
User 1:N OrderItem as seller

Order 1:1 Payment
Order 1:1 Shipment

User 1:N Conversation as buyer
User 1:N Conversation as seller
Order 1:1 Conversation
Conversation 1:N Message

User 1:N Review
Product 1:N Review
Order 1:N Review
```

-------------------

## 4. Decisões de Modelagem

### Usuários com múltiplas roles

Um usuário pode ser comprador e vendedor ao mesmo tempo.

Por isso, será usado o relacionamento de roles do Spatie Permission:

`model_has_roles`

------------------

### Produto pertence a um vendedor

Cada produto possui um `seller_id`, apontando para a tabela `user`.

Isso permite controlar ownership:
`seller só pode editar os próprios produtos`

----------------

### OrderItem guarda seller_id

Mesmo produto já pertencendo a um seller, o `seller_id` também será salvo em `order_items`.

Motivo:

```
Facilitar consultas de vendas por vendedor
Preservar histórico caso o produto mude futuramente
```

Mas no momento da criação do pedido, o preço final e confiável será salvo em `order_items`.

---------------


### Payment separado de Order

Pagamento fica em tabela própria para manter o sistema organizado.

Isso facilita integração com gateways como:

`Stripe, Mercado Pago ou Fake Gateway`

-----------------------


### Shipment separado de Order

Envio fica em tabela própria porque tem regras e status próprios.

-------------------------

### Conversation ligada ao Order

Como decidimos que mensagens só acontecem após interação comercial, a conversa será ligada a um pedido.

-------------------

## 5. Próximas Melhorias Futuras

Possíveis tabelas para versões futuras:

```
addresses
coupons
refunds
notifications
seller_profiles
product_variants
wishlist
audit_logs
```
