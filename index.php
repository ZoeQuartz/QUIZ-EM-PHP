<?php
$jsonString = file_get_contents('perguntas.json');
$questoes = json_decode($jsonString, true) ?? [];
$resultado = null;
$feedback = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $acertos = 0;
    $total = count($questoes);

    //construção das questoes:
    foreach($questoes as $indice => $questao){
        $respostaUsuario = $_POST["questao_{$indice}"] ?? '';
        $correta = ($respostaUsuario === $questao['resposta']);

        //Valida respostas do usuario
        if ($correta){
            $acertos++;
        }

        //Guarda o feedback de cada questao
        $feedback[$indice] = [
            'resposta_usuario' => $respostaUsuario,
            'correta' => $correta
        ];
    }

    $erros = $total - $acertos;
    $porcentagem = $total > 0 ? round(($acertos / $total) * 100) : 0;

    $resultado = "Você acertou <strong>{$acertos}</strong> de <strong>{$total}</strong> questoes!";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz de Perguntas Olimpiadas</title>
    <!-- Vinculando o arquivo CSS externo -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Quiz de Perguntas Olimpiadas</h1>
<img src="imagem.png" alt="Imagem do quiz de Olimpíadas" class="banner">

<?php if ($resultado !== null): ?>
    <!-- Placar -->
    <div class="placar">
        <div class="item acerto">
            <span>Acertos</span>
            <strong><?php echo $acertos; ?></strong>
        </div>
        <div class="item erro">
            <span>Erros</span>
            <strong><?php echo $erros; ?></strong>
        </div>
        <div class="item">
            <span>Aproveitamento</span>
            <strong><?php echo $porcentagem; ?>%</strong>
        </div>
    </div>

    <div class="resultado">
        <h2>Resultado:</h2>
        <p><?php echo $resultado; ?></p>
        <a href="">Tentar novamente</a>
    </div>
<?php endif; ?>

<!-- Construindo as perguntas e opcoes -->
<form method="POST">
    <?php foreach ($questoes as $indice => $questao): ?>
        <?php
            // Define a classe visual conforme o acerto ou erro
            $status = '';
            if (isset($feedback[$indice])) {
                $status = $feedback[$indice]['correta'] ? 'certa' : 'errada';
            }
        ?>

        <div class="questao <?php echo $status; ?>">
            <p><?php echo ($indice + 1) . ". " . htmlspecialchars($questao['Pergunta']); ?></p>

            <?php foreach ($questao['alternativa'] as $alt): ?>
                <?php
                    // Mantém marcada a alternativa escolhida pelo usuário
                    $marcada = (isset($feedback[$indice]) && $feedback[$indice]['resposta_usuario'] === $alt) ? 'checked' : '';
                ?>
                <label>
                    <br><input type="radio" name="questao_<?php echo $indice; ?>" value="<?php echo htmlspecialchars($alt); ?>" required <?php echo $marcada; ?>>
                    <?php echo htmlspecialchars($alt); ?>
                </label>
            <?php endforeach; ?>

            <!-- Feedback de acerto ou erro -->
            <?php if (isset($feedback[$indice])): ?>
                <p class="feedback">
                    <?php if ($feedback[$indice]['correta']): ?>
                        ✅ Correto!
                    <?php else: ?>
                        ❌ Errado. Sua resposta: <?php echo htmlspecialchars($feedback[$indice]['resposta_usuario']) ?: 'nenhuma'; ?>
                        | Correta: <strong><?php echo htmlspecialchars($questao['resposta']); ?></strong>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <button type="submit">Enviar Respostas</button>
</form>

<?php if ($resultado !== null): ?>
    <a href="index.php" class="botao-reiniciar">Responder novamente</a>
<?php endif; ?>

</body>
</html>