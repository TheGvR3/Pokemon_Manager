<?php
function get_pokemon_pre_evolution($conn, $pokemon_id) {
    $sql_pokemon_pre_evolution = "
    SELECT evolves_from_pokemon.name AS evolves_from_pokemon_name, 
        evolves_from_pokemon.pokemon_icon AS evolves_from_pokemon_icon,
        pokemon_evolutions.description AS evolves_from_description,
        evolves_from_pokemon.id AS evolves_from_pokemon_id
    FROM pokemon_evolutions
    JOIN pokemon AS evolves_from_pokemon ON pokemon_evolutions.pokemon_id = evolves_from_pokemon.id
    WHERE pokemon_evolutions.evolves_to_pokemon_id = ?;
    ";
    $stmt = $conn->prepare($sql_pokemon_pre_evolution);
    $stmt->bind_param("i", $pokemon_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
