# Postaqui - Módulo de integração para Magento 1.9.x
Integração oficial para lojas em Magento 1.9.x via Postaqui

## Pre-requisitos

- Magento a partir da versão 1.9.0;
- PHP versão 5.6 ou superior;
- Biblioteca CURL habilitada.

# Recursos

Integre sua loja com as principais transportadoras do mercado

## Transportadoras

- Correios (PAC, SEDEX)
- Jadlog (Convencional e Expresso)

## Características

- Integre sua loja diretamente com as transportadoras, sem necessidade de tabelas;
- Cálculo transparênte de preços;
- Emissão de etiqueta automática ao término do pedido;
- Painel de controle da Postaqui para você acompanhar suas etiquetas;
- Muito mais, confira no link: [link]

# Módulo

## Instalação

1) Baixe o módulo a partir deste repositório para sua máquina;
2) Cole as pastas do módulo na raiz da sua loja virtual;
3) Faça login novamente na plataforma.
4) Apague o cache do Magento. Todas as opções de cache podem ser vistas em "Sistema" > "Gerenciamento de cache";

## Configuração

1) Acesse o Menu Configurações > grupo Clientes > Configurações, na aba "Opções de Nome e Endereço", deixe o valor "4" para o campo "Número de linhas p/Endereço";
2) Ainda na mesma aba, deixe como obrigatório o campo "Exibir CPF/CNPJ".
3) As parametrizações do módulo estão no menu Configurações > Vendas > Métodos de entrega. Encontre a aba da Postaqui.
4) Marque "Sim" para habilitar o módulo.
5) Insira o título da transportadora ou serviço que será exibida ao cliente, no momento de exibir a cotação de frete.
6) Insira o CEP de localização onde será feito a postagem do(s) produto(s).
7) Insira o Token de autenticação do seu usuário no portal Postaqui. Para mais informações acesse: [link]
8) Se preferir, altere a ordem de exibição das opções de transporte em sua loja, se não souber mantenha o padrão.