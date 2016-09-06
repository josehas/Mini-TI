<?php
	include 'php/funcoes.php';
	include 'php/constantes.php';
	session_start();

	if (!verificaSeSessaoExiste()) {
		$msgErroGeral = "Faça login para acessar o sistema!";
	} else {
		$nome = $_SESSION["nome"];
		$email = $_SESSION["email"];
		$senha = $_SESSION["senha"];
		$telefone = $_SESSION["telefone"];
		$endereco = $_SESSION["endereco"];

		$msgErroEmail = $msgErroNome = $msgErroTelefone = $msgErroEndereco = $msgErroSenha = "";
		$msgErroGeral = "";
		$msgSucessoAlteracao = "";

		if ($_SERVER["REQUEST_METHOD"] == "POST") {

			// Verifica se 'nome' foi enviado pelo POST:
			if (empty($_POST["nome"])) {
				$msgErroNome = "Nome é requerido!";
			} else {
				$nome = trataEntrada($_POST["nome"]);
				if (!verificaSeNomeEValido($nome)) {
					$msgErroNome = "Nome deve conter apenas LETRAS e ESPAÇOS!";
				}
			}
			// - - - - - - - - - - - - - - - - - - - - - -

			// Verifica se 'email' foi enviado pelo POST:
			if (empty($_POST["email"])) {
				$msgErroEmail = "E-mail é requerido!";
			} else {
				$email = trataEntrada($_POST["email"]);

				if (!verificaSeEmailEValido($email)) {
					$msgErroEmail = "Formato de e-mail inválido!";
				}

				$emailJaExiste = verificaSeOutroEmailExiste($email, $_SESSION["idCliente"]);
				if ($emailJaExiste === true) {
					$msgErroEmail = "Este e-mail já foi cadastrado!";
				} else if ($emailJaExiste === null) {
					$msgErroGeral = "Não foi possível conectar ao servidor!<br>Tente novamente mais tarde.";
					terminaSessao();
				}
			}
			// - - - - - - - - - - - - - - - - - - - - - - - - -

			$telefone = trataEntrada($_POST["telefone"]);
			if (!verificaSeTelefoneEValido($telefone)) {
				$msgErroTelefone = "Formato de telefone inválido!";
			}

			$endereco = trataEntrada($_POST["endereco"]);
			if (!verificaSeEnderecoEValido($endereco)) {
				$msgErroEndereco = "Número máximo de caracteres alcançado!";
			}

			if (verificaSeFormularioSemSenhaEValido($msgErroNome, $msgErroEmail, $msgErroTelefone, $msgErroEndereco)) {
				// Grava na base de dados:
				if (verificaSeFormularioEValido($msgErroNome, $msgErroEmail, $msgErroSenha, $msgErroTelefone, $msgErroEndereco)) {
					if (atualizaDadosDeCliente($nome, $email, $senha, $telefone, $endereco)) {
						mudaDePagina("sistema.php");
					} else {
						$msgErroGeral = "Não foi possível conectar ao servidor!<br>Tente novamente mais tarde.";
						terminaSessao();
					}
				}
			}
		}
	}




?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Edite sua conta Mini TI!</title>
	<link href="css/estilos_gerais.css" rel="stylesheet" type="text/css">
</head>
<body>
		<?php
		if (verificaSeSessaoExiste()) {
		?>
			<div class='caixaInterface sistema'>
				<div class='cabecalhoSistema editarDadosCadastrais'>
					<p class='bemVindo'>Edite seus dados</p>
					<a href='sistema.php'><button type='button' class='botao sair'>Voltar</button></a>
				</div>

				<div class='caixaInterface editarDados'>

					<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
						<input type='text' name='nome' class='campoDeEntrada' placeholder='nome completo' required value="<?php echo $nome;?>">
						<?php verificaMsgECriaBalao($msgErroNome, "balaoMsg erro");?>
						<?php verificaMsgECriaBalao($msgSucessoAlteracao, "balaoMsg");?>

						<input type="text" name="email" class="campoDeEntrada" placeholder="e-mail" value="<?php echo $email;?>" maxlength="<?php echo MAX_CHAR_EMAIL;?>" required>
						<?php verificaMsgECriaBalao($msgErroEmail, "balaoMsg erro");?>

						<input type="text" name="telefone" class="campoDeEntrada" value="<?php echo $telefone;?>" placeholder="telefone" maxlength="<?php echo MAX_CHAR_TELEFONE;?>">
						<?php verificaMsgECriaBalao($msgErroTelefone, "balaoMsg erro");?>

						<input type="text" name="endereco" class="campoDeEntrada" value="<?php echo $endereco;?>" placeholder="endereço" maxlength="<?php echo MAX_CHAR_ENDERECO;?>">
						<?php verificaMsgECriaBalao($msgErroEndereco, "balaoMsg erro");?>

						<br><input type="submit" name="botaoEnviarDados" value="Efetuar Alterações">
					</form>

					<a href=""><button type="button" class="botao botaoCadastrese">Alterar Senha</button></a>
					<br><a href=""><button type="button" class="botao excluirConta">Excluir Conta</button></a>

				</div>
			</div>
		<?php
		} else {
			apresentaTelaDeErro($msgErroGeral);
		}
		?>
</body>
</html>