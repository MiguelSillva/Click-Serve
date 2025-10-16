<?php
require_once 'Bd.php';


$id = (int) $_SESSION['id'];

$sql = "SELECT 
    HOUR(data) AS hora,
    SUM(CASE WHEN valor > 0 THEN valor ELSE 0 END) as total
FROM 
    vendas
WHERE 
    usuario_id = $id
    AND DATE(data) = CURDATE()
GROUP BY 
    hora
ORDER BY 
    hora;";

$resultado = $con->query($sql);


$vendasPorHora = [];
for ($hora = 8; $hora <= 23; $hora++) {
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
    echo "['{$hora}h', $total],\n";
}
?>