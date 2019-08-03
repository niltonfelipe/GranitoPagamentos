=== WooCommerce Granito ===
Contributors: granito, Douglas Morais
Tags: woocommerce, granito, banco bmg, payment
Requires at least: 4.0
Tested up to: 5.1
Stable tag: 2.0.15
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Receba pagamentos por cartão de crédito

== Description ==

O [Granito.com.vc](https://granito.com.vc/) é a melhor forma de receber pagamentos online por cartão de crédito, sendo possível o cliente fazer todo o pagamento sem sair da sua loja WooCommerce.

Saiba mais como o Granito funciona:

[URL vídeo]

= Compatibilidade =

Compatível com desde a versão 2.2.x do WooCommerce.

Este plugin funciona integrado com o [WooCommerce Extra Checkout Fields for Brazil](http://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/), desta forma é possível enviar documentos do cliente como "CPF" ou "CNPJ", além dos campos "número" e "bairro" do endereço. Caso você queira remover todos os campos adicionais de endereço para vender Digital Goods, é possível utilizar o plugin [WooCommerce Digital Goods Checkout](https://wordpress.org/plugins/wc-digital-goods-checkout/).

= Instalação =

Confira o nosso guia de instalação e configuração do Granito na aba [Installation](http://wordpress.org/plugins/woocommerce-granito/installation/).

= Dúvidas? =

Você pode esclarecer suas dúvidas usando:

* A nossa sessão de [FAQ](http://wordpress.org/plugins/woocommerce-granito/faq/).
* Criando um tópico no [fórum de ajuda do WordPress](http://wordpress.org/support/plugin/woocommerce-granito).
* Criando um tópico no [fórum do Github](https://github.com/claudiosmweb/woocommerce-granito/issues).

= Colaborar =

Você pode contribuir com código-fonte em nossa página no [GitHub](https://github.com/claudiosmweb/woocommerce-granito).

== Installation ==

= Instalação do plugin: =

* Envie os arquivos do plugin para a pasta wp-content/plugins, ou instale usando o instalador de plugins do WordPress.
* Ative o plugin.

= Requerimentos: =

É necessário possuir uma conta no [Granito](https://Granito/) e ter instalado o [WooCommerce](http://wordpress.org/plugins/woocommerce/).

= Configurações do Plugin: =

Com o plugin instalado acesse o admin do WordPress e entre em "WooCommerce" > "Configurações" > "Finalizar compra" e configure as opção "Granito - Boleto bancário" e "Granito - Cartão de crédito".

Habilite a opção que você deseja, preencha as opções de **Chave de API** e **Chave de Criptografia** que você pode encontrar dentro da sua conta no Granito em **API Keys**.

Também será necessário utilizar o plugin [WooCommerce Extra Checkout Fields for Brazil](http://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/) para poder enviar campos de CPF e CNPJ.

Pronto, sua loja já pode receber pagamentos pelo Granito.

== Frequently Asked Questions ==

= Qual é a licença do plugin? =

Este plugin esta licenciado como GPL.

= O que eu preciso para utilizar este plugin? =

* Ter instalado o plugin WooCommerce 2.2 ou superior.
* Possuir uma conta no [Granito](https://Granito/).
* Pegar sua **Chave de API** e **Chave de Criptografia** no Granito.
* Desativar a opção **Manter Estoque (minutos)** do WooCommerce.

= Quanto custa o Granito? =

Confira os preços em "[Granito - Preços](https://Granito/precos/)".

= Funciona com o Checkout Granito? =

Sim, funciona desde a versão 2.0.0 para pagamentos com cartão de crédito.

= É possível utilizar a opção de pagamento recorrente? =

No momento ainda não é possível, entretanto iremos fazer esta integração em breve.

= O pedido foi pago e ficou com o status de "processando" e não como "concluído", isto esta certo ? =

Sim, esta certo e significa que o plugin esta trabalhando como deveria.

Todo gateway de pagamentos no WooCommerce deve mudar o status do pedido para "processando" no momento que é confirmado o pagamento e nunca deve ser alterado sozinho para "concluído", pois o pedido deve ir apenas para o status "concluído" após ele ter sido entregue.

Para produtos baixáveis a configuração padrão do WooCommerce é permitir o acesso apenas quando o pedido tem o status "concluído", entretanto nas configurações do WooCommerce na aba *Produtos* é possível ativar a opção **"Conceder acesso para download do produto após o pagamento"** e assim liberar o download quando o status do pedido esta como "processando".

= É obrigatório enviar todos os campos para processar o pagamento? =

Não é obrigatório caso você não utilize antifraude, no caso para digital goods.

É possível remover os campos de endereço, empresa e telefone, mantendo apenas nome, sobrenome e e-mail utilizando o plugin [WooCommerce Digital Goods Checkout](https://wordpress.org/plugins/wc-digital-goods-checkout/).

= Problemas com a integração? =

Primeiro de tudo ative a opção **Log de depuração** e tente realizar o pagamento novamente.
Feito isso copie o conteúdo do log e salve usando o [pastebin.com](http://pastebin.com) ou o [gist.github.com](http://gist.github.com), depois basta abrir um tópico de suporte [aqui](http://wordpress.org/support/plugin/woocommerce-granito).

= Mais dúvidas relacionadas ao funcionamento do plugin? =

Entre em contato [clicando aqui](http://wordpress.org/support/plugin/woocommerce-granito).

== Screenshots ==

1. Exemplo de checkout com cartão de crédito e boleto bancário do Granito no tema Storefront.
2. Exemplo do Checkout Granito para cartão de crédito.
3. Configurações para boleto bancário.
4. Configurações para cartão de crédito.

== Changelog ==

= 2.0.15 - 2019-07-01 =

* Adicionado o novo status `analyzing` do Granito

= 2.0.14 - 2018/05/02 =

* Corrigido problema de resetar para 1 parcela ao recarregar o formulário de dados de cartão.

= 2.0.13 - 2018/05/29 =

* Permitido resgistro de pedido para transação recusada no checkout Granito.
* Corrige o timeout na captura no checkout Granito.

= 2.0.12 - 2018/07/31 =

* Adicionado cidade e estado nos dados enviados para o Checkout Granito.
* Adicionado suporte para "pending_review", melhorando a integração com boleto bancário.

= 2.0.11 - 2017/09/08 =

* Adicionada opção para suportar boletos assíncronos.

= 2.0.10 - 2016/09/29 =

* Adicionado `order_number` (número do pedido) como meta dado para transações com o Checkout Granito.

= 2.0.9 - 2016/09/27 =

* Corrigido vendas canceladas com o Checkout Granito feitas quando a parcela mínima era menor do que o mínimo permitido.

= 2.0.8 - 2016/09/15 =

* Adicionado `order_number` (número do pedido) como meta dado das transações.

= 2.0.7 - 2016/09/12 =

* Corrigido o valor da primeira parcela quando é menor do que o mínimo permitido.
* Adicionado código para corrigir o valor da taxa de juros antes de usar no Checkout Granito.

= 2.0.6 - 2016/09/09 =

* Corrigida a compatibilidade com o WordPress 4.6.
* Corrigido o calculo das parcelas do cartão de crédito.

= 2.0.5 - 2016/07/15 =

* Correções para previnir mensagens de erro ao receber notificações de pagamentos.

= 2.0.4 - 2016/06/08 =

* Melhorado o fluxo das transações feitas com o Checkout Granito.

= 2.0.3 - 2016/06/02 =

* Corrigido erro ao fazer uma transação com o Checkout Granito onde é adicionada taxa de juros.
* Adicionado campo informando o total pago pelo cliente incluindo juros quando aplicável.

= 2.0.2 - 2016/05/11 =

* Corrigida a validação de campos da finalização para o Checkout Granito.
* Melhorada das mensagens de erro para quando não abrir o Checkout Granito.

= 2.0.1 - 2016/04/04 =

* Permitida a validação dos campos da finalização antes de abrir o Checkout Granito.
* Corrigida a mudança de status do Checkout Granito.

= 2.0.0 - 2016/04/02 =

* Adicionado novo método para pagamento com cartões de crédito.
* Adicionado novo método para pagamentos com boleto bancário.
* Adicionado suporte ao Checkout Granito para pagamentos com cartão de crédito.
* Corrigida a exibição do boleto na página "Minha conta", fazendo os boletos aparecer apenas quando o pedido esta com os status de pendente ou aguardando.

= 1.2.4 - 2016/02/04 =

* Adiciona opção para cobrar juros de todas as parcelas do cartão de crédito.

= 1.2.3 - 2016/01/27 =

* Removida dependência do plugin WooCommerce Extra Checkout Fields From Brazil.
* Removida dependência dos campos de endereço, telefone e empresa (obrigatório apenas nome, sobrenome e e-mail).
* Adicionado link para segunda via do boleto na tela de administração de pedidos e na página "Minha Conta".

= 1.2.2 - 2014/10/27 =

* Atualizada URL da biblioteca JavaScript do Granito.

= 1.2.1 - 2014/10/27 =

* Corrigido o método que manipula os retornos do Granito.

= 1.2.0 - 2014/10/12 =

* Adicionada opção para controlar o número de parcelas sem juros.

= 1.1.0 - 2014/09/07 =

* Adicionado suporte para a API de parcelas do Granito.
* Adicionada opção de taxa de juros para as parcelas.
* Adicionado suporte para o WooCommerce 2.2.

= 1.0.0 =

* Versão incial do plugin.

== Upgrade Notice ==

= 2.0.12 =

* Adicionado cidade e estado nos dados enviados para o Checkout Granito.
* Adicionado suporte para "pending_review" melhorando a integração com boleto bancário.
