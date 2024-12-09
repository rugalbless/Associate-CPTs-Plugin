# Gerenciamento de Empresas e Vagas

## Descrição

Este é um plugin WordPress desenvolvido para gerenciar a associação entre **Empresas** e **Vagas**, oferecendo controle de acesso específico para autores. Ele facilita a criação e manutenção de postagens relacionadas a empresas e vagas, limitando permissões de usuários e permitindo associações personalizadas.

## Funcionalidades

- **Custom Post Types (CPT):**
  - **Empresas**: Gerenciamento de empresas com suporte a título, descrição e imagem destacada.
  - **Vagas**: Gerenciamento de vagas com suporte a título, descrição, campos personalizados como salário e telefone.

- **Campos Personalizados:**
  - Adição de salário e telefone às vagas, com a opção de "A combinar" no campo de salário.

- **Controle de Acesso:**
  - Restringe autores a visualizarem apenas as vagas criadas por eles.
  - Limita a associação de empresas a autores, configurada exclusivamente por administradores.

- **Interface do Usuário:**
  - Customizações na interface administrativa para ocultar opções irrelevantes para autores.
  - Seleção de empresas associadas diretamente no perfil do usuário.

- **Filtragem Automática:**
  - Empresas são filtradas para exibição apenas para seus autores associados.
  - Vagas aparecem apenas para seus criadores.

## Como Funciona

1. **Administração de Empresas:**
   - Somente administradores podem criar e gerenciar empresas.
   - Administradores podem associar autores a uma empresa específica.

2. **Gerenciamento de Vagas:**
   - Autores podem criar, editar e visualizar vagas vinculadas a eles.
   - Vagas possuem campos personalizados para informações adicionais, como salário e telefone.

3. **Controle de Menu:**
   - Itens de menu irrelevantes (como comentários, plugins e temas) são ocultados para usuários que não possuem permissões administrativas.

## Diretrizes

1. Apenas administradores podem criar empresas e associá-las a autores.
2. Autores podem gerenciar somente as vagas relacionadas à empresa associada.
3. Todos os campos personalizados de vagas são validados e sanitizados antes de serem salvos.


## Exemplo de Uso

1. O administrador cria uma empresa no menu "Empresas".
2. O administrador associa um autor à empresa recém-criada no perfil do usuário.
3. O autor acessa o menu "Vagas" e cria vagas associadas à empresa para a qual está autorizado, podendo vizualizar, editar e excluir a vaga posteriormente.

## Requisitos

- WordPress 5.8 ou superior.
- PHP 7.4 ou superior.

## Autor

Desenvolvido por **Ruan Pablo**.  
Versão: **2.3**

---

