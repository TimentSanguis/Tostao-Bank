Tostão Bank é um projeto desenvolvido na Fatec Mauá pelos alunos Caio Luis Cara, Giovanni França da Silva, Thiago Oliveira Barbero, Victor Baccon Vieira, Danilo Gonçalves Francelino, Eduardo Santos Urbano e Alan Alonso Fiorelini. Nem todos os integrantes participaram integralmente do projeto do início ao fim.

O conceito é desenvolver um banco fictício utilizando PHP e CSS, com o objetivo de ser simples e acessível, permitindo que os usuários o utilizem com facilidade.

![tostão bank apresentação id visual](https://github.com/user-attachments/assets/afdfeaac-6484-4a4a-907a-c4432aed3363)

⚠️ Aviso
Este projeto foi desenvolvido unicamente para fins educacionais, como parte de um estudo acadêmico na Fatec Mauá.

O código-fonte está disponível livremente e pode ser utilizado por qualquer pessoa, sem garantias de funcionamento, segurança ou manutenção.

Sinta-se à vontade para estudar, modificar e reutilizar o código conforme necessário. :)

![tostão bank apresentação id visual (2)](https://github.com/user-attachments/assets/8d33c94b-87a6-4626-af62-7d3159027342)

![image](https://github.com/user-attachments/assets/be8fa3a1-f028-4f88-9955-ed3e4981c123)


📄 Licença
Este projeto está licenciado sob a Licença MIT. Consulte o arquivo [LICENSE](./LICENSE) para mais detalhes.

<h3>Tradução da Licença MIT (versão não oficial)

A permissão é concedida, sem custos, para qualquer pessoa obter uma cópia deste software e dos arquivos de documentação associados (o "Software"), para usá-lo sem restrições, incluindo, sem limitação, os direitos de usar, copiar, modificar, mesclar, publicar, distribuir, sublicenciar e/ou vender cópias do Software, e permitir que as pessoas a quem o Software for fornecido façam o mesmo, desde que as seguintes condições sejam atendidas:

A nota de copyright acima e esta permissão devem ser incluídas em todas as cópias ou partes substanciais do Software.

O SOFTWARE É FORNECIDO "COMO ESTÁ", SEM GARANTIAS DE QUALQUER TIPO, EXPRESSAS OU IMPLÍCITAS, INCLUINDO, MAS NÃO SE LIMITANDO ÀS GARANTIAS DE COMERCIALIZAÇÃO, ADEQUAÇÃO A UM DETERMINADO FIM E NÃO INFRAÇÃO. EM NENHUM CASO OS AUTORES OU DETENTORES DE DIREITOS AUTORAIS SERÃO RESPONSÁVEIS POR QUAISQUER RECLAMAÇÕES, DANOS OU OUTRAS RESPONSABILIDADES, SEJA EM AÇÃO DE CONTRATO, TORTO OU OUTRO, DECORRENTES DE, OU EM CONEXÃO COM O SOFTWARE OU O USO OU OUTRAS NEGOCIAÇÕES NO SOFTWARE.</h3>

✅ Como utilizar o Programa:
🔧 Requisitos para rodar o projeto:
Antes de iniciar, certifique-se de ter os seguintes programas instalados:

Java JDK 17 ou superior
→ https://www.oracle.com/java/technologies/javase-downloads.html

IDE Java (recomendado: IntelliJ IDEA ou Eclipse)
→ https://www.jetbrains.com/idea/ ou https://www.eclipse.org/downloads/

XAMPP (para rodar o servidor PHP + HTML)
→ https://www.apachefriends.org/

🧩 Parte 1 – Executando a API (Java + Spring Boot)
Abra a IDE (IntelliJ ou Eclipse)

Importe o projeto da API:

Vá em File > Open e selecione a pasta do projeto Java (onde está o pom.xml).

Aguarde a IDE importar as dependências do Maven.

Verifique o arquivo de configuração

Abra o arquivo src/main/resources/application.properties e verifique se a configuração do banco está assim:

properties
Copiar
Editar
spring.datasource.url=jdbc:h2:mem:tostaobankdb
spring.datasource.driverClassName=org.h2.Driver
spring.datasource.username=sa
spring.datasource.password=
spring.jpa.database-platform=org.hibernate.dialect.H2Dialect
spring.h2.console.enabled=true
spring.h2.console.path=/h2-console
⚠️ O banco H2 é temporário (em memória), reiniciado a cada execução.

Inicie a API

Na classe principal do projeto (TostaoBankApplication.java), clique com o botão direito e selecione Run.

Exemplo da classe principal:

java
Copiar
Editar
@SpringBootApplication
public class TostaoBankApplication {
    public static void main(String[] args) {
        SpringApplication.run(TostaoBankApplication.class, args);
    }
}
Testar a API
Após iniciar, a API estará disponível em:
→ http://localhost:8080

Endpoints disponíveis (exemplos):

/api/login

/api/cadastro

/api/transferencia

/api/seguros

🌐 Parte 2 – Executando o Front-end PHP (XAMPP)
Abra o XAMPP Control Panel

Inicie o módulo Apache

Clique em Start ao lado de Apache

Copie os arquivos PHP para a pasta certa

Mova os arquivos PHP e HTML do projeto para a pasta:

makefile
Copiar
Editar
C:\xampp\htdocs\tostaobank
Acesse pelo navegador:
Digite no navegador:

arduino
Copiar
Editar
http://localhost/tostaobank/index.php
⚠️ Certifique-se de que a API (Java) já esteja rodando antes de usar o sistema.

🔗 Integração entre PHP e a API
Nesta versão, o sistema PHP se comunica com a API via requisições HTTP usando file_get_contents ou curl, dispensando AJAX.
O PHP atua como ponte entre o usuário e os serviços da API (login, cadastro, etc.).

🧪 Testes e observações
O banco H2 é temporário, então os dados não são salvos entre execuções.

Para dados persistentes, basta trocar o H2 por um banco real como PostgreSQL ou MySQL no application.properties.
