# Query-Class

## Inclusão da Classe

```php
    include_once "diretorio/Query.class.php";
```

## Chamada da Classe

Existem 2 formas de utilizar chamar a classe, cada uma oferece uma solução diferente para determinado tipo de problema.

### Solução 1: Para uso em apenas UMA DATABASE

Deve-se editar o arquivo **Query.class.php** colocar as informação de conexão dentro da classe:

```php
    private $ip = "IP";
    private $database = "DATABASE";
    private $user = "USER";
    private $pass = "PASSWORD";
```

Concluído a edição basta guardar a chamada da classe em uma variálvel.

```php
    $query = new Query();
```

### Solução 2: Para utiliza-la em várias DATABASES

Você pode utiliza-la de 2 formas:

> Definindo uma DATABASE em cada requisição:

Toda vez que for chamar a classe deverá colocar as informações de conexão dentro da chamada:
```php
	$query1 = new Query("IP1", "DATABSE1", "USER1", "PASSWORD1");
	$query2 = new Query("IP2", "DATABSE2", "USER2", "PASSWORD2");
```
OU

> Definir uma DATABASE principal na classe:

Dessa forma você configura a conexão da DATABASE principal dentro do **Query.class.php** e chama outras DATABASES da forma anterior:

```php
	// DATABASE principal
	$query1 = new Query();
	// DATABASE secundária
	$query2 = new Query("IP", "DATABSE", "USER", "PASSWORD");
```
Uma outra solução para a definição da conexão é por meio de um **Array**:

```php
	$conexao = Array(
		["ip"] => "IP",
		["database"] => "DATABASE",
		["user"] => "USER",
		["pass"] => "PASSWORD"
	);
	
	$query = new Query($conexao);
```

## Modo de uso:
Suponhamos que temos a seguinte situção:

***Tabela usuario:***

 id | nome | sobrenome 
----|------|----------
 1 | Guilherme | Biancardi
 2 | Leiliane| Paiva

Editamos a classe **Query.class.php** e já está tudo pronto para o uso, preciso selecionar todos nomes da tabela **usuario**:
```php
	$query = new Query();
	$sql = "SELECT * FROM usuarios";
	$dados = $query->Select($sql);
```
O retorno contido em **$dados** será um array com as informações a seguir:

```php
	Array(
		[0] => Array(
			["id"] => 1,
			["nome"] => "Guilherme",
			["sobrenome"] => "Biancardi"
		),
		[1] => Array(
			["id"] => 2,
			["nome"] => "Leiliane",
			["sobrenome"] => "Paiva"
		),
	);
```

No caso de uma atualização em algum registro ou remoção, deve-se utilizar dessa forma:
```php
	$query = new Query();
	// Update
	$sql = "UPDATE usuarios SET nome = 'Leily' WHERE id = 2";
	// Remoção
	$sql = "DELETE FROM `usuarios` WHERE id = 2";
	$dados = $query->Atualizar($sql);
```
O retorno será um boleano `TRUE` ou `FALSE`, e para o caso de inserção:
```php
	$query = new Query();
	$sql = "INSERT INTO usuarios (id, nome, sobrenome) VALUES (NULL, 'Ana', 'Clara')";
	$dados = $query->Inserir($sql);
```
Neste exemplo nosso campo **ID** da tabela **usuario** é uma chave primária auto incrementada (padrão), o retorno contido em **$dados** será o **ID** deste novo registro na tabela.

E caso precise visualizar o retorno de uma busca à determinada tabela utilize:

```php
	$query = new Query();
	$sql = "SELECT * FROM usuarios";
	$dados = $query->Select($sql);
	$query->printr($dados);
```
Assim será mostrado na tela o **Array** resultante já mostrado anteriormente:

```php
	Array(
		[0] => Array(
			["id"] => 1,
			["nome"] => "Guilherme",
			["sobrenome"] => "Biancardi"
		),
		[1] => Array(
			["id"] => 2,
			["nome"] => "Leiliane",
			["sobrenome"] => "Paiva"
		),
	);
```

## Segurança

Para evitar qualquer tipo de problema com SQL Injection, pode-se utilizar um método de sua preferência ou o disponível na própria classe:

```php
	$query = new Query();
	$sql = sprintf(
		"SELECT * FROM usuarios WHERE nome = '%s'",
		$query->AntiSqlInjection("1' OR '1'='1") // Tentativa de SQLInjection a ser tratada
	);
	$response = $query->Select($sql);
```
O Resultado de **$sql** será o SQL Tratado para inserção:

    SELECT * FROM usuarios WHERE nome = '1&#39; OR &#39;1&#39;=&#39;1'

Pode-se utilizar tambem o metodo **AntiSqlInjection()** para guardar caracteres especiais no banco sem ter problemas com charset na hora de exibi-los:

```php
	$query = new Query();
	$sql = sprintf(
		"SELECT * FROM usuarios WHERE nome = '%s'",
		$query->AntiSqlInjection("\nIñtërnâtiônàlizætiøn\t") // String a ser tratada
	);
	$response = $query->Select($sql);
```

O Resultado de **$sql** será o SQL Tratado para inserção:

    SELECT * FROM usuarios WHERE nome = 'I&#195;&#177;t&#195;&#171;rn&#195;&#162;ti&#195;&#180;n&#195;&#160;liz&#195;&#166;ti&#195;&#184;n'

Aproveitem!!!
