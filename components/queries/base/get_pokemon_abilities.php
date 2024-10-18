<?php
function get_pokemon_abilities($conn, $pokemon_id) {
    $sql_pokemon_abilities = "
    SELECT abilities.ability_name, abilities.ability_description, pokemon_abilities.ability_type
    FROM pokemon_abilities
    JOIN abilities ON pokemon_abilities.ability_id = abilities.id
    WHERE pokemon_abilities.pokemon_id = ?
    ORDER BY pokemon_abilities.ability_type;
    ";
    $stmt = $conn->prepare($sql_pokemon_abilities);
    $stmt->bind_param("i", $pokemon_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}
