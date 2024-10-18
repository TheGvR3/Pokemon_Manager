<?php
function get_pokemon_egg_groups($conn, $pokemon_id) {
    $sql_pokemon_egg_groups = "
    SELECT egg_groups.group_name
    FROM pokemon_egg_groups
    JOIN egg_groups ON pokemon_egg_groups.egg_group_id = egg_groups.id
    WHERE pokemon_egg_groups.pokemon_id = ?
    ";
    $stmt = $conn->prepare($sql_pokemon_egg_groups);
    $stmt->bind_param("i", $pokemon_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}
