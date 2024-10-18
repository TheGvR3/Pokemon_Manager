<?php
function get_pokemon_next_evolution($conn, $pokemon_id) {
    $sql_pokemon_next_evolution = "
    SELECT evolves_to_pokemon.name AS evolves_to_pokemon_name, 
        evolves_to_pokemon.pokemon_icon AS evolves_to_pokemon_icon,
        pokemon_evolutions_evolve_to.description AS evolves_to_description,
        evolves_to_pokemon.id AS evolves_to_pokemon_id
    FROM pokemon_evolutions AS pokemon_evolutions_evolve_to
    JOIN pokemon AS evolves_to_pokemon ON pokemon_evolutions_evolve_to.evolves_to_pokemon_id = evolves_to_pokemon.id
    WHERE pokemon_evolutions_evolve_to.pokemon_id = ?;
    ";
    $stmt = $conn->prepare($sql_pokemon_next_evolution);
    $stmt->bind_param("i", $pokemon_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
