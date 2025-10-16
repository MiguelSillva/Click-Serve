<?php
require_once 'Bd.php';


$id = (int) $_SESSION['id'];

$sql = "SELECT DAY(data) AS dia, SUM(Valor) AS total FROM vendas WHERE usuario_id = $id GROUP BY dia ORDER BY dia;";

$resultado = $con->query($sql);

while ($linha = $resultado->fetch_assoc()) {
    echo "['" . $linha['dia'] . "', " . $linha['total'] . "],";
}
?>
