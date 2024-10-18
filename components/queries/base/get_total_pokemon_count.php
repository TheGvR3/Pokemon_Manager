<?php
function get_total_pokemon_count($conn) {
    $sql_total_pokemon_count = "SELECT COUNT(*) AS total FROM pokemon";
    $stmt = $conn->prepare($sql_total_pokemon_count);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['total'];
}
