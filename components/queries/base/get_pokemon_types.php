<?php
function get_pokemon_types($conn, $pokemon_id) {
    $sql_pokemon_types = "
    SELECT types.type_name, types.type_name_img, pokemon_types.type_position
    FROM pokemon_types
    JOIN types ON pokemon_types.type_id = types.id
    WHERE pokemon_types.pokemon_id = ?
    ORDER BY pokemon_types.type_position;
    ";
    $stmt = $conn->prepare($sql_pokemon_types);
    $stmt->bind_param("i", $pokemon_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}
