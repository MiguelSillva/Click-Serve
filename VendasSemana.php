<?php
require_once 'Bd.php';

$id = (int) $_SESSION['id'];

$nomes_dias = [
    1 => 'Domingo',
    2 => 'Segunda-feira',
    3 => 'Terça-feira',
    4 => 'Quarta-feira',
    5 => 'Quinta-feira',
    6 => 'Sexta-feira',
    7 => 'Sábado'
];

$sql = "SELECT 
    DAYOFWEEK(data) AS dia_da_semana,
    HOUR(data) AS hora,
    SUM(valor) AS total
FROM 
    vendas
WHERE 
    usuario_id = $id
    AND DATE(data) >= CURDATE() - INTERVAL 7 DAY  
GROUP BY 
    dia_da_semana, hora
ORDER BY 
    dia_da_semana, hora;";

$resultado = $con->query($sql);

/* while ($linha = $resultado->fetch_assoc()) {
    $dia_nome = $nomes_dias[$linha['dia_da_semana']];
    echo "['$dia_nome', " . $linha['total'] . "],";


}*/

$vendasPorHora = [];
for ($hora = 1; $hora <= 7; $hora++) {
    $vendasPorHora[$hora] = 0.0;
}
if ($resultado) {
    while ($linha = $resultado->fetch_assoc()) {
        $hora = (int) $linha['hora'];
        $total = (float) $linha['total'];
        $vendasPorHora[$hora] = $total;
    }
} else {
    echo "console.error('Erro na SQL: " . $con->error . "');";
    return;
}

// Gera saída no formato [hora, total]
foreach ($vendasPorHora as $hora => $total) {
    echo "['{$hora}º', $total],\n";
}
?>
