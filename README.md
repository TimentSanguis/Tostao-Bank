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

<h1>✅ Como utilizar o Programa:</h1>
<h2>🔧 Requisitos para rodar o projeto:</h2>
<h3>Antes de iniciar, certifique-se de ter os seguintes programas instalados:</h3>

<h3>Java JDK 17 ou superior</h3>
<p>→ https://www.oracle.com/java/technologies/javase-downloads.html</p>

<h3>IDE Java (recomendado: IntelliJ IDEA ou Eclipse)</h3>
<p>→ https://www.jetbrains.com/idea/ ou https://www.eclipse.org/downloads/</p>

<h3>XAMPP (para rodar o servidor PHP + HTML)</h3>
<p>→ https://www.apachefriends.org/</p>

<h1>🧩 Parte 1 – Executando a API (Java + Spring Boot)</h1>

<h2>Abra a IDE (IntelliJ ou Eclipse)</h2>

<p>Importe o projeto da API:</p>

Vá em File > Open e selecione a pasta do projeto Java (onde está o pom.xml).

Aguarde a IDE importar as dependências do Maven.

Verifique o arquivo de configuração

Abra o arquivo src/main/resources/application.properties e verifique se a configuração do banco está assim:

<p>properties</br>
</br>
spring.datasource.url=jdbc:h2:mem:tostaobankdb</br>
spring.datasource.driverClassName=org.h2.Driver</br>
spring.datasource.username=sa</br>
spring.datasource.password=</br>
spring.jpa.database-platform=org.hibernate.dialect.H2Dialect</br>
spring.h2.console.enabled=true</br>
spring.h2.console.path=/h2-console</p></br>
<p>⚠️ O banco H2 é temporário (em memória), reiniciado a cada execução.</p></br>

<h2>Inicie a API</h2>

Na classe principal do projeto (TostaoBankApplication.java), clique com o botão direito e selecione Run.

Exemplo da classe principal:

<pre>
    <code>
java

@SpringBootApplication
public class TostaoBankApplication {
    public static void main(String[] args) {
        SpringApplication.run(TostaoBankApplication.class, args);
    }
}
</code>
</pre>

<h2>Testar a API</h2>
<p>Após iniciar, a API estará disponível em:</br>
→ http://localhost:8080</p>

Endpoints disponíveis (exemplos):

/api/login

/api/cadastro

/api/transferencia

/api/seguros

<h1>🌐 Parte 2 – Executando o Front-end PHP (XAMPP)</h1>

<ol>
<li>Abra o XAMPP Control Panel</li>
<li>Inicie o módulo Apache</li>
<li>Clique em Start ao lado de Apache</li>
<li>Copie os arquivos PHP para a pasta certa</li>
<li>Mova os arquivos PHP e HTML do projeto para a pasta:</li>
</ol>
</br>
<p><strong><MARK>C:\xampp\htdocs\tostaobank</MARK></strong></p>

<h2>Acesse pelo navegador</h2>

Digite no navegador:

<p><strong><MARK>http://localhost/tostaobank/index.php</MARK></strong></p></br>
<strong>⚠️ Certifique-se de que a API (Java) já esteja rodando antes de usar o sistema.</strong>

<h2>🔗 Integração entre PHP e a API</h2>
<p>Nesta versão, o sistema PHP se comunica com a API via requisições HTTP usando file_get_contents ou curl, dispensando AJAX.
O PHP atua como ponte entre o usuário e os serviços da API (login, cadastro, etc.).</p>

<h2>🧪 Testes e observações</h2>
<ol>
    <li>O banco H2 é temporário, então os dados não são salvos entre execuções.</li>
    <li>Para dados persistentes, basta trocar o H2 por um banco real como PostgreSQL ou MySQL no application.properties.</li>
</ol>
