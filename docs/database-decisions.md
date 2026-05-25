
## 1. Primary keys

### Decisão 

Usar ULID como chave primária nas tabelas principais.

Exemplo:

```
id CHAR(26)
```

### Motivo

- Evita exposição de IDs sequenciais em APIs Públicas.
- Reduz o risco de enumeração de recursos.
- Preocupação com segurança desde a modelagem
- Funciona Nativamente com Laravel
- Mantém ordenação temporal diferente que UUID v4.



### Laravel  
  
Models principais devem usar:  
  
```php  
use Illuminate\Database\Eloquent\Concerns\HasUlids;  
```

Migrations:  
  
```php  
$table->ulid('id')->primary();  
```
  
Aplicar ULID em:  
  
```txt  
users  
roles  
products  
categories  
product_images  
carts  
cart_items  
orders  
order_items  
payments  
shipments  
conversations  
messages  
reviews  
```

-----------------

## 2. Soft Deletes  
  
### Decisão  
  
Usar Soft Deletes nas entidades principais.  
  
Tabelas:  
  
```txt  
users  
products  
categories  
orders  
```  
  
### Motivo  
  
- Preservar histórico.  
- Evitar perda permanente de dados.  
- Permitir desabilitação sem exclusão física.  
  
### Laravel  
  
```php  
$table->softDeletes();  
```  
  
---  
  
## 3. Roles  
  
### Decisão  
  
Usar tabela `roles` + tabela pivô `role_user`.  
  
### Motivo  
  
Um usuário pode possuir múltiplas roles.  
  
Exemplo:  
  
```txt  
buyer + seller  
admin  
```  
  
### Estrutura  
  
```txt  
users  
roles  
role_user  
```  
  
Roles iniciais:  
  
```txt  
buyer  
seller  
admin  
```  
  
---  
  
## 4. Status Fields  
  
### Decisão  
  
Usar status baseados em string + Laravel Enums.  
  
Exemplos:  
  
```txt  
products.status  
orders.status  
payments.status  
shipments.status  
```  
  
### Motivo  
  
- Simples.  
- Expressivo.  
- Fácil manutenção.  
- Boa integração com PHP Enums.  
  
### Exemplo  
  
```php  
enum OrderStatus: string  
{  
case Pending = 'pending';  
case Paid = 'paid';  
case Shipped = 'shipped';  
case Delivered = 'delivered';  
case Cancelled = 'cancelled';  
}  
```  
  
---  
  
## 5. Cart  
  
### Decisão  
  
Cada usuário possui um carrinho ativo.  
  
Relacionamento:  
  
```txt  
users 1:1 carts  
carts 1:N cart_items  
```  
  
### Motivo  
  
- Simplifica MVP.  
- Fluxo claro.  
- Menos complexidade operacional.  
  
### Regra  
  
Após checkout bem sucedido:  
  
```txt  
cart pode ser limpo.  
```  
  
---  
  
## 6. Orders  
  
### Decisão  
  
Orders pertencem ao buyer.  
  
```txt  
orders.buyer_id  
```  
  
Order possui múltiplos items.  
  
```txt  
orders 1:N order_items  
```  
  
### Motivo  
  
Uma mesma order pode conter produtos de vários sellers.  
  
Por isso:  
  
```txt  
order_items.seller_id  
```  
  
é persistido.  
  
---  
  
## 7. Product Ownership  
  
### Decisão  
  
Produto pertence a um seller.  
  
```txt  
products.seller_id  
```  
  
### Motivo  
  
Permitir ownership authorization.  
  
Exemplo:  
  
```txt  
seller só pode editar produtos próprios.  
```  
  
---  
  
## 8. Product Images  
  
### Decisão  
  
Tabela dedicada `product_images`.  
  
Relacionamento:  
  
```txt  
products 1:N product_images  
```  
  
### Motivo  
  
Produtos possuem múltiplas imagens.  
  
Campo:  
  
```txt  
is_main  
```  
  
define imagem principal.  
  
---  
  
## 9. Payments  
  
### Decisão  
  
Pagamento separado de Order.  
  
Relacionamento:  
  
```txt  
orders 1:1 payments  
```  
  
### Motivo  
  
- Separação de responsabilidade.  
- Integração limpa com gateways.  
- Tratamento adequado de webhooks.  
  
MVP:  
  
```txt  
1 payment por order.  
```  
  
Futuro:  
  
```txt  
1:N payments  
```  
  
---  
  
## 10. Shipments  
  
### Decisão  
  
Shipment separado de Order.  
  
Relacionamento:  
  
```txt  
orders 1:1 shipments  
```  
  
### Motivo  
  
Envio possui:  
  
- status próprio  
- tracking code  
- lifecycle próprio  
  
MVP:  
  
```txt  
shipping fake/simulado.  
```  
  
---  
  
## 11. Conversations  
  
### Decisão  
  
Conversations exigem interação comercial.  
  
Relacionamento:  
  
```txt  
conversations.order_id  
```  
  
### Regra  
  
Buyer e seller só podem conversar após order.  
  
### Constraint  
  
```txt  
unique(order_id, buyer_id, seller_id)  
```  
  
### Motivo  
  
Uma order pode envolver múltiplos sellers.  
  
---  
  
## 12. Messages  
  
### Decisão  
  
Mensagens pertencem a conversations.  
  
Relacionamento:  
  
```txt  
messages.conversation_id  
messages.sender_id  
```  
  
### Regra  
  
Somente participantes da conversation podem enviar mensagens.  
  
---  
  
## 13. Reviews  
  
### Decisão  
  
Reviews pertencem a:  
  
```txt  
buyer_id  
product_id  
order_id  
```  
  
### Regras  
  
- Buyer deve ter comprado produto.  
- Seller não pode avaliar próprio produto.  
- Rating entre 1–5.  
  
Constraint:  
  
```txt  
unique(buyer_id, product_id, order_id)  
```  
  
---  
  
## 14. Addresses  
  
### Decisão  
  
Fora do MVP.  
  
### Motivo  
  
Reduzir complexidade inicial.  
  
### Futuro  
  
Criar:  
  
```txt  
addresses  
```  
  
---  
  
## 15. Audit Logs  
  
### Decisão  
  
Fora do MVP.  
  
### Futuro  
  
Adicionar:  
  
```txt  
audit_logs  
```  
  
---  
  
## 16. Money Fields  
  
### Decisão  
  
Salvar valores monetários em centavos.  
  
Exemplo:  
  
```txt  
5990  
```  
  
Representa:  
  
```txt  
R$ 59,90  
```  
  
### Campos  
  
```txt  
price_cents  
subtotal_cents  
shipping_total_cents  
total_cents  
amount_cents  
```  
  
### Motivo  
  
Evitar erros de ponto flutuante.  
  
---  
  
## 17. Timestamps  
  
### Decisão  
  
Usar timestamps padrão Laravel.  
  
```txt  
created_at  
updated_at  
```  
  
Campos específicos:  
  
```txt  
paid_at  
shipped_at  
delivered_at  
read_at  
```  
  
---  
  
## 18. Indexes  
  
### Decisão  
  
Criar índices para campos de consulta frequente.  
  
Índices:  
  
```txt  
products.seller_id  
products.category_id  
products.status  
  
orders.buyer_id  
orders.status  
  
order_items.seller_id  
  
payments.provider_payment_id  
  
conversations.order_id  
  
messages.conversation_id  
  
reviews.product_id  
```  
  
---  
  
## Resumo Técnico  
  
```txt  
Primary Key: ULID  
Roles: roles + role_user  
Status: Laravel Enums  
Money: centavos  
Cart: 1 ativo por buyer  
Order: pertence ao buyer  
OrderItem: guarda seller_id  
Payment: separado  
Shipment: separado  
Conversation: vinculada à order  
Reviews: apenas compra válida  
SoftDeletes: entidades principais  
Addresses: fora MVP  
AuditLogs: fora MVP  
```