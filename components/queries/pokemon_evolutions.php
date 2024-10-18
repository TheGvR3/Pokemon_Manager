<?php
$sql_evolves_from = "
SELECT evolves_from_pokemon.name AS evolves_from_pokemon_name, 
    evolves_from_pokemon.pokemon_icon AS evolves_from_pokemon_icon,
    pokemon_evolutions.description AS evolves_from_description,
    evolves_from_pokemon.id AS evolves_from_pokemon_id
FROM pokemon_evolutions
JOIN pokemon AS evolves_from_pokemon ON pokemon_evolutions.pokemon_id = evolves_from_pokemon.id
WHERE pokemon_evolutions.evolves_to_pokemon_id = ?;
";

$sql_evolves_to = "
SELECT evolves_to_pokemon.name AS evolves_to_pokemon_name, 
    evolves_to_pokemon.pokemon_icon AS evolves_to_pokemon_icon,
    pokemon_evolutions_evolve_to.description AS evolves_to_description,
    evolves_to_pokemon.id AS evolves_to_pokemon_id
FROM pokemon_evolutions AS pokemon_evolutions_evolve_to
JOIN pokemon AS evolves_to_pokemon ON pokemon_evolutions_evolve_to.evolves_to_pokemon_id = evolves_to_pokemon.id
WHERE pokemon_evolutions_evolve_to.pokemon_id = ?;
";
